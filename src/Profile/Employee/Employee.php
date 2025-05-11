<?php
declare(strict_types=1);

require_once __DIR__ .'/../../Database/Database.php';
require_once __DIR__ .'/../../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();


final class Employee {

        use ExceptionTrait;
    
        private $pdo;
        private $employeId;
        private $firstName;
        private $lastName;
        private $phone;
        private $email;
        private $road;
        private $roadComplement;
        private $zipCode;
        private $city;
    
        public function __construct($employeId, $pdo) {
            $this->employeId = $employeId;
            $this->pdo = $pdo;
        }
    
        // -------------------------------------------- Setters -------------------------------------------
    
        private function setPhone($phone) {
            $phone = trim($phone);
            $phone = stripslashes($phone);
            if (preg_match('/^(\+33|0)[1-9](\d{8})$/', $phone)) {
                $this->phone = $phone;
            } else {
                return $this->sendUserError("Téléphone invalide", "phone");
            }
        }
    
        private function setEmail($email) {
            $email = trim($email);
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->sendUserError("Adresse email non conforme", "email");
            }
        
            $this->email = $email;
        }
            
        private function setFirstName($firstName) {
            $firstName = trim($firstName);
            $firstName = stripslashes($firstName);
            if (strlen($firstName) > 50) {
                return $this->sendUserError('Le prénom ne doit pas dépasser 50 caractères.', 'firstName');
            }
            $this->firstName = htmlspecialchars($firstName, ENT_NOQUOTES, 'UTF-8');    
        }
    
        private function setLastName($lastName) {
            $lastName = trim($lastName);
            $lastName = stripslashes($lastName);
            if (strlen($lastName) > 50) {
                return $this->sendUserError('Le nom de famille ne doit pas dépasser 50 caractères.', 'lastName');
            }
            $this->lastName = htmlspecialchars($lastName, ENT_NOQUOTES, 'UTF-8');    
        }
    
        private function setRoad($road) {
            $road = trim($road);
            $road = stripslashes($road);
            if (strlen($road) > 100) {
                return $this->sendUserError('L’adresse ne doit pas dépasser 100 caractères.', 'road');
            }
            $this->road = htmlspecialchars($road, ENT_NOQUOTES, 'UTF-8');    
        }
    
        private function setComplement($roadComplement) {
            $roadComplement = trim($roadComplement);
            $roadComplement = stripslashes($roadComplement);
            if (strlen($roadComplement) > 100) {
                return $this->sendUserError('Le complément d’adresse ne doit pas dépasser 100 caractères.', 'roadComplement');
            }
            $this->roadComplement = htmlspecialchars($roadComplement, ENT_NOQUOTES, 'UTF-8');    
        }
    
        private function setZipCode($zipCode) {
            $zipCode = trim($zipCode);
            $zipCode = stripslashes($zipCode);
            if (!preg_match('/^\d{5}$/', $zipCode)) {
                return $this->sendUserError('Le code postal doit être composé de 5 chiffres.', 'zipCode');
            }
            $this->zipCode = htmlspecialchars($zipCode, ENT_NOQUOTES, 'UTF-8');    
        }
    
        private function setCity($city) {
            $city = trim($city);
            $city = stripslashes($city);
            if (strlen($city) > 50) {
                return $this->sendUserError('Le nom de la ville ne doit pas dépasser 50 caractères.', 'city');
            }
            $this->city = htmlspecialchars($city, ENT_NOQUOTES, 'UTF-8');
        }
    
        // -------------------------------------------- Getters -------------------------------------------
    
        private function getPdo() { return $this->pdo; }
        private function getEmployeId() { return $this->employeId; }
        private function getFirstName() { return htmlspecialchars_decode($this->firstName); }
        private function getLastName() { return htmlspecialchars_decode($this->lastName); }
        private function getPhone() { return htmlspecialchars($this->phone); }
        private function getEmail() { return htmlspecialchars_decode($this->email); }
        private function getRoad() { return htmlspecialchars_decode($this->road); }
        private function getRoadComplement() { return htmlspecialchars_decode($this->roadComplement); }
        private function getZipCode() { return htmlspecialchars_decode($this->zipCode); }
        private function getCity() { return htmlspecialchars_decode($this->city); }
    
     
        // -------------------------------------------- Fonction Principales -------------------------------------------
    
        public function getEmployeeInfo() {
            $query = "SELECT e.firstname, e.lastname, e.phone, e.road, e.roadcomplement, e.zipcode, e.city, l.email
                      FROM employees e
                      JOIN logins l ON e.idlogin = l.loginid
                      WHERE e.employeid = :employeId";
            
            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':employeId', $this->getEmployeId(), PDO::PARAM_INT);
            
            try {
                $statement->execute();
                $employeInfo = $statement->fetch(PDO::FETCH_ASSOC);
                
                if ($employeInfo) {
                    $employeInfo = array_map('trim', $employeInfo);// array_map pour créer un tableau propre avec des valeurs nettoyées (trim)
                    return [
                        'status' => 'success',
                        'data' => $employeInfo
                    ];
                }
                return $this->sendToDev('Utilisateur non trouvé');
            } catch (PDOException $e) {
                return $this->saveLog('Erreur lors de la récupération des données employé. Pour l\'employé: '.$this->getEmployeId().$e->getMessage(),'CRITICAL');
            }
        }
       
    
        public function updateEmployeInfo($firstName, $lastName, $phone, $email, $road, $complement, $zipCode, $city) {
            try {
                if ($error = $this->setFirstName($firstName)) return $error;
                if ($error = $this->setLastName($lastName)) return $error;
                if ($error = $this->setPhone($phone)) return $error;
                if ($error = $this->setEmail($email)) return $error;
                if ($error = $this->setRoad($road)) return $error;
                if ($error = $this->setComplement($complement)) return $error;
                if ($error = $this->setZipCode($zipCode)) return $error;
                if ($error = $this->setCity($city)) return $error;
    
                $queryUsers = "UPDATE employees 
                SET firstname = :firstName, 
                    lastname = :lastName, 
                    phone = :phone, 
                    road = :road, 
                    roadcomplement = :complement, 
                    zipcode = :zipCode, 
                    city = :city
                WHERE employeid = :employeId";
    
                $statement = $this->pdo->prepare($queryUsers);
                $statement->bindValue(':employeId', $this->getEmployeId(), PDO::PARAM_INT);
                $statement->bindValue(':firstName', $this->getFirstName());
                $statement->bindValue(':lastName', $this->getLastName());
                $statement->bindValue(':phone', $this->getPhone());
                $statement->bindValue(':road', $this->getRoad());
                $statement->bindValue(':complement', $this->getRoadComplement());
                $statement->bindValue(':zipCode', $this->getZipCode());
                $statement->bindValue(':city', $this->getCity());
    
                $statement->execute();
    
                //mise a jour de l'email
                $queryLogin = "UPDATE logins 
                            SET email = :email 
                            WHERE loginid = (SELECT idlogin FROM employees WHERE employeid = :employeId)";
    
                $statementLogin = $this->pdo->prepare($queryLogin);
                $statementLogin->bindValue(':employeId', $this->getEmployeId(), PDO::PARAM_INT);
                $statementLogin->bindValue(':email', $this->getEmail());
    
                $statementLogin->execute();
    
                return $this->sendUserSuccess('Vos données ont bien été mises à jour avec succès.', 'sendInfo');
    
            } catch (PDOException $e) {
                return $this->saveLog('Echec de la mise à jour de vos données employé.'.$this->getEmployeId().$e->getMessage(),'CRITICAL');
            }
        }
      
    }
    
    
    ?>
    