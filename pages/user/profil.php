<?php
require_once __DIR__ . '/../../src/Authentification/auth.php';
checkAuthUser();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/utilisateur.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.css">
    <link rel="stylesheet" href="/assets/css/popup.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.min.js"></script>
    <script src="/assets/js/ui/event/popup.js"></script>
    <script src="/assets/js/api/map/map.js"></script>
    <script type="module" src="/assets/js/data-management/users/user-manager.js"></script>
    <script type="module" src="/assets/js/data-management/users.opinion.js"></script>
    <title>Espace utilisateur</title>
</head>

<body>
    <header>
        <?php include __DIR__ . '/../../includes/header.php'; ?>
        <h1 id="page-title">Espace de <?php echo $_SESSION['firstName']?></h1>
    </header>

   <?php $role = $_SESSION['role'] ?? 'passenger'; ?>
<div class="profil-contain">
    <aside class="menu-onglet" role="navigation" aria-label="Menu onglets">

        <img id="userPhotoOnglet" class="user-photo-onglet" alt="Photo de profil utilisateur">
        
        <button class="onglets" aria-controls="pageProfil">Profil</button>
        <button class="onglets" aria-controls="pageCredits">Crédits</button>
        <button class="onglets" aria-controls="pageReservations">Réservations</button>
        
        <button class="onglets" aria-controls="pageCar"
            style="<?= $role === 'passenger' ? 'display: none;' : '' ?>">
            Auto et Préférences
        </button>
        <button class="onglets" aria-controls="pageTrajet"
            style="<?= $role === 'passenger' ? 'display: none;' : '' ?>">
            Publier un Trajet
        </button>
        <button class="onglets" aria-controls="pageGestion"
            style="<?= $role === 'passenger' ? 'display: none;' : '' ?>">
            Mes trajets proposés
        </button>
    </aside>
