<?php
require_once __DIR__ . '/../../src/Authentification/auth.php';
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mention Légales Ecoride</title>
        <link rel="stylesheet" href="/assets/css/mention.css">
    </head>
    <body>
      
        <header>
            <?php include __DIR__. '/../../includes/header.php'; ?>
        </header>
        <main>
            <h1>Mention légales</h1>

            <h2>Éditeur du site</h2>
            <div class="champs">
                <p>Nom du site : Ecoride</p>
                <p>Adresse : <span class="grease">[A DEFINIR]</span></p>
                <p>Numéro de téléphone :<span class="grease">[A DEFINIR]</span></p>
                <p>Email : <span class="grease">[A DEFINIR]</span></p>
                <p>Numéro Siret:<span class="grease">[A DEFINIR]</span></p>
                <p>Directeur de publication:<span class="grease">[A DEFINIR]</span></p>
            </div>

            <h2>Hébergeur du site</h2>            
            <p>Nom du hébergeur :  <span class="grease">[A DEFINIR]</span></p>
            <p>Adresse :  <span class="grease">[A DEFINIR]</span></p>
            <p>Numéro de téléphone :  <span class="grease">[A DEFINIR]</span></p>

            <h2>Propriété intellectuelle</h2>
            <p>Le site EcoRide et tous ses contenus (textes, images, logos, graphismes, etc.) sont la 
                propriété exclusive de [Nom de l'entreprise]. Toute reproduction, distribution ou modification sans 
                autorisation préalable est strictement interdite.</p>

            <h2>Conditions d'utilisation</h2>
            <p>Les utilisateurs du site EcoRide acceptent les conditions d'utilisation suivantes. 
                Si vous ne les acceptez pas, vous ne pouvez pas utiliser ce site.</p>

            <h2>Protection des données personnelles</h2>
            <p>Les informations collectées sur le site EcoRide sont traitées conformément au RGPD 
                (Règlement Général sur la Protection des Données).
                Vous disposez d’un droit d’accès, de rectification et de suppression de vos données personnelles.
                Pour exercer ce droit, veuillez nous contacter à : [Adresse e-mail].</p>

            <h2>Responsabilité</h2>
            <p>EcoRide ne peut être tenu responsable des dommages résultant de l’utilisation du site ou des services 
                proposés.</p>

            <h2>Litiges</h2>
            <p>Tout litige relatif à l’utilisation du site est soumis à la législation française et relève de la 
                compétence des tribunaux de [Ville].</p>

            </main>
        <footer>
            <?php include __DIR__. '/../../includes/footer.php'; ?>
        </footer>
    </body>
</html>