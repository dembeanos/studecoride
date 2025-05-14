<?php
require_once  __DIR__. '/../../src/Authentification/auth.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie</title>
    <link rel="stylesheet" href="/assets/css/message.css">
    <link rel="stylesheet" href="/assets/css/popup.css">
    <script src='/assets/js/ui/event/popup.js'></script>
    <script type='module' src='/assets/js/data-management/message/message-management.js'></script>
    <script src='/assets/js/ui/message/users-search.js'></script>
</head>
<header>
    <?php include __DIR__. '/../../includes/header.php'; ?>
</header>
<body>
    <div class="container">

        <h1>Bienvenue dans votre Messagerie</h1>

       
        <div class="tabs">
            <button class="tab-button active" data-tab="receive-message">Boite de reception</button>
            <button class="tab-button active" data-tab="send-message">Envoyer un message</button>
            <?php if (isUserConnected() || isAdminConnected()): ?>
                <button class="tab-button" data-tab="messagerie-visiteurs">Messages visiteurs</button>
            <?php endif; ?>
        </div>

        <div class="standard-message">
 
    <div class="sidebar">
        <h3>Messages reçus</h3>
        <ul class="ourMessage-list" id="ourMessage-list">
       
        </ul>
    </div>

   
    <div class="message-details">
        <form id="message-form">
        
            <div style="display: flex ; align-items: center;">
                <img id="avatar-img" src="" alt="Avatar" style="display : none; width: 50px; margin-right: 10px;">
                <input type="text" id="username" placeholder="Nom d'utilisateur" autocomplete="off" readonly>
            </div>

            <input type="text" id="object" placeholder="Objet" readonly>

          
            <textarea id="message-received" placeholder="Votre réponse..." rows="5" readonly></textarea>

            <button id ='deleteReceived'type="button">Supprimer</button>
        </form>
    </div>
</div>


        <div class="send-message">

        <style>
  
  #userSuggestionBox {
    position: absolute;
    top: 30%; 
    left: 12%; 
    border: 1px solid #ccc;
    background-color: white;
    max-height: 200px;
    overflow-y: auto;
    width: 30%; 
    z-index: 10;
    display: none;  
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
}

#userSuggestionBox div {
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid #ccc;
    display: flex;
    align-items: center;
}

#userSuggestionBox div img {
    width: 30px; 
    height: 30px; 
    border-radius: 50%;
    margin-right: 10px; 
}

#userSuggestionBox div:hover {
    background-color: #f0f0f0; 
}


</style>
            <div class="message-details">
                <form id="message-form">
                  
                    <input type="text" id="dest-username" placeholder="Nom d'utilisateur">
                    <div id="userSuggestionBox" style="border: 1px solid #ccc; display: none;"></div>
                  
                    <input type="text" id="send-objet" placeholder="Objet">

                    <textarea id="send-message" placeholder="Votre message..." rows="5"></textarea>

                    <button id="send-button" type="button">Envoyer</button>
                </form>
            </div>
        </div>

    
        <?php if (isset($_SESSION['employeId']) || isset($_SESSION['adminId'])): ?>
            <div class="public-message">
               
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

            
                <div class="message-details">
                    <form id="message-form-visiteurs">
                    
                        <input type="text" id="nom" placeholder="Nom" readonly>
                        <input type="text" id="prenom" placeholder="Prénom" readonly>
                        <input type="email" id="email" placeholder="Email" readonly>

                      
                        <input type="text" id="objet-visiteur" placeholder="Objet" readonly>

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
    <?php include __DIR__. '/../../includes/footer.php'; ?>
</footer>

</html>