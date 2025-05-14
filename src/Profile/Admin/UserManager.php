<?php

declare(strict_types=1);


require_once __DIR__ .'/../../Database/Database.php';
require_once __DIR__ .'/../../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

final class UserManager {
    use ExceptionTrait;

    private $pdo;
    private $adminId;
    private $employeId;
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

    // Getter
    public function getBanId() { return $this->banId; }



    private function checkIfAuthorized(): bool {

        $sql = "SELECT COUNT(*) FROM admins WHERE adminId= :adminId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':adminId', $this->adminId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }




    public function getUsers()
    {
        if (!$this->checkIfAuthorized()) {
            return $this->sendToDev('L\'ID administrateur est invalide.');
        }
        $sql = "SELECT u.idlogin, u.userid, u.lastname, u.firstname, u.road, u.roadcomplement,u.zipcode, u.city,phone, 
        u.updateddate, u.creationdate, u.userrole, u.credit, u.note FROM users u
        JOIN logins l ON l.loginid = u.idlogin
         WHERE l.status = 'ok'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $users;
    }





    public function banUser()
    {
        if ($error = $this->setBanId($this->getBanId())) return $error;

        if (!$this->checkIfAuthorized()) {
            return $this->sendToDev('L\'ID administrateur est invalide.');
        }

        $banId = $this->getBanId();

        if (empty($banId)) {
            return $this->sendToDev('L\'id à bannir n\'est pas valide.');
        }

        try {
            $query = "UPDATE logins
            SET status = 'banned'
            WHERE loginid = :loginId";

            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':loginId', $banId, PDO::PARAM_INT);
            $statement->execute();

            return $this->sendPopup('Utilisateur banni avec succès');
        } catch (PDOException $e) {
            return $this->saveLog('Echec lors du bannissement de l\'utilisateur'.$this->getBanId().$e->getMessage(),'CRITICAL');
        }
    }
}
