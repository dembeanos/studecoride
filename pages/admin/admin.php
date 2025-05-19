<?php
require_once __DIR__ . '/../../src/Authentification/auth.php';
checkAuthAdmin();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type='module' src="/assets/js/data-management/admins/admin-manager.js"></script>
    <script src='../../assets/js/ui/event/popup.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href='/assets/css/popup.css'>
    <link rel="stylesheet" href="/assets/css/administrateur.css">
    <title>Espace Administrateur</title>
</head>

<body>

    <header>
        <?php include __DIR__ . '/../../includes/header.php';
        $page = isset($_GET['page']) ? $_GET['page'] : 'admin';
        ?>
        <h1>Espace Administrateur de <?php echo $_SESSION['firstName'] ?></h1>
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

            <section class="pages" id="pagesProfil">
                <div class="form-informations">
                    <fieldset class="form-contact">
                        <legend>Vos Informations</legend>
                        <div id="info-desc" class="visually-hidden">
                            Formulaire pour mettre à jour vos informations personnelles.
                        </div>
                        <form method="POST">
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

                    <div class="profil-colonne-droite">

                        <fieldset class="photo-profil">
                            <legend>Photo de Profil</legend>
                            <img id="adminPhotoProfil" alt="Photo de profil">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="file" id="photoUpload" name="photoUpload">
                                <button type="submit" name="updatePhoto" id="updatePhoto">Changer la photo</button>
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

                    </div>
                </div>
            </section>

            <section class="pages" id="utilisateurs">
                <fieldset>
                    <h2>Utilisateurs</h2>
                    <table id="usersTable">
                        <thead>
                            <tr>
                                <th>
                                    Référence Utilisateur
                                    <button id="sortUserid">Trier</button>
                                </th>
                                <th>
                                    Nom
                                    <button id="sortLastname">Trier</button>
                                </th>
                                <th>
                                    Prénom
                                    <button id="sortFirstname">Trier</button>
                                </th>
                                <th>Téléphone</th>
                                <th>Rue</th>
                                <th>Code Postale</th>
                                <th>Ville</th>
                                <th>
                                    Date d'inscription
                                    <button id="sortCreationDate">Trier</button>
                                </th>
                                <th>
                                    Note
                                    <button id="sortNote">Trier</button>
                                </th>
                                <th>Role Utilisateur</th>
                                <th>Credit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody id="usersContainer">
                            <tr class="userRow">
                                <td class="usersRef"></td>
                                <td class="lastname"></td>
                                <td class="firstname"></td>
                                <td class="usersPhone"></td>
                                <td class="usersRoad"></td>
                                <td class="zipcode"></td>
                                <td class="usersCity"></td>
                                <td class="usersCreationDate"></td>
                                <td class="usersNote"></td>
                                <td class="usersRole"></td>
                                <td class="usersCredit"></td>
                                <td class="usersAction"></td>
                            </tr>
                        </tbody>
                    </table>

                    <div id="pagination">
                        <button id="prevPage">Précédent</button>
                        <span id="currentPage">Page 1</span>
                        <button id="nextPage">Suivant</button>
                    </div>
                </fieldset>

            </section>


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
                            <input id='addUserPassword' type="password" aria-label="Mot de passe (obligatoire)" name="password" placeholder="Mot de passe..." required>
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
                                    <option value="0" disabled selected>Séléctionner un role</option>
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

                    <table id="employeTable">
                        <thead>
                            <tr>
                                <th>
                                    Référence Employé
                                    <button id="sortEmployeid">Trier</button>
                                </th>
                                <th>
                                    Nom
                                    <button id="sortEmployeLastname">Trier</button>
                                </th>
                                <th>
                                    Prénom
                                    <button id="sortEmployeFirstname">Trier</button>
                                </th>
                                <th>Téléphone</th>
                                <th>Rue</th>
                                <th>Code Postale</th>
                                <th>Ville</th>
                                <th>
                                    Date d'inscription
                                    <button id="sortEmployeCreationDate">Trier</button>
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody id="employeContainer">
                            <tr class="employeRow">
                                <td class="EmployeRef"></td>
                                <td class="employelastname"></td>
                                <td class="employefirstname"></td>
                                <td class="employePhone"></td>
                                <td class="employeRoad"></td>
                                <td class="employezipcode"></td>
                                <td class="employeCity"></td>
                                <td class="employeCreationDate"></td>
                                <td class="employeAction"></td>
                            </tr>
                        </tbody>
                    </table>

                    <div id="pagination">
                        <button id="employePrevPage">Précédent</button>
                        <span id="employeCurrentPage">Page 1</span>
                        <button id="employeNextPage">Suivant</button>
                    </div>
                </fieldset>

            </section>


            <section class="pages" id="covoiturage">
                <fieldset>
                    <h2>Surveillance Covoiturage</h2>
                    <canvas id="offerChart" width="400" height="200"></canvas>
                    <div class="total">
                        <span id="totalOffer"></span>
                    </div>
                </fieldset>
            </section>


            <section class="pages" id="chiffreAffaire">
                <fieldset>
                    <h2>Chiffre d'affaire</h2>
                    <canvas id="profitChart" width="400" height="200"></canvas>
                    <div class="total">
                        <span id="totalCA"></span>
                    </div>
                </fieldset>
            </section>


            <section class="pages" id="moniteurErreurs">
                <fieldset>
                    <h2>Logs</h2>

                    <table id="logTable">
                        <thead>
                            <tr>
                                <th>
                                    Date Événement
                                    <button id="sortLogDate">Trier</button>
                                </th>
                                <th>Message</th>
                                <th>
                                    Gravité
                                    <button id="sortLogLevel">Trier</button>
                                </th>
                            </tr>
                        </thead>

                        <tbody id="logContainer">
                            <tr class="logRow">
                                <td class="logDate"></td>
                                <td class="message"></td>
                                <td class="loglevel"></td>
                            </tr>
                        </tbody>
                    </table>

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
        <?php include __DIR__ . '/../../includes/footer.php'; ?>
    </footer>

</body>

</html>