<?php // Ce fichier suit la même logique que userRoute, voir commentaires dans userRoute.php
session_start();
require_once __DIR__ .'/../Profile/Employee/Employee.php';
require_once __DIR__ .'/../Profile/Employee/OpinionManager.php';
require_once __DIR__ .'/../Profile/shared/Photo.php';
require_once __DIR__ .'/../Profile/shared/Secure.php';
require_once __DIR__ .'/../Profile/shared/Messages.php';


$employeId = $_SESSION['employeId'];
$data = null;
$response = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['updatePhoto'])) {
    $photo = $_FILES['updatePhoto'];

    if ($photo['error'] === UPLOAD_ERR_OK) {
        $send = new Photo($pdo, null, null, $employeId);
        try {
            $send->updatePhoto($photo);
            $response = ['status' => 'success', 'message' => 'Photo mise à jour avec succès'];
        } catch (Exception $e) {
            $response = ['status' => 'error', 'message' => $e->getMessage()];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Erreur lors du téléchargement de la photo'];
    }
} else {
    $data = json_decode(file_get_contents('php://input'), true);
}



if ($data) {
    $action = isset($data['action']) ? $data['action'] : null;
    switch ($action) {
        case 'getEmployeeInfo':
            $send = new Employee($employeId, $pdo);
            $adminData = $send->getEmployeeInfo();
            $response = $adminData;
            break;
        case 'getPhoto':
            $send = new Photo($pdo, null, null, $employeId);
            $photoData = $send->getUserPhoto();
            $response =  $photoData;
            break;
        case 'getOpinion':
            $send = new OpinionManager($pdo, $employeId);
            $opinionData = $send->getOpinion();
            $response = $opinionData;
            break;
        case 'getMessages':
            $send = new Messages($pdo, $employeId);
            $messagesData = $send->getMessages();
            $response = $messagesData;
            break;
        case 'updatePassword':
            $send = new Secure($pdo, null, null, $employeId);
            $send->setBackPassword($data['data']['backPassword'] ?? null);
            $send->setNewPassword($data['data']['newPassword'] ?? null);
            $send->setConfirmPassword($data['data']['confirmPassword'] ?? null);
            $passwordData = $send->updatePassword();
            $response = $passwordData;
            break;
        case 'updateEmployeInfo':
            $send = new Employee($employeId, $pdo);
            $employeData = $send->updateEmployeInfo(
                $data['data']['firstName'] ?? null,
                $data['data']['lastName'] ?? null,
                $data['data']['phone'] ?? null,
                $data['data']['email'] ?? null,
                $data['data']['road'] ?? null,
                $data['data']['complement'] ?? null,
                $data['data']['zipCode'] ?? null,
                $data['data']['city'] ?? null
            );
            $response = $employeData;
            break;
        case 'validateOpinion':
            $send = new OpinionManager($pdo, $employeId);
            $send->setOpinionId($data['data']['opinionId'] ?? null);
            $opinionData = $send->validateOpinion();
            $response = $opinionData;
            break;
        case 'rejectedOpinion':
            $send = new OpinionManager($pdo, $employeId);
            $send->setOpinionId($data['data']['opinionId'] ?? null);
            $opinionData = $send->rejectedOpinion();
            $response = $opinionData;
            break;
        case 'getTripDetail':
            $send = new OpinionManager($pdo, $employeId);
            $send->setOpinionId($data['data']['opinionId'] ?? null);
            $opinionData = $send->getTripDetail();
            $response = $opinionData;
            break;
        case 'sendMessage':
            $send = new Messages($pdo, $employeId);
            $send->setUsername($data['data']['username'] ?? null);
            $send->setMessageText($data['data']['messageText'] ?? null);
            $messageData = $send->sendMessage();
            $response = $messageData;
            break;
        default:
            $response = [
                'type' => 'dev',
                'message' => 'Action non reconnue'
            ];
            break;
    }
}
echo trim(json_encode($response));
