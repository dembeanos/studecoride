<?php
// ce fichier a été rédigé de manière à contrôler l'accès à certaines pages
//j'ai remis la logique de check de connexion dans des fonctions de manière 
//à éviter la répétition

function checkAuthUser() {
    session_start();
    
    if (!isset($_SESSION['userId'])) {
        header("Location: ../connexion/connexion.php");
        exit;
    }
}

function checkAuthAdmin() {
    session_start();
    
    if (!isset($_SESSION['adminId'])) {
        header("Location: ../connexion/connexion.php");
        exit;
    }
}
function checkAuthEmploye() {
    session_start();
    
    if (!isset($_SESSION['employeId'])) {
        header("Location: ../connexion/connexion.php");
        exit;
    }
}

?>
