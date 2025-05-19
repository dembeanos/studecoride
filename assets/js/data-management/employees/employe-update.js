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
    let {inputPhoto} = initEmployeVar();
    let photo = inputPhoto.files[0];
    if (!photo) {
           console.error("Aucun fichier sélectionné");
           return;
       }
   
       try {
           const formData = new FormData();
           formData.append('action', 'updatePhoto');
           formData.append('updatePhoto', photo);
   
           const response = await fetch('/src/Router/employeRoute.php', { method: 'POST', body: formData });
   
           if (!response.ok) {
               console.error('Erreur serveur, statut:', response.status);
               return;
           }
           const updateResponse = await response.json();
           if (updateResponse.type) {
               handleResponse(updateResponse)
           }
           adminInfo();
       } catch (error) {
           console.error('Erreur lors de la requête ou du parsing JSON:', error);
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
        let response = await fetch('/src/Router/employeRoute.php', {
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
                                  <p style="font-size: 1.5rem; font-weight: bold;">Offre ID: <span style="font-size: 2rem; font-weight: normal;">${tripDetails.offerid}</span></p>
                                  <p style="font-size: 1.5rem; font-weight: bold;">Date départ: <span style="font-size: 2rem; font-weight: normal;">${tripDetails.datedepart}</span></p>
                                  <p style="font-size: 1.5rem; font-weight: bold;">Ville départ: <span style="font-size: 2rem; font-weight: normal;">${tripDetails.citydepart}</span></p>
                                  <p style="font-size: 1.5rem; font-weight: bold;">Route départ: <span style="font-size: 2rem; font-weight: normal;">${tripDetails.roaddepart}</span></p>
                                  <p style="font-size: 1.5rem; font-weight: bold;">Date arrivée: <span style="font-size: 2rem; font-weight: normal;">${tripDetails.datearrival}</span></p>
                                  <p style="font-size: 1.5rem; font-weight: bold;">Ville arrivée: <span style="font-size: 2rem; font-weight: normal;">${tripDetails.arrivalcity}</span></p>
                                  <p style="font-size: 1.5rem; font-weight: bold;">Route arrivée: <span style="font-size: 2rem; font-weight: normal;">${tripDetails.arrivalroad}</span></p>
                                  <p style="font-size: 1.5rem; font-weight: bold;">Passager: <span style="font-size: 2rem; font-weight: normal;">${tripDetails.passengerusername}</span></p>
                                  <p style="font-size: 1.5rem; font-weight: bold;">Email Passager: <span style="font-size: 2rem; font-weight: normal;">${tripDetails.passengeremail}</span></p>
                                  <p style="font-size: 1.5rem; font-weight: bold;">Conducteur: <span style="font-size: 2rem; font-weight: normal;">${tripDetails.driverusername}</span></p>
                                  <p style="font-size: 1.5rem; font-weight: bold;">Email Conducteur: <span style="font-size: 2rem; font-weight: normal;">${tripDetails.driveremail}</span></p>
                                </div>
                                `;


            // Affichage
            sendInteractivePopup(tripMessage);
        } else {
            console.error("Réponse du serveur mal formée ou vide", json);
        }
    } catch (error) {
        console.error("Erreur lors de la requête:", error);
    }
}


