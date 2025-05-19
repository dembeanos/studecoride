<?php
require_once __DIR__ . '/../../src/Authentification/auth.php';
checkAuthUser();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace utilisateur</title>

    <!-- Styles -->
    <link rel="stylesheet" href="/assets/css/utilisateur.css">
    <link rel="stylesheet" href="/assets/css/popup.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.css" />

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.min.js"></script>
    <script src="/assets/js/ui/event/popup.js"></script>
    <script src="/assets/js/api/map/map.js"></script>
    <script type="module" src="/assets/js/data-management/users/user-manager.js"></script>
    <script type="module" src="/assets/js/data-management/users.opinion.js"></script>
</head>

<body>
    <header>
        <?php include __DIR__ . '/../../includes/header.php'; ?>
        <h1 id="page-title">Espace de <?php echo htmlspecialchars($_SESSION['firstName'], ENT_QUOTES); ?></h1>
    </header>

    <?php $role = $_SESSION['role'] ?? 'passenger'; ?>
    <div class="profil-contain">

        <!-- Sidebar Onglets -->
        <aside class="menu-onglet" role="navigation" aria-label="Menu onglets">
            <img id="userPhotoOnglet" class="user-photo-onglet" alt="Photo de profil utilisateur">

            <button class="onglets" aria-controls="pageProfil">Profil</button>
            <button class="onglets" aria-controls="pageCredits">Crédits</button>
            <button class="onglets" aria-controls="pageReservations">Réservations</button>

            <?php if ($role !== 'passenger'): ?>
                <button class="onglets" aria-controls="pageCar">Auto et Préférences</button>
                <button class="onglets" aria-controls="pageTrajet">Publier un Trajet</button>
                <button class="onglets" aria-controls="pageGestion">Mes trajets proposés</button>
            <?php endif; ?>
        </aside>

        <!-- Contenu principal -->
        <main>

            <!-- Page Profil -->
            <section id="pageProfil" class="pages" aria-labelledby="page-title">
                <div class="profil-container">

                    <!-- Formulaire de mise à jour des informations -->
                    <fieldset class="form-contact" aria-describedby="info-desc">
                        <legend>Vos Informations</legend>
                        <div id="info-desc" class="visually-hidden">
                            Formulaire pour mettre à jour vos informations personnelles.
                        </div>
                        <form method="POST">
                            <label for="firstName">Nom :</label>
                            <input type="text" id="firstName" name="firstName" placeholder="Nom...">

                            <label for="lastName">Prénom :</label>
                            <input type="text" id="lastName" name="lastName" placeholder="Prénom...">

                            <label for="phone">Téléphone :</label>
                            <input type="tel" id="phone" name="phone" placeholder="Téléphone...">

                            <label for="email">E-mail :</label>
                            <input type="email" id="email" name="email" placeholder="Email...">

                            <fieldset aria-label="Adresse">
                                <legend>Adresse</legend>
                                <label for="road">Rue :</label>
                                <input type="text" id="road" name="road" placeholder="Rue...">

                                <label for="roadComplement">Complément :</label>
                                <input type="text" id="roadComplement" name="roadComplement"
                                    placeholder="Complément, Bâtiment...">

                                <label for="zipCode">Code postal :</label>
                                <input type="text" id="zipCode" name="zipCode" placeholder="Code postal...">

                                <label for="city">Ville :</label>
                                <input type="text" id="city" name="city" placeholder="Ville...">
                            </fieldset>

                            <button type="submit" id="sendInfo" name="sendInfo">Valider</button>
                        </form>
                    </fieldset>

                    <div class="profil-colonne-droite">

                        <!-- Photo de profil -->
                        <fieldset class="photo-profil">
                            <legend>Photo de Profil</legend>
                            <img id="userPhotoProfil" alt="Photo de profil">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="file" id="photoUpload" name="photoUpload">
                                <button type="submit" name="updatePhoto" id="updatePhoto">Changer la photo</button>
                            </form>
                        </fieldset>

                        <!-- Changer de mot de passe -->
                        <fieldset class="formPassword" aria-label="Changer de mot de passe">
                            <legend>Changer de Mot de passe</legend>
                            <form id="formPassword" method="POST">
                                <label for="backPassword">Ancien Mot de passe :</label>
                                <input type="password" id="backPassword" name="backPassword"
                                    placeholder="Ancien mot de passe" required>

                                <label for="newPassword">Nouveau mot de passe :</label>
                                <input type="password" id="newPassword" name="newPassword"
                                    placeholder="Nouveau mot de passe" required>

                                <label for="confirmPassword">Confirmer :</label>
                                <input type="password" id="confirmPassword" name="confirmPassword"
                                    placeholder="Confirmer mot de passe" required>

                                <button type="submit" id="sendPassword" name="sendPassword">Valider</button>
                            </form>
                        </fieldset>
                    </div>

                    <!-- Changement de rôle -->
                    <form class="roleChange" method="POST">
                        <p>Sélectionnez votre rôle :</p>
                        <label for="driver">
                            <input type="radio" id="driver" name="role" value="driver">
                            Conducteur
                        </label>
                        <label for="passengerAndDriver">
                            <input type="radio" id="passengerAndDriver" name="role" value="passengerAndDriver">
                            Conducteur et passager
                        </label>
                        <label for="passenger">
                            <input type="radio" id="passenger" name="role" value="passenger">
                            Passager
                        </label>
                        <button id="updateRole" type="submit">Mettre à jour</button>
                    </form>

                </div> 
            </section>

            <!-- Page Crédits -->
            <section id="pageCredits" class="pages">
                <fieldset>
                    <h2>Historique des Mouvements</h2>
                    <table class="credits-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Libellé</th>
                                <th>Débit</th>
                                <th>Crédit</th>
                            </tr>
                        </thead>
                        <tbody id="creditsContainer">
                            <tr class="movementRow">
                                <td id="historyDate"></td>
                                <td id="historyLabel"></td>
                                <td id="historyDebit"></td>
                                <td id="historyCredit"></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align:right"><strong>Total :</strong></td>
                                <td><strong><span id="totalAmountCredits"></span> €</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="cb">
                        <span>Bientôt rechargement CB</span>
                    </div>
                </fieldset>
            </section>


            <!-- Page Réservations -->
            <section id="pageReservations" class="pages">
                <fieldset>
                    <h2>Vos Réservations</h2>
                    <table class="reservations-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Référence</th>
                                <th>Prix</th>
                                <th>Date/Heure Départ</th>
                                <th>Lieu Départ</th>
                                <th>Date/Heure Arrivée</th>
                                <th>Lieu Arrivée</th>
                                <th>Chauffeur</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="reservation-container">
                            <tr class="reservation-line" style="display: none;">
                                <td class="reservation-date"></td>
                                <td class="reservation-ref"></td>
                                <td class="reservation-price"></td>
                                <td class="reservation-depart-date"></td>
                                <td class="city-depart"></td>
                                <td class="reservation-arrival-date"></td>
                                <td class="arrival-city"></td>
                                <td class="driver-name"></td>
                                <td class="reservation-action"></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>

            </section>

            <!-- Page Auto et Préférences -->
            <section id="pageCar" class="pages">
                <form class="ownAuto" id="ownAuto" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <fieldset class="newCar">
                        <legend>Ajout Voiture</legend>
                        <label for="marque">Marque :</label>
                        <input id="marque" type="text" name="marque" placeholder="Marque...">
                        <label for="modele">Modèle :</label>
                        <input id="modele" type="text" name="modele" placeholder="Modèle...">
                        <label for="immatriculation">Immatriculation :</label>
                        <input id="immatriculation" type="text" name="immatriculation" placeholder="Immatriculation...">
                        <label for="firstImmatriculation">1ère Immatriculation :</label>
                        <input id="firstImmatriculation" type="date" name="firstImmatriculation">
                        <label for="color">Couleur :</label>
                        <input id="color" type="text" name="color" placeholder="Couleur...">
                        <label for="energy">Énergie :</label>
                        <select id="energy" name="energy" required>
                            <option value="">Sélectionnez...</option>
                            <option value="Gasoil">Gasoil</option>
                            <option value="Essence">Essence</option>
                            <option value="Hybride">Hybride</option>
                            <option value="Electrique">Électrique</option>
                        </select>
                        <div>
                                <label for="places">Nombre de places restantes :</label>
                                <select id='places' name="places" required>
                                    <option value="0">Sélectionnez...</option>
                                    <option value="1">1 place</option>
                                    <option value="2">2 places</option>
                                    <option value="3">3 places</option>
                                    <option value="4">4 places</option>
                                    <option value="5">5 places</option>
                                    <option value="6">6 places</option>
                                    <option value="7">7 places</option>
                                    <option value="8">8 places</option>
                                    <option value="9">9 places</option>
                                </select>
                            </div>
                        <button type="submit" id="addCar" name="sendCar">Valider</button>
                    </fieldset>
                     <fieldset class="yourCar" name="yourCar">
                            <legend>Vos Voitures enregistrées</legend>
                            <div id="carLine">
                                <div class="car-template" style="display:none;">
                                    <span data-field="marque"></span>
                                    <span data-field="modele"></span>
                                    <span data-field="immatriculation"></span>
                                    <span data-field="color"></span>
                                    <span data-field="energy"></span>
                                    <button data-field="deleteAuto"></button>
                                </div>
                            </div>
                        </fieldset>
                </form>
                <form action="/edit">
                    <fieldset class="acceptance">
                        <legend>Ajouter des Préférences</legend>
                        <label for="allowAnimals">
                            <input id="allowAnimals" type="checkbox" name="acceptance"> J'accepte les animaux
                        </label>
                        <label for="allowSmoke">
                            <input id="allowSmoke" type="checkbox" name="acceptance"> Fumeur
                        </label>
                        <label for="otherPreference">Autres :</label>
                        <input id="otherPreference" type="text" name="otherAcceptance" placeholder="Précisez...">
                        <button type="submit" id="addPref" name="sendPref">Valider</button>
                    </fieldset>
                </form>
            </section>

            <!-- Page Publier un Trajet -->
            <section id="pageTrajet" class="pages">
                <fieldset class="your-trajet">
                    <legend>Ajouter un Trajet</legend>
                    <form id="formTrajet" method="POST">
                        <div class="trajet-colonne">
                            <label for="cityDepart">Ville de départ :</label>
                            <input id="cityDepart" type="text" name="cityDepart" placeholder="Ville de départ">
                            <div id="departSuggestionBox" class="suggestion-box"></div>

                            <label for="roadDepart">Rue ou lieu de départ :</label>
                            <input id="roadDepart" type="text" name="roadDepart" placeholder="Lieu de départ" required>

                            <label for="tripDateDepart">Date de départ :</label>
                            <input id="tripDateDepart" type="date" name="departureDate" required>

                            <label for="tripHourDepart">Heure de départ :</label>
                            <input id="tripHourDepart" type="time" name="departureHour" required>
                        </div>
                        <div class="trajet-colonne">
                            <label for="cityArrival">Ville d'arrivée :</label>
                            <input id="cityArrival" type="text" name="arrival" placeholder="Ville d'arrivée">
                            <div id="arrivalSuggestionBox" class="suggestion-box"></div>

                            <label for="arrivalRoad">Rue ou lieu d'arrivée :</label>
                            <input id="arrivalRoad" type="text" name="arrivalRoad" placeholder="Lieu d'arrivée" required>

                            <label for="tripArrivalDate">Date d'arrivée :</label>
                            <input id="tripArrivalDate" type="date" name="arrivalDate" required>

                            <label for="tripArrivalHour">Heure d'arrivée :</label>
                            <input id="tripArrivalHour" type="time" name="arrivalHour" required>
                        </div>
                        <label for="tripPrice">Montant du trajet :</label>
                        <input id="tripPrice" type="number" step="0.01" name="price" min="0" required>

                        <label for="tripDuration">Durée du trajet :</label>
                        <input id="tripDuration" type="time" name="duration" required>

                        <label for="tripPlaces">Nombre de places disponible :</label>
                        <select id="tripPlaces" name="disponiblePlaces" required>
                            <option value="">Sélectionnez...</option>
                        </select>

                        <label for="autoTrip">Véhicule :</label>
                        <select name="autoTrip" id="autoTrip"></select>

                        <button type="submit" id="addTrip" name="sendTrip">Soumettre</button>
                    </form>
                </fieldset>
                <fieldset>
                    <legend>Graphiques et Carte</legend>
                    <div id="graph" style="display: none;">
                        <div id="graphDepart"></div>
                        <div id="travelTime"></div>
                        <div id="distance"></div>
                        <div id="graphDest"></div>
                    </div>
                    <div id="map" style="height: 300px; width: 100%;"></div>
                </fieldset>
            </section>

            <!-- Page Mes trajets proposés -->
            <section id="pageGestion" class="pages">
                <fieldset class="table-trip">
                    <legend>Trajets à venir</legend>

                    <table class="trips-table">
                        <thead>
                            <tr>
                                <th>Date de départ</th>
                                <th>Référence</th>
                                <th>Prix</th>
                                <th>Heure de départ</th>
                                <th>Lieu de départ</th>
                                <th>Lieu d'arrivée</th>
                                <th>Participants</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tripsContainer">
                            <tr class="tripLine">
                                <td class="trip-date"></td>
                                <td class="trip-ref"></td>
                                <td class="trip-price"></td>
                                <td class="trip-hour"></td>
                                <td class="trip-depart"></td>
                                <td class="trip-arrival"></td>
                                <td class="trip-participation"></td>
                                <td class="trip-status"></td>
                                <td class="trip-action"></td>
                            </tr>
                          
                        </tbody>
                    </table>

                </fieldset>

            </section>

        </main>
    </div>

    <footer>
        <?php include __DIR__ . '/../../includes/footer.php'; ?>
    </footer>
</body>

</html>