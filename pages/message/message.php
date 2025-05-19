<?php
require_once __DIR__ . '/../../src/Authentification/auth.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie</title>
    <link rel="stylesheet" href="/assets/css/message.css">
    <link rel="stylesheet" href="/assets/css/popup.css">
    <script src="/assets/js/ui/event/popup.js"></script>
    <script src="/assets/js/ui/message/users-search.js"></script>
    <script src="/assets/js/data-management/message/message-management.js"></script>
</head>

<header>
    <?php include __DIR__ . '/../../includes/header.php'; ?>
</header>

<body>
    <div class="container">
        <h1>Bienvenue dans votre Messagerie</h1>

        <div class="tabs">
            <button class="tab-button active" data-tab="receive-message">Boîte de réception</button>
            <button class="tab-button active" data-tab="send-message">Envoyer un message</button>
            <?php if (isAdminConnected() || isEmployeConnected()): ?>
                <button class="tab-button" data-tab="messagerie-visiteurs">Messages visiteurs</button>
            <?php endif; ?>
        </div>

        <!-- ========== SECTION “Boîte de réception” ========== -->
        <div class="standard-message">
            <div class="sidebar">
                <h3>Messages reçus</h3>
                <ul class="ourMessage-list" id="ourMessage-list">
                    
                </ul>
            </div>

            <div class="message-details">
                <form id="message-form">
                    <div style="display: flex; align-items: center;">
                        <img id="avatar-img" src="" alt="Avatar"
                            style="display: none; width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
                        <input type="text" id="username" placeholder="Nom d'utilisateur" autocomplete="off" readonly>
                    </div>

                    
                    <input type="text" id="objet" placeholder="Objet" readonly>

                    
                    <textarea id="message-received" placeholder="Votre message reçu..." rows="5" readonly></textarea>

                    <button id="deleteReceived" type="button">Supprimer</button>
                </form>
            </div>
        </div>

        <!-- ========== SECTION “Envoyer un message” ========== -->
        <div class="send-message">
            <div class="message-details">
                <form id="message-form-send">
                    <div class="username-container" style="position: relative; width: 100%;">
                        <input type="text" id="dest-username" placeholder="Nom d'utilisateur" autocomplete="off">
                        <div id="userSuggestionBox" style="border: 1px solid #ccc; display: none;"></div>
                    </div>

                    <input type="text" id="send-objet" placeholder="Objet">

                    <textarea id="send-message" placeholder="Votre message..." rows="5"></textarea>

                    <button id="send-button" type="button">Envoyer</button>
                </form>
            </div>
        </div>

        <!-- ========== SECTION “Messages visiteurs” (uniquement si admin/employé) ========== -->
        <?php if (isset($_SESSION['employeId']) || isset($_SESSION['adminId'])): ?>
            <div class="public-message">
                <div class="sidebar">
                    <h3>Messages visiteurs</h3>
                    <ul class="publicMessage-list" id="message-list-visiteurs">
                   
                    </ul>
                </div>

                <div class="message-details">
                    <form id="message-form-visiteurs">
                        <input type="text" id="nom" placeholder="Nom" readonly>
                        <input type="text" id="prenom" placeholder="Prénom" readonly>
                        <input type="email" id="email" placeholder="Email" readonly>

                        <input type="text" id="objet-visiteur" placeholder="Objet" readonly>

                        <textarea id="message-recu-visiteur" placeholder="Votre message..." rows="5"></textarea>

                        <button id="deletePublicReceived" type="button">Supprimer</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>

</html>