</div>


        <main>

            <section id="pageProfil" class="pages" aria-labelledby="page-title">
                <div class="profil-container">

                    <fieldset class="form-contact" aria-describedby="info-desc">
                        <legend>Vos Informations</legend>
                        <form method="POST" aria-describedby="info-desc">
                            <div id="info-desc" class="visually-hidden">
                                Formulaire pour mettre à jour vos informations personnelles.
                            </div>
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
                                <input type="text" id="roadComplement" name="roadComplement" placeholder="Complément, Bâtiment...">

                                <label for="zipCode">Code postal :</label>
                                <input type="text" id="zipCode" name="zipCode" placeholder="Code postal...">

                                <label for="city">Ville :</label>
                                <input type="text" id="city" name="city" placeholder="Ville...">
                            </fieldset>

                            <button type="submit" id="sendInfo" name="sendInfo">Valider</button>
                        </form>
                    </fieldset>

                    <div class="profil-colonne-droite">

                        <fieldset class="photo-profil">
                            <legend>Photo de Profil</legend>
                            <img id="userPhotoProfil" alt="Photo de profil">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="file" id="photoUpload" name="photoUpload">
                                <button type="submit" name="updatePhoto" id="updatePhoto">Changer la photo</button>
                            </form>
                        </fieldset>

                        <fieldset class="formPassword" aria-label="Changer de mot de passe">
                            <legend>Changer de Mot de passe</legend>
                            <form id="formPassword" method="POST">
                                <label for="backPassword">Ancien Mot de Passe :</label>
                                <input type="password" id="backPassword" name="backPassword" placeholder="Ancien mot de passe" required>

                                <label for="newPassword">Nouveau Mot de Passe :</label>
                                <input type="password" id="newPassword" name="newPassword" placeholder="Nouveau mot de passe" required>

                                <label for="confirmPassword">Confirmer :</label>
                                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirmer mot de passe" required>

                                <button type="submit" id="sendPassword" name="sendPassword">Valider</button>
                            </form>
                        </fieldset>

                    </div>
                    <form class='roleChange' method="POST">
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
            </section>


             <section class="pages">
                <fieldset>
                    <h2>Historique des Mouvements</h2>
                    <div class="creditTable">
                        <div class="tableHeaderCredits">
                            <div>Date</div>
                            <div>Libellé</div>
                            <div>Débit</div>
                            <div>Crédit</div>
                        </div>
                    </div>

                    <div class="movementsCredits">
                        <div class="movementRow">
                            <div id="historyDate"></div>
                            <div id="historyLabel"></div>
                            <div id="historyDebit"></div>
                            <div id="historyCredit"></div>
                        </div>
                    </div>

                    <div class="total">
                        <strong>Total : <span id="totalAmountCredits"></span> €</strong>
                    </div>
                    <div class="cb">
                        <span> Bientôt rechargement CB</span>
                    </div>
                </fieldset>
            </section>

            <section class="pages">
                <fieldset>
                    <div id="reservationsContain">
                        <h2>Vos Réservations</h2>

                        <div class="header-reseveation">
                            <div>Date</div>
                            <div>Référence</div>
                            <div>Prix</div>
                            <div>Date/Heure Départ</div>
                            <div>Lieu Départ</div>
                            <div>Date/Heure Arrivée</div>
                            <div>Lieu Arrivée</div>
                            <div>Chauffeur</div>
                            <div>Actions</div>
                        </div>

                        <div class="reservation-container">
                            <div class="reservation-line" style="display: none;">
                                <span class="reservation-date"></span>
                                <span class="reservation-ref"></span>
                                <span class="reservation-price"></span>
                                <span class="reservation-depart-date"></span>
                                <span class="city-depart"></span>
                                <span class="reservation-arrival-date"></span>
                                <span class="arrival-city"></span>
                                <span class="driver-name"></span>
                                <span class="reservation-action"></span>
                            </div>
                        </div>
                    </div>


                </fieldset>
            </section>



                <section id='pageCar' class="pages">
                    <form class="ownAuto" id="ownAuto" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                        <fieldset class="newCar" id="newCar">
                            <legend>Ajout Voiture</legend>
                            <label for="marque">Marque :</label>
                            <input id="marque" type="text" name="marque" placeholder="Marque...">

                            <label for="modele">Modèle :</label>
                            <input id="modele" type="text" name="modele" placeholder="Modèle...">

                            <label for="immatriculation">Immatriculation :</label>
                            <input id="immatriculation" type="text" name="immatriculation" placeholder="Immatriculation...">

                            <label for="firstImmatriculation">1ère Immatriculation :</label>
                            <input id="firstImmatriculation" type="date" name="firstImmatriculation" placeholder="1ère Immatriculation...">

                            <label for="color">Couleur :</label>
                            <input id="color" type="text" name="color" placeholder="Couleur...">

                            <div>
                                <label for="energy">Energie:</label>
                                <select id="energy" name="energy" required>
                                    <option value="0">Sélectionnez...</option>
                                    <option value="Gasoil">Gasoil</option>
                                    <option value="Essence">Essence</option>
                                    <option value="Hybride">Hybride</option>
                                    <option value="Electric">Electrique</option>
                                </select>
                            </div>
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
                            <button type="submit" id='addCar' name="sendCar">Valider</button>
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
                        <fieldset class="acceptance" name="acceptance">
                            <legend>Ajouter des Préférences</legend>
                            <div class="acceptance-check">
                                <label for="allowAnimals">J'accepte les animaux</label>
                                <input id='allowAnimals' type="checkbox" name="acceptance">

                                <label for="allowSmoke">Fumeur</label>
                                <input id='allowSmoke' type="checkbox" name="acceptance">
                            </div>
                            <div class="flex">
                                <label for="otherPreference">Autres:</label>
                                <input id='otherPreference' type="text" name="otherAcceptance" placeholder="Autres, Précisez...">
                            </div>
                            <button type="submit" id='addPref' name="sendPref">Valider</button>
                        </fieldset>
                    </form>
                </section>

           

            
                <section id='pageTrajet' class="pages">
                    <fieldset class="your-trajet">
                        <legend>Ajouter un Trajet</legend>
                        <form id="formTrajet" method='POST'>
                            <div>
                                <label for="cityDepart">Ville de départ :</label>
                                <input id="cityDepart" type="text" name="cityDepart" placeholder="Ville de départ"><br>
                                <div id="departSuggestionBox" style="border: 1px solid #ccc; display: none;"></div>

                                <label for="roadDepart">Rue ou lieu de départ :</label>
                                <input id="roadDepart" type="text" name="roadDepart" placeholder="Rue ou Lieu de depart" required><br>

                                <label for="tripDateDepart">Date de départ :</label>
                                <input id="tripDateDepart" type="Date" name="departureDate" required><br>

                                <label for="tripHourDepart">Heure de départ :</label>
                                <input id="tripHourDepart" type="time" name="departureHour" required><br>
                            </div>
                            <div>
                                <label for="cityArrival">Ville d'arrivée :</label>
                                <input id="cityArrival" type="text" name="arrival" placeholder="Ville d'arrivée"><br>
                                <div id="arrivalSuggestionBox" style="border: 1px solid #ccc; display: none;"></div>

                                <label for="arrivalRoad">Rue ou lieu d'arrivée :</label>
                                <input id="arrivalRoad" type="text" name="arrivalRoad" placeholder="Rue ou Lieu d'arrivée" required><br>

                                <label for="tripArrivalDate">Date d'arrivée :</label>
                                <input id="tripArrivalDate" type="Date" name="arrivaDate" required><br>

                                <label for="tripArrivalHour">Heure d'arrivée :</label>
                                <input id="tripArrivalHour" type="time" name="arrivalHour" required><br>
                            </div>

                            <label for="tripPrice">Montant du trajet :</label>
                            <p>Un minimum de 3 crédits est requis, 2 crédits sont prélevés pour les frais de plateforme </p>
                            <input id="tripPrice" type="number" step="0.01" name="price" min="0" required><br>

                            <label for="tripDuration">Durée du trajet :</label>
                            <input id="tripDuration" type="time" name="duration" min="0" required><br>
                            <div>
                                <label for="tripPlaces">Nombre de places disponible :</label>
                                <select id="tripPlaces" name="disponiblePlaces" class="disponible-places-select" required>
                                    <option value="0">Sélectionnez...</option>

                                </select>
                            </div>

                            <label for="autoTrip">Véhicule :</label>
                            <select name="autoTrip" id='autoTrip'>
                            </select>


                            <button type="submit" data-carId='' data-prefId='' id='addTrip' name="otherAuto">Soumettre</button>
                        </form>
                    </fieldset>

                    <fieldset class=''>
                        <legend>Graphiques et Maps</legend>
                        <div class=>
                            <div id="graph" style="display: none;">
                                <div id="graphDepart"></div>
                                <img>
                                <div id="travelTime"></div>
                                <div id="distance"></div>
                                <div id="graphDest"></div>
                            </div>
                            <div id="map" style="height: 300px; width:300px;display :block;"></div>
                        </div>
                    </fieldset>
                </section>
                <section id='pageTrajet' class="pages">
                    <fieldset class="table-trip">
                        <legend>Trajets à venir</legend>

                        <div class="header-upcomming-trip">
                            <div>Date de départ</div>
                            <div>Référence</div>
                            <div>Prix</div>
                            <div>Heure de départ</div>
                            <div>Lieu de départ</div>
                            <div>Lieu d'arrivée</div>
                            <div>Participants</div>
                            <div>Status</div>
                            <div>Actions</div>
                        </div>
                        <div class="tripLine">
                            <span class="trip-date"></span>
                            <span class="trip-ref"></span>
                            <span class="trip-price"></span>
                            <span class="trip-hour"></span>
                            <span class="trip-depart"></span>
                            <span class="trip-arrival"></span>
                            <span class="trip-participation"></span>
                            <span class="trip-status"></span>
                            <span class="trip-action"></span>

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