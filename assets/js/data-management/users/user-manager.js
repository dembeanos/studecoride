window.addEventListener("load", () => {
    const onglets = document.querySelectorAll(".onglets");
    const pages = document.querySelectorAll("section");
    const functions = [userInfo, credit, reservation, cars, setTrip, tripManager];

    const hidePages = () => { pages.forEach((page) => page.style.display = "none"); };

    const showPage = (index) => {
        hidePages();
        pages[index].style.display = "block";
        const forms = pages[index].querySelectorAll("form");
        forms.forEach(form => form.reset());

        if (typeof functions[index] === "function") {
            functions[index]();
        }

    };

    hidePages()
    showPage(0)

    onglets.forEach((onglet, index) => {
        onglet.addEventListener("click", (event) => {
            event.preventDefault();
            showPage(index);;
        });
    });
});



export function initUserVar() {
    let inputInfo = {
        firstname: document.getElementById("firstName"),
        lastname: document.getElementById("lastName"),
        phone: document.getElementById("phone"),
        email: document.getElementById("email"),
        road: document.getElementById("road"),
        roadcomplement: document.getElementById("roadComplement"),
        zipcode: document.getElementById("zipCode"),
        city: document.getElementById("city"),
    }
    let inputRole = document.querySelectorAll('input[name="role"]')
    let inputPhoto = document.getElementById('photoUpload')
    let inputPass = {
        backPassword: document.getElementById("backPassword"),
        newPassword: document.getElementById("newPassword"),
        confirmPassword: document.getElementById("confirmPassword")
    }
    let inputCar = {
        marque: document.getElementById("marque"),
        modele: document.getElementById("modele"),
        immatriculation: document.getElementById("immatriculation"),
        firstImmatriculation: document.getElementById("firstImmatriculation"),
        color: document.getElementById("color"),
        energy: document.getElementById("energy"),
        places: document.getElementById("places"),
        carLine: document.querySelector(`[data-immatriculation="${immatriculation}"]`)
    }
    let inputPref = {
        animal: document.getElementById('allowAnimals'),
        smoke: document.getElementById('allowSmoke'),
        other: document.getElementById('otherPreference')
    }
    let creditVar = {
        movementsContainer: document.getElementById('creditsContainer'),
        movementLine: document.querySelector('.movementRow'),
        totalCreditElem: document.getElementById('totalAmountCredits'),

    }
    let reservationVar = {
        reservationContainer: document.querySelector('.reservation-container'),
        templateLine: document.querySelector('.reservation-line'),
    }
    let inputTrip = {
        cityDepart: document.getElementById('cityDepart'),
        roadDepart: document.getElementById('roadDepart'),
        dateDepart: document.getElementById('tripDateDepart'),
        hourDepart: document.getElementById('tripHourDepart'),
        arrivalCity: document.getElementById('cityArrival'),
        arrivalRoad: document.getElementById('arrivalRoad'),
        dateArrival: document.getElementById('tripArrivalDate'),
        hourArrival: document.getElementById('tripArrivalHour'),
        price: document.getElementById('tripPrice'),
        duration: document.getElementById('tripDuration'),
        placeAvailable: document.getElementById('tripPlaces')
    }
    let tripVar = {
        autoTripSelect: document.getElementById('autoTrip'),
        tripButton: document.getElementById("addTrip"),
        prefId: null,
        carId: null,
        carPlaces: null
    }

    let inputOpinion = {
        opinionText: document.getElementById('containOpinion'),
        note: document.getElementById('note')
    }

    let inputReclam = {
        reclamText: document.getElementById('containReclamation')
    }

    let opinionVar = {
        tripOpinion: document.getElementById('tripOpinion'),
        tripReclamation: document.getElementById('tripReclamation'),
        userOpinionContainer: document.querySelector('.userOpinionContainer'),
        container: document.querySelector('.opinionContainer'),
        reclamationContainer: document.querySelector('.reclamationContainer'),
        addOpinionButton: document.getElementById('addOpinion'),
        addClaimButton: document.getElementById('addClaim'),
    }
    
    return { inputInfo, inputPhoto, inputRole, inputPass, inputCar, inputPref, creditVar, reservationVar, 
            inputTrip, tripVar, inputOpinion, inputReclam, opinionVar };
}

const carLine = document.getElementById('carLine');
const carTemplate = carLine.querySelector('.car-template');

//Méthode Importé d'autres fichiers

