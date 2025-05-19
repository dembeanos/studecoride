<?php

declare(strict_types=1);

final class Database
{

    private $pdo;
    private $host;
    private $port;
    private $dbname;
    private $username;
    private $password;
    private $isConnected = false;

    public function __construct()
    {
        $this->host = getenv('PGSQL_HOST');
        $this->port = getenv('PGSQL_PORT');
        $this->dbname = getenv('PGSQL_DBNAME');
        $this->username = getenv('PGSQL_USERNAME');
        $this->password = getenv('PGSQL_PASSWORD');


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

    public function getPdo()
    {
        return $this->pdo;
    }

    public function isConnected()
    {
        return $this->isConnected;
    }
    public function disconnect()
    {
        $this->pdo = null;
        $this->isConnected = false;
    }
}
