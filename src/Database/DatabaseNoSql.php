<?php

declare(strict_types=1);

require_once __DIR__ .'/../../vendor/autoload.php';

//Importation des methode Mongo
use MongoDB\Client;
use MongoDB\Database;

final class DatabaseNoSql {

    private Client $client;
    private Database $database;

    public function __construct() {
        $host = 'localhost';
        $port = 27017;
        $username = 'php';
        $password = 'j7krqxf8v23j42m4bjxygwr6w*';
        $dbname = 'Ecoride';

        $uri = "mongodb://$username:$password@$host:$port/$dbname";

        try {
            $this->client = new Client($uri);
            $this->database = $this->client->selectDatabase($dbname);
        } catch (Exception $e) {
            die('Erreur de connexion MongoDB : ' . $e->getMessage());
        }
    }

    public function getDatabase(): Database {
        return $this->database;
    }
}