import {
    updateUserInfo,
    updatePassword,
    updatePhoto,
    updateRole,
    addCar,
    addPreference,
    addTrip,
    addOpinion,
    addClaim,
    deleteCar,
    cancelReservation,
    validateReservation,
    cancelTrip,
    startTrip,
    endTrip
} from "./user-update.js";

import { sendOpinion } from "./opinion.js";

async function fetchRequest(action) {
    try {
        let request = await fetch(`/src/Router/userRoute.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ action: action })
        });
        const responseText = await request.text();


        const responseData = JSON.parse(responseText);
        return responseData;
    } catch (error) {

        handleResponse(error);


        console.error("Erreur lors de la récupération de la réponse :", error);
    }
}



export function setupButton(buttons) {
    Object.entries(buttons).forEach(([action, button]) => {
        if (button && !button.dataset.listenerAttached) {
            button.addEventListener("click", function (event) {
                event.preventDefault();
                let functionToCall;
                let immatriculation = button.dataset.immatriculation;
                let carId = button.dataset.carId;
                let prefId = button.dataset.prefId
                let tripId = button.dataset.tripId
                let reservationId = button.dataset.reservationId
                let srcClaim = button.dataset.srcClaim
                let srcOpinion = button.dataset.srcOpinion
                switch (action) {
                    case "updateUserInfo":
                        functionToCall = updateUserInfo;
                        break;
                    case "updatePassword":
                        functionToCall = updatePassword;
                        break;
                    case "updatePhoto":
                        functionToCall = updatePhoto;
                        break;
                    case "updateRole":
                        functionToCall = updateRole;
                        break;
                    case "addCar":
                        functionToCall = addCar;
                        break;
                    case "addPreference":
                        functionToCall = addPreference;
                        break;
                    case "addTrip":
                        functionToCall = () => addTrip(carId, prefId);
                        break;
                    case "addOpinion":
                        functionToCall = () => addOpinion(srcOpinion);
                        break;
                    case "addClaim":
                        functionToCall = () => addClaim(srcClaim);
                        break;
                    case "deleteCar":
                        functionToCall = () => deleteCar(immatriculation);
                        break;
                    case "cancelTrip":
                        functionToCall = () => cancelTrip(tripId);
                        break;
                    case 'startTrip':
                        functionToCall = () => startTrip(tripId);
                        break;
                    case 'endTrip':
                        functionToCall = () => endTrip(tripId);
                        break;
                    case 'cancelReservation':
                        functionToCall = () => cancelReservation(parseInt(reservationId));
                        break;
                    case 'validateReservation':
                        sendOpinion(reservationId);
                        functionToCall = () => validateReservation(reservationId)
                        break;
                    default:
                        console.error(`La fonction ${action} n'est pas définie.`);
                }

                if (functionToCall) {
                    functionToCall();
                }
            });
            button.dataset.listenerAttached = "true";
        }
    });
}




export async function userInfo() {
    try {

        const requests = [
            fetchRequest('getUserInfo'),
            fetchRequest('getRole'),
            fetchRequest('getPhoto')
        ];

        const [userInfo, userRole, userPhoto] = await Promise.all(requests);

        if (userInfo && userInfo.status === 'success') {
            const userInfoData = userInfo.data;
            let { inputInfo } = initUserVar();
            for (let key in userInfoData) {

                if (inputInfo[key]) {
                    inputInfo[key].value = userInfoData[key];
                }
            }
        } else {
            console.error('js : Erreur lors de la récupération des données utilisateur');
        }

        if (userRole) {
            let { inputRole } = initUserVar();
            inputRole.forEach((radio) => {
                if (radio.value === userRole.userrole) {
                    radio.checked = true;
                }
            });
        }

        if (userPhoto) {
            let photoOnglet = document.getElementById('userPhotoOnglet');
            let photoProfile = document.getElementById('userPhotoProfil');
            if (photoOnglet || photoProfile) {
                photoOnglet.src = userPhoto;
                photoProfile.src = userPhoto;
            }
        } else {
            console.log('Erreur : Photo manquante dans la réponse');
        }

        const buttons = {
            updateUserInfo: document.getElementById("sendInfo"),
            updatePassword: document.getElementById("sendPassword"),
            updatePhoto: document.getElementById("updatePhoto"),
            updateRole: document.getElementById("updateRole"),
        };

        setupButton(buttons);

    } catch (error) {
        console.error('Erreur lors de la requête:', error);
    }
}


