<?php

declare(strict_types=1);

require_once __DIR__ .'/../Database/Database.php';
require_once __DIR__ .'/../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();


final class Subscribe {

    use ExceptionTrait;

    private $pdo;
    private $firstName;
    private $lastName;
    private $email;
    private $username;
    private $password;
    private $confirmPassword;
    private $phone;
    private $road;
    private $roadComplement;
    private $zipCode;
    private $city;
    private $credit;
    private $userType;
    private $dateEntrance;



    public function __construct($pdo)
    {

        $this->pdo = $pdo;
    }


    //--------------------------------------------------------SETTERS---------------------------------------


    private function setFirstName(string $firstName)
    {
        if (empty($firstName)) {
            return $this->sendUserError("Un prénom doit être renseigné", 'firstName');
        }
        $firstName = trim($firstName);
        if (!preg_match('/^(?![-])[A-Za-zÀ-ÿ-]{1,25}(?<![-])$/u', $firstName)) {
            return $this->sendUserError("Seulement les tirets et les lettres sont acceptés", 'firstName');
        }
        $this->firstName = htmlspecialchars($firstName);
    }

    private function setLastName(string $lastName)
    {
        if (empty($lastName)) {
            return $this->sendUserError("Un nom doit être renseigné", 'lastName');
        }
        $lastName = trim($lastName);

        if (!preg_match('/^(?![-])[A-Za-zÀ-ÿ-]{1,25}(?<![-])$/u', $lastName)) {
            return $this->sendUserError("Seulement les tirets et les lettres sont acceptés", 'lastName');
        }
        $this->lastName = htmlspecialchars($lastName);
    }

