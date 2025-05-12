<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\Database;
use Dotenv\Dotenv;

final class DatabaseNoSql {

    private Client $client;
    private Database $database;

    public function __construct() {
        
        $uri = getenv('MONGO_URI');

        try {
            $this->client = new Client($uri);
            $this->database = $this->client->selectDatabase('ecoride');
        } catch (Exception $e) {
            die('Erreur de connexion MongoDB : ' . $e->getMessage());
        }
    }

    public function getDatabase(): Database {
        return $this->database;
    }
}