export async function cars() {
    const savedCar = await fetchRequest('getCar');
    carLine.innerHTML = "";
    let cars = [];
    if (Array.isArray(savedCar.data)) {
        cars = savedCar.data;
    }

    cars.forEach(car => {
        let newCarDiv = carTemplate.cloneNode(true);
        newCarDiv.style.display = "block";

        let fields = {
            marque: newCarDiv.querySelector('[data-field="marque"]'),
            modele: newCarDiv.querySelector('[data-field="modele"]'),
            immatriculation: newCarDiv.querySelector('[data-field="immatriculation"]'),
            couleur: newCarDiv.querySelector('[data-field="color"]'),
            energie: newCarDiv.querySelector('[data-field="energy"]'),
            deleteAuto: newCarDiv.querySelector('[data-field="deleteAuto"]'),
        };

        if (fields.marque) fields.marque.textContent = car.marque;
        if (fields.modele) fields.modele.textContent = car.modele;
        if (fields.immatriculation) fields.immatriculation.textContent = car.immatriculation;
        if (fields.couleur) fields.couleur.textContent = car.color;
        if (fields.energie) fields.energie.textContent = car.energy;

        let deleteButton = document.createElement("button");
        deleteButton.textContent = "🗑️ Supprimer";
        deleteButton.classList.add("delete-car");
        deleteButton.dataset.immatriculation = car.immatriculation;
        deleteButton.type = "submit"

        setupButton({
            deleteCar: deleteButton
        });

        if (fields.deleteAuto) {
            fields.deleteAuto.innerHTML = "";
            fields.deleteAuto.appendChild(deleteButton);
        }

        carLine.appendChild(newCarDiv);
    });

    const savedPref = await fetchRequest('getPreference');
    let { inputPref } = initUserVar();

    if (savedPref) {
        inputPref.animal.checked = savedPref.animal;
        inputPref.smoke.checked = savedPref.smoke;
        inputPref.other.value = savedPref.other;

    }
    const buttons = {
        addCar: document.getElementById("addCar"),
        addPreference: document.getElementById("addPref"),
    };

    setupButton(buttons);
}



export async function credit() {
    try {
        const [creditHistory, getTotalCredit] = await Promise.all([
            fetchRequest('getCredit'),
            fetchRequest('getTotalCredit')
        ]);

        let { creditVar } = initUserVar();

        if (!creditVar.movementsContainer || !creditVar.movementLine) {
            console.error("L'élément .movementsCredits ou .movementRow est introuvable !");
            return;
        }

        creditVar.movementsContainer.innerHTML = "";
        if (creditHistory && creditHistory.data) {
            let items = creditHistory.data;
            items.forEach(item => {
                let newHistCreditDiv = creditVar.movementLine.cloneNode(true);
                newHistCreditDiv.querySelector('#historyDate').textContent = item.creationdate;
                newHistCreditDiv.querySelector('#historyLabel').textContent = item.label;
                newHistCreditDiv.querySelector('#historyCredit').textContent = item.credit;
                newHistCreditDiv.querySelector('#historyDebit').textContent = item.debit;

                creditVar.movementsContainer.appendChild(newHistCreditDiv);
            });
        } else {
            creditVar.movementLine.innerHTML = "<p>Aucun mouvement enregistré.</p>";
        }

        if (getTotalCredit) {
            let parsedtotal = JSON.parse(getTotalCredit);
            let total = parsedtotal.data.credit;
            creditVar.totalCreditElem.textContent = Number(total).toFixed(2);
        } else {
            creditVar.totalCreditElem.textContent = "0";
        }

    } catch (error) {
        console.error('Erreur lors de la récupération des crédits:', error);
    }
}









