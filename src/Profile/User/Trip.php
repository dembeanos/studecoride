<?php

declare(strict_types=1);

require_once __DIR__ .'/../../Database/Database.php';
require_once __DIR__ .'/../../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

final class Trip
{

    use ExceptionTrait;

    private $pdo;
    private $userId;
    private $cityDepart;
    private $arrivalCity;
    private $roadDepart;
    private $arrivalRoad;
    private $hourDepart;
    private $hourArrival;
    private $dateArrival;
    private $dateDepart;
    private $price;
    private $duration;
    private $car;
    private $preference;
    private $placeAvailable;
    private $inseeArrival;
    private $inseeDepart;
    private $totalReservations;
    private $status;
    private $tripId;

    public function __construct($pdo, $userid)
    {
        $this->userId = $userid;
        $this->pdo = $pdo;
    }


    // SETTERS
    private function setCityDepart($cityDepart)
    {
        if (!preg_match('/^[\p{L}0-9\s\'\-]{1,50}$/u', $cityDepart)) {
            return $this->sendUserError('Ce champ ne peut contenir que des lettres, chiffres, tirets et apostrophes.', 'cityDepart');
        }
        $this->cityDepart = htmlspecialchars($cityDepart, ENT_QUOTES, 'UTF-8');
    }

    private function setArrivalCity($arrivalCity)
    {
        if (!preg_match('/^[\p{L}0-9\s\'\-]{1,50}$/u', $arrivalCity)) {
            return $this->sendUserError('Ce champ ne peut contenir que des lettres, chiffres, tirets et apostrophes.', 'cityArrival');
        }
        $this->arrivalCity = htmlspecialchars($arrivalCity, ENT_QUOTES, 'UTF-8');
    }

    private function setRoadDepart($roadDepart)
    {
        if (!preg_match('/^[\p{L}0-9\s\'\-]{1,50}$/u', $roadDepart)) {
            return $this->sendUserError('Ce champ ne peut contenir que des lettres, chiffres, tirets et apostrophes.', 'roadDepart');
        }
        $this->roadDepart = htmlspecialchars($roadDepart, ENT_QUOTES, 'UTF-8');
    }

    private function setArrivalRoad($arrivalRoad)
    {
        if (!preg_match('/^[\p{L}0-9\s\'\-]{1,50}$/u', $arrivalRoad)) {
            return $this->sendUserError('Ce champ ne peut contenir que des lettres, chiffres, tirets et apostrophes.', 'arrivalRoad');
        }
        $this->arrivalRoad = htmlspecialchars($arrivalRoad, ENT_QUOTES, 'UTF-8');
    }


    private function setHourDepart($hourDepart)
    {
        if (!preg_match('/^([01]?[0-9]|2[0-3]):([0-5]?[0-9])$/', $hourDepart)) {
            return $this->sendUserError('L\'heure doit être au format HH:MM.', 'tripHourDepart');
        }
        $this->hourDepart = htmlspecialchars($hourDepart, ENT_QUOTES, 'UTF-8');
    }

    private function setHourArrival($hourArrival)
    {
        if (!preg_match('/^([01]?[0-9]|2[0-3]):([0-5]?[0-9])$/', $hourArrival)) {
            return $this->sendUserError('L\'heure doit être au format HH:MM.', 'tripArrivalHour');
        }
        $this->hourArrival = htmlspecialchars($hourArrival, ENT_QUOTES, 'UTF-8');
    }

