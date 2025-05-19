<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['action']) && $input['action'] === 'checkSession') {
        $isLoggedIn = isUserConnected();
        header('Content-Type: application/json');
        echo json_encode(['isLoggedIn' => $isLoggedIn]);
        exit;
    }
}
// Fonctions verification de connexion
function isUserConnected(): bool {
    
    return isset($_SESSION['userId']) && isset($_SESSION['role']);
}

function isAdminConnected(): bool {
    return isset($_SESSION['adminId']);
}

function isEmployeConnected(): bool {
    return isset($_SESSION['employeId']);
}

// Fonctions de protection pour restreindre une page
function checkAuthUser(): void {
    if (!isUserConnected()) {
        header("Location: ../connexion/connexion.php");
        exit;
    }
}

function checkAuthAdmin(): void {
    if (!isAdminConnected()) {
        header("Location: ../connexion/connexion.php");
        exit;
    }
}

function checkAuthEmploye(): void {
    if (!isEmployeConnected()) {
        header("Location: ../connexion/connexion.php");
        exit;
    }
}



?>