export async function reservation() {
    try {
        const reservationInfo = await fetchRequest('getReservation');

        let { reservationVar } = initUserVar();

        if (!reservationVar.templateLine) {
            console.error("Aucune ligne modèle (.reservation-line) trouvée !");
            return;
        }

        reservationVar.reservationContainer.innerHTML = "";

        if (reservationInfo) {
            let infos = Array.isArray(reservationInfo) ? reservationInfo : [reservationInfo];

            infos.forEach(info => {
                let newReservationLine = reservationVar.templateLine.cloneNode(true);
                newReservationLine.style.display = "table-row";

                newReservationLine.querySelector(".reservation-date").textContent = info.dateReservation;
                newReservationLine.querySelector(".reservation-ref").textContent = info.reservationId;
                newReservationLine.querySelector(".reservation-price").textContent = info.price;
                newReservationLine.querySelector(".reservation-depart-date").textContent = info.dateDepart;
                newReservationLine.querySelector(".city-depart").textContent = info.cityDepart;
                newReservationLine.querySelector(".reservation-arrival-date").textContent = info.dateArrival;
                newReservationLine.querySelector(".arrival-city").textContent = info.arrivalCity;
                newReservationLine.querySelector(".driver-name").textContent = info.driver;

                let action = newReservationLine.querySelector('.reservation-action');
                action.innerHTML = "";

                let currentDate = new Date();
                let dateDepart = new Date(info.dateDepart);

                if (info.status === 'canceled') {
                    action.textContent = "❌ Annulé";
                } else if (currentDate < dateDepart) {
                    let deleteButton = document.createElement("button");
                    deleteButton.textContent = "❌ Annuler";
                    deleteButton.classList.add("delete-reservation");
                    deleteButton.dataset.reservationId = info.reservationId;
                    action.appendChild(deleteButton);

                    setupButton({
                        cancelReservation: deleteButton
                    });
                } else if (currentDate >= dateDepart && info.status !== 'validated') {
                    let validationButton = document.createElement("button");
                    validationButton.textContent = "Valider Transaction";
                    validationButton.classList.add("valid-reservation");
                    validationButton.dataset.reservationId = info.reservationId;
                    action.appendChild(validationButton);
                    
                    setupButton({
                        validateReservation: validationButton
                    });
                } else {
                    action.textContent = info.status === "validated" ? "✅ Validé" : info.status;
                }

                reservationVar.reservationContainer.appendChild(newReservationLine);
            });
        } else {
            reservationVar.reservationContainer.innerHTML = "<tr><td colspan='9'>Aucune réservation pour le moment.</td></tr>";
        }
    } catch (error) {
        console.error('Erreur lors de la récupération des réservations:', error);
    }
}

export async function setTrip() {
    try {
        
        let { tripVar } = initUserVar();
        const [savedCar, savedPref] = await Promise.all([
            fetchRequest('getCar'),
            fetchRequest('getPreference'),
        ]);

        carLine.innerHTML = "";
        let cars = Array.isArray(savedCar.data) ? savedCar.data : [];
        //affectation de preferenceId au dataset bouton
        tripVar.tripButton.dataset.prefId = savedPref.preferenceid
        //affectation du select de voiture diponible à une sous variable
        let autoTripSelect = tripVar.autoTripSelect;

        //Incrémentantion des voiture de l'utilisateur a la selection de voiture connue
        if (autoTripSelect) {
            autoTripSelect.innerHTML = "";
            cars.forEach(car => {
                let option = document.createElement('option');
                option.value = car.carid;
                option.textContent = `${car.marque} ${car.modele} ${car.color} ${car.immatriculation}`;
                autoTripSelect.appendChild(option);
            });
            //redirection vers ajout de voiture si l'utilisateur veut creer une autre voiture
            let addCarOption = document.createElement('option');
            addCarOption.value = "addCar";
            addCarOption.textContent = "+ Ajouter un véhicule";
            autoTripSelect.appendChild(addCarOption);
            //ecoute de la selection d'une volonté d'ajout d'un vehicule et redirection
            autoTripSelect.addEventListener('click', function () {
                if (autoTripSelect.value === "addCar") {
                    document.getElementById('pageCar').style.display = 'block';
                    document.getElementById('pageTrajet').style.display = 'none';
                    getCar();
                } else {// sinon recuperation de de l'id du vehicule et enregistrement dans le dataset du bouton
                    let selectedCar = cars.find(car => car.carid == autoTripSelect.value);
                    if (selectedCar) {
                        tripVar.carId = selectedCar.carid;
                        tripVar.carPlaces = selectedCar.places;
                        tripVar.tripButton.dataset.carId = tripVar.carId;

                        let tripPlacesSelect = document.getElementById('tripPlaces');
                        tripPlacesSelect.innerHTML = "";

                        let defaultOption = document.createElement('option');
                        defaultOption.value = "0";
                        defaultOption.textContent = "Sélectionnez...";
                        tripPlacesSelect.appendChild(defaultOption);
                        // j'enleve la place conducteur du nombre de place du vehicule conducteur
                        let availablePlaces = tripVar.carPlaces - 1;

                        for (let i = 1; i <= availablePlaces; i++) {
                            let option = document.createElement('option');
                            option.value = i;
                            option.textContent = i + (i > 1 ? " places" : " place");
                            tripPlacesSelect.appendChild(option);
                        }
                    }
                }
            });
            
        }
        setupButton({ addTrip: tripVar.tripButton });
    } catch (err) {
        console.error("Erreur dans setTrip :", err);
    }
}


