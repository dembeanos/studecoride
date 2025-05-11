<?php
require $_SERVER['DOCUMENT_ROOT'] . '/Ecoride/src/Authentification/auth.php'; 

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie</title>
    <link rel="stylesheet" href="/Ecoride/assets/css/message.css">
    <link rel="stylesheet" href="/Ecoride/assets/css/popup.css">
    <script src='../../assets/js/ui/event/popup.js'></script>
    <script type='module' src='../../assets/js/data-management/message/message-management.js'></script>
    <script src='../../assets/js/ui/message/users-search.js'></script>
</head>
<header>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/Ecoride/includes/header.php'; ?>
</header>
<body>
    <div class="container">

        <h1>Bienvenue dans votre Messagerie</h1>

        <!-- Onglets -->
        <div class="tabs">
            <button class="tab-button active" data-tab="receive-message">Boite de reception</button>
            <button class="tab-button active" data-tab="send-message">Envoyer un message</button>
            <?php if (isset($_SESSION['employeId']) || isset($_SESSION['adminId'])): ?>
                <button class="tab-button" data-tab="messagerie-visiteurs">Messages visiteurs</button>
            <?php endif; ?>
        </div>

        <div class="standard-message">
    <!-- Liste des messages -->
    <div class="sidebar">
        <h3>Messages reçus</h3>
        <ul class="ourMessage-list" id="ourMessage-list">
            <!-- Li générés dynamiquement -->
            <!-- Exemple statique, sera remplacé par JS -->
        </ul>
    </div>

    <!-- Contenu + formulaire de réponse -->
    <div class="message-details">
        <form id="message-form">
            <!-- Input Username avec avatar -->
            <div style="display: flex ; align-items: center;">
                <img id="avatar-img" src="" alt="Avatar" style="display : none; width: 50px; margin-right: 10px;">
                <input type="text" id="username" placeholder="Nom d'utilisateur" autocomplete="off" readonly>
            </div>

            <!-- Input Objet -->
            <input type="text" id="object" placeholder="Objet" readonly>

            <!-- message reçu -->
            <textarea id="message-received" placeholder="Votre réponse..." rows="5" readonly></textarea>

            <button id ='deleteReceived'type="button">Supprimer</button>
        </form>
    </div>
</div>


        <div class="send-message">

        <style>
  
  #userSuggestionBox {
    position: absolute;
    top: 30%; /* Décale la boîte un peu sous l'input */
    left: 12%; /* Aligne à gauche par rapport à l'input */
    border: 1px solid #ccc;
    background-color: white;
    max-height: 200px;
    overflow-y: auto;
    width: 30%; /* On peut laisser 100% ou ajuster en fonction du conteneur parent */
    z-index: 10;
    display: none;  /* Par défaut, la boîte est cachée */
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Ajout d'ombre pour séparer visuellement */
}

#userSuggestionBox div {
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid #ccc;
    display: flex;
    align-items: center;
}

#userSuggestionBox div img {
    width: 30px;  /* Taille de l'image */
    height: 30px; /* Taille de l'image */
    border-radius: 50%; /* Forme circulaire */
    margin-right: 10px; /* Espace entre l'image et le texte */
}

#userSuggestionBox div:hover {
    background-color: #f0f0f0; /* Changer la couleur au survol */
}


</style>
            <div class="message-details">
                <form id="message-form">
                    <!-- Input Username (avec avatar) -->
                    <input type="text" id="dest-username" placeholder="Nom d'utilisateur">
                    <div id="userSuggestionBox" style="border: 1px solid #ccc; display: none;"></div>
                    <!-- Input Objet -->
                    <input type="text" id="send-objet" placeholder="Objet">

                    <textarea id="send-message" placeholder="Votre message..." rows="5"></textarea>

                    <button id="send-button" type="button">Envoyer</button>
                </form>
            </div>
        </div>

        <!-- Onglet visiteurs (employé ou admin uniquement) -->
        <?php if (isset($_SESSION['employeId']) || isset($_SESSION['adminId'])): ?>
            <div class="public-message">
                <!-- Liste des messages visiteurs -->
                <div class="sidebar">
                    <h3>Messages visiteurs</h3>
                    <ul class="publicMessage-list" id="message-list-visiteurs">
                        <li class="message-item" data-username="JeanV" data-objet="Demande d'information" data-message="Je voudrais en savoir plus sur vos services.">
                            JeanV - Demande d'information
                        </li>
                        <li class="message-item" data-username="AliceV" data-objet="Problème rencontré" data-message="Le service ne fonctionne pas comme prévu.">
                            AliceV - Problème rencontré
                        </li>
                    </ul>
                </div>

                <!-- Contenu + formulaire de réponse -->
                <div class="message-details">
                    <form id="message-form-visiteurs">
                        <!-- Champs supplémentaires -->
                        <input type="text" id="nom" placeholder="Nom" readonly>
                        <input type="text" id="prenom" placeholder="Prénom" readonly>
                        <input type="email" id="email" placeholder="Email" readonly>

                        <!-- Input Objet -->
                        <input type="text" id="objet-visiteur" placeholder="Objet" readonly>

                        <!-- message -->
                        <textarea id="message-recu-visiteur" placeholder="Votre réponse..."></textarea>

                        <button id='deletePublicReceived'type="button">Supprimer</button>
                    </form>
                </div>
            </div>
    </div>
<?php endif; ?>
</div>



</body>
<footer>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/Ecoride/includes/footer.php'; ?>
</footer>

</html>