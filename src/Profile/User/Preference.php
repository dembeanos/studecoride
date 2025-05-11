<?php

declare(strict_types=1);

require_once __DIR__ .'/../../Database/Database.php';
require_once __DIR__ .'/../../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

final class Preference {

    use ExceptionTrait;

    private $pdo;
    private $userId;
    private $animal;
    private $smoke;
    private $other;

    public function __construct ($pdo, $userId){
        $this->userId = $userId;
        $this->pdo = $pdo;
    }

    private function setAnimal($animal){
        if ($animal !== true && $animal !== false){
            $this->sendToDev('Valeur de animal rejeté');
        }else{
            $this->animal = $animal;}
        }
    private function setSmoke($smoke){
        if ($smoke !== true && $smoke !== false){
            $this->sendToDev('Valeur de smoke rejeté');
        } else { 
        $this->smoke = $smoke;}
    }
    private function setOther($other) {
        $this->other = preg_replace('/[<>{}\[\]\/\\\&;`\'"!#$%^*()|\\?^~_+¬]/', '', $other);
        $this->other = nl2br($this->other); // conserve les sauts de lignes en les convertissant en <br>
        $this->other = htmlspecialchars($this->other, ENT_NOQUOTES, 'UTF-8');
    }

    private function getUserId(){return $this->userId;}
    private function getPdo(){return $this->pdo;}
    private function getAnimal(){return $this->animal;}
    private function getSmoke(){return $this->smoke;}
    private function getOther(){return htmlspecialchars_decode($this->other);}

    // Fonctions principales

    public function getPref(){

        $query = "SELECT preferenceid,animal, smoke, other FROM preferences WHERE iduser = :userId";
        $statement = $this->pdo ->prepare($query);
        $statement->bindValue('userId',$this->getUserId(),PDO::PARAM_INT);

        try{
            $statement->execute();
            $prefInfo = $statement->fetch(PDO::FETCH_ASSOC);

            if ($prefInfo){
            $this->setAnimal($prefInfo['animal']);
            $this->setSmoke($prefInfo['smoke']);
            $this->setOther($prefInfo['other']);
            }
            return $prefInfo;
            
        }catch (PDOException $e){
            return $this->saveLog('Erreur lors de la récuperation des préférences'.$e->getMessage(),'ERROR');
        }
    }



    public function updatePref($animal, $smoke, $other) {

        if ($error = $this->setAnimal($animal)) return $error;
        if ($error = $this->setSmoke($smoke)) return $error;
        if ($error = $this->setOther($other)) return $error;
    
        try {
            $queryCheck = "SELECT COUNT(*) FROM preferences WHERE iduser = :userId";
            $statementCheck = $this->pdo->prepare($queryCheck);
            $statementCheck->bindValue(':userId', $this->getUserId(), PDO::PARAM_INT);
            $statementCheck->execute();
            
            $count = $statementCheck->fetchColumn();
            
            if ($count > 0) {
                $queryUpdate = "UPDATE preferences 
                                SET animal = :animal, smoke = :smoke, other = :other 
                                WHERE iduser = :userId";
                $statementUpdate = $this->pdo->prepare($queryUpdate);
                $statementUpdate->bindValue(':animal', $this->animal, PDO::PARAM_BOOL);
                $statementUpdate->bindValue(':smoke', $this->smoke, PDO::PARAM_BOOL); 
                $statementUpdate->bindValue(':other', $this->other, PDO::PARAM_STR); 
                $statementUpdate->bindValue(':userId', $this->getUserId(), PDO::PARAM_INT);
                $statementUpdate->execute();

                return $this->sendUserSuccess('Préférences prise en compte','addPref');
            } else {
                $queryInsert = "INSERT INTO preferences (iduser, animal, smoke, other) 
                                VALUES (:userId, :animal, :smoke, :other)";
                $statementInsert = $this->pdo->prepare($queryInsert);
                $statementInsert->bindValue(':userId', $this->getUserId(), PDO::PARAM_INT);
                $statementInsert->bindValue(':animal', $this->animal, PDO::PARAM_BOOL);
                $statementInsert->bindValue(':smoke', $this->smoke, PDO::PARAM_BOOL);
                $statementInsert->bindValue(':other', $this->other, PDO::PARAM_STR);
                $statementInsert->execute();

                return $this->sendUserSuccess('Préférences prise en compte','addPref');
            }
    
        } catch (Exception $e) {
            return $this->saveLog("Erreur lors de l'ajout ou de la mise à jour des préférences : " . $e->getMessage().'Pour :'.$this->getUserId(),'ERROR');
        }
    }
    

}
?>