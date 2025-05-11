<?php

declare(strict_types=1);

require_once __DIR__ . '/../../Database/Database.php';
require_once __DIR__ . '/../../Database/DatabaseNoSql.php';
require_once __DIR__ . '/../../Traits/Exception.php';


$db = new Database();
$pdo = $db->getPdo();

final class Messages
{

    use ExceptionTrait;


    private $pdo;
    private $loginId;
    private $mongo;
    private $date;
    private $username;
    private $object;
    private $firstName;
    private $lastName;
    private $email;
    private $userSearch;
    private $messageText;
    private $messageId;


    public function __construct($pdo, $loginId)
    {
        $this->pdo = $pdo;
        $this->loginId = $loginId;
    }

    //Mongo Connexion 
    private function connectMongo()
    {
        if (!$this->mongo) {
            $db = new DatabaseNoSql();
            $this->mongo = $db->getDatabase();
        }
    }

    //--------------------------------------------------------Setters--------------------------------------------

    public function setUserSearch($userSearch)
    {
        // Vérifie si la chaîne ne contient que des lettres majuscules et minuscules ok
        if (preg_match('/^[a-zA-Z]+$/', $userSearch)) {
            $this->userSearch = htmlspecialchars(trim($userSearch));
        } else {
            return $this->sendUserError("La recherche doit contenir uniquement des lettres.", 'dest-username');
        }
    }

    public function setUsername($username)
    {
        // Vérifie si le nom d'utilisateur contient uniquement des lettres, chiffres, underscores, tirets et apostrophes
        if (preg_match("/^[a-zA-Z0-9_\-\']+$/", $username)) {
            $this->username = htmlspecialchars(trim($username));
        } else {
            return $this->sendUserError("Nom d'utilisateur invalide.", 'dest-username');
        }
    }

    public function setMessageText($messageText)
    {
        $this->messageText = htmlspecialchars(trim($messageText)); // Échapper les caractères spéciaux pour éviter des failles XSS
    }

    public function setObject($object)
    {
        // Vérifie la longueur du texte
        if (strlen($object) < 1 || strlen($object) > 50) {
            return $this->sendToDev("L'objet manquant ou trop long.");
        }

        // Vérifie que l'objet ne contient que des lettres, chiffres, apostrophes et parenthèses
        if (preg_match("/^[a-zA-Z0-9'\(\)]+$/", $object)) {

            $this->object = htmlspecialchars(trim($object));
        } else {
            return $this->sendToDev("L'objet contient des caractères invalides.");
        }
    }

    public function setFirstName($firstName)
    {
        // Vérifie que le prénom ne contient que des lettres et des espaces
        if (preg_match("/^[a-zA-Z\s'\-]+$/", $firstName)) {
            $this->firstName = htmlspecialchars(trim($firstName));
        } else {
            return $this->sendToDev("Le prénom contient des caractères invalides.");
        }
    }

    public function setLastName($lastName)
    {
        // Même logique que pour le prénom
        if (preg_match("/^[a-zA-Z\s'\-]+$/", $lastName)) {
            $this->lastName = htmlspecialchars(trim($lastName));
        } else {
            return $this->sendToDev("Le nom contient des caractères invalides.");
        }
    }

