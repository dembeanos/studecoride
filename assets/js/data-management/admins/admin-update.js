
import { initAdminVar } from "./admin-manager.js";
import { adminInfo } from "./admin-manager.js";


async function fetchRequestUpdate(action, data) {
    try {
        let response = await fetch(`/src/Router/adminRoute.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action, data })
        });

        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }

        const json = await response.json();

        if (json.type) {
            handleResponse(json)
        }
        

    } catch (error) {
        console.error("Erreur lors de la récupération de la réponse :", error.text);
    }
}







export async function updateAdminInfo() {
    let { inputInfo } = initAdminVar();
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

    await fetchRequestUpdate('updateAdminInfo', data);
}
export async function updatePassword() {
    let { inputPass } = initAdminVar();
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
    let { inputPhoto } = initAdminVar();
    let photo = inputPhoto.files[0];

    if (!photo) {
        console.error("Aucun fichier sélectionné");
        return;
    }

    try {
        const formData = new FormData();
        formData.append('action', 'updatePhoto');
        formData.append('updatePhoto', photo);

        const response = await fetch('/src/Router/adminRoute.php', { method: 'POST', body: formData });

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



export function updateUser() {
    console.log('ok')
}
export function updateEmploye() {
    console.log('ok')
}
export function updateOpinion() {
    console.log('ok')
}
export async function banUser(banId) {
    const banData = {
        banId: banId
    }

    try {
        await fetchRequestUpdate('banUser', banData);
    } catch (error) {
        console.error('Erreur js lors du bannissement de l\'utilisateur :', error);
    }
}

