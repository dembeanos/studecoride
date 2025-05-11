<?php
declare(strict_types=1);

require_once __DIR__ .'/../Database/Database.php';
require_once __DIR__ .'/../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

//Classe permettant la saisie semi automatique
final class City {

    use ExceptionTrait;

    private $pdo;
    private $citySearch;

    public function __construct($pdo, $citySearch)
    {
        $this->pdo = $pdo;
        $this->setCitySearch($citySearch);
    }

    //Tri de la recherche limité strictement à la casse de la base de donnée
    public function setCitySearch($citySearch)
    {
        $citySearch = preg_replace('/[^a-zA-ZÀ-ÖØ-öø-ÿ\s]/u', '', trim(strip_tags($citySearch))); // autorise aussi les tirets 
         // Contrôle de la longueur
        if  (strlen($citySearch) > 100) {
            $this->sendToDev('Le nom de la ville est invalide ou trop long.');
        }
        $this->citySearch = $citySearch;
    }


    private function getPdo()
    {
        return $this->pdo;
    }
    public function getCitySearch()
    {
        return $this->citySearch;
    }



    // Récupère les résultats triés par ordre alphabétique,
    // dont le nom commence par la saisie de l'utilisateur,
    // limité à 10 résultats.

    public function getCity()
    {
        if ($error = $this->setCitySearch($this->citySearch)) return $error;

        $citySearch = strtoupper(trim($this->getCitySearch()));
        $query = "SELECT insee_code, city_code, department_name,latitude, longitude FROM cities WHERE city_code LIKE :citySearch ORDER BY city_code ASC LIMIT 10"; // Limité à 10 résultats
        $statement = $this->pdo->prepare($query);
        try {
            $statement->execute(['citySearch' => $citySearch . '%']);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {

            $this->saveLog('Erreur lors de l\'obtention des villes' . $e->getMessage(), 'FATAL');
        }
    }
}