    public function setEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->sendToDev("Email invalide.");
        }
        //Netoyage de l'email en supprimant les caractères invalides
        $this->email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    public function setMessageId($messageId)
    {
        // Si l'ID est censé être un entier, vérifie-le
        if (is_numeric($messageId)) {
            $this->messageId = (int)$messageId;
        } else {
            return $this->sendToDev("ID du message invalide.");
        }
    }


    //--------------------------------------------------------Getters-------------------------------

    private function getObject(){ return $this->object;}
    private function getFirstName(){ return $this->firstName;}
    private function getLastName(){ return $this->lastName;}
    private function getEmail(){ return $this->email;}
    private function getUsername(){ return $this->username;}
    private function getUserSearch(){ return $this->userSearch;}
    private function getMessageText(){ return $this->messageText;}
    private function getMessageId(){ return $this->messageId;}


    //Check valide user
    private function checkUser()
    {
        $query = 'SELECT loginid FROM logins WHERE loginid = :loginId';

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':loginId', $this->loginId);
        $statement->execute();

        if ($statement->fetchColumn() > 0) {
            return true;
        } else {
            return false;
        }
    }


    public function getUser()
    {
        $query = 'SELECT 
            l.username,
            COALESCE(u.photo, ad.photo, e.photo) AS photo
            FROM logins l
            LEFT JOIN users u ON l.loginid = u.idlogin
            LEFT JOIN admins ad ON l.loginid = ad.idlogin
            LEFT JOIN employees e ON l.loginid = e.idlogin
            WHERE LOWER (l.username) LIKE LOWER (:userSearch)
            ORDER BY l.username ASC
            LIMIT 10';

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':userSearch', $this->getUserSearch() . '%', PDO::PARAM_STR);
        $statement->execute();

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($results)) {
            return $this->sendUserError('Aucun Pseudo ne correspond à votre recherche', 'dest-username');
        }

        foreach ($results as &$user) {
            if ($user['photo']) {
                $user['photo'] = base64_encode(stream_get_contents($user['photo']));
            }
        }

        return $results;
    }



    public function sendMessage()
    {

        if (!$this->checkUser()) {
            return $this->sendToDev('Utilisateur invalide');
        }

        //Recherche du destinataire de l'email par username de manière a récupérer son id Login
        $query = 'SELECT loginid FROM logins WHERE username = :username';

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':username', $this->getUsername(), PDO::PARAM_STR);
        $statement->execute();

        $receiverId = $statement->fetchColumn();

        //changement de base initialisation de la base Mongo
        $this->connectMongo();

        //assemblement du message
        $message = [
            'senderId' => $this->loginId,
            'receiverId' => $receiverId,
            'object' => $this->getObject(),
            'messageText' => $this->getMessageText(),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        try {
            //Envoi du message a Mongo
            $this->mongo->messages->insertOne($message);
            return $this->sendPopup('Message remis avec succès');
        } catch (Exception $e) {
            return $this->saveLog("Erreur MongoDB: " . $e->getMessage(), 'CRITICAL');
        }
    }



    public function getMessages()
    {
        //Verification de l'existence de l'user
        if (!$this->checkUser()) {
            return $this->sendToDev('Utilisateur invalide');
        }

        //Initialisation de la connexion Mongo
        $this->connectMongo();

        //requete de recherche de message à Mongo
        $messagesListe = $this->mongo->messages->find(
            ['receiverId' => $this->loginId],
            ['sort' => ['timestamp' => -1]]
        );

        if (empty($messagesListe)) {
            return $this->sendPopup('Pas de message');
        }

        //initialisation d'une variable resultat
        $resultMessages = [];

        //Boucle pour chaque message traitement avant envoi 
        foreach ($messagesListe as $message) { //Utilisation d'un alias messageListe devient message
            $senderId = $message['senderId']; //association de l'expediteur des messages a une variable

            // Si l'expéditeur n'est pas de type numérique alors il doit être de type lettre 
            // On considère ici les messages de type System
            if (!is_numeric($senderId)) {
                $message['senderUsername'] = $senderId; //dans ce cas on associe senderUsername comme etant le id expediteur
                $message['senderPhoto'] = null; // on met photo a null car inexistante
                $resultMessages[] = $message; // et on ajoute à message
                continue; //on indique ici une suite
            }

            // On exécute requête pour récupérer la photo de l'utilisateur dans postgres 
            $query = '
            SELECT 
                l.username, 
                COALESCE(u.photo, ad.photo, e.photo) AS photo
            FROM logins l
            LEFT JOIN users u ON l.loginid = u.idlogin
            LEFT JOIN admins ad ON l.loginid = ad.idlogin
            LEFT JOIN employees e ON l.loginid = e.idlogin
            WHERE l.loginid = :senderId
        ';

            $statement = $this->pdo->prepare($query);
            $statement->execute([':senderId' => $senderId]);
            $sender = $statement->fetch(PDO::FETCH_ASSOC);

            // Vérification et traitement de la photo
            if ($sender) {
                $message['senderUsername'] = $sender['username'];
                // Si une photo est présente
                if ($sender['photo']) {
                    $imageData = stream_get_contents($sender['photo']);
                    if ($imageData !== false) {
                        $message['senderPhoto'] = 'data:image/jpeg;base64,' . base64_encode($imageData);  // Conversion de l'image en base64
                    } else {
                        $message['senderPhoto'] = null;  // Pas de photo d'erreur d'encodage
                    }
                } else {
                    $message['senderPhoto'] = null;  // Pas de photo si aucune photo n'est présente
                }
            } else {
                $message['senderUsername'] = 'Inconnu';
                $message['senderPhoto'] = null;  // Pas de photo si l'utilisateur n'existe pas
            }

            $resultMessages[] = $message;// ajout des resultats avec photo à la réponse
        }

        if (empty($resultMessages)) {
            return $this->sendPopup("Vous n'avez pas de message.");
        } else {
            return $resultMessages;
        }
    }



    //------------------------------------------------Public Messages------------------------------------------


    //Methode appelé via contactez nous
    public function sendPublicMessage()
    {

        $this->connectMongo();

        $message = [
            'senderId' => 'Public',
            'receiverId' => 0,
            'object' => $this->getObject(),
            'lastName' => $this->getLastName(),
            'firstName' => $this->getFirstName(),
            'email' => $this->getEmail(),
            'messageText' => $this->getMessageText(),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        try {
            $this->mongo->messages->insertOne($message);
            return $this->sendPopup('Message remis avec succès');
        } catch (Exception $e) {
            return $this->saveLog("Erreur MongoDB: " . $e->getMessage(), 'CRITICAL');
        }
    }


    //Methode utilisé par les admins et employees

    public function getPublicMessages()
    {

        //Verification de présence d'id
        if (!$this->loginId) {
            return $this->sendToDev('Erreur : Identifiant de l\'utilisateur non valide.');
        }

        //Verification du role utilisateur dans logins 
        $query = 'SELECT usertype FROM logins WHERE loginid = :id';

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $this->loginId, PDO::PARAM_STR);
        $statement->execute();

        $verifRole = $statement->fetchColumn();

        if ($verifRole === 'admin' || $verifRole === 'employe') {

            $this->connectMongo();

            // Conversion du curseur MongoDB en tableau
            $messagesListe = $this->mongo->messages->find(
                ['receiverId' => 0],
                ['sort' => ['timestamp' => -1]]
            )->toArray();  // ->toArray() pour obtenir un tableau

            if (empty($messagesListe)) {
                return $this->sendPopup('Pas de Message de visiteur');
            } else {
                return $messagesListe;
            }
        } else {
            return $this->sendPopup('Accès refusé');
        }
    }
}
