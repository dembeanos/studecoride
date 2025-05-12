//Définition des inputs et variables 
let arrivalLat
let arrivalLong
let departLat
let departLong;

let cityArrival = document.getElementById('cityArrival');
let cityDepart = document.getElementById('cityDepart');



//Ecoute du chargement complet de la page
window.addEventListener('load', function () {

    //Ecoute des input a chaque frappe une requete est envoyé
    cityArrival.addEventListener('input', async function () {
        let citySearch = cityArrival.value;
        let cityList = await fetchRequest(citySearch);
        arrivalCitySuggestion(cityList, 'arrivalSuggestionBox');

    });
    cityDepart.addEventListener('input', async function () {

        let citySearch = cityDepart.value;
        let cityList = await fetchRequest(citySearch);
        departCitySuggestion(cityList, 'departSuggestionBox');

    });
});

// Méthode fetch d'envoi reception des données
async function fetchRequest(data) {
    let request = await fetch(`/src/Router/cityRoute.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ data })
    });

    if (!request.ok) {
        throw new Error(`Erreur HTTP : ${request.status}`);
    }

    const responseText = await request.text();

    try {
        return JSON.parse(responseText);
    } catch (error) {
        console.error("Erreur de parsing JSON:", error);
        throw error;
    }
}


//Variables importées dans user-update pour l'ajout d'offre de covaoiturage
export const inseeCity = {
    inseeDepart: null,
    inseeArrival: null
};


function arrivalCitySuggestion(cityList) {
    const suggestionContainer = document.getElementById('arrivalSuggestionBox');
    suggestionContainer.innerHTML = '';
    if (cityList && cityList.data && cityList.data.length > 0) {
        console.log('Liste des villes :', cityList.data);
        cityList.data.forEach(city => {
            const div = document.createElement('div');
            div.textContent = city.city_code + ' - ' + city.department_name;
            div.addEventListener('mousedown', function (event) {
                event.preventDefault()
                cityArrival.value = city.city_code + ' - ' + city.department_name;
                suggestionContainer.style.display = 'none';
                
                arrivalLat = city.latitude;
                arrivalLong = city.longitude;
                inseeCity.inseeArrival = city.insee_code; // c'est ici que je renseigne inseeArrival
                if (arrivalLat && arrivalLong && departLat && departLong) {
                    updateRoute(arrivalLat, arrivalLong, departLat, departLong);
                }
            
            });

            suggestionContainer.appendChild(div);
        });
        suggestionContainer.style.display = 'block';
    } else {
        suggestionContainer.style.display = 'none';
    }
}



cityArrival.addEventListener('blur', function () {
    const inputValue = this.value;
    const suggestionContainer = document.getElementById('arrivalSuggestionBox');
    const validSuggestions = Array.from(suggestionContainer.children)
        .map(div => div.textContent);

    if (!validSuggestions.includes(inputValue)) {
        this.value = '';
        
    }
});


function departCitySuggestion(cityList) {
    const suggestionContainer = document.getElementById('departSuggestionBox');
    suggestionContainer.innerHTML = '';
    if (cityList && cityList.data && cityList.data.length > 0) {
        console.log('Liste des villes :', cityList.data);
        cityList.data.forEach(city => {
            const div = document.createElement('div');
            div.textContent = city.city_code + ' - ' + city.department_name;
            div.addEventListener('mousedown', function (event) {
                event.preventDefault()
                cityDepart.value = city.city_code + ' - ' + city.department_name;
                suggestionContainer.style.display = 'none';

                departLat = city.latitude;
                departLong = city.longitude;
                inseeCity.inseeDepart = city.insee_code// c'est ici que je renseigne inseeDepart
                if (arrivalLat && arrivalLong && departLat && departLong) {
                    updateRoute(arrivalLat, arrivalLong, departLat, departLong);
                }
            
            });
            suggestionContainer.appendChild(div);
        });
        suggestionContainer.style.display = 'block';
    } else {
        suggestionContainer.style.display = 'none';
       
    }
}

cityDepart.addEventListener('blur', function () {
    const inputValue = this.value;
    const suggestionContainer = document.getElementById('departSuggestionBox');
    const validSuggestions = Array.from(suggestionContainer.children)
        .map(div => div.textContent);
    if (!validSuggestions.includes(inputValue)) {
        this.value = '';
        
    }
});