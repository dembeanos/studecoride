<?php
require_once __DIR__ . '/../Profile/shared/Messages.php';

session_start();

header('Content-Type: application/json');

//A l'inverse des autres routeurs ici je récupère le loginId
//c'est sur cette variable que nous traiterons les messages car unique à tous les utilisateurs
$loginId = $_SESSION['loginId'] ?? null;

$response = null;

$data = json_decode(file_get_contents('php://input'), true);

// Verification de la présence de donnée
if (!$data || !isset($data['action'])) {
    throw new Exception("Aucune action spécifiée.");
}

//Comme ce routeur ne traite que les messages, on crée directement la variable d'initialisation
$send = new Messages($pdo, $loginId);

// on rentre dans le switch en collectant l'action
if ($data) {
    $action = isset($data['action']) ? $data['action'] : null;
    switch ($action) {
        case 'searchUser':
            $send->setUserSearch($data['data'] ?? null);
            $receiverUser = $send->getUser(); // Saisie automatique du destinataire
            if (empty($receiverUser)) {
                $response = [
                    'type' => 'user_error',
                    'message' => 'dest-username',
                    'target' => $target
                ];
            } else {
                $response = $receiverUser;
            }
            break;
        case 'getMessages':
            $messagesData = $send->getMessages();
            $response =  $messagesData;
            break;
        case 'getPublicMessages':
            $messagesData = $send->getPublicMessages();
            $response =  $messagesData;
            break;
        case 'sendMessage':
            $send->setUsername($data['data']['username'] ?? null);
            $send->setObject($data['data']['object'] ?? null);
            $send->setMessageText($data['data']['messageText'] ?? null);
            $messageData = $send->sendMessage();
            $response = $messageData;
            break;
        case 'sendPublicMessage':
            $send->setObject($data['data']['object'] ?? null);
            $send->setLastName($data['data']['lastName'] ?? null);
            $send->setFirstName($data['data']['firstName'] ?? null);
            $send->setEmail($data['data']['email'] ?? null);
            $send->setMessageText($data['data']['messageText'] ?? null);
            $messageData = $send->sendPublicMessage();
            $response = $messageData;
            break;
    /*case 'deleteMessage':
            $send->setMessageId($data['data'] ?? null);
            $messageData = $send->deleteMessage();
            $response = $messageData;
            break;*/ 
        default:
            $response = [
                'type' => 'dev',
                'message' => 'Router: Action non reconnue'
            ];
            break;
    }
}
echo trim(json_encode($response));
