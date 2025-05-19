<?php

declare(strict_types=1);
require_once __DIR__ . '/Filtres.php';

require_once __DIR__ . '/../Database/Database.php';
require_once __DIR__ . '/../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

class Extraction extends Filtres {
    
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

  public function searchOffersByZone(): array
    {
        $geoms = $this->getGeom();
        $geomDepart = $geoms['depart'];
        $geomArrival = $geoms['arrival'];

        // Conversion de la zone (km -> mètres) et calcul en degrés
        $zoneInDegrees = ($this->getZone() * 1000) / 111320;

        // Requête combinée : offres actives autour du départ ET arrivée
        $sql = <<<SQL
SELECT
    o.offerid,
    o.datedepart,
    o.citydepart,
    o.arrivalcity,
    o.price,
    o.placeavailable,
    o.duration,
    o.datearrival,
    o.roaddepart,
    o.arrivalroad,
    o.hourdepart,
    o.hourarrival,
    o.status,
    p.animal,
    p.smoke,
    p.other,
    c.energy,
    c.modele,
    c.marque,
    c.color,
    u.note,
    u.photo,
    l.username
FROM offers o
JOIN preferences p ON o.iduser = p.iduser
JOIN cars c        ON o.idauto  = c.carid
JOIN users u       ON o.iduser  = u.userId
JOIN logins l      ON u.idlogin = l.loginid
WHERE
    o.status = :status
    AND ST_DWithin(o.geomdepart, :geomDepart, :zoneInDegrees)
    AND ST_DWithin(o.geomarrival, :geomArrival, :zoneInDegrees);
SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':geomDepart',    $geomDepart);
        $stmt->bindValue(':geomArrival',   $geomArrival);
        $stmt->bindValue(':zoneInDegrees', $zoneInDegrees);
        $stmt->bindValue(':status', 'active');
        $stmt->execute();

        $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($offers)) {
            throw new RuntimeException('Aucune offre active trouvée dans la zone de départ et d’arrivée.');
        }

        return $offers;
    }

    /*
     * Filtre les offres selon les préférences de l'utilisateur
     
     * Tableau des offres filtrées ou message d'erreur
     */
    public function filterOffers()
    {
        try {
            $offers = $this->searchOffersByZone();

            // Vérification supplémentaire : ne traiter que les offres actives
            $offers = array_filter(
                $offers,
                fn(array $offer) => isset($offer['status']) && $offer['status'] === 'active'
            );

            $filteredOffers = array_filter(
                $offers,
                fn(array $offer): bool => $this->isOfferValid($offer)
            );

            if (empty($filteredOffers)) {
                return $this->sendPopup(
                    'Aucune offre ne correspond à vos critères de filtrage. Essayez d’assouplir vos préférences ou de changer la date.'
                );
            }

            return array_values($filteredOffers);

        } catch (Throwable $e) {
            return $this->sendPopup(
                'Aucune offre ne correspond à vos critères de filtrage. Essayez d’assouplir vos préférences ou de changer la date.'
            );
        }
    }

    /**
     * Validation d'une offre selon les critères utilisateur
     */
     private function isOfferValid(array $offer): bool
    {
        // Ne pas traiter les offres inactives
        if (isset($offer['status']) && $offer['status'] !== 'active') {
            return false;
        }

        // Vérification de la date de départ
        if (strtotime($offer['datedepart']) < strtotime($this->getDepartDate())) {
            return false;
        }

        // Vérification du nombre de places disponibles
        if ($offer['placeavailable'] < $this->getPlaceAvailable()) {
            return false;
        }

        // Vérification de la présence d'animal
        if ($offer['animal'] !== $this->getAnimal()) {
            return false;
        }

        // Vérification de la préférence tabac
        if ($offer['smoke'] !== $this->getSmoke()) {
            return false;
        }

        // Critère écologique : énergie électrique
        if ($this->getEco() && $offer['energy'] !== 'Electric') {
            return false;
        }

        // Durée du trajet maximale
        if ($offer['duration'] > $this->getDuration()) {
            return false;
        }

        // Note minimale
        if ($offer['note'] !== null && $offer['note'] < $this->getNote()) {
            return false;
        }

        return true;
    }
}