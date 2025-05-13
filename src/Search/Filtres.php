<?php

declare(strict_types=1);

require_once __DIR__ . '/../Traits/Exception.php';

class Filtres
{

    use ExceptionTrait;

    private $inseeDepart;
    private $inseeArrival;
    private $departDate;
    private $placeAvailable;
    private $animal;
    private $smoke;
    private $eco;
    private $duration;
    private $note;
    private $zone;


    public function __construct(
        $inseeDepart,
        $inseeArrival,
        $departDate,
        $placeAvailable,
        $animal,
        $smoke,
        $eco,
        $duration,
        $note,
        $zone
    ) {

        $this->setInseeDepart($inseeDepart);
        $this->setInseeArrival($inseeArrival);
        $this->setDepartDate($departDate);
        $this->setPlaceAvailable($placeAvailable);
        $this->setAnimal($animal);
        $this->setSmoke($smoke);
        $this->setEco($eco);
        $this->setDuration($duration);
        $this->setNote($note);
        $this->setZone($zone);
    }

    // Vérification que le code INSEE contient exactement 5 chiffres.
    // Utilisation de saveLog pour ces setters car les codes INSEE sont récupérés depuis la base de données.
    // Si un problème se produit dans ces setters, cela indique soit un problème interne dans la récupération des données,
    // soit une tentative d'accès malveillante ou erronée aux données.
protected function setInseeDepart($inseeDepart)
{
    if (is_numeric($inseeDepart) && strlen((string)$inseeDepart) === 5) {
        $this->inseeDepart = (int)$inseeDepart;
    } else {
        $this->saveLog("Le code INSEE doit être un nombre à 5 chiffres valide. inseeDepart: " . $inseeDepart, 'CRITICAL');
    }
}

protected function setInseeArrival($inseeArrival)
{
    if (is_numeric($inseeArrival) && strlen((string)$inseeArrival) === 5) {
        $this->inseeArrival = (int)$inseeArrival;
    } else {
        $this->saveLog("Le code INSEE doit être un nombre à 5 chiffres valide. inseeArrival: " . $inseeArrival, 'CRITICAL');
    }
}

    // Utilisation de l'objet DateTime pour vérifier que la date est valide et au format Y-m-d
   protected function setDepartDate($departDate)
{
    $date = \DateTime::createFromFormat('Y-m-d', $departDate);
    if ($date && $date->format('Y-m-d') === $departDate) {
        $this->departDate = $departDate;
    } else {
        $this->sendUserError("Date de départ invalide.", 'departureDate');
    }
}

    protected function setAnimal($animal)
    {
        if (is_bool($animal)) {
            $this->animal = $animal;
        } else {
            $this->sendUserError("Valeur Incorrecte.", 'animal');
        }
    }

    protected function setSmoke($smoke)
    {
        if (is_bool($smoke)) {
            $this->smoke = $smoke;
        } else {
            $this->sendUserError("Valeur Incorrecte.", 'smoke');
        }
    }

    protected function setEco($eco)
    {
        if (is_bool($eco)) {
            $this->eco = $eco;
        } else {
            $this->sendUserError("Valeur Incorrecte.", 'eco');
        }
    }

    // Vérification que la durée du trajet est au format HH:MM (heures et minutes)
    protected function setDuration($duration)
    {
        if (preg_match("/^\d{1,2}:\d{2}$/", $duration)) {
            $this->duration = $duration;
        } else {
            $this->sendUserError("La durée du trajet est invalide.", 'tripDuration');
        }
    }

    // Vérification que la valeur numérique est valide et se situe dans l'intervalle
    protected function setNote($note)
    {
        if (is_numeric($note) && $note >= 1 && $note <= 5) {
            $this->note = (int)$note;
        } else {
            $this->sendUserError("La note doit être un nombre entre 1 et 5.", 'note');
        }
    }

    protected function setZone($zone)
    {
        if (is_numeric($zone) && $zone >= 1 && $zone <= 100) {
            $this->zone = (int)$zone;
        } else {
            $this->sendUserError("La zone doit être un nombre entre 1 et 100.", 'kmRange');
        }
    }

    protected function setPlaceAvailable($placeAvailable)
    {
        if (is_numeric($placeAvailable) && $placeAvailable >= 0 && $placeAvailable <= 9) {
            $this->placeAvailable = (int)$placeAvailable;
        } else {
            $this->sendUserError("Doit être compris entre 0 et 9.", 'places');
        }
    }


    // Getters

    protected function getInseeDepart(){ return $this->inseeDepart; }
    protected function getInseeArrival(){ return $this->inseeArrival; }
    protected function getDepartDate(){ return $this->departDate; }
    protected function getPlaceAvailable(){ return $this->placeAvailable; }
    protected function getAnimal(){ return $this->animal; }
    protected function getSmoke(){ return $this->smoke; }
    protected function getEco(){ return $this->eco; }
    protected function getDuration(){ return htmlspecialchars_decode($this->duration, ENT_QUOTES); }
    protected function getNote(){ return $this->note; }
    protected function getZone(){ return $this->zone; }
}
