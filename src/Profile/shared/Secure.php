<?php
declare(strict_types=1);

require_once __DIR__ .'/../../Database/Database.php';
require_once __DIR__ .'/../../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

final class Secure
{
    use ExceptionTrait;

    private $pdo;
    private $userId;
    private $adminId;
    private $employeId;
    private $backPassword;
    private $newPassword;
    private $confirmPassword;
    private $finalPassword;

    public function __construct($pdo, $userId = null, $adminId = null, $employeId = null)
    {
        $this->pdo = $pdo;
        $this->userId = $userId;
        $this->adminId = $adminId;
        $this->employeId = $employeId;
    }

    // Setters
    public function setBackPassword($backPassword){ $this->backPassword = $backPassword;}
    public function setConfirmPassword($confirmPassword){ $this->confirmPassword = $confirmPassword;}
    public function setNewPassword($newPassword){ $this->newPassword = $newPassword;}

    // Getters
    private function getNewPassword(){ return $this->newPassword;}
    private function getBackPassword(){ return $this->backPassword;}
    private function getConfirmPassword(){ return $this->confirmPassword;}

    // Validation du mot de passe
    private function validPassword($newPassword)
    {
        // Verification que le nouveau mdp contient au moins une majuscule, un chiffre, et un caractère spécial)
        return strlen($newPassword) >= 8 && preg_match('/[\W]/', $newPassword) && preg_match('/[A-Z]/', $newPassword) && preg_match('/[0-9]/', $newPassword);
    }

    // Comparaison et validation des mots de passe
    private function comparePassword()
    {
        if ($this->newPassword !== $this->confirmPassword) {
            return $this->sendUserError('Les mots de passe ne correspondent pas.', 'backPassword');
        }

        if (!$this->validPassword($this->newPassword)) {
            return $this->sendUserError("Le mot de passe doit contenir au moins 8 caractères, une majuscule, un chiffre et un caractère spécial.", 'sendPassword');
        }

        // Si la validation passe, on retourne le mot de passe final
        $this->finalPassword = $this->newPassword;
        return null;
    }

    // Fonction de sélection de la bonne table
    private function getTargetTableAndColumn()
    {
        if ($this->adminId !== null) {
            return ['admins', 'adminid', $this->adminId];
        } elseif ($this->employeId !== null) {
            return ['employees', 'employeid', $this->employeId];
        } elseif ($this->userId !== null) {
            return ['users', 'userid', $this->userId];
        } else {
            $this->sendToDev("Aucun identifiant utilisateur défini");
        }
    }

    // Mise à jour du mot de passe
    public function updatePassword()
    {
        try {
            $this->getBackPassword();

            //Verification des données avant traitement
            if ($error = $this->comparePassword()) return $error;

            list($table, $column, $id) = $this->getTargetTableAndColumn();

            // On récupère le loginid et le mot de passe actuel
            $query = "SELECT l.password, l.loginid 
                      FROM logins l 
                      JOIN $table t ON l.loginid = t.idlogin 
                      WHERE t.$column = :id";

            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();

            $result = $statement->fetch(PDO::FETCH_ASSOC);

     
            // Vérification du mot de passe actuel
            if (!$result || !password_verify($this->getBackPassword(), $result['password'])) {
                return $this->sendUserError('Le mot de passe actuel n\'est pas correct','sendPassword');
            }

            // Hash du nouveau mot de passe avec un "cost" sécurisé pour Bcrypt
            $hashedPassword = password_hash($this->getNewPassword(), PASSWORD_BCRYPT, ['cost' => 12]);

            // Mise à jour du mot de passe dans la base de données
            $updateQuery = "UPDATE logins SET password = :finalPassword WHERE loginid = :loginId";
            $updateStmt = $this->pdo->prepare($updateQuery);
            $updateStmt->bindParam(':finalPassword', $hashedPassword);
            $updateStmt->bindParam(':loginId', $result['loginid'], PDO::PARAM_INT);

            if ($updateStmt->execute()) {
                return $this->sendPopup('Votre mot de passe a été mis à jour avec succès');
            }

            return $this->sendPopup('Une erreur est survenue lors de la mise à jour du mot de passe.\n Réessayer un peu plus tard.');
        } catch (PDOException $e) {
            // Journalisation de l'erreur avec un niveau critique
            return $this->saveLog("Erreur update password: " . $e->getMessage(), "ID: $id", 'CRITICAL');
        }
    }
}
