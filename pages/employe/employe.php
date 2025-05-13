<?php
require __DIR__. '/../../src/Authentification/auth.php';
checkAuthEmploye();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="module" src='/assets/js/data-management/employees/employe-manager.js'></script>
    <script src='/assets/js/ui/event/popup.js'></script>
    <link rel="stylesheet" href='/assets/css/popup.css'>
    <link rel="stylesheet" href="/assets/css/utilisateur.css">
</head>

<body>
    <header>
        <?php include __DIR__ . '/../../includes/header.php'; ?>
        <h1>Bienvenue dans votre Espace</h1>
    </header>

    <div class="profil-contain">
        <aside class="menu-onglet">
            <img class="user-photo-onglet" id="employePhotoOnglet" alt="Photo de l'employé">
            <button class="onglets">Profil</button>
            <button class="onglets">Avis</button>
        </aside>

        <main>
            <section class="pages" id="pagesProfil">
                <div class="form-informations">
                    <fieldset class="form-contact">
                        <legend>Vos Informations</legend>
                        <form id="form-contact" action="/edit" method="POST">
                            <label for="firstName">Prénom :</label>
                            <input type="text" id="firstName" name="firstName" placeholder="Prénom...">

                            <label for="lastName">Nom :</label>
                            <input type="text" id="lastName" name="lastName" placeholder="Nom...">

                            <label for="email">E-mail :</label>
                            <input type="email" id="email" name="email" placeholder="E-mail...">

                            <label for="phone">Téléphone :</label>
                            <input type="tel" id="phone" name="phone" placeholder="Téléphone...">

                            <div>
                                <p class="adress">Adresse</p>
                                <div>
                                    <label for="road">Rue :</label>
                                    <input type="text" id="road" name="road" placeholder="Rue...">
                                </div>
                                <div>
                                    <label for="roadComplement">Complément :</label>
                                    <input type="text" id="roadComplement" name="roadComplement" placeholder="Complément, Bâtiment, etc.">
                                </div>
                                <div>
                                    <label for="zipCode">Code postal :</label>
                                    <input type="text" id="zipCode" name="zipCode" placeholder="Code postal...">
                                </div>
                                <div>
                                    <label for="city">Ville :</label>
                                    <input type="text" id="city" name="city" placeholder="Ville...">
                                </div>
                            </div>
                            <button type="submit" name="send" id="sendInfo">Valider</button>
                        </form>
                    </fieldset>

                    <fieldset class="formPassword">
                        <legend>Changer de Mot de passe</legend>
                        <form id="formPassword" action="/changer-mdp" method="POST">
                            <label for="backPassword">Ancien Mot de Passe :</label>
                            <input type="password" id="backPassword" name="backPassword" placeholder="Ancien Mot de passe..." required>

                            <label for="newPassword">Nouveau Mot de Passe :</label>
                            <input type="password" id="newPassword" name="newPassword" placeholder="Nouveau Mot de Passe..."><!-- A voir LE REQUIRED-->

                            <label for="confirmPassword">Confirmer Nouveau Mot de Passe :</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirmer Nouveau Mot de Passe..." required>

                            <button type="submit" name="sendPassword" id="sendPassword">Valider</button>
                        </form>
                    </fieldset>

                    <fieldset class="photo-profil">
                    <legend>Photo de Profil</legend>
                    <img id="employePhotoProfil" alt="Photo de profil">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="file" id="photoUpload" name="photoUpload">
                        <button type="submit" name="updatePhoto" id="updatePhoto">Changer la photo</button>
                    </form>
                </fieldset>

                </div>
            </section>
            <section class="pages">
                <fieldset>
                    <div id="opinionContain">
                        <h2>Avis</h2>

                        <div class="header-reservation">
               
                        <div>
                            <span>Référence Opinion</span>
                            <button id="sortOpinionId">Trier</button>
                        </div>
                        <div>
                            <span>note</span>
                            <button id="sortBynote">Trier</button>
                        </div>
                        <div>
                            <span>Message</span>
                        </div>
                        <div>
                            <span>Date de depot</span>
                        </div>
                        <div>
                            <span>Valider</span>
                        </div>
                        <div>
                            <span>Rejeter</span>
                        </div>
                        <div>
                            <span>Détails</span>
                        </div>
                    </div>

                    <div id="opinionContainer">
                        <div class="opinionRow">
                            <div class="opinionRef"></div>
                            <div class="note"></div>
                            <div class="message"></div>
                            <div class="opinionDate"></div>
                            <div class="opinionvalidation"></div>
                            <div class="rejectOpinion"></div>
                            <div class="tripInfo"></div>
                        </div>
                    </div>

                    <div id="pagination">
                        <button id="prevPage">Précédent</button>
                        <span id="currentPage">Page 1</span>
                        <button id="nextPage">Suivant</button>
                    </div>
            </section>

        </main>
    </div>

    <footer>
        <?php include __DIR__. '/../../includes/footer.php'; ?>
    </footer>
</body>

</html>