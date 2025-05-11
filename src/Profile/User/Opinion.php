<?php

declare(strict_types=1);

require_once __DIR__ .'/../../Database/Database.php';
require_once __DIR__ .'/../../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();



final class Opinion {

    use ExceptionTrait;

    private $pdo;
    private $userId;
    private $srcOpinion;
    private $note;
    private $opinionText;

    public function __construct($userId, $pdo)
    {
        $this->userId = $userId;
        $this->pdo = $pdo;
    }


    private function setSrcOpinion($srcOpinion)
    {
        if (!is_numeric($srcOpinion) || (int)$srcOpinion <= 0) {
           return $this->sendToDev("L'ID de l'opinion doit être un nombre positif.");
        }
        $this->srcOpinion = (int)$srcOpinion;
    }

    private function setNote($note)
    {
        if ($note <= 1 && $note >= 5) {
            return $this->sendUserError("La note doit être comprise entre 1 et 5.",'note');
        }
        $this->note = (int)$note;
    }

    private function setOpinionText($opinionText)
    {
        if (empty($opinionText)) {
             return $this->sendUserError("Le texte de l'opinion ne peut pas être vide.",'containOpinion');
        }
    
        $allowedChars = '/[^a-zA-Z0-9\s\-\/\?!\:\.¨\^\'\€\)\(]/';
    
        $filteredText = preg_replace($allowedChars, '', $opinionText);
    
        $this->opinionText = htmlspecialchars($filteredText, ENT_QUOTES, 'UTF-8');
    }


//Fonctions Principales :

    public function addOpinion($note, $opinionText, $srcOpinion)
    {
        try {
            if ($error = $this->setNote($note)) return $error;
            if ($error = $this->setSrcOpinion($srcOpinion)) return $error;
            if ($error = $this->setOpinionText($opinionText)) return $error;

            $checkQuery = 'SELECT COUNT(*) FROM avis 
                           WHERE iduser = :userId AND idreservation = :srcOpinion';
            $checkStatement = $this->pdo->prepare($checkQuery);
            $checkStatement->bindValue(':userId', $this->userId);
            $checkStatement->bindValue(':srcOpinion', (int)$this->srcOpinion);
            $checkStatement->execute();
            $existingOpinion = $checkStatement->fetchColumn();
    
            if ($existingOpinion > 0) {
                return $this->sendUserError('Vous avez déja saisie un avis pour ce trajet', 'addOpinion');
            }
    
            $driverStatement = $this->pdo->prepare("SELECT o.iduser 
                                                    FROM offers o 
                                                    JOIN reservations r ON o.offerid = r.idoffer 
                                                    WHERE r.reservationid = :srcOpinion
                                                    ");
            $driverStatement->bindValue(':srcOpinion', $this->srcOpinion, PDO::PARAM_INT);
            $driverStatement->execute();
            $result = $driverStatement->fetch(PDO::FETCH_ASSOC);
    
            if (!$result) {
                return $this->sendToDev("Offre introuvable.");
            }
    
            $driverId = $result['iduser'];
    
            $query = "INSERT INTO avis (iduser, note, comment, status, idreservation, driverid) 
                      VALUES (:userId, :note, :opinionText, :status, :srcOpinion, :driverId)";
    
            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':userId', $this->userId);
            $statement->bindValue(':note', (int)$this->note);
            $statement->bindValue(':opinionText', $this->opinionText = is_array($this->opinionText) ? implode(" ", $this->opinionText) : $this->opinionText);
            $statement->bindValue(':status', 'Traitement en cours');
            $statement->bindValue(':srcOpinion', $this->srcOpinion);
            $statement->bindValue(':driverId', (int)$driverId);
    
            $statement->execute();
    
            return $this->sendPopup('Votre Avis à été ajouté avec succès');
        } catch (Exception $e) {
            return $this->saveLog('Erreur de l\'ajout d\'un avis'. $e->getMessage().'UserId :'.$this->userId,'CRITICAL');
        }
    }
    

    

}