async function tripManager() {
    try {
        
        let tripInfo = await fetchRequest('getTrip');
        const tripLineTemplate = document.querySelector('.tripLine');

        if (tripInfo.data && Array.isArray(tripInfo.data) && tripInfo.data.length > 0) {
            let trips = tripInfo.data;
            const fragment = document.createDocumentFragment();
            let parentElement = tripLineTemplate.parentElement;

            parentElement.querySelectorAll('.tripLine').forEach(line => line.remove());

            trips.forEach(trip => {
            
                let newTripLine = tripLineTemplate.cloneNode(true);
            
                newTripLine.querySelector('.trip-date').textContent = trip.datedepart || 'Date inconnue';
                newTripLine.querySelector('.trip-ref').textContent = trip.offerid;
                newTripLine.querySelector('.trip-price').textContent = trip.price || 'Prix inconnu';
                newTripLine.querySelector('.trip-hour').textContent = trip.hourdepart || 'Heure inconnue';
                newTripLine.querySelector('.trip-depart').textContent = trip.citydepart || 'Lieu inconnu';
                newTripLine.querySelector('.trip-arrival').textContent = trip.arrivalcity || 'Arrivée inconnue';
                newTripLine.querySelector('.trip-participation').textContent = `${trip.totalreservations} / ${trip.placeavailable}`;
                newTripLine.querySelector('.trip-status').textContent = trip.status;
            
                let currentDate = new Date();
                let dateDepart = new Date(trip.datedepart);
                let action = newTripLine.querySelector('.trip-action');
            
                if (trip.status === 'active' && currentDate < dateDepart) {
                    let deleteButton = document.createElement('button');
                    deleteButton.textContent = '❌ Annuler';
                    deleteButton.classList.add('delete-trip');
                    deleteButton.dataset.tripId = trip.offerid; 
                    deleteButton.type = 'submit';
            
                    action.innerHTML = '';
                    action.appendChild(deleteButton);
            
                    setupButton({ cancelTrip: deleteButton });
                }
            
                 // Si le trajet est déjà en cours, afficher directement "Arrivée"
                 if (trip.status === 'in process') {
                    let endTrip = document.createElement('button');
                    endTrip.textContent = 'Arrivée';
                    endTrip.classList.add('end-trip');
                    endTrip.dataset.tripId = trip.offerid; 
                    endTrip.type = 'submit';
            
                    action.innerHTML = '';
                    action.appendChild(endTrip);
                    console.log(trip.offerid)
                    setupButton({ endTrip: endTrip });
                }

                function isSameDay(date1, date2) {
                    return date1.toISOString().split('T')[0] === date2.toISOString().split('T')[0];
                }
            
                // Si on est le jour J, afficher le bouton "Démarrer"
                if (trip.status === 'active' && isSameDay(currentDate, dateDepart)) {
                    let startTrip = document.createElement('button');
                    startTrip.textContent = 'Démarrer';
                    startTrip.classList.add('start-trip');
                    startTrip.dataset.tripId = trip.offerid;  
                    startTrip.type = 'submit';
            
                    action.innerHTML = '';
                    action.appendChild(startTrip);
            
                    setupButton({ startTrip: startTrip });
            
                    startTrip.addEventListener('click', function () {
                        startTrip.disabled = true;
            
                        let processTrip = document.createElement('button');
                        processTrip.textContent = 'Processing...';
                        processTrip.classList.add('process-trip');
                        processTrip.disabled = true;
            
                        action.innerHTML = '';
                        action.appendChild(processTrip);
            
                        setTimeout(() => {
                            let endTrip = document.createElement('button');
                            endTrip.textContent = 'Arrivée';
                            endTrip.classList.add('end-trip');
                            endTrip.dataset.tripId = trip.offerid; 
                            endTrip.type = 'submit';
            
                            action.innerHTML = '';
                            action.appendChild(endTrip);
            
                            setupButton({ endTrip: endTrip });
                        }, 10000);
                    });
                }
                fragment.appendChild(newTripLine);
            });
            parentElement.appendChild(fragment);
        } else {
            parentElement.querySelectorAll('.tripLine').forEach(line => line.remove());

            const noTripMessage = document.createElement('p');
            noTripMessage.textContent = "Aucun trajet pour le moment.";
            parentElement.appendChild(noTripMessage);
        }

        
    } catch (error) {
        console.error('Erreur lors de la récupération des trajets ou des véhicules :', error);
    }
}
