<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: /Ecoride/index.php");
    exit;
}
?>
<link rel="stylesheet" href="/Ecoride/assets/css/header.css">

<nav class="menuBar">
    <div class="menuBar-container">
        <a href="/Ecoride/Index.php" title="Accueil">
            <img class="logo" alt="Logo Ecoride" src="/Ecoride/assets/images/menu_icon/logo.png">
        </a>
    </div>

    <!-- Hamburger button -->
    <div class="hamburger" id="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <ul class="menu-list" id="menuList">
        <li class="menu">
            <img class="icon" src="/Ecoride/assets/images/menu_icon/home.svg">
            <a href="/Ecoride/Index.php" title="Accueil">Accueil</a>
        </li>

        <li class="menu">
            <img class="icon" src="/Ecoride/assets/images/menu_icon/logoCovoiturage.svg">
            <a href="/Ecoride/pages/covoiturage/covoiturage.php" title="Covoiturage">Covoiturage</a>
        </li>

        <?php if (isset($_SESSION['adminId'])): ?>
            <li class="menu">
                <img class="icon" src="/Ecoride/assets/images/menu_icon/message.svg">
                <a href="/Ecoride/pages/message/message.php" title="Messagerie">Messagerie</a>
            </li>
            <li class="menu">
                <img class="icon" src="/Ecoride/assets/images/menu_icon/connectYou.svg">
                <a href="/Ecoride/pages/Admin/Admin.php" title="Profil Admin">Mon Profil</a>
            </li>
        <?php elseif (isset($_SESSION['employeId'])): ?>
            <li class="menu">
                <img class="icon" src="/Ecoride/assets/images/menu_icon/message.svg">
                <a href="/Ecoride/pages/message/message.php" title="Messagerie">Messagerie</a>
            </li>
            <li class="menu">
                <img class="icon" src="/Ecoride/assets/images/menu_icon/connectYou.svg">
                <a href="/Ecoride/pages/employe/employe.php" title="Profil Employé">Mon Profil</a>
            </li>
        <?php elseif (isset($_SESSION['userId'])): ?>
            <li class="menu">
                <img class="icon" src="/Ecoride/assets/images/menu_icon/message.svg">
                <a href="/Ecoride/pages/message/message.php" title="Messagerie">Messagerie</a>
            </li>
            <li class="menu">
                <img class="icon" src="/Ecoride/assets/images/menu_icon/connectYou.svg">
                <a href="/Ecoride/pages/user/profil.php" title="Profil Utilisateur">Mon Profil</a>
            </li>
        <?php else: ?>
            <li class="menu">
                <img class="icon" id="connectIcon" src="/Ecoride/assets/images/menu_icon/connectYou.svg">
                <a href="/Ecoride/pages/connexion/connexion.php" title="Se connecter">Vous Connecter</a>
            </li>
        <?php endif; ?>

        <?php if (isset($_SESSION['userId']) || isset($_SESSION['employeId']) || isset($_SESSION['adminId'])): ?>
            <li class="menu"><img class="icon" src="/Ecoride/assets/images/menu_icon/logout.svg">
                <form method="post" style="display:inline;">
                    <button type="submit" name="logout" title="Déconnexion">Déconnexion</button>
                </form>
            </li>
        <?php endif; ?>

        <li id="contact" class="menu"><img class="icon" src="/Ecoride/assets/images/menu_icon/contactUs.svg">
            <a href="/Ecoride/pages/contact/contact.php" title="Contactez-nous">Contactez-Nous</a>
        </li>
    </ul>
</nav>

<script>
    const hamburger = document.querySelector(".hamburger");
    const menuList = document.querySelector(".menu-list");

    hamburger.addEventListener("click", () => {
        menuList.classList.toggle("show");
    });
</script>

