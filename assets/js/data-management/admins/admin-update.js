
import { initAdminVar } from "./admin-manager.js";
import {adminInfo} from "./admin-manager.js";


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
    let {inputPhoto} = initAdminVar();
    let photo = inputPhoto.files[0];
    if (photo) {
        try {
            const formData = new FormData();
            formData.append('action', 'updatePhoto');
            formData.append('updatePhoto', photo);
            const response = await fetch('/src/Profile/Admin/adminRequestRoute.php', { method: 'POST', body: formData });
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

