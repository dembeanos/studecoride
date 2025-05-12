<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <link rel="stylesheet" href="/assets/css/covoiturage.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.css" />
    <link rel="stylesheet" href="/assets/css/popup.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.min.js"></script>
    <script src="/assets/js/ui/event/popup.js"></script>
    <script type="module" src="/assets/js/ui/covoiturage/results.js"></script>
    <script type="module" src="/assets/js/api/map/map.js"></script>
    <script type="module" src="/assets/js/ui/menu/autocomplete.js"></script>
    <script type="module" src="/assets/js/data-management/search/search.js"></script>

    <title>Recherche de trajets - EcoRide</title>
</head>
<body>
    <header>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'; ?>
    </header>

    <div class="pageIntro">
        <h1 class="pageTitle">Recherche de trajets en covoiturage</h1>
        <p class="introText">Entrez vos critères pour trouver le trajet idéal parmi nos nombreuses offres de covoiturage partout en France.</p>
    </div>

    <div class="separation"></div>

    <div class="contain">
        <aside class="option">
            <h2>🔎 Filtrer les trajets</h2>

            <div class="filter-group">
                <form action="" method="POST">
                    <label for="cityDepart">Ville de Départ :</label>
                    <input type="text" id="cityDepart" name="depart" placeholder="Départ..." required>
                    <div id="departSuggestionBox"></div>

                    <label for="cityArrival">Ville d'Arrivée :</label>
                    <input type="text" id="cityArrival" name="arrive" placeholder="Arrivée..." required>
                    <div id="arrivalSuggestionBox"></div>
                </form>
            </div>

            <div class="zone-selector">
                <h2>Zone de Recherche</h2>
                <label for="kmRange" class="sr-only">Zone (km)</label>
                <input type="range" id="kmRange" name="kmRange" min="1" max="100" value="10" step="1">
                <div class="range-value"><span id="zone">10</span> km</div>
            </div>

            <div class="filter-group">
                <span class="filter-title">Date de départ</span>
                <label for="departureDate">Date de Départ :</label>
                <input type="date" id="departureDate" name="departureDate" class="filter-date">

                <label for="arrivalDate">Date d'Arrivée :</label>
                <input type="date" id="arrivalDate" name="arrivalDate" class="filter-date">
            </div>

            <div class="filter-group">
                <span class="filter-title">Heure de départ</span>
                <label for="places">
                    Nombre de places désirées :
                </label>
                <select id="places" name="places" class="filter-select" required>
                    <option value="">Sélectionnez...</option>
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

            <div class="filter-group">
                <span class="filter-title">Options supplémentaires</span>
                <label for="smoke">
                    <input id="smoke" type="checkbox" name="options[]" value="smoke" checked>
                    Cigarette Autorisée
                </label>
                <label for="animal">
                    <input id="animal" type="checkbox" name="options[]" value="animal" checked>
                    Animaux Autorisés
                </label>
                <label for="eco">
                    <input id="eco" type="checkbox" name="options[]" value="eco">
                    Trajet Écologique
                </label>

                <label for="tripDuration">Durée du trajet :</label>
                <input id="tripDuration" type="time" name="duration" required>

                <label for="note">Note :</label>
                <select id="note" name="note">
                    <option value="1">⭐</option>
                    <option value="2">⭐⭐</option>
                    <option value="3">⭐⭐⭐</option>
                    <option value="4">⭐⭐⭐⭐</option>
                    <option value="5">⭐⭐⭐⭐⭐</option>
                </select>
            </div>

            <div class="validationBtn">
                <button type="button" id="getResult" class="btn-apply">Rechercher</button>
                <button type="reset" id="resetFilter" class="btn-reset">Réinitialiser les filtres</button>
            </div>

            <h2>🗺️ Carte interactive des trajets</h2>
            <div id="map"></div>
        </aside>

        <main>
            <div class="sort">
                <label for="sortby">Trier par :</label>
                <select name="sortby" id="sortby">
                    <option value="default">Sélectionnez</option>
                    <option value="priceasc">Du - cher au + cher</option>
                    <option value="pricedesc">Du + cher au - cher</option>
                    <option value="datedepartasc">Du + récent au - récent</option>
                    <option value="datedepartdesc">Du - récent au + récent</option>
                </select>
            </div>

            <h2>📋 Résultats de la recherche</h2>
            <div id="resultContainer"></div>

            <div id="popupDetail" class="popup">
                <div class="popup-content">
                    <span id="closePopup" class="close-btn">&times;</span>
                    <h2>Détail du voyage</h2>
                    <div id="popupDetailsContent">Chargement...</div>
                </div>
            </div>
        </main>
    </div>

    <div class="separation"></div>

    <article classe='pub'>
        <div class="pubText">
            <p>Besoin d'un hébergement ?</p>
            <p>Notre partenaire Booking vous aide dans vos réservations</p>
        </div>
        <div class="pubImg">
            <a target="_blank" rel="noopener noreferrer" href="https://www.booking.com/index.fr.html" title="Vers le site de Booking">
                <img src="/assets/images/covoiturage/booking.png" alt="Logo Booking">
            </a>
        </div>
    </article>

    <footer>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
    </footer>
</body>
</html>
