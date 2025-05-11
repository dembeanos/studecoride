<?php

declare(strict_types=1);

require_once __DIR__ . '/../../Database/Database.php';
require_once __DIR__ . '/../../Traits/MessageTrait.php';
require_once __DIR__ . '/../../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

final class Reservation
{

    use ExceptionTrait;
    use MessageTrait;

    private $pdo;
    private $userId;
    private $reservationId;
    protected $idOffer;
    private $price;
    private $placeAvailable;
    private $reservedPlaces;
    private $dateReservation;
    private $hourDepart;
    private $dateDepart;
    private $dateArrival;
    private $hourArrival;

    public function __construct($pdo, $userId)
    {
        $this->pdo = $pdo;
        $this->userId = $userId;
    }

    // SETTERS

    private function setIdOffer($idOffer)
    {
        if (is_int($idOffer) && $idOffer > 0) {
            $this->idOffer = $idOffer;
        } else {
            $this->sendPopup('Erreur lors de la réservation');
        }
    }

    private function setPrice($price)
    {
        if (is_int($price) && $price > 0) {
            $this->price = $price;
        } else {
            $this->sendToDev('Le prix est invalide');
        }
    }

    private function setReservedPlaces($reservedPlaces)
    {
        if (is_int($reservedPlaces) && $reservedPlaces > 0) {
            $this->reservedPlaces = $reservedPlaces;
        } else {
            $this->sendToDev('Erreur lors de la réservation');
        }
    }

    public function setReservationId($reservationId)
    {
        if (!is_numeric($reservationId)) {
            return $this->sendToDev('La référence de la réservation est invalide');
        }

        $this->reservationId = (int)$reservationId;
    }


    // GETTERS

    public function getUserId(){ return $this->userId;}
    public function getPrice(){ return $this->price;}
    public function getIdOffer(){ return $this->idOffer;}
    public function getReservedPlaces(){ return $this->reservedPlaces;}
    public function getReservationId(){ return $this->reservationId;}    
    public function getDateReservation(){ return $this->dateReservation;}    
    public function getHourDepart(){ return $this->hourDepart; }
    public function getDateDepart(){ return $this->dateDepart;}
    public function getDateArrival(){ return $this->dateArrival;}    
    public function getHourArrival(){ return $this->hourArrival;}


    //Fonction Principales :

    public function getReservation()
    {
        try {
            $queryReservation = "SELECT creationdate, idoffer, reservationid, status 
                                 FROM reservations WHERE iduser = :userId";
            $checkStatement = $this->pdo->prepare($queryReservation);
            $checkStatement->bindValue(':userId', $this->userId, PDO::PARAM_INT);
            $checkStatement->execute();
            $reservations = $checkStatement->fetchAll(PDO::FETCH_ASSOC);

            if (!$reservations) {
                return $this->sendToDev('Aucune réservation trouvé pour cette utilisateur');
            }

            $result = [];

            foreach ($reservations as $reservation) {
                if (!isset($reservation['idoffer'])) {
                    throw new Exception("La réservation ne contient pas de 'idoffer'.");
                }

                $queryOffer = 'SELECT price, datedepart, hourdepart, citydepart, datearrival, hourarrival, arrivalcity, iduser 
                               FROM offers WHERE offerid = :idoffer';
                $statement = $this->pdo->prepare($queryOffer);
                $statement->bindValue(':idoffer', $reservation['idoffer'], PDO::PARAM_INT);
                $statement->execute();
                $offer = $statement->fetch(PDO::FETCH_ASSOC);

                if (!$offer) {
                    return $this->sendToDev("Aucune offre trouvée pour cette réservation.");
                }

                if (!isset($offer['iduser'])) {
                    return $this->sendToDev("Le conducteur n'est pas défini pour cette offre.");
                }

                $queryDriver = 'SELECT l.username 
                                FROM logins l
                                JOIN users u ON l.loginId = u.idlogin
                                WHERE u.userId = :driverId';
                $statement = $this->pdo->prepare($queryDriver);
                $statement->bindValue(':driverId', $offer['iduser'], PDO::PARAM_INT);
                $statement->execute();
                $driver = $statement->fetch(PDO::FETCH_ASSOC);

                if (!$driver) {
                    return $this->sendToDev("Aucun conducteur correspondant trouvé.");
                }

                $result[] = [
                    'reservationId' => $reservation['reservationid'],
                    'dateReservation' => $this->getDateReservation(),
                    'status' => $reservation['status'],
                    'price' => $offer['price'],
                    'dateDepart' => $this->getDateDepart(),
                    'hourDepart' => $this->getHourDepart(),
                    'cityDepart' => $offer['citydepart'],
                    'dateArrival' => $this->getDateArrival(),
                    'hourArrival' => $this->getHourArrival(),
                    'arrivalCity' => $offer['arrivalcity'],
                    'driver' => $driver['username']
                ];
            }


            echo json_encode($result);
            exit;
        } catch (Exception $e) {
            $this->saveLog('Erreur lors de la récupération des réservations de l\'utilisateur' . $e, 'CRITICAL');
        }
    }



