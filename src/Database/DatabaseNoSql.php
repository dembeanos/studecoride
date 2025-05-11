<?php

declare(strict_types=1);

require_once __DIR__ .'/../../vendor/autoload.php';

//Importation des methode Mongo
use MongoDB\Client;
use MongoDB\Database;
use Dotenv\Dotenv;

final class DatabaseNoSql {

    private Client $client;
    private Database $database;

    public function __construct() {
         $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();

         $host = $_ENV['MONGO_HOST'];
        $port = $_ENV['MONGO_PORT'];
        $username = $_ENV['MONGO_USERNAME'];
        $password = $_ENV['MONGO_PASSWORD'];
        $dbname = $_ENV['MONGO_DBNAME'];

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
