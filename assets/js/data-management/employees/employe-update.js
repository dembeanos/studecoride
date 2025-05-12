import { initEmployeVar } from "./employe-manager.js";
import { opinion } from "./employe-manager.js";

async function fetchRequestUpdate(action, data) {
    try {
        let response = await fetch(`/src/Router/employeRoute.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action, data })
        });

        const json = await response.json();

        if (json.type) {
            handleResponse(json);
            return;
        }

        if (typeof json.data === 'string') {
            try {
                json.data = JSON.parse(json.data);
            } catch (e) {
                console.warn("Données déjà parsées ou mal formées :", json.data);
            }
        }

    } catch (error) {
        console.error("Erreur lors de la récupération de la réponse :", error);
    }
}


export async function updateEmployeInfo() {
    let { inputInfo } = initEmployeVar();
    let data = {
        firstName: inputInfo.firstname.value,
        lastName: inputInfo.lastname.value,
        phone: inputInfo.phone.value,
        email: inputInfo.email.value,
        road: inputInfo.road.value,
        complement: inputInfo.roadcomplement.value,
        zipCode: inputInfo.zipcode.value,
        city: inputInfo.city.value
    };

    await fetchRequestUpdate('updateEmployeInfo', data);
}


export async function updatePassword() {
    let { inputPass } = initEmployeVar();
    const data = {
        backPassword: inputPass.backPassword.value,
        newPassword: inputPass.newPassword.value,
        confirmPassword: inputPass.confirmPassword.value
    };
    const updateResponse = await fetchRequestUpdate('updatePassword', data);

    if (updateResponse) {
        return updatemessage(updateResponse)
    }
}



export async function updatePhoto() {
    let { inputPhoto } = initEmployeVar();
    let photo = inputPhoto.files[0];
    if (photo) {
        try {
            const formData = new FormData();
            formData.append('action', 'updatePhoto');
            formData.append('updatePhoto', photo);
            const response = await fetch('/src/Profile/Employee/EmployeRequestRoute.php', { method: 'POST', body: formData });
            const responseText = await response.text();
            console.log("Réponse brute du serveur:", responseText); 
            try {
                const updateResponse = JSON.parse(responseText); 
                if (updateResponse.status === 'success') {
                    console.dir(updateResponse);
                } else {
                    console.error('Erreur:', updateResponse.message); 
                }
                userInfo();
            } catch (error) {
                console.error('Erreur de parsing JSON:', error);
                console.log('Contenu de la réponse:', responseText);
            }
        } catch (error) {
            console.error('Erreur de requête:', error);
        }
    } else {
        console.error("Aucun fichier sélectionné");
    }
}


export async function validationOpinion(validOpinion) {
    
    const updateData = {
        opinionId: validOpinion
    }
    console.log(updateData.opinionId)
    const updateResponse = await fetchRequestUpdate('validateOpinion', updateData)
    if (updateResponse) {
        setTimeout(() => opinion(), 1000)
    }

}
export async function rejectOp(rejectOpinion) {
    const updateData = {
        opinionId: rejectOpinion
    }
    console.log(updateData.opinionId)
    const updateResponse = await fetchRequestUpdate('rejectedOpinion', updateData)
    if (updateResponse) {
        setTimeout(() => opinion(), 1000)
    }
}


export async function getTripInfo(tripInfo) {
    const updateData = {
        opinionId: tripInfo
    };

    console.log(updateData.opinionId);

    try {
        let response = await fetch('/src/Profile/Employee/EmployeRequestRoute.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'getTripDetail', data :updateData })
        });

        const json = await response.json();
        
        if (json.type) {
            handleResponse(json);
            return;
        } else if (Array.isArray(json) && json.length > 0) {
            const tripDetails = json[0];  // On prend le premier objet de l'array

            // Construction du message à afficher dans l'alert
            const tripMessage = `
                <div class="trip-details">
        <p class="title">Offre ID: <span class="value">${tripDetails.offerid}</span></p>
        <p class="title">Date départ: <span class="value">${tripDetails.datedepart}</span></p>
        <p class="title">Ville départ: <span class="value">${tripDetails.citydepart}</span></p>
        <p class="title">Route départ: <span class="value">${tripDetails.roaddepart}</span></p>
        <p class="title">Date arrivée: <span class="value">${tripDetails.datearrival}</span></p>
        <p class="title">Ville arrivée: <span class="value">${tripDetails.arrivalcity}</span></p>
        <p class="title">Route arrivée: <span class="value">${tripDetails.arrivalroad}</span></p>
        <p class="title">Passager: <span class="value">${tripDetails.passengerusername}</span></p>
        <p class="title">Email Passager: <span class="value">${tripDetails.passengeremail}</span></p>
        <p class="title">Conducteur: <span class="value">${tripDetails.driverusername}</span></p>
        <p class="title">Email Conducteur: <span class="value">${tripDetails.driveremail}</span></p>
    </div>
            `;

            // Affichage dans l'alert
            sendInteractivePopup(tripMessage);
        } else {
            console.error("Réponse du serveur mal formée ou vide", json);
        }
    } catch (error) {
        console.error("Erreur lors de la requête:", error);
    }
}


