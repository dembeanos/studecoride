<?php

declare(strict_types=1);
require_once __DIR__ .'/../../Database/Database.php';
require_once __DIR__ .'/../../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

final class Profit
{
    use ExceptionTrait;

    private $pdo;
    private $adminId;

    public function __construct($pdo, $adminId)
    {
        $this->pdo = $pdo;
        $this->adminId = $adminId;
    }

    //Setter
    private function setAdminId($adminId)
    {
        if (!is_numeric($adminId) || (int)$adminId <= 0) {
            $this->sendToDev("L'ID administrateur est invalide.");
        }
        $this->adminId = (int)$adminId;
    }

    //Getter
    private function getAdminId() { return $this->adminId; }


    //Fonction check
    private function checkIfAuthorized(): bool
    {
        $sql = "SELECT COUNT(*) FROM admins WHERE adminid = :adminId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':adminId', $this->getAdminId(), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }


    // Fonction Principale :
    public function getProfit()
    {
        if (!$this->checkIfAuthorized()) {
            return $this->sendToDev('L\'ID administrateur est invalide.');
        }

        // Requête SQL pour récupérer les crédits de l'entreprise (iduser = 0)
        $sql = "SELECT credit, creationdate
                FROM credits
                WHERE iduser = 0";  // Crédit attribué à l'entreprise

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $credits = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($credits)) {
            $this->sendToDev('Aucune donnée trouvée.');
        }

        return $credits;
    }
}
