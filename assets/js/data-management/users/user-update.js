import { initUserVar} from "./user-manager.js";
import { userInfo } from "./user-manager.js";
import { cars } from "./user-manager.js";
import { reservation} from "./user-manager.js";
import { inseeCity } from "../../ui/menu/autocomplete.js";



async function fetchRequestUpdate(action, data) {
    console.log (data)
    try {
        let request = await fetch(`/Ecoride/src/Router/userRoute.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json'},
            body: JSON.stringify({ action, data })
        });

        if (!request.ok) {
            console.log(`La requête a échoué pour l'action : ${action} avec le statut ${request.status}`);
            return;
        }

        const json = await request.text();
        console.log(json)
        if (typeof json.data === 'string') {
            try {
                json.data = JSON.parse(json.data);
            } catch (e) {
                console.warn("Données déjà parsées ou mal formées :", json.data);
            }
        }

        handleResponse(json);

    } catch (error) {
        console.error("Erreur lors de la récupération de la réponse :", error.text);
    }
}


export async function updateUserInfo() {
    let { inputInfo } = initUserVar();
    let data = {
        firstName: inputInfo.firstname.value,
        lastName: inputInfo.lastname.value,
        phone: inputInfo.phone.value,
        email: inputInfo.email.value,
        road: inputInfo.road.value,
        roadComplement: inputInfo.roadcomplement.value,
        zipCode: inputInfo.zipcode.value,
        city: inputInfo.city.value
    };

    await fetchRequestUpdate('updateUserInfo', data);
}


export async function updateRole(){
    let {inputRole} = initUserVar();
    let selectedRole = Array.from(inputRole).find(role => role .checked);
    const updateData = {
        role: selectedRole.value
    };
    const updateResponse = await fetchRequestUpdate('updateRole', updateData);

    if (updateResponse){
        return updatemessage(updateResponse)
    }
}

export async function updatePassword(){   
    let  {inputPass}= initUserVar();
    const data = {
        backPassword: inputPass.backPassword.value,
        newPassword: inputPass.newPassword.value,
        confirmPassword: inputPass.confirmPassword.value
    };
    const updateResponse = await fetchRequestUpdate('updatePassword', data);

    if (updateResponse){
        return updatemessage(updateResponse)
    } 
}

export async function updatePhoto() {
    let {inputPhoto} = initUserVar();
    let photo = inputPhoto.files[0];
    if (photo) {
        try {
            const formData = new FormData();
            formData.append('action', 'updatePhoto');
            formData.append('updatePhoto', photo);
            const response = await fetch('/Ecoride/src/Router/userRoute.php', { method: 'POST', body: formData });
            const responseText = await response.text();
            console.log("Réponse brute du serveur:", responseText); // Ajoute un log pour afficher la réponse

            try {
                const updateResponse = JSON.parse(responseText); // Essaie de parser la réponse en JSON
                if (updateResponse.status === 'success') {
                    console.dir(updateResponse); // Affiche le message si succès
                } else {
                    console.error('Erreur:', updateResponse.message); // Affiche le message d'erreur si échec
                }
                userInfo();
            } catch (error) {
                console.error('Erreur de parsing JSON:', error);
                console.log('Contenu de la réponse:', responseText); // Affiche la réponse brute pour déboguer
            }
        } catch (error) {
            console.error('Erreur de requête:', error);
        }
    } else {
        console.error("Aucun fichier sélectionné");
    }
}

export function addCar() {
    let { inputCar } = initUserVar();
    const updateData = {
        marque: inputCar.marque.value,
        modele: inputCar.modele.value,
        immatriculation: inputCar.immatriculation.value,
        firstImmatriculation: inputCar.firstImmatriculation.value,
        color: inputCar.color.value,
        energy: inputCar.energy.value,
        places: inputCar.places.value
    };

    fetchRequestUpdate('addCar', updateData)
        setTimeout(() => cars(), 1000);  
    }



