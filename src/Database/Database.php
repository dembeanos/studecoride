<?php

declare(strict_types=1);

final class Database {

    private $pdo;
    private $host = 'localhost';
    private $port = '5432';
    private $dbname = 'postgres';
    private $username = 'postgres';
    private $password = '231287';
    private $isConnected = false;  

    public function __construct() {
        try {
            $this->pdo = new PDO(
                "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}",
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                )
            );
            $this->isConnected = true;
        } catch (PDOException $e) {
            die("Impossible de se connecter à la base : " . $e->getMessage());
        }
    }

    public function getPdo() {
        return $this->pdo;
    }

    public function isConnected() {
        return $this->isConnected;
    }
    public function disconnect() {
        $this->pdo = null;
        $this->isConnected = false;
    }
}
?>
