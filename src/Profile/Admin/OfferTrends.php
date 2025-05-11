<?php

declare(strict_types=1);

require_once __DIR__ .'/../../Database/Database.php';
require_once __DIR__ .'/../../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

final class OfferTrends
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
    private function getAdminId()
    {
        return $this->adminId;
    }

    //Check autorisation
    private function checkIfAuthorized(): bool
    {
        $sql = "SELECT COUNT(*) FROM admins WHERE adminid = :adminId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':adminId', $this->getAdminId(), PDO::PARAM_INT); 
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    //Fonction Principale:
    public function getTrends()
    {
        if (!$this->checkIfAuthorized()) {
            $this->sendToDev('L\'ID administrateur est invalide.');
        }

        $sql = "SELECT offerid, datedepart FROM offers ORDER BY creationdate DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$employees) {
            $this->sendToDev('Aucune donnée trouvée.');
        }

        return $employees;
    }
}
