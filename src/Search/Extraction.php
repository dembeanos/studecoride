<?php

declare(strict_types=1);
require_once __DIR__ . '/Filtres.php';

require_once __DIR__ . '/../Database/Database.php';
require_once __DIR__ . '/../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

class Extraction extends Filtres
{
    use ExceptionTrait;

    private $pdo;
    //variables des geometries
    private $geomFromCitiesDepart;
    private $geomFromCitiesArrival;
    //variables des resultats filtrées
    public $filteredOffers;
    //Variable de la classe Filtres
    private $filtre;

    public function __construct(Filtres $filtre, PDO $pdo)
    {
        $this->pdo = $pdo;

        // Récupération des informations filtrées
        parent::__construct(

            $filtre->getInseeDepart(),
            $filtre->getInseeArrival(),
            $filtre->getDepartDate(),
            $filtre->getPlaceAvailable(),
            $filtre->getAnimal(),
            $filtre->getSmoke(),
            $filtre->getEco(),
            $filtre->getDuration(),
            $filtre->getNote(),
            $filtre->getZone()
        );
    }

    public function getGeom()
    {

        // Assigner à des variables les valeurs récupérées de filtres
        $inseeDepart = $this->getInseeDepart();
        $inseeArrival = $this->getInseeArrival();
        $departDate = $this->getDepartDate();
        $placeAvailable = $this->getPlaceAvailable();
        $animal = $this->getAnimal();
        $smoke = $this->getSmoke();
        $eco = $this->getEco();
        $tripDuration = $this->getDuration();
        $note = $this->getNote();
        $zone = $this->getZone();

        //Appel au setter de Filtres pour vérifier que toutes les données sont conformes
        if ($error = $this->setInseeDepart($inseeDepart)) return $error;
        if ($error = $this->setInseeArrival($inseeArrival)) return $error;
        if ($error = $this->setDepartDate($departDate)) return $error;
        if ($error = $this->setPlaceAvailable($placeAvailable)) return $error;
        if ($error = $this->setAnimal($animal)) return $error;
        if ($error = $this->setSmoke($smoke)) return $error;
        if ($error = $this->setEco($eco)) return $error;
        if ($error = $this->setDuration($tripDuration)) return $error;
        if ($error = $this->setNote($note)) return $error;
        if ($error = $this->setZone($zone)) return $error;



        try {

            //Initialisation des variables de géometrie
            $geomFromCitiesArrival = null;
            $geomFromCitiesDepart = null;

            //Récupération insee et geometrie de la table cities
            $query = "SELECT insee_code, geom FROM cities WHERE insee_code = :inseeDepart OR insee_code = :inseeArrival";

            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':inseeDepart', $inseeDepart);
            $statement->bindValue(':inseeArrival', $inseeArrival);
            $statement->execute();

            //boucle pour associer les bonnes géom aux bonnes variables
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                if (isset($row['insee_code'])) {
                    if ($row['insee_code'] == $inseeDepart) {
                        $geomFromCitiesDepart = $row['geom'];
                    } elseif ($row['insee_code'] == $inseeArrival) {
                        $geomFromCitiesArrival = $row['geom'];
                    }
                }
            }

