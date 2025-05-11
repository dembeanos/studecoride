<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription/Connexion</title>
    <link rel="stylesheet" href="/Ecoride//assets/css/subscribtion.css">
    <link rel="stylesheet" href="/Ecoride/assets/css/popup.css">
    <script src="/Ecoride/assets/js/ui/event/popup.js"></script>
    <script src="/Ecoride/assets/js/data-management/subscribtion/subscribtion.js"></script>
</head>

<body>
    <header>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/Ecoride/includes/header.php'; ?>
    </header>
    <main>
        <section class="formContact">
            <h1>Formulaire d'Inscription</h1>
            <form method="POST">

                <div>
                    <h2>Informations Personnel</h2>
                </div>
                <a href="/Ecoride/Index.php">Vous avez deja un compte ?</a> <!--Voir pour faire ouvrir le popup dans le menu-->
                <section class="border">
                    <div>
                        <label for="lastName">Nom:</label>
                        <input id='lastName' type="text" name="lastName" placeholder="Nom..." required>
                    </div>
                    <div>
                        <label for="firstName">Prénom:</label>
                        <input id='firstName' type="text" name="firstName" placeholder="Prénom..." required>
                    </div>
                    <div>
                        <label for="email">E-mail:</label>
                        <input id='email' type="email" aria-label="E-mail (obligatoire)" name="email" placeholder="E-mail..." required>
                    </div>
                    <div>
                        <label for="username">Pseudo :</label>
                        <input id='username' type="text" aria-label="username" name="username" placeholder="Pseudo..." required>
                    </div>
                    <div>
                        <label for="password">Mot de passe:</label>
                        <input id= 'password' type="password" aria-label="Mot de passe (obligatoire)" name="password" placeholder="Mot de passe..." required>
                    </div>
                    <div>
                        <label for="confirmPassword">Confirmer mot de passe:</label>
                        <input id='confirmPassword' type="password" aria-label="Confirmer mot de passe (obligatoire)" name="confirmPassword" placeholder="Confirmer mot de passe..." required>
                    </div>

                    <div>
                        <label for="phone">Téléphone:</label>
                        <input id='phone' type="tel" name="phone" placeholder="Téléphone...">
                    </div>
                    <div>
                        <p class="adress">Adresse</p>
                        <div>
                            <label for="road">Rue :</label>
                            <input id='road' type="text" name="road" placeholder="Rue..." required>
                        </div>
                        <div>
                            <label for="roadComplement">Complément :</label>
                            <input id='roadComplement' type="text" name="roadComplement" placeholder="Complément, Bâtiment, etc.">
                        </div>
                        <div>
                            <label for="zipCode">Code postal :</label>
                            <input id='zipCode' type="text" name="zipCode" placeholder="Code postal..." required>
                        </div>
                        <div>
                            <label for="city">Ville :</label>
                            <input id='city' type="text" name="city" placeholder="Ville..." required>
                        </div>

                    </div>

                </section>
                <div>
                    <button id='subscribe' class="btn-secondary" type="submit" name="submit">Creer un Compte</button>
                </div>
            </form>

            <!--
                <p class="errorMessage">Tous les champs sont obligatoires.</p>
                <p class="confirmationMessage">Merci pour votre message ! Nous vous répondrons dès que possible.</p>
                -->
        </section>
    </main>

    <footer>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/Ecoride/includes/footer.php'; ?>
    </footer>
</body>

</html>