    private function setEmail(string $email)
    {
        if (empty($email)) {
            return $this->sendUserError("Un email doit être renseigné", 'email');
        }
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return  $this->sendUserError("Format d'email invalide", 'email');
        }
        $this->email = htmlspecialchars($email);
    }

    private function setUsername($username)
    {
        if (empty($username)) {
            return $this->sendUserError("Un pseudo est requis", 'username');
        }

        $username = trim($username);
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            return $this->sendPopup($username);
            $this->sendUserError("Seulement les lettres, chiffres, traits d'union et underscores sont autorisés", 'username');
        }

        $this->username = htmlspecialchars($username);
    }


    private function setPassword(string $password, string $confirmPassword)
    {
        $password = trim($password);
        $confirmPassword = trim($confirmPassword);

        if (strlen($password) < 8) {
            return  $this->sendUserError("Le mot de passe doit contenir au moins 8 caractères", 'password');
        }
        if ($password !== $confirmPassword) {
            return  $this->sendUserError("Les mots de passe ne sont pas identiques.", 'password');
        }
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    private function setPhone($phone)
    {
        $phone = trim($phone);
        $phone = stripslashes($phone);
        $cleanPhone = str_replace([' ', '-'], '', $phone);
        if ($cleanPhone && !preg_match('/^\d{10}$/', $cleanPhone)) {
            return  $this->sendUserError("Le numéro de téléphone n'est pas valide.", 'phone');
        }
        $this->phone = htmlspecialchars($cleanPhone);
    }


    private function setRoad(string $road)
    {
        if (empty($road)) {
            return  $this->sendUserError("Vous devez renseigner une rue", 'road');
        }

        $road = trim($road);
        if (!preg_match('/^[a-zA-Zà-ÿÀ-Ÿ0-9\s\-]{1,50}$/', $road)) {
            return  $this->sendUserError("La rue doit comporter uniquement des lettres, chiffres, espaces, et traits d'union, et ne pas dépasser 50 caractères", 'road');
        }
        $this->road = htmlspecialchars($road);
    }

    private function setRoadComplement(string $roadComplement)
    {
        if (empty($roadComplement)) {
            $this->roadComplement = null;
        } else {

            $roadComplement = trim($roadComplement);
            if (!preg_match('/^[a-zA-Zà-ÿÀ-Ÿ0-9\s\-\']{1,50}$/', $roadComplement)) {
                return $this->sendUserError("Le complément d'adresse doit comporter uniquement des lettres, chiffres, espaces, traits d'union, apostrophes, et ne pas dépasser 50 caractères", 'roadComplement');
            }
            $this->roadComplement = htmlspecialchars($roadComplement);
        }
    }

    private function setZipCode($zipCode)
    {
        $zipCode = trim($zipCode);
        if ($zipCode && !preg_match('/^\d{5,6}$/', $zipCode)) {
            return  $this->sendUserError("Code postal non valide", 'zipCode');
        } else {
            $this->zipCode = htmlspecialchars($zipCode);
        }
    }

    private function setCity(string $city)
    {
        if (empty($city)) {
            return  $this->sendUserError("Vous devez renseigner une ville", 'city');
        }

        $city = trim($city);
        if (!preg_match('/^[a-zA-Zà-ÿÀ-Ÿ0-9\s\-]+$/', $city)) {
            return  $this->sendUserError("La ville doit comporter uniquement des lettres, chiffres, espaces, et traits d'union", 'city');
        }
        $this->city = htmlspecialchars(strtoupper($city));
    }

    private function setCredit(int $credit)
    {
        if ($credit < 0 || $credit > 9999999999) {
            return $this->sendUserError("Le crédit doit être un nombre entre 0 et 10 chiffres.", 'credit');
        }
        $this->credit = $credit;
    }

    private function setUserType(string $userType)
    {
        $validTypes = ['user', 'admin', 'employe'];
        if (!in_array($userType, $validTypes)) {
            return $this->sendToDev("Le type d'utilisateur n'est pas valide");
        }
        $this->userType = htmlspecialchars(trim($userType));
    }

    private function setDateEntrance(string $dateEntrance)
    {
        $date = DateTime::createFromFormat('Y-m-d', $dateEntrance);
        if (!$date || $date->format('Y-m-d') !== $dateEntrance) {
            return $this->sendUserError("La date d'entrée n'est pas valide. Le format attendu est 'YYYY-MM-DD'.", 'dateEntrance');
        } else {
            $this->dateEntrance = htmlspecialchars(trim($dateEntrance));
        }
    }



    //--------------------------------------------------------GETTERS (avec decode)---------------------------------------

    private function getFirstName(){ return ucfirst(strtolower(htmlspecialchars_decode($this->firstName, ENT_QUOTES))); }
    private function getLastName(){ return ucfirst(strtolower(htmlspecialchars_decode($this->lastName, ENT_QUOTES))); }
    private function getEmail(){ return htmlspecialchars_decode($this->email, ENT_QUOTES); }
    private function getUsername(){ return htmlspecialchars_decode($this->username, ENT_QUOTES); }
    private function getPhone(){ return htmlspecialchars_decode($this->phone, ENT_QUOTES); }
    private function getRoad(){ return htmlspecialchars_decode($this->road, ENT_QUOTES); }
    private function getZipCode(){ return htmlspecialchars_decode($this->zipCode, ENT_QUOTES); }
    private function getCity(){ return htmlspecialchars_decode($this->city, ENT_QUOTES); }
    private function getCredit(){ return $this->credit; }
    private function getUserType(){ return htmlspecialchars_decode($this->userType, ENT_QUOTES); }
    private function getDateEntrance(){ return htmlspecialchars_decode($this->dateEntrance, ENT_QUOTES); }
    private function getRoadComplement()
    {
        if (is_null($this->roadComplement)) {
            $this->roadComplement = '';
        }
    
        return htmlspecialchars_decode($this->roadComplement);
    } 
    

    public function registerUser($firstName, $lastName, $email, $username, $password, $confirmPassword, 
                                $phone, $road, $roadComplement, $zipCode, $city, $credit, $userType){

        try {

            //Verification de toutes les valeurs
            if ($error = $this->setFirstName($firstName)) return $error;
            if ($error = $this->setLastName($lastName)) return $error;
            if ($error = $this->setEmail($email)) return $error;
            if ($error = $this->setUsername($username)) return $error;
            if ($error = $this->setPassword($password, $confirmPassword)) return $error;
            if ($error = $this->setPhone($phone)) return $error;
            if ($error = $this->setRoad($road)) return $error;
            if ($error = $this->setRoadComplement($roadComplement)) return $error;
            if ($error = $this->setZipCode($zipCode)) return $error;
            if ($error = $this->setCity($city)) return $error;
            if ($error = $this->setCredit($credit)) return $error;
            if ($error = $this->setUserType($userType)) return $error;

            //Verification de doublons
            $checkStatement = $this->pdo->prepare("SELECT COUNT(*) FROM logins WHERE email = :email");
            $checkStatement->bindValue(':email', $this->getEmail());
            $checkStatement->execute();
            $emailCount = $checkStatement->fetchColumn();

            if ($emailCount > 0) {
                return $this->sendPopup("L'email est déjà utilisé.");
            }

            //On copie dans logins
            $loginStatement = $this->pdo->prepare("INSERT INTO logins (email, password, username, usertype, status) 
                                            VALUES (:email, :password, :username, :userType, :status)");
            $loginStatement->bindValue(':email', $this->getEmail());
            $loginStatement->bindValue(':password', $this->password);
            $loginStatement->bindValue(':username', $this->getUsername());
            $loginStatement->bindValue(':userType', $this->getUserType());
            $loginStatement->bindValue(':status', 'ok');
            $loginStatement->execute();
            //on recupere la clé primaire de logins
            $loginId = $this->pdo->lastInsertId();

            //on détermine le type d'user traité qui permettra de définir sur que table on injecte le reste
            switch ($this->getUserType()) {
                case 'user':
                    $table = 'users';
                    break;
                case 'admin':
                    $table = 'admins';
                    break;
                case 'employe':
                    $table = 'employees';
                    break;
                default:
                    return $this->sendToDev('Type utilisateurs non reconnu');
            }

            //Je met une photo par defaut stocké en base 
            $query = "SELECT contenu FROM ecoride_files WHERE nom = 'photo_default.jpg'";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $photo = $stmt->fetchColumn();

            //Insertion du nouvel utilisateur dans la bonne table
            $usersStatement = $this->pdo->prepare("INSERT INTO $table (idlogin, firstname, lastname, phone, road, roadcomplement, zipcode, city, photo)
                                                VALUES (:idLogin, :firstName, :lastName, :phone, :road, :roadComplement, :zipCode, :city, :photo)");

            $usersStatement->bindValue(':idLogin', $loginId);
            $usersStatement->bindValue(':firstName', $this->getFirstName());
            $usersStatement->bindValue(':lastName', $this->getLastName());
            $usersStatement->bindValue(':phone', $this->getPhone());
            $usersStatement->bindValue(':road', $this->getRoad());
            $usersStatement->bindValue(':roadComplement', $this->getRoadComplement());
            $usersStatement->bindValue(':zipCode', $this->getZipCode());
            $usersStatement->bindValue(':city', $this->getCity());
            $usersStatement->bindValue(':photo', $photo, PDO::PARAM_LOB);
            $usersStatement->execute();

            
            //si  user alors on intègre quelques informations supplémentaires
            if ($table === 'users') {
                //on récupere la clé primaire de users qui deviendra le userId
                $userIdStatement = $this->pdo->prepare("SELECT userid FROM users WHERE idlogin = :loginId");

                $userIdStatement->bindValue(':loginId', $loginId);
                $userIdStatement->execute();

                $userId = $userIdStatement->fetchColumn();
                //on injecte les credit offert dans credit le total est calculer automatiquement dans la table users par fonction postgres
                $creditStatement = $this->pdo->prepare("INSERT INTO credits (iduser, label, credit, debit)
                                                VALUES (:userId, :label, :credit, :debit)");
                
                $creditStatement->bindValue(':userId', $userId);
                $creditStatement->bindValue(':label', 'Bienvenue Ecoride');
                $creditStatement->bindValue(':credit', $this->getCredit());
                $creditStatement->bindValue(':debit', null, PDO::PARAM_NULL);
                $creditStatement->execute();
                //on definit des valeurs par defaut pour éviter le undefined corréspondant a l'user
                $preferenceStatement = $this->pdo->prepare("INSERT INTO preferences (iduser, animal, smoke, other)
                                                VALUES (:userId, :animal, :smoke, :other)");
                
                $preferenceStatement->bindValue(':userId', $userId);
                $preferenceStatement->bindValue(':animal', false);
                $preferenceStatement->bindValue(':smoke', false);
                $preferenceStatement->bindValue(':other', null);
                $preferenceStatement->execute();


            }

            $this->clean();
            $this->success();


            return $this->sendPopup('Votre inscriptions à bien été enregistré, vous pouvez vous connecter');
        } catch (PDOException $e) {
            return $this->saveLog("Erreur PDO : " . $e->getMessage(),'FATAL');
        }
    }


    private function success()
    {
        return $this->sendToDev('Inscritpion réussi');
    }
    private function clean()
    {
        unset($this->firstName);
        unset($this->lastName);
        unset($this->email);
        unset($this->username);
        unset($this->password);
        unset($this->confirmPassword);
        unset($this->telephone);
        unset($this->road);
        unset($this->roadComplement);
        unset($this->zipCode);
        unset($this->city);
        unset($this->credit);
        unset($this->photo);
        unset($this->loginId);
        unset($this->dateEntrance);
    }
}