            return [
                'depart' => $geomFromCitiesDepart,
                'arrival' => $geomFromCitiesArrival
            ];
        } catch (PDOException $e) {
            $this->saveLog("Erreur lors de la récupération des géométries : " . $e->getMessage(), 'FATAL');
        }
    }

    public function searchOffersByZone()
    {
        try {
            // Appel de la méthode précédente qui retourne un tableau de valeur
            $geoms = $this->getGeom();
            $geomDepart = $geoms['depart'];
            $geomArrival = $geoms['arrival'];

            // Convertir les kilomètres en mètres
            $zone = $this->getZone() * 1000;

            // Calcul de la zone en degrés 
            $zoneInDegrees = $zone / 111320;

            // Première requête pour récupérer les offres correspondant à la zone autour du départ
            $query = "
            SELECT o.offerid, o.datedepart, o.citydepart, o.arrivalcity, o.price, o.placeavailable, o.duration, o.datearrival,
                   o.roaddepart, o.arrivalroad, o.hourdepart, o.hourarrival, 
                   p.animal, p.smoke, p.other, 
                   c.energy,c.modele,c.marque, c.color, 
                   u.note,u.photo,
                   l.username
            FROM offers o
            INNER JOIN preferences p ON o.iduser = p.iduser
            INNER JOIN cars c ON o.idauto = c.carid
            INNER JOIN users u ON o.iduser = u.userId
            INNER JOIN logins l ON l.loginid = u.idlogin
            WHERE 
                ST_DWithin(o.geomdepart, :geomDepart, :zoneInDegrees) AND
                o.status = 'active'
            ";

            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':geomDepart', $geomDepart);
            $statement->bindValue(':zoneInDegrees', $zoneInDegrees);
            $statement->execute();

            $offers = $statement->fetchAll(PDO::FETCH_ASSOC);

            if (empty($offers)) {
                return $this->sendToDev('Aucune offre disponible dans la zone de départ');
            }

            // Étape 2 : Filtrage pour exclure les offres dont la géométrie d'arrivée n'est pas dans la zone
            $validOffers = [];
            foreach ($offers as $offer) {
                $queryArrival = "
                SELECT 1
                FROM offers o
                WHERE ST_DWithin(o.geomarrival, :geomArrival, :zoneInDegrees)
                AND o.offerid = :offerid
                ";

                $arrivalStatement = $this->pdo->prepare($queryArrival);
                $arrivalStatement->bindValue(':geomArrival', $geomArrival);
                $arrivalStatement->bindValue(':zoneInDegrees', $zoneInDegrees);
                $arrivalStatement->bindValue(':offerid', $offer['offerid']);
                $arrivalStatement->execute();

                $arrivalValid = $arrivalStatement->fetch(PDO::FETCH_ASSOC);

                if ($arrivalValid) {
                    $validOffers[] = $offer; // Ajouter l'offre à la liste des offres valides
                }
            }

            if (empty($validOffers)) {
                return $this->sendToDev('Aucune offre valide ne correspond à vos critères');
            }

            return $validOffers;
        } catch (PDOException $e) {
            $this->saveLog("Erreur lors de la recherche des offres : " . $e->getMessage(), 'FATAL');
        }
    }

    //Affinage des offres selon les paramètres saisis par l'utilisateur
    public function filterOffers()
    {
        try {
            $offers = $this->searchOffersByZone();
        } catch (Exception $e) {
            return $offers;
        }

        $filteredOffers = [];
        try {
            foreach ($offers as $offer) {
                $isValid = true;

                if (strtotime($offer['datedepart']) < strtotime($this->getDepartDate())) {
                    $isValid = false;
                }

                if ($offer['placeavailable'] < $this->getPlaceAvailable()) {
                    $isValid = false;
                }

                if ($offer['animal'] != $this->getAnimal()) {
                    $isValid = false;
                }

                if ($offer['smoke'] != $this->getSmoke()) {
                    $isValid = false;
                }

                if ($offer['energy'] != 'Electric' && $this->getEco() === true) {
                    $isValid = false;
                }

                if ($offer['duration'] > $this->getDuration()) {
                    $isValid = false;
                }

                if ($offer['note'] !== null && $offer['note'] < $this->getNote()) {
                    $isValid = false;
                }

                if ($isValid) {
                    $filteredOffers[] = $offer;
                }
            }

            if (count($filteredOffers) == 0) {
                return $this->sendPopup('Aucune offre ne correspond à vos critères de filtrage. Peut-être qu’une autre date pourrait vous convenir ?');
            }

            return $filteredOffers;
        } catch (Exception $e) {
            $this->saveLog("Erreur lors du filtrage des offres : " . $e->getMessage(), 'FATAL');
        }
    }
}
