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
    public function getHourDepart(){ return $this->hourDepart;}
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
                    'dateReservation' => $reservation['creationdate'],
                    'status' => $reservation['status'],
                    'price' => $offer['price'],
                    'dateDepart' => $offer['datedepart'],
                    'hourDepart' => $offer['hourdepart'],
                    'cityDepart' => $offer['citydepart'],
                    'dateArrival' => $offer['datearrival'],
                    'hourArrival' => $offer['hourarrival'],
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
            $this->pdo->beginTransaction();

            // 1. Vérification de la réservation
            $query = "
            SELECT idoffer 
            FROM Reservations 
            WHERE iduser = :userId 
              AND reservationid = :reservationId
        ";
            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':userId', (int)$this->userId, PDO::PARAM_INT);
            $statement->bindValue(':reservationId', (int)$this->reservationId, PDO::PARAM_INT);
            $statement->execute();

            $reservation = $statement->fetch(PDO::FETCH_ASSOC);
            if (!$reservation) {
                $this->pdo->rollBack();
                return $this->sendToDev("Réservation non trouvée pour userId : {$this->userId}, reservationId : {$this->reservationId}");
            }
            $offerId = $reservation['idoffer'];

            // 2. Récupération de l'offre
            $offerQuery = "
            SELECT citydepart, datedepart, offerid, price, arrivalcity, iduser 
            FROM offers 
            WHERE offerid = :offerId
        ";
            $OfferStatement = $this->pdo->prepare($offerQuery);
            $OfferStatement->bindValue(':offerId', (int)$offerId, PDO::PARAM_INT);
            $OfferStatement->execute();

            $offer = $OfferStatement->fetch(PDO::FETCH_ASSOC);
            if (!$offer) {
                $this->pdo->rollBack();
                return $this->sendToDev("Aucun détail pour l'offre ID : $offerId");
            }

            // 3. Vérifier que la date est valide avant DateTime
            if (empty($offer['datedepart']) || !strtotime($offer['datedepart'])) {
                $this->pdo->rollBack();
                return $this->sendToDev("Date de départ invalide sur l'offre ID : $offerId");
            }
            $departureDate = new DateTime($offer['datedepart']);
            $currentDate   = new DateTime();

            if ($departureDate < $currentDate) {
                $this->pdo->rollBack();
                return $this->sendToDev("Annulation impossible : la date de départ est déjà passée ({$offer['datedepart']}).");
            }

            // 4. Passage en statut 'canceled'
            $updateQuery = "
            UPDATE Reservations 
            SET status = 'canceled' 
            WHERE iduser = :userId 
              AND reservationid = :reservationId
        ";
            $updateStatement = $this->pdo->prepare($updateQuery);
            $updateStatement->bindValue(':userId', (int)$this->userId, PDO::PARAM_INT);
            $updateStatement->bindValue(':reservationId', (int)$this->reservationId, PDO::PARAM_INT);
            if (!$updateStatement->execute()) {
                $this->pdo->rollBack();
                return $this->sendToDev("Erreur SQL lors de l'annulation (UPDATE Reservations).");
            }

            $restoreQuery = $restoreQuery = "
                                            UPDATE offers AS o
                                            SET placeavailable = o.placeavailable + r.reservedplaces
                                            FROM reservations AS r
                                            WHERE o.offerid = r.idoffer
                                              AND r.reservationid = :reservationId
                                              AND o.offerid = :offerId
                                            ";
            $restorPlaceStatement = $this->pdo->prepare($restoreQuery);
            $restorPlaceStatement->bindValue(':offerId', (int)$offerId, PDO::PARAM_INT);
            $restorPlaceStatement->bindValue(':reservationId', (int)$this->reservationId, PDO::PARAM_INT);
            $restorPlaceStatement->execute();


            // 5. Suppression des crédits liés
            $deleteCreditsQuery = "
            DELETE FROM credits 
            WHERE idreservation = :reservationId
        ";
            $delStmt = $this->pdo->prepare($deleteCreditsQuery);
            $delStmt->bindValue(':reservationId', (int)$this->reservationId, PDO::PARAM_INT);
            if (!$delStmt->execute()) {
                $errorInfo = $delStmt->errorInfo();
                $this->pdo->rollBack();
                $this->saveLog(
                    "Échec suppression crédits pour reservation #{$this->reservationId} : " . $errorInfo[2],
                    'ERROR'
                );
                return $this->sendUserError(
                    "Erreur lors de l'annulation (suppression crédits).",
                    'reservationAction'
                );
            }

            // 6. Récupérer l'idlogin du conducteur
            //    Attention : vérifier que ta table 'logins' a bien 'loginid' ou 'idlogin'
            $queryDriver = "
            SELECT l.loginid AS idlogin 
            FROM users u
            JOIN logins l ON u.idlogin = l.loginid
            WHERE u.userid = :driverUserId
        ";
            $stmtDriver = $this->pdo->prepare($queryDriver);
            $stmtDriver->bindValue(':driverUserId', (int)$offer['iduser'], PDO::PARAM_INT);
            $stmtDriver->execute();

            $driverInfo = $stmtDriver->fetch(PDO::FETCH_ASSOC);
            if (!$driverInfo || empty($driverInfo['idlogin'])) {
                $this->saveLog("Conducteur non trouvé pour userId : {$offer['iduser']}", 'ERROR');
            } else {
                $driverLogin = $driverInfo['idlogin'];

                // 7. Récupérer le pseudo du réservateur
                $queryUser = "
                SELECT l.username 
                FROM logins l
                JOIN users u ON u.idlogin = l.loginid
                WHERE u.userid = :userId
            ";
                $stmtUser = $this->pdo->prepare($queryUser);
                $stmtUser->bindValue(':userId', (int)$this->userId, PDO::PARAM_INT);
                $stmtUser->execute();

                $userInfo = $stmtUser->fetch(PDO::FETCH_ASSOC);
                if (!$userInfo || empty($userInfo['username'])) {
                    $this->saveLog("Réservateur non trouvé pour userId : {$this->userId}", 'ERROR');
                } else {
                    $username = $userInfo['username'];

                    // 8. Envoi du message au conducteur
                    $object  = 'Annulation de réservation';
                    $message = "L'utilisateur $username a annulé sa réservation.\n"
                        . "Trajet : {$offer['citydepart']} → {$offer['arrivalcity']}";

                    $this->systemMessage($driverLogin, $object, $message);
                }
            }

            // 9. Commit et retour OK
            $this->pdo->commit();
            $this->saveLog("Réservation #{$this->reservationId} annulée avec succès.", 'INFO');
            return $this->sendPopup('Annulation enregistrée avec succès.');
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $this->saveLog('Exception cancelReservation : ' . $e->getMessage(), 'FATAL');
            // Pour le debug, on renvoie le message complet de l’exception
            return $this->sendToDev('Erreur interne lors de l\'annulation : ' . $e->getMessage());
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
                    $reserverMessage = "
                    Bonjour {$infoReserver['username']},\n\n
                    Votre réservation pour le trajet {$infoDriver['citydepart']} → {$infoDriver['arrivalcity']} prévu le {$infoDriver['datedepart']} a bien été prise en compte.\n\n
                    Nous vous invitons à contacter votre conducteur, {$infoDriver['username']}, à l’adresse suivante : {$infoDriver['email']}, pour organiser les modalités de rencontre.\n\n
                    Merci d’avoir choisi Ecoride.\n\n
                    Cordialement,\n
                    L’équipe Ecoride
                ";


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
