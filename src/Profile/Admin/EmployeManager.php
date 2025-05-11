<?php

declare(strict_types=1);

require_once __DIR__ .'/../../Database/Database.php';
require_once __DIR__ .'/../../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

final class EmployeManager
{
    use ExceptionTrait;

    private $pdo;
    private $adminId;
    private $banId;

    public function __construct($pdo, $adminId)
    {
        $this->pdo = $pdo;
        $this->adminId = $adminId;
    }

    //Setters
    private function setAdminId($adminId)
    {
        if (!is_numeric($adminId) || (int)$adminId <= 0) {
            $this->sendToDev("L'ID administrateur est invalide.");
        }
        $this->adminId = (int)$adminId;
    }

    public function setBanId($banId)
    {
        if (!is_numeric($banId) || (int)$banId <= 0) {
            $this->sendToDev("L'ID utilisateur est invalide.");
        }
        $this->banId = (int)$banId;
    }

    //Getters
    private function getAdminId(){ return $this->adminId;}
    public function getBanId(){ return $this->banId;}


    //Check autorisation
    private function checkIfAuthorized(): bool
    {
        $sql = "SELECT COUNT(*) FROM admins WHERE adminid = :adminId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':adminId', $this->getAdminId(), PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetchColumn() > 0;
    }


    //Fonction Principales :
    
    public function getEmployees()
    {
        if ($this->checkIfAuthorized() === false) {
            return $this->sendToDev('L\'ID administrateur est invalide.');
        }
    
        $sql = "SELECT idlogin, employeid, lastname, firstname, road, roadcomplement, zipcode, city, phone, updateddate, creationdate FROM employees";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $employees;
    }


    public function banUser()
    {

        if (!$this->checkIfAuthorized()) {
            echo json_encode(['status' => 'error', 'message' => 'L\'ID administrateur est invalide.']);
            exit();
        }
        $banId = $this->getBanId();

        try {
            $query = "UPDATE logins
            SET status = 'banned'
            WHERE loginid = :loginId";

            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':loginId', $banId, PDO::PARAM_INT);
            $statement->execute();

            return $this->sendPopup('Utilisateur banni avec succès');

        } catch (PDOException $e) {
            return $this->saveLog('Echec lors du bannissement de l\'employé'.$this->getBanId().$e->getMessage(),'CRITICAL');
        }
    }
}
