<?php
require $_SERVER['DOCUMENT_ROOT'] . '/Ecoride/src/Authentification/auth.php';
checkAuthAdmin();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type='module' src="/Ecoride/assets/js/data-management/admins/admin-manager.js"></script>
    <script src='../../assets/js/ui/event/popup.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href='/Ecoride/assets/css/popup.css'>
    <link rel="stylesheet" href="/Ecoride/assets/css/utilisateur.css">
    <title>Espace Administrateur</title>
</head>

<body>

    <header>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/Ecoride/includes/header.php';
        $page = isset($_GET['page']) ? $_GET['page'] : 'admin';
        ?>
        <h1>Bienvenue dans votre Espace Administrateur</h1>
    </header>
    </div>

    <div class="profil-contain">
        <aside class="menu-onglet">
            <img class="user-photo-onglet" id="adminPhotoOnglet" alt="Photo de l'administrateur">
            <button class="onglets">Profil</button>
            <button class="onglets">Gestion Utilisateur</button>
            <button class="onglets">Gestion des Employés</button>
            <button class="onglets">Surveillance Covoiturage</button>
            <button class="onglets">Chiffre d'affaire</button>
            <button class="onglets">Moniteur d'erreurs</button>
        </aside>

        <main>

            <!-- Section Profil -->
            <section class="pages" id="profil">
                <fieldset class="form-contact">
                    <legend>Vos Informations</legend>
                    <form method="POST">
                        <label for="firstName">Nom :</label>
                        <input type="text" id="firstName" name="firstName" placeholder="Nom...">

                        <label for="lastName">Prénom :</label>
                        <input type="text" id="lastName" name="lastName" placeholder="Prénom...">

                        <label for="phone">Téléphone :</label>
                        <input type="tel" id="phone" name="phone" placeholder="Téléphone...">

                        <label for="email">E-mail :</label>
                        <input type="email" id="email" name="email" placeholder="Email...">

                        <p class="adress">Adresse</p>

                        <label for="road">Rue :</label>
                        <input type="text" id="road" name="road" placeholder="Rue...">

                        <label for="roadComplement">Complément :</label>
                        <input type="text" id="roadComplement" name="roadComplement" placeholder="Complément, Bâtiment, etc.">

                        <label for="zipCode">Code postal :</label>
                        <input type="text" id="zipCode" name="zipCode" placeholder="Code postal...">

                        <label for="city">Ville :</label>
                        <input type="text" id="city" name="city" placeholder="Ville...">

                        <button type="submit" name="sendInfo" id="sendInfo">Valider</button>
                    </form>
                </fieldset>

                <!-- Section Changement de mot de passe -->
                <fieldset class="formPassword">
                    <legend>Changer de Mot de passe</legend>
                    <form id="formPassword" action="/changer-mdp" method="POST">
                        <label for="backPassword">Ancien Mot de Passe :</label>
                        <input type="password" id="backPassword" name="backPassword" placeholder="Ancien Mot de passe..." required>

                        <label for="newPassword">Nouveau Mot de Passe :</label>
                        <input type="password" id="newPassword" name="newPassword" placeholder="Nouveau Mot de Passe..." required>

                        <label for="confirmPassword">Confirmer Nouveau Mot de Passe :</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirmer Nouveau Mot de Passe..." required>

                        <button type="submit" name="sendPassword" id="sendPassword">Valider</button>
                    </form>
                </fieldset>

                <!-- Section Photo de profil -->
                <fieldset class="photo-profil">
                    <legend>Photo de Profil</legend>
                    <img id="adminPhotoProfil" alt="Photo de profil">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="file" id="photoUpload" name="photoUpload">
                        <button type="submit" name="updatePhoto" id="updatePhoto">Changer la photo</button>
                    </form>
                </fieldset>
            </section>

            <style>
                #usersContainer {
                    display: flex;
                    flex-direction: column;
                }

                .userRow {
                    display: flex;
                }

                .userRow>div {
                    width: 150px;
                    /* fixe pour alignement, ajustable */
                    padding: 5px;
                    border-bottom: 1px solid #ccc;
                }
            </style>
            <!-- Section Utilisateurs -->
            <section class="pages" id="utilisateurs">
                <fieldset>
                    <h2>Utilisateurs</h2>
                    <div class="header-reservation">
                        <!-- Filtres de tri -->
                        <div>
                            <span>Référence Utilisateur</span>
                            <button id="sortUserid">Trier</button>
                        </div>
                        <div>
                            <span>Nom</span>
                            <button id="sortLastname">Trier</button>
                        </div>
                        <div>
                            <span>Prénom</span>
                            <button id="sortFirstname">Trier</button>
                        </div>
                        <div>
                            <span>Téléphone</span>
                        </div>
                        <div>
                            <span>Rue</span>
                        </div>
                        <div>
                            <span>Code Postale</span>
                        </div>
                        <div>
                            <span>Ville</span>
                        </div>
                        <div>
                            <span>Date d'inscription</span>
                            <button id="sortCreationDate">Trier</button>
                        </div>
                        <div>
                            <span>Note</span>
                            <button id="sortNote">Trier</button>
                        </div>
                        <div>
                            <span>Role Utilisateur</span>
                        </div>
                        <div>
                            <span>Credit</span>
                        </div>
                        <div>
                            <span>Actions</span>
                        </div>
                    </div>

                    <div id="usersContainer">
                        <div class="userRow">
                            <div class="usersRef"></div>
                            <div class="firstname"></div>
                            <div class="lastname"></div>
                            <div class="usersPhone"></div>
                            <div class="usersRoad"></div>
                            <div class="zipcode"></div>
                            <div class="usersCity"></div>
                            <div class="usersCreationDate"></div>
                            <div class="usersNote"></div>
                            <div class="usersRole"></div>
                            <div class="usersCredit"></div>
                            <div class="usersAction"></div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div id="pagination">
                        <button id="prevPage">Précédent</button>
                        <span id="currentPage">Page 1</span>
                        <button id="nextPage">Suivant</button>
                    </div>
                </fieldset>
            </section>


            <!-- Section Employés -->
            <section class="pages" id="employes">
                <fieldset>
                    <legend>Ajout d'un utilisateur</legend>
                    <form method="POST">
                    <div>
                        <label for="addUserLastName">Nom:</label>
                        <input id='addUserLastName' type="text" name="lastName" placeholder="Nom..." required>
                    </div>
                    <div>
                        <label for="addUserFirstName">Prénom:</label>
                        <input id='addUserFirstName' type="text" name="firstName" placeholder="Prénom..." required>
                    </div>
                    <div>
                        <label for="addUserEmail">E-mail:</label>
                        <input id='addUserEmail' type="email" aria-label="E-mail (obligatoire)" name="email" placeholder="E-mail..." required>
                    </div>
                    <div>
                        <label for="addUserUsername">Pseudo :</label>
                        <input id='addUserUsername' type="text" aria-label="username" name="username" placeholder="Pseudo..." required>
                    </div>
                    <div>
                        <label for="addUserPassword">Mot de passe:</label>
                        <input id= 'addUserPassword' type="password" aria-label="Mot de passe (obligatoire)" name="password" placeholder="Mot de passe..." required>
                    </div>
                    <div>
                        <label for="addUserConfirmPassword">Confirmer mot de passe:</label>
                        <input id='addUserConfirmPassword' type="password" aria-label="Confirmer mot de passe (obligatoire)" name="confirmPassword" placeholder="Confirmer mot de passe..." required>
                    </div>

                    <div>
                        <label for="addUserPhone">Téléphone:</label>
                        <input id='addUserPhone' type="tel" name="phone" placeholder="Téléphone...">
                    </div>
                    <div>
                        <p class="adress">Adresse</p>
                        <div>
                            <label for="addUserRoad">Rue :</label>
                            <input id='addUserRoad' type="text" name="road" placeholder="Rue..." required>
                        </div>
                        <div>
                            <label for="addUserRoadComplement">Complément :</label>
                            <input id='addUserRoadComplement' type="text" name="roadComplement" placeholder="Complément, Bâtiment, etc.">
                        </div>
                        <div>
                            <label for="addUserZipCode">Code postal :</label>
                            <input id='addUserZipCode' type="text" name="zipCode" placeholder="Code postal..." required>
                        </div>
                        <div>
                            <label for="addUserCity">Ville :</label>
                            <input id='addUserCity' type="text" name="city" placeholder="Ville..." required>
                        </div>
                        <div>
                            <select name="role" id="role">
                                <option value="0"disabled selected>Séléctionner un role</option>
                                <option value="admin">Admin</option>
                                <option value="employe">Employé</option>
                                <option value="user">Utilisateur</option>
                            </select>
                        </div>
                    </div>
                    <div>
                    <button id='subscribe' class="btnUser" type="submit" name="submit">Creer un Compte</button>
                </div>
                </form>
                </fieldset>

                <fieldset>
                    <h2>Employés</h2>
                    < <div class="header-employe">
                        <!-- Filtres de tri -->
                        <div>
                            <span>Référence Employe</span>
                            <button id="sortEmployeid">Trier</button>
                        </div>
                        <div>
                            <span>Nom</span>
                            <button id="sortEmployeLastname">Trier</button>
                        </div>
                        <div>
                            <span>Prénom</span>
                            <button id="sortEmployeFirstname">Trier</button>
                        </div>
                        <div>
                            <span>Téléphone</span>
                        </div>
                        <div>
                            <span>Rue</span>
                        </div>
                        <div>
                            <span>Code Postale</span>
                        </div>
                        <div>
                            <span>Ville</span>
                        </div>
                        <div>
                            <span>Date d'inscription</span>
                            <button id="sortEmployeCreationDate">Trier</button>
                        </div>
                        <div>
                            <span>Actions</span>
                        </div>
                    

                    <div id="employeContainer">
                        <div class="employeRow">
                            <div class="EmployeRef"></div>
                            <div class="employefirstname"></div>
                            <div class="employelastname"></div>
                            <div class="employePhone"></div>
                            <div class="employeRoad"></div>
                            <div class="employezipcode"></div>
                            <div class="employeCity"></div>
                            <div class="employeCreationDate"></div>
                            <div class="employeNote"></div>
                            <div class="employeAction"></div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div id="pagination">
                        <button id="employePrevPage">Précédent</button>
                        <span id="employeCurrentPage">Page 1</span>
                        <button id="employeNextPage">Suivant</button>
                    </div>
                </fieldset>
            </section>

            <!-- Section Covoiturage -->
            <section class="pages" id="covoiturage">
                <fieldset>
                    <h2>Surveillance Covoiturage</h2>
                    <canvas id="offerChart" width="400" height="200"></canvas>
                    <div class="total">
                        <span id="totalOffer"></span>
                    </div>
                </fieldset>
            </section>

            <!-- Section Chiffre d'affaire -->
            <section class="pages" id="chiffreAffaire">
                <fieldset>
                    <h2>Chiffre d'affaire</h2>
                    <canvas id="profitChart" width="400" height="200"></canvas>
                    <div class="total">
                        <span id="totalCA"></span>
                    </div>
                </fieldset>
            </section>

            <!-- Section Moniteur d'erreurs -->
            <section class="pages" id="moniteurErreurs">
                <fieldset>
                    <h2>Logs</h2>
                    <div class="header-log">
                        <!-- Filtres de tri -->
                        <div>
                            <span>Date Evenement</span>
                            <button id="sortLogDate">Trier</button>
                        </div>
                        <div>
                            <span>Message</span>
                        </div>
                        <div>
                            <span>Gravité</span>
                            <button id="sortLogLevel">Trier</button>
                        </div>
                    </div>

                    <div id="logContainer">
                        <div class="logRow">
                            <div class="logDate"></div>
                            <div class="message"></div>
                            <div class="loglevel"></div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div id="pagination">
                        <button id="prevLogPage">Précédent</button>
                        <span id="currentLogPage">Page 1</span>
                        <button id="nextLogPage">Suivant</button>
                    </div>
                </fieldset>
            </section>

        </main>
    </div>

    <footer>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/Ecoride/includes/footer.php'; ?>
    </footer>

</body>

</html>