    public function cancelReservation()
    {

        try {
            // Annonce du démmarage d'une transaction complexe
            $this->pdo->beginTransaction();

            // Recuperation de l'id de l'offre
            $query = "SELECT idoffer FROM Reservations WHERE iduser = :userId AND reservationId = :reservationId";
            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':userId', (int)$this->userId, PDO::PARAM_INT);
            $statement->bindValue(':reservationId', (int)$this->reservationId, PDO::PARAM_INT);
            $statement->execute();

            $reservation = $statement->fetch(PDO::FETCH_ASSOC);
            if (!$reservation) {
                $this->sendToDev("Réservation non trouvée.");
            }

            $offerId = $reservation['idoffer'];

            // Recuperation des informations de du trajet
            $offerQuery = 'SELECT citydepart, datedepart, offerid, price, arrivalcity,iduser  FROM offers WHERE offerid = :offerId';
            $OfferStatement = $this->pdo->prepare($offerQuery);
            $OfferStatement->bindValue(':offerId', $offerId, PDO::PARAM_INT);

            if (!$OfferStatement->execute()) {
                $this->sendToDev("Erreur lors de la récupération des détails de l'offre.");
            }

            $offer = $OfferStatement->fetch(PDO::FETCH_ASSOC);
            if (!$offer) {
                $this->sendToDev("Aucun détail trouvé pour cette offre.");
            }
            //Sauvegarde des éléments nécéssaire en cas d'envoi de message
            $cityDepart = $offer['citydepart'];
            $arrivalCity = $offer['arrivalcity'];
            $driverUserId = $offer['iduser'];

            // Verification que la date ne soit pas inférieur à la date du jour.
            $currentDate = new DateTime();
            $departureDate = new DateTime($offer['datedepart']);

            if ($departureDate < $currentDate) {
                $this->sendToDev("Annulation impossible : la date de départ est déjà passée.");
            }

            //Lancement de la procedure d'annulation
            $updateQuery = "UPDATE Reservations SET status = 'canceled' WHERE iduser = :userId AND reservationId = :reservationId";
            $updateStatement = $this->pdo->prepare($updateQuery);
            $updateStatement->bindValue(':userId', (int)$this->userId, PDO::PARAM_INT);
            $updateStatement->bindValue(':reservationId', (int)$this->reservationId, PDO::PARAM_INT);

            // si la procédure reussi envoi d'un message de confirmation ainsi qu'un message d'information au conducteur.
            if ($updateStatement->execute()) {
                $this->sendPopup('Annulation enregistré avec succès');
                // Recuperation de l'id du conducteur pour envoi de message 
                $queryDriver = "SELECT idlogin FROM users WHERE userid = :driverUserId";
                $stmtDriver = $this->pdo->prepare($queryDriver);
                $stmtDriver->bindValue(':driverUserId', (int)$driverUserId, PDO::PARAM_INT);
                $stmtDriver->execute();
                $driverInfo = $stmtDriver->fetch(PDO::FETCH_ASSOC);

                if (!$driverInfo) {
                    $this->sendToDev("Information du conducteur non trouvée.");
                }
                $driverLogin = $driverInfo['idlogin'];

                //récuperation du pseudo du réservateur pour spécification dans le message au conducteur
                $queryUser = "SELECT username FROM logins l
                    JOIN users u ON u.idlogin = l.loginid 
                     WHERE u.userid = :userId";
                $stmtUser = $this->pdo->prepare($queryUser);
                $stmtUser->bindValue(':userId', (int)$this->userId, PDO::PARAM_INT);
                $stmtUser->execute();
                $userInfo = $stmtUser->fetch(PDO::FETCH_ASSOC);

                if (!$userInfo) {
                    $this->sendToDev("Information du réservateur non trouvée.");
                }
                $username = $userInfo['username'];

                // Message type d'annulation
                $object = 'Annulation de réservation';

                $message = "L'utilisateur $username a annulé sa réservation.
                Pour votre trajet au départ de $cityDepart à destination de $arrivalCity 
                Le total des passagers a donc été mis à jour.";

                // Envoi du Message
                $this->systemMessage($driverLogin, $object, $message);
            } else {
                $this->sendToDev('Erreur lors de l\'annulation de la réservation');
            }
            // Commit ou execution si tout c'est bien passée
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return $this->saveLog('Erreur lors de l\'annulation de la réservation' . $e, 'FATAL');
        }
    }



    public function validateReservation()
    {

        $checkQuery = "SELECT status FROM reservations WHERE reservationid = :reservationId AND iduser = :userId";
        $checkStmt = $this->pdo->prepare($checkQuery);
        $checkStmt->bindValue(':userId', (int)$this->getUserId(), PDO::PARAM_INT);
        $checkStmt->bindValue(':reservationId', (int)$this->getReservationId(), PDO::PARAM_INT);
        $checkStmt->execute();
        $reservation = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($reservation && $reservation['status'] == 'validated') {
            return $this->sendUserError("Cette réservation a déjà été validée.", 'reservation-line');
        }

        $query = "UPDATE reservations 
              SET status = :status,  
                  approver = :approver 
              WHERE iduser = :userId AND reservationid = :reservationId";

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':userId', (int)$this->getUserId(), PDO::PARAM_INT);
        $statement->bindValue(':reservationId', (int)$this->getReservationId(), PDO::PARAM_INT);
        $statement->bindValue(':status', 'validated');
        $statement->bindValue(':approver', 'user');

        if ($statement->execute()) {
            $rowsAffected = $statement->rowCount();
            if ($rowsAffected > 0) {
                return $this->sendToDev("Réservation validée avec succès.");
            } else {
                return $this->sendToDev("Aucune réservation mise à jour, veuillez vérifier les paramètres.");
            }
        } else {
            $errorInfo = $statement->errorInfo();
            return $this->saveLog("Echec de la validation d'une offre " . $errorInfo, 'CRITICAL');
        }
    }


    public function addReservation($idOffer, $reservedPlaces)
    {

        if ($error = $this->setIdOffer($idOffer)) return $error;
        if ($error = $this->setReservedPlaces($reservedPlaces)) return $error;

        //Récupération d'informations complémentaires sur l'offre
        $query = 'SELECT placeavailable, price, iduser FROM offers WHERE offerid = :idOffer';
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':idOffer', (int)$this->getIdOffer());
        $statement->execute();
        $infoOffer = $statement->fetch(PDO::FETCH_ASSOC);

        // Vérifie si l'user ne reserve pas sa propre offres
        if ($infoOffer['iduser'] === $this->getUserId()) {
           return $this->sendPopup('Vous ne pouvez pas reserver reserver votre offres');
        }

        // Vérifie le nombre de places disponibles
        if ($infoOffer['placeavailable'] >= $this->getReservedPlaces()) {

            // Vérifie le crédit de l'utilisateur
            $query = 'SELECT credit FROM users WHERE userid = :userId';
            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':userId', (int)$this->getUserId(), PDO::PARAM_INT);
            $statement->execute();
            $userCredit = $statement->fetchColumn();

            if ($userCredit >= $infoOffer['price'] * $this->getReservedPlaces()) {

                // Annonce du démmarage d'une transaction complexe
                $this->pdo->beginTransaction();

                // Insertion de la réservation
                $query = 'INSERT INTO reservations (iduser, idoffer, reservedplaces) VALUES (:userId, :idOffer, :reservedplaces)';
                $statement = $this->pdo->prepare($query);
                $statement->bindValue(':userId', $this->getUserId(), PDO::PARAM_INT);
                $statement->bindValue(':idOffer', $this->getIdOffer());
                $statement->bindValue(':reservedplaces', $this->getReservedPlaces());

                if ($statement->execute()) {

                    // Récupère les infos du passager
                    $queryReserver = '
                    SELECT u.idlogin, l.username, l.email
                    FROM users u
                    JOIN logins l ON u.idlogin = l.loginid
                    WHERE u.userid = :userId
                    ';
                    $stmtReserver = $this->pdo->prepare($queryReserver);
                    $stmtReserver->bindValue(':userId', $this->getUserId(), PDO::PARAM_INT);
                    $stmtReserver->execute();
                    $infoReserver = $stmtReserver->fetch(PDO::FETCH_ASSOC);
                    if (!$infoReserver) {
                        $this->sendToDev('Erreur obtention informations du passager');
                    }


                    // Récupère les infos du conducteur
                    $queryDriver = '
                    SELECT o.citydepart, o.arrivalcity, o.datedepart, u.idlogin, l.username, l.email
                    FROM offers o
                    JOIN users u ON o.iduser = u.userid
                    JOIN logins l ON u.idlogin = l.loginid
                    WHERE o.offerid = :idOffer
                    ';
                    $stmtDriver = $this->pdo->prepare($queryDriver);
                    $stmtDriver->bindValue(':idOffer', $this->getIdOffer());
                    $stmtDriver->execute();
                    $infoDriver = $stmtDriver->fetch(PDO::FETCH_ASSOC);
                    if (!$infoDriver) {
                        $this->sendToDev('Erreur obtention informations du conducteur');
                    }

                    // Message passager
                    $reserverObject = "Confirmation de réservation";
                    $reserverMessage = `
                    Bonjour {$infoReserver['username']},\n\n
                    Votre réservation pour le trajet {$infoDriver['citydepart']} → {$infoDriver['arrivalcity']} prévu le {$infoDriver['datedepart']} a bien été prise en compte.\n\n
                    Nous vous invitons à contacter votre conducteur, {$infoDriver['username']}, à l’adresse suivante : {$infoDriver['email']}, pour organiser les modalités de rencontre.\n\n
                    Merci d’avoir choisi Ecoride.\n\n
                    Cordialement,\n
                    L’équipe Ecoride
                `;


                    //Envoi du message au passager

                    $this->systemMessage($infoReserver['idlogin'], $reserverObject, $reserverMessage);

                    // Message conducteur
                    $driverObject = "Nouvelle réservation reçue";
                    $driverMessage = "
                    Bonjour {$infoDriver['username']},\n\n
                    Votre offre pour le trajet {$infoDriver['citydepart']} → {$infoDriver['arrivalcity']} du {$infoDriver['datedepart']} a reçu une nouvelle réservation.\n\n
                    Vous pouvez contacter votre passager, {$infoReserver['username']}, à l’adresse suivante : {$infoReserver['email']} pour convenir des détails du rendez-vous.\n\n
                    Merci d’avoir utilisé Ecoride.\n\n
                    Cordialement,\n
                    L’équipe Ecoride
                ";

                    // Envoi du message au conducteur

                    $this->systemMessage($infoDriver['idlogin'], $driverObject, $driverMessage);
                    // Commit ou execution si tout c'est bien passée
                    $this->pdo->commit();

                    return $this->sendPopup('Votre réservation a bien été enregistrée. Une confirmation vous a été envoyée par message.');
                } else {
                    // en cas d'erreur de la transaction rien ne ce passe
                    //evite l'instabilité des données dans la base
                    $this->pdo->rollBack();
                    return $this->sendPopup('Une erreur est survenue lors de la réservation. Veuillez réessayer.');
                }
            } else {
                return $this->sendPopup('Crédit insuffisant');
            }
        } else {
            return $this->sendPopup('Il ne reste pas suffisamment de places disponibles pour cette offre.');
        }
    }
}
