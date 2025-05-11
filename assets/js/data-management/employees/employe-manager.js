window.addEventListener("load", () => {
    const onglets = document.querySelectorAll(".onglets");
    const pages = document.querySelectorAll("section.pages");
    const functions = [employeInfo, opinion];

    const hidePages = () => { pages.forEach((page) => page.style.display = "none"); };

    const showPage = (index) => {
        hidePages();

        if (pages[index]) {
            pages[index].style.display = "block";
            const forms = pages[index].querySelectorAll("form");
            forms.forEach(form => form.reset());

            if (typeof functions[index] === "function") {
                functions[index]();
            }

            localStorage.setItem('activePage', index);
        } else {
            console.error("La page avec l'index", index, "n'existe pas.");
        }
    };

    const activePage = localStorage.getItem('activePage');
    const initialPage = (activePage !== null && !isNaN(activePage) && activePage < pages.length) ? parseInt(activePage) : 0;

    hidePages();
    showPage(initialPage);

    onglets.forEach((onglet, index) => {
        onglet.addEventListener("click", (event) => {
            event.preventDefault();
            showPage(index);
        });
    });
});



export function initEmployeVar() {
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
    let inputPhoto = document.getElementById('photoUpload')
    let inputPass = {
        backPassword: document.getElementById("backPassword"),
        newPassword: document.getElementById("newPassword"),
        confirmPassword: document.getElementById("confirmPassword")
    }

    return { inputInfo, inputPhoto, inputPass };
}


import {
    updateEmployeInfo,
    updatePassword,
    updatePhoto,
    validationOpinion,
    rejectOp,
    getTripInfo,

} from "./employe-update.js";



async function fetchRequest(action) {
    try {
        let request = await fetch(`/Ecoride/src/Router/employeRoute.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ action: action })
        });
        const responseText = await request.text();
        console.log(responseText)
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
                let validOpinion = button.dataset.validOpinion;
                let rejectOpinion = button.dataset.rejectOpinion;
                let tripInfo = button.dataset.tripOpinion;
                switch (action) {
                    case "updateEmployeInfo":
                        functionToCall = updateEmployeInfo;
                        break;
                    case "updatePassword":
                        functionToCall = updatePassword;
                        break;
                    case "updatePhoto":
                        functionToCall = updatePhoto;
                        break;
                    case 'validationOpinion':
                        functionToCall = () => validationOpinion(validOpinion);
                        break;
                    case 'rejectOp':
                        functionToCall = () => rejectOp(rejectOpinion);
                        break;
                    case 'getTripInfo':
                        functionToCall = () => getTripInfo(tripInfo);
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


export async function employeInfo() {
    try {

        const requests = [
            fetchRequest('getEmployeeInfo'),
            fetchRequest('getPhoto')
        ];

        const [employeInfo, employePhoto] = await Promise.all(requests);

        if (employeInfo && employeInfo.status === 'success') {
            const employeInfoData = employeInfo.data;
            let { inputInfo } = initEmployeVar();
            for (let key in employeInfoData) {

                if (inputInfo[key]) {
                    inputInfo[key].value = employeInfoData[key];
                }
            }
        } else {
            console.error('js : Erreur lors de la récupération des données administrateur');
        }

        if (employePhoto) {
            console.log(employePhoto)
            let photoOnglet = document.getElementById('employePhotoOnglet');
            let photoProfile = document.getElementById('employePhotoProfil');
            if (photoOnglet || photoProfile) {
                photoOnglet.src = employePhoto;
                photoProfile.src = employePhoto;
            }
        } else {
            console.log('Erreur : Photo manquante dans la réponse');
        }

        const buttons = {
            updateEmployeInfo: document.getElementById("sendInfo"),
            updatePassword: document.getElementById("sendPassword"),
            updatePhoto: document.getElementById("updatePhoto"),
        };

        setupButton(buttons);

    } catch (error) {
        console.error('Erreur lors de la requête:', error);
    }
}

export async function opinion() {

    let opinionInfo = await fetchRequest('getOpinion')
    const opinionContainer = document.getElementById('opinionContainer');
    const model = opinionContainer.querySelector('.opinionRow');

    const allOpinion = opinionInfo;
    let currentPage = 1;
    const opinionPerPage = 10;

    opinionContainer.innerHTML = '';

    function displayOpinion() {
        opinionContainer.innerHTML = '';

        const startIndex = (currentPage - 1) * opinionPerPage;
        const endIndex = currentPage * opinionPerPage;
        const opinionToDisplay = allOpinion.slice(startIndex, endIndex);

        opinionToDisplay.forEach(opinion => {
            const newDiv = model.cloneNode(true);
            newDiv.querySelector('.opinionRef').textContent = opinion.opinionid;
            newDiv.querySelector('.note').textContent = opinion.note;
            newDiv.querySelector('.message').textContent = opinion.comment;
            newDiv.querySelector('.opinionDate').textContent = opinion.creationdate;

            const validCell = newDiv.querySelector('.opinionvalidation');
            const rejectCell = newDiv.querySelector('.rejectOpinion');
            const tripInfo = newDiv.querySelector('.tripInfo');

            const existingValidButton = validCell.querySelector('.valid-opinion');
            if (!existingValidButton) {
                const validButton = document.createElement('button');
                validButton.textContent = 'Valider';
                validButton.classList.add("valid-opinion");
                validButton.dataset.validOpinion = opinion.opinionid;
                validCell.appendChild(validButton);
                setupButton({ validationOpinion: validButton });
            }


            const existingRejectButton = rejectCell.querySelector('.reject-opinion');
            if (!existingRejectButton) {
                const rejectButton = document.createElement('button');
                rejectButton.textContent = 'Rejeter';
                rejectButton.classList.add("reject-opinion");
                rejectButton.dataset.rejectOpinion = opinion.opinionid;
                rejectCell.appendChild(rejectButton);
                setupButton({ rejectOp: rejectButton });
            }


            const existingTripButton = tripInfo.querySelector('.trip-opinion');
            if (!existingTripButton) {
                const tripButton = document.createElement('button');
                tripButton.textContent = 'Détails';
                tripButton.classList.add("reject-opinion");
                tripButton.dataset.tripOpinion = opinion.opinionid;
                tripInfo.appendChild(tripButton);
                setupButton({ getTripInfo: tripButton });
            }

            opinionContainer.appendChild(newDiv);
        });

        document.getElementById('currentPage').textContent = `Page ${currentPage}`;
    }

    displayOpinion();

    document.getElementById('nextPage').addEventListener('click', () => {
        if (currentPage * opinionPerPage < allOpinion.length) {
            currentPage++;
            displayOpinion();
        }
    });

    document.getElementById('prevPage').addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            displayOpinion();
        }
    });

    const sortOrder = {
        opinionRef: 'asc',
        note: 'asc',
        firstname: 'asc',
        creationdate: 'asc',
        note: 'asc',
    };

    function sortTable(column) {
        sortOrder[column] = sortOrder[column] === 'asc' ? 'desc' : 'asc';
        allOpinion.sort((a, b) => {
            const aValue = a[column].toString().trim();
            const bValue = b[column].toString().trim();

            return sortOrder[column] === 'asc'
                ? aValue.localeCompare(bValue, undefined, { numeric: true })
                : bValue.localeCompare(aValue, undefined, { numeric: true });
        });

        displayOpinion();
    }

    document.getElementById('sortOpinionId').addEventListener('click', () => sortTable('opinionRef'));
    document.getElementById('sortBynote').addEventListener('click', () => sortTable('note'));

}