export async function addPreference(){ 
    let {inputPref} = initUserVar(); 
    const updateData = {
    animal: inputPref.animal.checked,
    smoke: inputPref.smoke.checked,
    other: inputPref.other.value,
}
    const updateResponse = await fetchRequestUpdate('addPreference', updateData);

    if (updateResponse){
        return updatemessage(updateResponse)
    }
}


export async function addTrip(carId, prefId){
    let {inputTrip} = initUserVar();  
    
    const updateData = {
        cityDepart : inputTrip.cityDepart.value,
        arrivalCity: inputTrip.arrivalCity.value,
        roadDepart: inputTrip.roadDepart.value,
        arrivalRoad: inputTrip.arrivalRoad.value,
        hourDepart: inputTrip.hourDepart.value,
        hourArrival: inputTrip.hourArrival.value,
        dateArrival: inputTrip.dateArrival.value,
        dateDepart: inputTrip.dateDepart.value,
        price: inputTrip.price.value,
        duration : inputTrip.duration.value,
        car: carId,
        preference: prefId,
        placeAvailable: inputTrip.placeAvailable.value,
        inseeArrival: inseeCity.inseeArrival,
        inseeDepart: inseeCity.inseeDepart
    } 
    const updateResponse = await fetchRequestUpdate('addTrip', updateData)

    if (updateResponse){
        setTimeout(() => trip(), 1000)
        return updatemessage(updateResponse)
    }
}

export async function addOpinion(srcOpinion){  
    let {inputOpinion} = initUserVar();

    const updateData = {
        srcOpinion : srcOpinion,
        opinionText: inputOpinion.opinionText.value,
        note: inputOpinion.note.value

    }
    const updateResponse = await fetchRequestUpdate('addOpinion', updateData)

    if (updateResponse){
        setTimeout(() => opinion(), 1000)
        return updatemessage(updateResponse)
    }
}
export async function addClaim(srcClaim){
    let {inputReclam} = initUserVar() 
    const updateData = {
        srcClaim: srcClaim,
        reclamText: inputReclam.reclamText.value,
    }
    
    const updateResponse = await fetchRequestUpdate('addClaim', updateData)

    if (updateResponse){
        setTimeout(() => opinion(), 1000)
        return updatemessage(updateResponse)
    }
}
export async function deleteCar(immatriculation){
    const deleteData = {
        immatriculation: immatriculation
    };
    await fetchRequestUpdate('deleteCar', deleteData);
    return cars()
}
 

export async function cancelReservation(reservationId){

    const updateData = {
        reservationId : reservationId
    }
    console.log(updateData)
    const updateResponse = await fetchRequestUpdate('cancelReservation', updateData)

    if (updateResponse){
        setTimeout(() => reservation(), 1000)
        return updatemessage(updateResponse)
    }
}

export async function validateReservation(reservationId){

    const updateData = {
        reservationId : reservationId
    }
    console.log(updateData)
    const updateResponse = await fetchRequestUpdate('validateReservation', updateData)

    if (updateResponse){
        setTimeout(() => reservation(), 1000)
        return updatemessage(updateResponse)
    }
}

export async function cancelTrip(tripId){

    const updateData = {
        tripId: tripId
    }
    console.log(updateData)
    const updateResponse = await fetchRequestUpdate('cancelTrip', updateData)

    if (updateResponse){
        setTimeout(() => trip(), 1000)
    
    }
}

export async function startTrip (tripId){

    const updateData ={
        tripId : tripId
    }
    console.log(updateData)
    const updateResponse = await fetchRequestUpdate('startTrip', updateData)

    if (updateResponse){
        setTimeout(() => trip(), 1000)
    }
}

export async function endTrip (tripId){

    const updateData ={
        tripId : tripId
    }
    console.log(updateData)
    const updateResponse = await fetchRequestUpdate('endTrip', updateData)

    if (updateResponse){
        setTimeout(() => trip(), 1000)
    }
}