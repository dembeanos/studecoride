<?php

declare(strict_types=1);

require_once __DIR__ .'/../../Database/Database.php';
require_once __DIR__ .'/../../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

final class Car
{
    use ExceptionTrait;

    private $pdo;
    private $userId;
    private $marque;
    private $modele;
    private $immatriculation;
    private $firstImmatriculation;
    private $color;
    private $energy;
    private $places;

    public function __construct($pdo, $userId)
    {
        $this->userId = $userId;
        $this->pdo = $pdo;
    }

    //Setters

    private function setMarque($marque)
    {
        if (!empty($marque) && is_string($marque)) {
            $this->marque = htmlspecialchars($marque, ENT_QUOTES, 'UTF-8');
            if (strlen($marque) > 15) {
                return $this->sendUserError('La marque ne peut pas dépasser 15 caractères', 'marque');
            }
        } else {
            return $this->sendUserError('La marque ne peut pas être vide', 'marque');
        }
    }
    private function setModele($modele)
    {
        if (!empty($modele) && is_string($modele)) {
            $this->modele = htmlspecialchars($modele, ENT_QUOTES, 'UTF-8');
            if (strlen($modele) > 15) {
                return $this->sendUserError('Le modele ne peut pas dépasser 15 caractères', 'modele');
            }
        } else {
            return $this->sendUserError('Le modele ne peut pas être vide', 'modele');
        }
    }

    private function setImmatriculation($immatriculation)
    {
        if (!$immatriculation) {
            return $this->sendUserError('L\'immatriculation est obligatoire', 'immatriculation');
        }
        $immatriculation = strtoupper($immatriculation);
        $immatriculation = preg_replace('/[^A-Z0-9-]/', '', $immatriculation);
        $immatriculation = str_replace('-', '', $immatriculation);
        $immatriculation = substr($immatriculation, 0, 2) . '-' . substr($immatriculation, 2, 3) . '-' . substr($immatriculation, 5, 2);
        $this->immatriculation = $immatriculation;
    }

    private function setFirstImmatriculation($firstImmatriculation)
    {
        $date = DateTime::createFromFormat('Y-m-d', $firstImmatriculation);
        if ($date && $date->format('Y-m-d') === $firstImmatriculation) {
            $this->firstImmatriculation = htmlspecialchars($firstImmatriculation, ENT_QUOTES, 'UTF-8');
        } else {
            return $this->sendUserError('La date de première mise en circulation est invalide', 'firstImmatriculation');
        }
    }
    private function setColor($color)
    {
        if (!empty($color) && is_string($color)) {
            $this->color = htmlspecialchars($color, ENT_QUOTES, 'UTF-8');
            if (strlen($color) > 15) {
                return $this->sendUserError('La color ne peut pas dépasser 15 caractères', 'color');
            }
        } else {
            return $this->sendUserError('Veuillez renseigner la couleur du véhicule', 'color');
        }
    }
    private function setEnergy($energy)
    {
        if (!empty($energy) && is_string($energy)) {
            $this->energy = htmlspecialchars($energy, ENT_QUOTES, 'UTF-8');
            if (strlen($energy) > 15) {
                return $this->sendUserError('L\'energie utilisé du véhicule ne peut pas dépasser 15 caractères', 'energy');
            }
        } else {
            return $this->sendUserError('Veuillez renseigner le type de carburant du véhicule', 'energy');
        }
    }
    private function setPlaces($places)
{
    $places = trim($places);
    
    if (!empty($places) && ctype_digit($places) && (int)$places <= 20) {
        $this->places = htmlspecialchars($places, ENT_QUOTES, 'UTF-8');
    } else {
        return $this->sendUserError('Veuillez indiquer un nombre valide de places (entre 1 et 20)', 'places');
    }
}


    // Getters

    public function getPdo(){ return $this->pdo; }
    public function getUserId(){ return $this->userId; }
    public function getMarque(){ return htmlspecialchars_decode($this->marque); }
    public function getModele(){ return htmlspecialchars_decode($this->modele); }
    public function getImmatriculation(){ return $this->immatriculation; }
    public function getFirstImmatriculation(){ return htmlspecialchars_decode($this->firstImmatriculation); }
    public function getColor(){ return htmlspecialchars_decode($this->color); }
    public function getEnergy(){ return htmlspecialchars_decode($this->energy); }
    public function getPlaces(){ return htmlspecialchars_decode($this->places); }