    private function setDateDepart($dateDepart)
    {
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $dateDepart)) {
            return $this->sendUserError("La date de départ est invalide.", 'tripDateDepart');
        }
        $this->dateDepart = htmlspecialchars($dateDepart, ENT_QUOTES, 'UTF-8');
    }

    private function setDateArrival($dateArrival)
    {
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $dateArrival)) {
            return $this->sendUserError("La date d'arrivée est invalide.", 'tripArrivalDate');
        }
        $this->dateArrival = htmlspecialchars($dateArrival, ENT_QUOTES, 'UTF-8');
    }

    private function setPrice($price)
    {
        if (!is_numeric($price) || $price < 3) {
            return $this->sendPopup("Le prix doit être un nombre positif superieur à 3.", 'tripPrice');
        }
        $this->price = htmlspecialchars($price, ENT_QUOTES, 'UTF-8');
    }

    private function setDuration($duration)
    {
        if (!preg_match('/^([01]?[0-9]|2[0-3]):([0-5]?[0-9])$/', $duration)) {
            return $this->sendUserError('L\'heure doit être au format HH:MM.', 'tripDuration');
        }
        $this->duration = htmlspecialchars($duration, ENT_QUOTES, 'UTF-8');
    }

    private function setCar($car)
    {
        $car = trim($car);
        if (!is_numeric($car)) {
            return $this->sendUserError('Véhicule saisie non valide', 'autoTrip');
        }
        $this->car = htmlspecialchars($car, ENT_QUOTES, 'UTF-8');
    }

    private function setPreference($preference)
    {
        if (!preg_match("/^[0-9\s]+$/", $preference)) {
            return $this->sendToDev("La préférence est invalide. Seuls les chiffres et les espaces sont autorisés.");
        }
        $this->preference = htmlspecialchars($preference, ENT_QUOTES, 'UTF-8');
    }

    private function setPlaceAvailable($placeAvailable)
    {
        $placeAvailable = trim($placeAvailable);

        if (!is_numeric($placeAvailable) || $placeAvailable > 20 || $placeAvailable < 1) {
            return $this->sendUserError('Le nombre de places ne peut excéder 20', 'tripPlaces');
        }
        $this->placeAvailable = htmlspecialchars($placeAvailable, ENT_QUOTES, 'UTF-8');
    }

    private function setInseeArrival($inseeArrival)
    {
        $inseeArrival = trim($inseeArrival);

        if (!is_numeric($inseeArrival) || strlen($inseeArrival) !== 5) {
            return $this->sendToDev("Le code INSEE de départ doit être constitué de 5 chiffres.");
        }
        $this->inseeArrival = htmlspecialchars($inseeArrival, ENT_QUOTES, 'UTF-8');
    }

    private function setInseeDepart($inseeDepart)
    {
        $inseeDepart = trim($inseeDepart);

        if (!is_numeric($inseeDepart) || strlen($inseeDepart) !== 5) {
            return $this->sendToDev("Le code INSEE d'arrivée doit être constitué de 5 chiffres.");
        }
        $this->inseeDepart = htmlspecialchars($inseeDepart, ENT_QUOTES, 'UTF-8');
    }
    private function setTripId($tripId)
    {
        if (!is_numeric($tripId) || $tripId <= 0) {
            return $this->sendToDev("L'ID du trajet est invalide.");
        }
        $this->tripId = $tripId;
    }


    // GETTERS
    public function getpdo(){return htmlspecialchars_decode($this->pdo, ENT_QUOTES);}
    public function getUserId(){return $this->userId;}
    public function getCityDepart(){return htmlspecialchars_decode($this->cityDepart, ENT_QUOTES);}
    public function getArrivalCity(){return htmlspecialchars_decode($this->arrivalCity, ENT_QUOTES);}
    public function getRoadDepart(){return htmlspecialchars_decode($this->roadDepart, ENT_QUOTES);}
    public function getArrivalRoad(){return htmlspecialchars_decode($this->arrivalRoad, ENT_QUOTES);}
    public function getHourDepart(){return htmlspecialchars_decode($this->hourDepart, ENT_QUOTES);}
    public function getHourArrival(){return htmlspecialchars_decode($this->hourArrival, ENT_QUOTES);}
    public function getDateDepart(){return htmlspecialchars_decode($this->dateDepart, ENT_QUOTES);}
    public function getDateArrival(){return htmlspecialchars_decode($this->dateArrival, ENT_QUOTES);}
    public function getPrice(){return htmlspecialchars_decode($this->price, ENT_QUOTES);}
    public function getDuration(){return htmlspecialchars_decode($this->duration, ENT_QUOTES);}
    public function getCar(){return htmlspecialchars_decode($this->car, ENT_QUOTES);}
    public function getPreference(){return htmlspecialchars_decode($this->preference, ENT_QUOTES);}
    public function getPlaceAvailable(){return htmlspecialchars_decode($this->placeAvailable, ENT_QUOTES);}
    public function getInseeArrival(){return htmlspecialchars_decode($this->inseeArrival, ENT_QUOTES);}
    public function getInseeDepart(){return htmlspecialchars_decode($this->inseeDepart, ENT_QUOTES);}
    public function getReservationsStatus(){return htmlspecialchars_decode($this->totalReservations, ENT_QUOTES);}
    public function getStatus(){return htmlspecialchars_decode($this->status, ENT_QUOTES);}
    public function getTripId(){return $this->tripId;}


    //Fonctions principales:

    public function addTrip( $cityDepart, $arrivalCity, $hourDepart, $roadDepart, $arrivalRoad, $hourArrival, 
    $dateDepart, $dateArrival, $price, $duration, $car, $preference, $placeAvailable, $inseeArrival, $inseeDepart) {

        try {

            $query = "SELECT COUNT(*) FROM offers 
                      WHERE citydepart = :cityDepart
                      AND arrivalcity = :arrivalCity
                      AND datedepart = :dateDepart
                      AND hourdepart = :hourDepart";

            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':cityDepart', $cityDepart, PDO::PARAM_STR);
            $statement->bindValue(':arrivalCity', $arrivalCity, PDO::PARAM_STR);
            $statement->bindValue(':dateDepart', $dateDepart, PDO::PARAM_STR);
            $statement->bindValue(':hourDepart', $hourDepart, PDO::PARAM_STR);
            $statement->execute();

            $count = $statement->fetchColumn();

            if ($count > 0) {
                $this->sendPopup("Une offre similaire existe déjà pour ce trajet à cette date et heure.");
            }

            if ($error = $this->setCityDepart($cityDepart)) return $error;
            if ($error = $this->setArrivalCity($arrivalCity)) return $error;
            if ($error = $this->setHourDepart($hourDepart)) return $error;
            if ($error = $this->setHourArrival($hourArrival)) return $error;
            if ($error = $this->setDateDepart($dateDepart)) return $error;
            if ($error = $this->setDateArrival($dateArrival)) return $error;
            if ($error = $this->setRoadDepart($roadDepart)) return $error;
            if ($error = $this->setArrivalRoad($arrivalRoad)) return $error;
            if ($error = $this->setPrice($price)) return $error;
            if ($error = $this->setDuration($duration)) return $error;
            if ($error = $this->setCar($car)) return $error;
            if ($error = $this->setPreference($preference)) return $error;
            if ($error = $this->setPlaceAvailable($placeAvailable)) return $error;
            if ($error = $this->setInseeDepart($inseeDepart)) return $error;
            if ($error = $this->setInseeArrival($inseeArrival)) return $error;

            $query = 'INSERT INTO offers (iduser, idauto, idpreference, hourdepart, datedepart, roaddepart, citydepart, arrivalcity,hourarrival,arrivalroad, datearrival, price, placeavailable, duration, inseeDepart, inseeArrival)
                      VALUES (:userId, :car, :preference, :hourDepart, :dateDepart, :roadDepart, :cityDepart, :arrivalCity, :hourArrival, :arrivalRoad, :dateArrival, :price, :placeAvailable, :duration, :inseeDepart, :inseeArrival)';

            $statement = $this->pdo->prepare($query);

            $statement->bindValue(':userId', $this->getUserId(), PDO::PARAM_INT);
            $statement->bindValue(':car', $this->getCar());
            $statement->bindValue(':preference', $this->getPreference());
            $statement->bindValue(':hourDepart', $this->getHourDepart(), PDO::PARAM_STR);
            $statement->bindValue(':hourArrival', $this->getHourArrival(), PDO::PARAM_STR);
            $statement->bindValue(':dateDepart', $this->getDateDepart());
            $statement->bindValue(':cityDepart', $this->getCityDepart());
            $statement->bindValue(':arrivalCity', $this->getArrivalCity());
            $statement->bindValue(':dateArrival', $this->getDateArrival());
            $statement->bindValue(':roadDepart', $this->getRoadDepart());
            $statement->bindValue(':arrivalRoad', $this->getArrivalRoad());
            $statement->bindValue(':price', $this->getPrice());
            $statement->bindValue(':duration', $this->getDuration(), PDO::PARAM_STR);
            $statement->bindValue(':placeAvailable', $this->getPlaceAvailable());
            $statement->bindValue(':inseeArrival', $this->getInseeArrival());
            $statement->bindValue(':inseeDepart', $this->getInseeDepart());

            $statement->execute();

            return $this->sendUserSuccess('Votre proposition de trajet à bien été prise en compte', 'addTrip');
        } catch (Exception $e) {
            $this->sendPopup('Oups ! Échec de l\'enregistrement de votre trajet, vérifiez les champs ou réessayez un peu plus tard.');
            return $this->saveLog('Erreur lors de l\enregistrement d\'une offres, Pour : '.$this->getUserId().$e,'FATAL');
        }
    }



    public function getTrip()
    {
        $updateStatus = "UPDATE offers SET status = 'passed' WHERE datedepart < CURRENT_DATE AND status !='passed'";

        $this->pdo->exec($updateStatus);

        $query = "SELECT o.datedepart, o.offerid, o.price, o.hourdepart, o.citydepart, o.hourarrival, o.datearrival, o.arrivalcity, o.placeAvailable, o.status,
            COUNT(r.idoffer) AS totalReservations
            FROM offers o
            LEFT JOIN reservations r ON o.offerid = r.idoffer AND r.status = 'false' 
            WHERE o.iduser = :userId
            GROUP BY o.offerid";

        try {
            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':userId', $this->getUserId(), PDO::PARAM_INT);
            $statement->execute();
            $offreInfo = $statement->fetchAll(PDO::FETCH_ASSOC);

            return [
                'status' => 'success',
                'data' => $offreInfo
            ];
        } catch (PDOException $e) {
            return $this->saveLog('Echec lors de la récuparation des offre de l\'utilisateur, Pour userId:' . 'UserId' . $this->getUserId() . $e->getMessage(), 'CRITICAL');
        }
    }

}