<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-Nous</title>
    <link rel="stylesheet" href="/Ecoride/assets/css/contact.css">
    <link rel="stylesheet" href="/Ecoride/assets/css/popup.css">
    <script src="/Ecoride/assets/js/ui/event/popup.js"></script>
    <script src='../../assets/js/data-management/message/contact-us-message.js'></script>
</head>

<body>
    <header>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/Ecoride/includes/header.php'; ?>
    </header>
    <main>
        <fieldset class="formContact">
            <legend>Formulaire de Contact</legend>

            <p class="introMessage">Si vous avez des questions, des suggestions ou besoin
                d'aide, n'hésitez pas à nous contacter. Nous vous répondrons dans les plus
                brefs délais.</p>

            <form id="formContact" action="/renseigner" method="POST">
                <label for="objet">Objet de votre demande :</label>

                <select id="object" name="object" style="display: block;" required>
                    <option value="" disabled selected>Sélectionner un objet</option>
                    <option value="support">Support technique</option>
                    <option value="information">Demande d'information</option>
                    <option value="suggestion">Suggestion</option>
                    <option value="autre">Autre (précisez)</option>
                </select>

                <div id="otherObject" style="display: none;">
                    <label for="customTitle">Votre demande :</label>
                    <input type="text" id="customTitle" name="customTitle" placeholder="Votre titre personnalisé...">
                </div>

                <label for="firstName">Nom:</label>
                <input type="text" id="firstName" name="firstName" placeholder="Nom..." required>

                <label for="lastName">Prénom:</label>
                <input type="text" id="lastName" name="lastName" placeholder="Prénom..." required>

                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" placeholder="E-mail..." required>

                <label for="message">Message:</label>
                <textarea id="message" name="message" placeholder="Votre Message..." required></textarea>
                <button type="button" name="send" id="send">Valider</button>
            </form>
        </fieldset>

    </main>
    <footer>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/Ecoride/includes/footer.php'; ?>
    </footer>
</body>

</html>