<?php // Ce fichier suit la même logique que userRoute, voir commentaires dans userRoute.php
require_once __DIR__ . '/../Authentification/auth.php';
require_once __DIR__ .'/../Profile/Admin/Admin.php';
require_once __DIR__ .'/../Profile/Admin/UserManager.php';
require_once __DIR__ .'/../Profile/Admin/EmployeManager.php';
require_once __DIR__ .'/../Profile/Admin/OfferTrends.php';
require_once __DIR__ .'/../Profile/shared/Photo.php';
require_once __DIR__ .'/../Profile/shared/Secure.php';
require_once __DIR__ .'/../Profile/Admin/Profit.php';
require_once __DIR__ .'/../Profile/Admin/Log.php';
require_once __DIR__ .'/../Profile/shared/Messages.php';


$adminId = $_SESSION['adminId'];
if (empty($adminId)){
    exit;
}

$data = null;
$response = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['updatePhoto'])) {
    $photo = $_FILES['updatePhoto'];

    if ($photo['error'] === UPLOAD_ERR_OK) {
        $send = new Photo($pdo,null, $adminId, null);
        try {
            $response = $send->updatePhoto($photo);
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
        case 'getAdminInfo':
            $send = new Admin($adminId, $pdo);
            $adminData = $send->getAdminInfo();
            $response = $adminData;
            break;
        case 'getPhoto':
            $send = new Photo($pdo,null ,$adminId,null);
            $photoData = $send->getUserPhoto();
            $response =  $photoData;
            break;
        case 'getUsers':
            $send = new UserManager($pdo, $adminId);
            $usersData = $send->getUsers();
            $response = $usersData;
            break;
        case 'getEmployees':
            $send = new EmployeManager($pdo, $adminId);
            $employeesData = $send->getEmployees();
            $response = $employeesData;
            break;
        case 'getTrends':
            $send = new OfferTrends($pdo, $adminId);
            $trendsData = $send->getTrends();
            $response = $trendsData;
            break;
        case 'getProfit':
            $send = new Profit($pdo, $adminId);
            $profitData = $send->getProfit();
            $response = $profitData;
            break;
        case 'getLogs':
            $send = new Log($pdo, $adminId);
            $logsData = $send->getLogs();
            $response = $logsData;
            break;
        case 'updatePassword':
            $send = new Secure($pdo, null, null, $adminId);
            $send->setBackPassword($data['data']['backPassword'] ?? null);
            $send->setNewPassword($data['data']['newPassword'] ?? null);
            $send->setConfirmPassword($data['data']['confirmPassword'] ?? null);
            $passwordData = $send->updatePassword();
            $response = $passwordData;
            break;
        case 'updateAdminInfo':
            $send = new Admin($adminId, $pdo);
            $adminData = $send->updateAdminInfo(
                $data['data']['firstName'] ?? null,
                $data['data']['lastName'] ?? null,
                $data['data']['phone'] ?? null,
                $data['data']['email'] ?? null,
                $data['data']['road'] ?? null,
                $data['data']['roadComplement'] ?? null,
                $data['data']['zipCode'] ?? null,
                $data['data']['city'] ?? null
            );
            $response = $adminData;
            break;
        case 'banUser':
            $send = new UserManager($pdo, $adminId);
            $send->setBanId($data['data']['banId']);
            $banData = $send->banUser();
            $response = $banData;
            break;
        case 'banEmploye':
                $send = new EmployeManager($pdo, $adminId);
                $send->setBanId($data['data']['banId']);
                $banData = $send->banUser();
                $response = $banData;
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
