<script src='/assets/js/api/map/map.js'></script>
<script type='module' src="/assets/js/ui/menu/autocomplete.js"></script>

<style>
    .input-wrapper {
        position: relative;
        width: 100%;
    }

    .suggestion-box {
        position: absolute;
        top: 100%;
        left: 0;
        border: 1px solid #ccc;
        background-color: white;
        max-height: 200px;
        overflow-y: auto;
        width: 100%;
        z-index: 999;
        display: none;
    }

    .suggestion-box div {
        padding: 8px;
        cursor: pointer;
    }

    .suggestion-box div:hover {
        background-color: #f0f0f0;
    }
</style>


<form class='formSearchBar' action="/pages/covoiturage/covoiturage.php" method="GET">
    <div class="input-wrapper">
        <input type="text" id="cityDepart" name="depart" placeholder="Départ..." required>
        <div id="departSuggestionBox" class="suggestion-box"></div>
    </div>

    <div class="input-wrapper">
        <input type="text" id="cityArrival" name="arrive" placeholder="Arrivée..." required>
        <div id="arrivalSuggestionBox" class="suggestion-box"></div>
    </div>

    <input type="date" id="date" name="date" required>
    <button name="search" type="submit">Rechercher</button>
</form>