    // Fonctions Principales


    public function getCar()
    {
        $query = 'SELECT carid, marque, modele, immatriculation, firstimmatriculation, color, energy, places
                FROM cars
                WHERE iduser = :userId';

        try {
            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':userId', $this->getUserId(), PDO::PARAM_INT);
            $statement->execute();
            $carInfo = $statement->fetchall(PDO::FETCH_ASSOC);

            return [
                'status' => 'success',
                'data' => $carInfo
            ];

        } catch (PDOException $e) {
            return  $this->saveLog('Erreur lors de la recupération des véhicules. Pour l\'user:'.$this->getUserId() . $e->getMessage(),'CRITICAL');
        }
    }




    public function addCar($marque, $modele,  $immatriculation, $firstImmatriculation, $color, $energy, $places)
    {
        try {

            if ($error = $this->setMarque($marque)) return $error;
            if ($error = $this->setModele($modele)) return $error;
            if ($error = $this->setImmatriculation($immatriculation)) return $error;
            if ($error = $this->setFirstImmatriculation($firstImmatriculation)) return $error;
            if ($error = $this->setColor($color)) return $error;
            if ($error = $this->setEnergy($energy)) return $error;
            if ($error = $this->setPlaces($places)) return $error;

            $queryCheck = 'SELECT COUNT(*) FROM cars WHERE immatriculation = :immatriculation AND iduser = :userId';
            $statementCheck = $this->pdo->prepare($queryCheck);
            $statementCheck->bindValue(':immatriculation', $this->immatriculation);
            $statementCheck->bindValue(':userId', $this->getUserId(), PDO::PARAM_INT);
            $statementCheck->execute();

            $count = $statementCheck->fetchColumn();

            if ($count > 0) {
                return $this->sendUserError('Ce véhicule est déjà enregistré.', 'addCar');
            }

            $query = 'INSERT INTO cars 
            (iduser, marque, modele, immatriculation, firstimmatriculation, color, energy, places)
            VALUES
            (:userId, :marque, :modele, :immatriculation, :firstImmatriculation, :color, :energy, :places)';

            $statement = $this->pdo->prepare($query);

            $statement->bindValue(':userId', $this->getUserId(), PDO::PARAM_INT);
            $statement->bindValue(':marque', $this->getMarque());
            $statement->bindValue(':modele', $this->getModele());
            $statement->bindValue(':immatriculation', $this->getImmatriculation());
            $statement->bindValue(':firstImmatriculation', $this->getFirstImmatriculation());
            $statement->bindValue(':color', $this->getColor());
            $statement->bindValue(':energy', $this->getEnergy());
            $statement->bindValue(':places', $this->getPlaces());

            $executeSuccess = $statement->execute();

            if ($executeSuccess) {
                return $this->sendUserSuccess('Votre véhicule a bien été ajouté.', 'addCar');
            } else {
                return $this->sendToDev("Erreur lors de l\'ajout du véhicule dans la base de données.");
            }
        } catch (Exception $e) {
            return $this->saveLog('Echec de l\'ajout d\'un véchicule pour l\'user:'.$this->getUserId().$e->getMessage(),'CRITICAL');
        }
    }




    public function deleteCar($immatriculation)
    {
        $query = "DELETE FROM cars WHERE immatriculation = :immatriculation";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':immatriculation', $immatriculation, PDO::PARAM_STR);

        if ($statement->execute()) {
            if ($statement->rowCount() > 0) {
                return $this->sendUserSuccess('Vehicule supprimé avec succès', 'carLine');
            } else {
                return $this->sendToDev('Vehicule non trouvé ou déjà supprimé');
            }
        }
        return $this->saveLog('Echec de la suppression d\'un véchicule pour l\'user:'.$this->getUserId(),'CRITICAL');
    }


}
