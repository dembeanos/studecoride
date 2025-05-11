<?php
require_once __DIR__ .'/../Profile/User/User.php';
require_once __DIR__ .'/../Profile/User/Trip.php';
require_once __DIR__ .'/../Profile/User/TripStatus.php';
require_once __DIR__ .'/../Profile/shared/Secure.php';
require_once __DIR__ .'/../Profile/User/Role.php';
require_once __DIR__ .'/../Profile/User/Reservation.php';
require_once __DIR__ .'/../Profile/User/Preference.php';
require_once __DIR__ .'/../Profile/shared/Photo.php';
require_once __DIR__ .'/../Profile/User/Opinion.php';
require_once __DIR__ .'/../Profile/User/Car.php';
require_once __DIR__ .'/../Profile/User/Credit.php';
require_once __DIR__ .'/../Profile/shared/Messages.php';

//Verification de session toutes ces méthodes sont soumise à authentification
session_start();

//Initialisation des variables
$userId = $_SESSION['userId'] ;
$data = null;
$response = null;

//Si l'action est updatePhoto on utilise $_FILES le traitement étant différent des autres méthode
//Je le traite alors ici
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['updatePhoto'])) {
    $photo = $_FILES['updatePhoto'];

    if ($photo['error'] === UPLOAD_ERR_OK) {
        $send = new Photo($pdo, $userId,null,null);
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
    //Si la Méthode n'est pas updatePhoto alors ou décode l'entrée json
    $data = json_decode(file_get_contents('php://input'), true);
}

//En rentre dans le switch
if ($data) {
    $action = isset($data['action']) ? $data['action'] : null;
    switch ($action) {
        case 'checkSession': // verification de connexion pour réservation d'offre
            $response = ['isLoggedIn' => isset($_SESSION['userId'])];
            break;
        case 'getUserInfo':
            $send = new User($userId, $pdo);
            $userData = $send->getUserInfo();
            $response = $userData;
            break;
        case 'getRole':
            $send = new Role($userId, $pdo);
            $roleData = $send->getUserRole();
            $response = $roleData;
            break;
        case 'getPhoto':
            $send = new Photo($pdo, $userId, null, null);
            $photoData = $send->getUserPhoto();
            $response =  $photoData;
            break;
        case 'getCar':
            $send = new Car($pdo, $userId);
            $carData = $send->getCar();
            $response = $carData;
            break;
        case 'getPreference':
            $send = new Preference($pdo, $userId);
            $prefData = $send->getPref();
            $response = $prefData;
            break;
        case 'getCredit':
            $send = new Credit($pdo, $userId);
            $creditData = $send->getUserCredit();
            $response = $creditData;
            break;
        case 'getTotalCredit':
            $send = new Credit($pdo, $userId);
            $creditData = $send->getUserTotalCredit();
            $response = $creditData;
            break;
        case 'getReservation':
            $send = new Reservation($pdo, $userId);
            $reservationData = $send->getReservation();
            $response = $reservationData;
            break;
        case 'getTrip':
            $send = new Trip($pdo, $userId);
            $tripData = $send->getTrip();
            $response = $tripData;
            break;
        case 'updateUserInfo':
            $send = new User($userId, $pdo);
            $userData = $send->updateUserInfo(
                $data['data']['firstName'] ?? null,
                $data['data']['lastName'] ?? null,
                $data['data']['phone'] ?? null,
                $data['data']['email'] ?? null,
                $data['data']['road'] ?? null,
                $data['data']['roadComplement'] ?? null,
                $data['data']['zipCode'] ?? null,
                $data['data']['city'] ?? null
            );
            $response = $userData;
            break;
        case 'updateRole':
            $send = new Role($userId, $pdo);
            $roleData = $send->updateRole($data['data']['role'] ?? null);
            $response = $roleData;
            break;
        case 'updatePassword':
            $send = new Secure($pdo, $userId);
            $send->setBackPassword($data['data']['backPassword'] ?? null);
            $send->setNewPassword($data['data']['newPassword'] ?? null);
            $send->setConfirmPassword($data['data']['confirmPassword'] ?? null);
            $passwordData = $send->updatePassword();
            $response = $passwordData;
            break;
        case 'addCar':
            $send = new Car($pdo, $userId);
            $carData = $send->addCar(
                $data['data']['marque'] ?? null,
                $data['data']['modele'] ?? null,
                $data['data']['immatriculation'] ?? null,
                $data['data']['firstImmatriculation'] ?? null,
                $data['data']['color'] ?? null,
                $data['data']['energy'] ?? null,
                $data['data']['places'] ?? null
            );
            $response =  $carData;
            break;
        case 'addPreference':
            $send = new Preference($pdo, $userId);
            $prefData = $send->updatePref(
                $data['data']['animal'] ?? null,
                $data['data']['smoke'] ?? null,
                $data['data']['other'] ?? null,
            );
            $response = $prefData;
            break;
        case 'addTrip':
            $send = new Trip($pdo, $userId);
            $tripData = $send->addTrip(
                $data['data']['cityDepart'] ?? null,
                $data['data']['arrivalCity'] ?? null,
                $data['data']['hourDepart'] ?? null,
                $data['data']['roadDepart'] ?? null,
                $data['data']['arrivalRoad'] ?? null,
                $data['data']['hourArrival'] ?? null,
                $data['data']['dateDepart'] ?? null,
                $data['data']['dateArrival'] ?? null,
                $data['data']['price'] ?? null,
                $data['data']['duration'] ?? null,
                $data['data']['car'] ?? null,
                $data['data']['preference'] ?? null,
                $data['data']['placeAvailable'] ?? null,
                $data['data']['inseeArrival'] ?? null,
                $data['data']['inseeDepart'] ?? null
            );
            $response = $tripData;
            break;
        case 'addOpinion':
            $send = new Opinion($userId, $pdo);
            $opinionData = $send->addOpinion(
                $data['data']['note'] ?? null,
                $data['data']['opinionText'] ?? null,
                $data['data']['srcOpinion'] ?? null
            );
            $response = $opinionData;
            break;
        case 'deleteCar':
            $send = new Car($pdo, $userId);
            $carData = $send->deleteCar(
                $data['data']['immatriculation'] ?? null
            );
            $response = $carData;
            break;
        case 'addReservation':
            $send = new Reservation($pdo, $userId);
            $reservationData = $send->addReservation($data['data']['offerid'] ?? null,
                                                        $data['data']['reservedPlaces'] ?? null
        );
            $response= $reservationData;
            break;
        case 'cancelReservation':
            $send = new Reservation($pdo, $userId);
            $send->setReservationId($data['data']['reservationId'] ?? null);
            $reservationData = $send->cancelReservation();
            $response = $reservationData;
            break;
        case 'validateReservation':
            $send = new Reservation($pdo, $userId);
            $send->setReservationId((int)$data['data']['reservationId'] ?? null);
            $reservationData = $send->validateReservation();
            $response = $reservationData;
            break;
        case 'cancelTrip':
            $send = new TripStatus ($pdo, $userId);
            $send->setTripId($data['data']['tripId'] ?? null);
            $tripData = $send->cancelTrip();
            $response = $tripData;
            break;
        case 'startTrip' :
            $send = new TripStatus ($pdo, $userId);
            $send->setTripId($data['data']['tripId'] ?? null);
            $tripData = $send->startTrip();
            $response = $tripData;
            break;
        case 'endTrip' :
            $send = new TripStatus ($pdo, $userId);
            $send->setTripId($data['data']['tripId'] ?? null);
            $tripData = $send->endTrip();
            $response = $tripData;
            break;
        default: //Si aucune action correspond retour d'un message au meme format que ExceptionTrait
            $response = [//de maniere a veiller a rentrer dans la condition de handleResponse coté js
                'type' => 'dev',
                'message' => 'Action non reconnue'
            ];
            break;
    }
}
echo trim(json_encode($response)); // Nettoyage et envoi propre de la réponse JSON au frontend
