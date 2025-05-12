<script src='/assets/js/api/map/map.js'></script>
<script type='module' src="/assets/js/ui/menu/autocomplete.js"></script>

<style>#departSuggestionBox, #arrivalSuggestionBox {
    position: absolute;
    border: 1px solid #ccc;
    background-color: white;
    max-height: 200px;
    overflow-y: auto;
    width: 100%;
    z-index: 10;
    display: none;  
}

#departSuggestionBox div, #arrivalSuggestionBox div {
    padding: 8px;
    cursor: pointer;
}

#departSuggestionBox div:hover, #arrivalSuggestionBox div:hover {
    background-color: #f0f0f0;
}
</style>
<form class= 'formSearchBar' action="" method="POST">
      <label for="cityDepart" style="display: none;">Ville de départ</label>
    <input type="text" id="cityDepart" name="depart" placeholder="Départ..." required>
    <div id="departSuggestionBox" style="border: 1px solid #ccc; display: none;"></div>

    <label for="cityArrival" style="display: none;">Ville d'arrivée</label>
    <input type="text" id="cityArrival" name="arrive" placeholder="Arrivée..." required>
    <div id="arrivalSuggestionBox" style="border: 1px solid #ccc; display: none;"></div>

     <label for="date" style="display: none;">Date de départ</label>
    <input type="date" id="date" name="date" required>

    <label for="search" style="display: none;">Rechercher un trajet</label>
    <button name="search" type="submit">Rechercher</button>
</form>
