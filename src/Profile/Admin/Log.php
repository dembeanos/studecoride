<?php

declare(strict_types=1);

require_once __DIR__ .'/../../Database/Database.php';
require_once __DIR__ .'/../../Database/DatabaseNoSql.php';
require_once __DIR__ .'/../../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

final class Log {
    
    use ExceptionTrait;

    private $pdo;
    private $adminId;
    private $mongo;
    private $date;


    public function __construct($pdo, $adminId)
    {
        $this->pdo = $pdo;
        $this->adminId = $adminId;
    }

    //Connection Mongo
    private function connectMongo() {
        if (!$this->mongo) {
            $db = new DatabaseNoSql();
            $this->mongo = $db->getDatabase();
        }
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
    private function getAdminId(){ return $this->adminId; }


    //Check autorisation
    private function checkIfAuthorized(): bool
    {
        $sql = "SELECT COUNT(*) FROM admins WHERE adminid = :adminId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':adminId', $this->getAdminId(), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }




    //Fonction Principale
    public function getLogs()
    {
        if (!$this->checkIfAuthorized()) {
            $this->sendToDev('L\'ID administrateur est invalide.');
        }
        try {
            $this->connectMongo();
            $logListe = $this->mongo->logs;
    
            $liste = $logListe->find([], ['sort' => ['timestamp' => -1]]);
            return iterator_to_array($liste);
    
        } catch (Exception $e) {
            return $this->sendToDev('Erreur lors de la récupération des logs : ' . $e->getMessage());
        }
    }
    
}