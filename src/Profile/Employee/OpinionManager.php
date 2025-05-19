<?php
declare(strict_types=1);

require_once __DIR__ .'/../../Database/Database.php';
require_once __DIR__ .'/../../Database/DatabaseNoSql.php';
require_once __DIR__ .'/../../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

final class OpinionManager{

    use ExceptionTrait;

    private $employeId;
    private $pdo;
    private $opinionId;



    public function __construct($pdo, $employeId)
    {
        $this->pdo = $pdo;
        $this->employeId = $employeId;
    }



    //Setter
    public function setOpinionId($opinionId)
    {
        if(!is_numeric($opinionId) ||$opinionId <= 0){
            return $this->sendToDev('L\'id de de l\'avis n\'est pas valide');
        }
        $this->opinionId = $opinionId;
    }

    //Getter
    private function getOpinionId() { return $this->opinionId; }

    //Fonction Principale :

    public function getOpinion()
    {
        // On receptionne tout les avis dont le status et en cours
        $query = "SELECT * FROM avis WHERE status = 'Traitement en cours'";

        $statement = $this->pdo->prepare($query);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }



    public function validateOpinion()
    {
        //On met a jour le statut à validated
        $query = "UPDATE avis SET status = 'validated' WHERE opinionid = :opinionId ";

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':opinionId', $this->getOpinionId());

        if ($statement->execute()) {
            return $this->sendPopup('Avis validé avec succès');
        }
    }



    public function rejectedOpinion()
    {

        //On met a jours le statut en rejected
        $query = "UPDATE avis SET status = 'rejected' WHERE opinionid = :opinionId ";

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':opinionId', (int)$this->getOpinionId());

        if ($statement->execute()) {
            if ($statement->rowCount() > 0) {
                return $this->sendPopup('Avis rejeté avec suucès');
            }
        }
    }



    public function getTripDetail() {
        // Méthode pour récupérer les détails d'une offre liée à un avis.
        // On utilise des alias pour distinguer les informations du passager et du conducteur,
        // car ils proviennent tous deux de la même table 'users' (et 'logins').
        // Cela nécessite deux JOIN séparés : un pour le passager (a.iduser) et un pour le conducteur (a.driverid).
        $query =  "SELECT  o.offerid, o.datedepart, o.citydepart, o.roaddepart, o.datearrival, o.arrivalcity, o.arrivalroad,

                    passlog.username AS passengerUsername,
                    passlog.email AS passengerEmail,

                    drivlog.username AS driverUsername,
                    drivlog.email AS driverEmail

                    FROM avis a
                    JOIN reservations r ON a.idreservation = r.reservationid
                    JOIN offers o ON r.idoffer = o.offerid

                    JOIN users passuser ON a.iduser = passuser.userid
                    JOIN logins passlog ON passuser.idlogin = passlog.loginid

                    JOIN users drivuser ON a.driverid = drivuser.userid
                    JOIN logins drivlog ON drivuser.idlogin = drivlog.loginid

                    WHERE a.opinionid = :opinionId";

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':opinionId', (int)$this->getOpinionId());

        if ($statement->execute()) {
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            if (count($result) > 0) {
                return $result;
            } else {
                return $this->sendToDev('Aucune offre trouvée pour l\'ID : ' . $this->getOpinionId());
            }
        } else {
            return $this->saveLog('Erreur lors de l\'exécution de la requête SQL dans OpinionManager getTripDetail','CRITICAL');
        }
    }
}
