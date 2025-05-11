// Importation des modules nécessaires
import { inseeCity } from "../../ui/menu/autocomplete.js";
import { showResult } from "../../ui/covoiturage/results.js";

// ------------------ 1. Récupération des éléments DOM ------------------
let zone = document.getElementById('zone');
let cityDepart = document.getElementById('cityDepart');
let arrivalCity = document.getElementById('cityArrival');
let departDate = document.getElementById('departureDate');
let arrivalDate = document.getElementById('arrivalDate');
let places = document.getElementById('places');
let smoke = document.getElementById('smoke');
let animal = document.getElementById('animal');
let eco = document.getElementById('eco');
let duration = document.getElementById('tripDuration');
let note = document.getElementById('note');
let kmRange = document.getElementById('kmRange');

// Boutons
let resetFilterButton = document.getElementById('resetFilter');
let getResultButton = document.getElementById('getResult');

// Sélection de tri
let sortBy = document.getElementById('sortby');

let currentResults = [];

// ------------------ 2. Gestion des événements ------------------

sortBy.addEventListener('change', () => {
    if (currentResults.length > 0) {
        let sortedData = SortResult(sortBy.value, currentResults);
        showResult(sortedData);
    }
});

resetFilterButton.addEventListener('click', (event) => {
    event.preventDefault();
    kmRange.value = 10;
    zone.textContent = "10";
    cityDepart.value = '';
    arrivalCity.value = '';
    departDate.value = '';
    arrivalDate.value = '';
    places.value = '';
    smoke.checked = false;
    animal.checked = false;
    eco.checked = false;
    duration.value = '';
    note.value = '';
});

kmRange.addEventListener('input', () => {
    zone.textContent = kmRange.value;
});

getResultButton.addEventListener('click', async (event) => {
    event.preventDefault();

    let today = new Date().toISOString().split('T')[0];

    let data = {
        inseeDepart: inseeCity.inseeDepart,
        inseeArrival: inseeCity.inseeArrival,
        zone: kmRange.value || 10,
        departDate: departDate.value || today,
        arrivalDate: arrivalDate.value,
        places: places.value || 1,
        smoke: smoke.checked,
        animal: animal.checked,
        eco: eco.checked,
        duration: duration.value || '99h00',
        note: note.value || 1
    };

    
    

    // ------------------ 3. Envoi de la requête ------------------

    let results;

    try {
        console.log(data);
        let request = await fetch(`/Ecoride/src/Router/searchRoute.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ data })
        });

        if (!request.ok) {
            console.log(`La requête a échoué avec le statut ${request.status}`);
            return;
        }

        results = await request.json();
        console.log(results)
        if (results.type){
            handleResponse(results)
        }
        if (results.status === 'success') {
            currentResults = results.data;
            let resultsData = SortResult(sortBy.value, currentResults);
            showResult(resultsData);
        }

    } catch (error) {
        console.error("Une erreur s'est produite lors de l'envoi de la requête :", error);
    }
});

// ------------------ 4. Fonction de tri des résultats ------------------

function SortResult(sortBy, results) {
    switch (sortBy) {
        case 'priceasc':
            return results.slice().sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
        case 'pricedesc':
            return results.slice().sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
        case 'datedepartasc':
            return results.slice().sort((a, b) => new Date(a.datedepart) - new Date(b.datedepart));
        case 'datedepartdesc':
            return results.slice().sort((a, b) => new Date(b.datedepart) - new Date(a.datedepart));
        default:
            return results;
    }
}

