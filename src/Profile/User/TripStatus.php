<?php

declare(strict_types=1);

require_once __DIR__ .'/../../Database/Database.php';
require_once __DIR__ .'/../../Traits/MessageTrait.php';
require_once __DIR__ .'/../../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

final class TripStatus {

    use ExceptionTrait;
    use MessageTrait;

    private $userId;
    private $pdo;
    private $tripId;

    public function __construct($pdo, $userid)
    {
        $this->userId = $userid;
        $this->pdo = $pdo;
    }


    public function setTripId($tripId)
    {
        if (!is_numeric($tripId) || $tripId <= 0) {
            return $this->sendToDev("L'ID du trajet est invalide.");
        }
        $this->tripId = $tripId;
    }

    private function getUserId(){
        return $this->userId;
    }
    private function getTripId()
    {
        return $this->tripId;
    }


    public function cancelTrip()
    {
        try {
            // Annonce du démmarage d'une transaction complexe
            $this->pdo->beginTransaction();
    
            // Extraction des détails de l'offre
            $offerId = $this->getTripId();
            $offerQuery = 'SELECT citydepart, datedepart, offerid, price, arrivalcity FROM offers WHERE offerid = :offerId';
            $OfferStatement = $this->pdo->prepare($offerQuery);
            $OfferStatement->bindValue(':offerId', $offerId, PDO::PARAM_INT);
    
            if (!$OfferStatement->execute()) {
                return $this->sendToDev("Erreur lors de la récupération des détails de l'offre");
            }
    
            $offer = $OfferStatement->fetch(PDO::FETCH_ASSOC);
            if (!$offer) {
                return $this->sendToDev("Aucun détail trouvé pour cette offre avec l'ID : " . $offerId);
            }
    
            // Vérification de la date d'annulation
            $currentDate = new DateTime();
            $departureDate = new DateTime($offer['datedepart']);
    
            if ($departureDate < $currentDate) {
                return $this->sendUserError('L\'offre ne peut être annulée le jour même ou à une date ultérieure', 'tripAction');
            }
    
            // Mise à jour du statut de l'offre
            $query = "UPDATE offers SET status = 'canceled' WHERE offerid = :offerId";
            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':offerId', $offerId, PDO::PARAM_INT);
    
            if (!$statement->execute()) {
                return $this->sendUserError('Échec de l\'annulation, réessayez un peu plus tard.');
            }
    
            // Récupération des utilisateurs ayant réservé
            $reservationQuery = 'SELECT u.idlogin 
                                 FROM reservations r
                                 JOIN users u ON r.iduser = u.userId
                                 WHERE r.idoffer = :offerId';
            $reservationStatement = $this->pdo->prepare($reservationQuery);
            $reservationStatement->bindValue(':offerId', $offerId, PDO::PARAM_INT);
            $reservationStatement->execute();
    
            $reservedUsers = $reservationStatement->fetchAll(PDO::FETCH_ASSOC);
    
            // Mise à jour du statut des réservations liées à l'offre
            if (!empty($reservedUsers)) {
                $reservationCancelQuery = 'UPDATE reservations SET status = \'canceled\' WHERE idoffer = :offerId';
                $reservationCancelStatement = $this->pdo->prepare($reservationCancelQuery);
                $reservationCancelStatement->bindValue(':offerId', $offerId, PDO::PARAM_INT);
    
                if (!$reservationCancelStatement->execute()) {
                    return $this->sendToDev('Échec de l\'annulation des réservations liées à l\'offre');
                }
    
                // Extraction du pseudo du conducteur
                $pseudoQuery = 'SELECT l.username 
                                FROM logins l
                                JOIN users u ON l.loginId = u.idlogin
                                WHERE u.userId = :userId';
                $statementPseudo = $this->pdo->prepare($pseudoQuery);
                $statementPseudo->bindValue(':userId', $this->getUserId(), PDO::PARAM_INT);
    
                if (!$statementPseudo->execute()) {
                    return $this->sendToDev("Erreur lors de la récupération du pseudo du conducteur.");
                }
    
                $driverPseudo = $statementPseudo->fetch(PDO::FETCH_ASSOC);
                if (!$driverPseudo) {
                    return $this->sendToDev("Erreur lors de la récupération du pseudo du conducteur.");
                }
    
                // Message d'annulation pour chaque passager
                foreach ($reservedUsers as $reservedUser) {
                    $messageText = "Bonjour, <br><br>
                    Nous tenons à vous informer que l'offre n°" . $offer['offerid'] . " au départ de " . $offer['citydepart'] . " à destination de " . $offer['arrivalcity'] . " prévue le " . $offer['datedepart'] . " a été annulée par le conducteur " . $driverPseudo['username'] . ". <br>
                    En conséquence, le prix de la course, soit " . $offer['price'] . "€, ne vous sera évidemment pas débité.
                    <br><br>
                    Nous comprenons la gêne occasionnée et vous invitons à programmer une nouvelle réservation en toute simplicité.
                    <br><br>
                    Si vous avez des questions ou besoin d’assistance, notre équipe reste à votre disposition.
                    <br><br>
                    Nous vous souhaitons une agréable journée et espérons vous voir bientôt sur Ecoride.";
    
                    $subject = "Annulation de l'offre";

                    // Insertion du message dans la base de données Mongo (appel MessageTrait)

                    $result = $this->systemMessage($reservedUser, $subject, $messageText);
    
                    if (!$result) {
                        return $this->saveLog('Échec lors de l\'envoi du message d\'annulation à l\'utilisateur ' . $reservedUser['idlogin'], 'CRITICAL');
                    }
                }
            }
    
            // Commit ou execution si tout c'est bien passée
            $this->pdo->commit();
    
            return $this->sendPopup('L\'offre a été annulée et les passagers avertis.');
        } catch (PDOException $e) {
            // en cas d'erreur de la transaction rien ne ce passe
            //evite l'instabilité des données dans la base
            $this->pdo->rollBack();
            return $this->saveLog('Erreur lors de l\'annulation de l\'offre : ' . $e->getMessage(), 'FATAL');
        }
    }
    
    
    public function startTrip()
    {
        $offerId = $this->getTripId();

        $offerQuery = 'SELECT  datedepart FROM offers WHERE offerid = :offerId';
        $OfferStatement = $this->pdo->prepare($offerQuery);
        $OfferStatement->bindValue(':offerId', $offerId, PDO::PARAM_INT);

        if ($OfferStatement->execute()) {
            $this->sendToDev("Détails de l'offre récupérés avec succès");
        } else {
            return $this->sendToDev("Erreur lors de la récupération des détails de l'offre");
        }

        $offer = $OfferStatement->fetch(PDO::FETCH_ASSOC);

        if (!$offer) {
            return $this->sendToDev("Aucun détail trouvé pour cette offre avec l'ID : " . $offerId);
        }

        //Verification de la date d'annulation 
        $currentDate = date('Y-m-d');
        $departureDate = date('Y-m-d', strtotime($offer['datedepart']));

        if ($departureDate !== $currentDate) {
            return $this->sendPopup('La date de départ ne correspond pas à la date de départ prévue');
        }

        $query = 'UPDATE offers SET status = :status WHERE offerid = :offerId';
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':offerId', $offerId, PDO::PARAM_INT);
        $statement->bindValue(':status', 'in process');

        if ($statement->execute()) {
            return $this->sendUserSuccess('Départ enregistré', 'tripAction');
        } else {
            return $this->sendUserError('Status non pris en compte', 'tripAction');
        }
    }


    public function endTrip()
    {
        $offerId = $this->getTripId();

        // Récupérer les détails de l'offre
        $offerQuery = 'SELECT status, citydepart, datedepart, offerid, price, arrivalcity FROM offers WHERE offerid = :offerId';
        $OfferStatement = $this->pdo->prepare($offerQuery);
        $OfferStatement->bindValue(':offerId', $offerId, PDO::PARAM_INT);

        if ($OfferStatement->execute())  {
            return $this->sendToDev("Erreur lors de la récupération des détails de l'offre");
        }

        $offer = $OfferStatement->fetch(PDO::FETCH_ASSOC);

        if (!$offer) {
            return $this->sendToDev("Aucun détail trouvé pour cette offre avec l'ID : " . $offerId);
        }

        if ($offer['status'] !== 'in process') {
            return $this->sendPopup('L\'arrivée ne peut être déclarée avant le départ');
        }

        // Mise à jour du statut de l'offre
        $query = 'UPDATE offers SET status = :status WHERE offerid = :offerId';
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':offerId', $offerId, PDO::PARAM_INT);
        $statement->bindValue(':status', 'ended');

        if (!$statement->execute()) {
            return $this->sendUserError('Status non pris en compte', 'tripAction');
        }

        // Récupérer les utilisateurs réservés pour l'offre
        $reservationQuery = 'SELECT u.idlogin FROM reservations r JOIN users u ON r.iduser = u.userId WHERE r.idoffer = :offerId';
        $reservationStatement = $this->pdo->prepare($reservationQuery);
        $reservationStatement->bindValue(':offerId', $offerId, PDO::PARAM_INT);
        $reservationStatement->execute();

        $reservedUsers = $reservationStatement->fetchAll(PDO::FETCH_ASSOC);

        // Mise à jour du statut de toutes les réservations liées à l'offre
        if (!empty($reservedUsers)) {
            foreach ($reservedUsers as $reservedUser) {
                $messageText = "Bonjour, <br><br>
                Nous vous informons que l'offre n°" . $offer['offerid'] . " au départ de " . $offer['citydepart'] . " à destination de " . $offer['arrivalcity'] . " prévue le " . $offer['datedepart'] . " 
                a été marquée comme terminée. <br> 
                En conséquence, vous pouvez dès à présent vous rendre dans votre espace personnel pour valider ce trajet dans la section de vos réservations. <br><br>
                Nous vous invitons à procéder à cette validation afin de finaliser votre expérience. <br><br> 
                Si vous avez des questions ou besoin d’assistance, notre équipe est à votre disposition pour vous accompagner. <br><br>
                Nous vous souhaitons une excellente journée et espérons vous retrouver bientôt sur Ecoride.";

                $subject = "Trajet terminé";

                // Insertion du message dans la base de données Mongo (appel MessageTrait)
                
                return $this->systemMessage($reservedUser, $subject, $messageText);

               
            }
        }

        return $this->sendUserSuccess('Arrivée enregistrée et messages envoyés', 'tripAction');
    }
}
