<?php

declare(strict_types=1);

require_once __DIR__ . '/../../Database/Database.php';
require_once __DIR__ . '/../../Traits/MessageTrait.php';
require_once __DIR__ . '/../../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

final class TripStatus
{

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

    private function getUserId()
    {
        return $this->userId;
    }
    private function getTripId()
    {
        return $this->tripId;
    }


public function cancelTrip()
{
    try {
        $offerId = $this->getTripId();
        if (!$offerId) {
            return $this->sendUserError('ID du trajet invalide.', 'tripAction');
        }

        // Récupération de l'offre
        $stmt = $this->pdo->prepare('SELECT citydepart, datedepart, offerid, price, arrivalcity, status FROM offers WHERE offerid = :offerId');
        $stmt->execute([':offerId' => $offerId]);
        $offer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$offer) {
            return $this->sendUserError("Trajet introuvable.", 'tripAction');
        }

        if ($offer['status'] === 'canceled') {
            return $this->sendUserError("Ce trajet est déjà annulé.", 'tripAction');
        }

        $departureDate = new DateTime($offer['datedepart']);
        $now = new DateTime();
        if ($departureDate <= $now) {
            return $this->sendUserError("Impossible d'annuler un trajet le jour même ou après.", 'tripAction');
        }

        // Supprimer les crédits
        $this->pdo->prepare('DELETE FROM credits WHERE idoffer = :offerId')->execute([':offerId' => $offerId]);

        // Annuler l'offre
        $this->pdo->prepare("UPDATE offers SET status = 'canceled' WHERE offerid = :offerId")->execute([':offerId' => $offerId]);

        // Récupérer les passagers
        $stmt = $this->pdo->prepare('
            SELECT u.idlogin, u.userid 
            FROM reservations r 
            JOIN users u ON r.iduser = u.userid 
            WHERE r.idoffer = :offerId
        ');
        $stmt->execute([':offerId' => $offerId]);
        $passagers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Récupérer le pseudo conducteur
        $stmt = $this->pdo->prepare('
            SELECT l.username 
            FROM logins l 
            JOIN users u ON l.loginid = u.idlogin 
            WHERE u.userid = :userId
        ');
        $stmt->execute([':userId' => $this->getUserId()]);
        $driver = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$driver) {
            return $this->sendToDev("Conducteur non trouvé.");
        }

        foreach ($passagers as $passager) {
            $message = "Bonjour,<br><br>L'offre n°" . $offer['offerid'] .
                " de " . $offer['citydepart'] . " à " . $offer['arrivalcity'] .
                " le " . $departureDate->format('d/m/Y') . " a été annulée par " . $driver['username'] . ".<br>" .
                "Vous ne serez pas débité.<br><br>Merci de votre compréhension.";

            if (!$this->systemMessage($passager['userid'], "Annulation de l'offre", $message)) {
                return $this->sendUserError("Erreur lors de l'envoi du message à un passager.", 'tripAction');
            }
        }

        return $this->sendPopup("Trajet annulé. Les passagers ont été notifiés.");
    } catch (\Throwable $e) {
        return $this->sendToDev("Erreur cancelTrip : " . $e->getMessage());
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

        if (!$OfferStatement->execute()) {
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

                $this->systemMessage($reservedUser, $subject, $messageText);
            }
        }

        return $this->sendUserSuccess('Arrivée enregistrée et messages envoyés', 'tripAction');
    }
}
