<?php

declare(strict_types=1);

require_once __DIR__ .'/../../Database/Database.php';
require_once __DIR__ .'/../../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

final class Role
{

    use ExceptionTrait;

    private $pdo;
    private $userId;
    private $role;

    //Global de user role valide
    private const VALID_ROLES = ['driver', 'passenger', 'passengerAndDriver'];

    public function __construct($userId, $pdo)
    {
        $this->userId = $userId;
        $this->pdo = $pdo;
    }

    //Setters
    private function setRole($role)
    {

        if (!in_array($role, self::VALID_ROLES, true)) {
            return $this->sendUserError("Rôle invalide !", 'updateRole');
        }
        $this->role = $role;
    }

    //Getters

    private function getRole() {return $this->role;}
    private function getUserId() {return $this->userId;}
    private function getPdo() {return $this->pdo;}

    //Fonctions principales
    public function getUserRole()
    {

        $query = 'SELECT userrole FROM users WHERE userid = :userId';

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':userId', $this->getUserId(), PDO::PARAM_INT);
        try {
            $statement->execute();
            $roleInfo = $statement->fetch(PDO::FETCH_ASSOC);

            if ($roleInfo) {
                $roleInfo = array_map('trim', $roleInfo);
                return $roleInfo;
            }
            return $this->sendToDev('Role utilisateur non trouvé');
        } catch (PDOException $e) {
            return  $this->saveLog('Erreur lors de la récupération du role utilisateur.' . 'UserId :' . $this->getUserId() . $e->getMessage(), 'ERROR');
        }
    }

    //Mise a jour du role
    public function updateRole($role)
    {
        try {
            if ($error = $this->setRole($role)) return $error;

            $query = 'UPDATE users SET userrole = :role WHERE userid = :userId';
            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':userId', $this->getUserId(), PDO::PARAM_INT);
            $statement->bindValue(':role', $this->getRole());
            $statement->execute();

            return $this->sendUserSuccess('Votre role à été mis a jour avec succès', 'updateRole');

        } catch (PDOException $e) {
            return $this->saveLog('Erreur lors de la mise a jour du role utilisateur.' .'Pour :'.$this->getUserId(). $e->getMessage(), 'ERROR');
        }
    }
}
