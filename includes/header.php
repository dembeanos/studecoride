<link rel="stylesheet" href="/assets/css/header.css">

<nav class="menuBar">
    <div class="menuBar-container">
        <a href="/index.php" title="Accueil">
            <img class="logo" alt="Logo Ecoride" src="/assets/images/menu_icon/logo.png">
        </a>
    </div>

    <div class="hamburger" id="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <ul class="menu-list" id="menuList">
        <?php if (basename($_SERVER['PHP_SELF']) !== 'index.php'): ?>
            <li class="menu">
                <img class="icon" src="/assets/images/menu_icon/home.svg">
                <a href="/index.php" title="Accueil">Accueil</a>
            </li>
        <?php endif; ?>

        <li class="menu">
            <img class="icon" src="/assets/images/menu_icon/logoCovoiturage.svg">
            <a href="/pages/covoiturage/covoiturage.php" title="Covoiturage">Covoiturage</a>
        </li>

        <?php if (isUserConnected() || isAdminConnected() || isEmployeConnected()): ?>
            <li class="menu">
                <img class="icon" src="/assets/images/menu_icon/message.svg">
                <a href="/pages/message/message.php" title="Messagerie">Messagerie</a>
            </li>

            <?php if (isAdminConnected()): ?>
                <li class="menu">
                    <img class="icon" src="/assets/images/menu_icon/connectYou.svg">
                    <a href="/pages/admin/admin.php" title="Profil Admin"><?php echo $_SESSION['firstName'] ?></a><!--ICI-->
                </li>
            <?php elseif (isEmployeConnected()): ?>
                <li class="menu">
                    <img class="icon" src="/assets/images/menu_icon/connectYou.svg">
                    <a href="/pages/employe/employe.php" title="Profil Employé"><?php echo $_SESSION['firstName'] ?></a><!--ICI-->
                </li>
            <?php elseif (isUserConnected()): ?>
                <li class="menu">
                    <img class="icon" src="/assets/images/menu_icon/connectYou.svg">
                    <a href="/pages/user/profil.php" title="Profil Utilisateur"><?php echo $_SESSION['firstName'] ?></a> <!--ICI-->
                </li>
            <?php endif; ?>

            <li class="menu">
                <img class="icon" src="/assets/images/menu_icon/logout.svg">
                <form action="/src/Authentification/logout.php" method="POST" style="display:inline;">
                    <button type="submit" name="logout" title="Déconnexion">Déconnexion</button>
                </form>
            </li>

        <?php else: ?>
            <li class="menu">
                <img class="icon" id="connectIcon" src="/assets/images/menu_icon/connectYou.svg">
                <a href="/pages/connexion/connexion.php" title="Se connecter">Connection</a>
            </li>
        <?php endif; ?>

        <li id="contact" class="menu">
            <img class="icon" src="/assets/images/menu_icon/contactUs.svg">
            <a href="/pages/contact/contact.php" title="Contactez-nous">Contact</a>
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