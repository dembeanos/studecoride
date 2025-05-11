window.addEventListener("load", () => {
    const onglets = document.querySelectorAll(".onglets");
    const pages = document.querySelectorAll("section");
    const functions = [adminInfo, users, employe, tripStat, moneyStat, errorLog];

    const hidePages = () => { pages.forEach((page) => page.style.display = "none"); };

    const showPage = (index) => {
        hidePages();
        pages[index].style.display = "block";
        const forms = pages[index].querySelectorAll("form");
        forms.forEach(form => form.reset());

        if (typeof functions[index] === "function") {
            functions[index]();
        }

        localStorage.setItem('activePage', index);
    };
    const activePage = localStorage.getItem('activePage');
    const initialPage = activePage ? parseInt(activePage) : 0

    hidePages()
    showPage(initialPage)

    onglets.forEach((onglet, index) => {
        onglet.addEventListener("click", (event) => {
            event.preventDefault();
            showPage(index);;
        });
    });
});


export function initAdminVar() {
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
    let addUserInfo ={
        firstName: document.getElementById("addUserFirstName"),
        lastName: document.getElementById("addUserLastName"),
        username : document.getElementById("addUserUsername"),
        password : document.getElementById("addUserPassword"),
        confirmPassword : document.getElementById("addUserConfirmPassword"),
        phone: document.getElementById("addUserPhone"),
        email: document.getElementById("addUserEmail"),
        road: document.getElementById("addUserRoad"),
        roadComplement: document.getElementById("addUserRoadComplement"),
        zipCode: document.getElementById("addUserZipCode"),
        city: document.getElementById("addUserCity"),
        role: document.getElementById("role"),
        subscribeButton: document.getElementById("subscribe"),

    }
    let inputPhoto = document.getElementById('photoUpload')
    let inputPass = {
        backPassword: document.getElementById("backPassword"),
        newPassword: document.getElementById("newPassword"),
        confirmPassword: document.getElementById("confirmPassword")
    }

    return { inputInfo, addUserInfo, inputPhoto, inputPass };
}


import {
    updateAdminInfo,
    updatePassword,
    updatePhoto,
    updateUser,
    updateEmploye,
    banUser,
} from "./admin-update.js";



async function fetchRequest(action) {
    try {
        let request = await fetch(`/Ecoride/src/Router/adminRoute.php`, {
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
                let banId = button.dataset.banId;
                let employeId = button.dataset.employeId;
                switch (action) {
                    case "updateAdminInfo":
                        functionToCall = updateAdminInfo;
                        break;
                    case "updatePassword":
                        functionToCall = updatePassword;
                        break;
                    case "updatePhoto":
                        functionToCall = updatePhoto;
                        break;
                    case "updateUser":
                        functionToCall = () => updateUser(banId);
                        break;
                    case "updateEmploye":
                        functionToCall = () => updateEmploye(employeId);
                        break;
                    case "banUser":
                        functionToCall = () => banUser(banId);
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


export async function adminInfo() {
    try {

        const requests = [
            fetchRequest('getAdminInfo'),
            fetchRequest('getPhoto')
        ];

        const [adminInfo, adminPhoto] = await Promise.all(requests);

        if (adminInfo && adminInfo.status === 'success') {
            const adminInfoData = adminInfo.data;
            let { inputInfo } = initAdminVar();
            for (let key in adminInfoData) {

                if (inputInfo[key]) {
                    inputInfo[key].value = adminInfoData[key];
                }
            }
        } else {
            console.error('js : Erreur lors de la récupération des données administrateur');
        }

        if (adminPhoto) {
            console.log(adminPhoto)
            let photoOnglet = document.getElementById('adminPhotoOnglet');
            let photoProfile = document.getElementById('adminPhotoProfil');
            if (photoOnglet || photoProfile) {
                photoOnglet.src = adminPhoto;
                photoProfile.src = adminPhoto;
            }
        } else {
            console.log('Erreur : Photo manquante dans la réponse');
        }

        const buttons = {
            updateAdminInfo: document.getElementById("sendInfo"),
            updatePassword: document.getElementById("sendPassword"),
            updatePhoto: document.getElementById("updatePhoto"),
        };

        setupButton(buttons);

    } catch (error) {
        console.error('Erreur lors de la requête:', error);
    }
}



async function users() {
    let usersInfo = await fetchRequest('getUsers');
    const userContainer = document.getElementById('usersContainer');
    const model = userContainer.querySelector('.userRow');

    const allUsers = usersInfo;
    let currentPage = 1;
    const usersPerPage = 10;

    userContainer.innerHTML = '';

    function displayUsers() {
        userContainer.innerHTML = '';

        const startIndex = (currentPage - 1) * usersPerPage;
        const endIndex = currentPage * usersPerPage;
        const usersToDisplay = allUsers.slice(startIndex, endIndex);

        usersToDisplay.forEach(user => {
            const newDiv = model.cloneNode(true);
            newDiv.querySelector('.usersRef').textContent = user.userid;
            newDiv.querySelector('.firstname').textContent = user.firstname;
            newDiv.querySelector('.lastname').textContent = user.lastname;
            newDiv.querySelector('.usersPhone').textContent = user.phone;
            newDiv.querySelector('.usersRoad').textContent = user.road;
            newDiv.querySelector('.zipcode').textContent = user.zipcode;
            newDiv.querySelector('.usersCity').textContent = user.city;
            newDiv.querySelector('.usersCreationDate').textContent = user.creationdate;
            newDiv.querySelector('.usersNote').textContent = user.note;
            newDiv.querySelector('.usersRole').textContent = user.userrole;
            newDiv.querySelector('.usersCredit').textContent = user.credit;

            const actionCell = newDiv.querySelector('.usersAction');

            const existingBanButton = actionCell.querySelector('.ban-user');
            if (!existingBanButton) {
                const banButton = document.createElement('button');
                banButton.textContent = 'Bannir';
                banButton.classList.add("ban-user");
                banButton.dataset.banId = user.idlogin;
                actionCell.appendChild(banButton);
                setupButton({ banUser: banButton });
            }

            userContainer.appendChild(newDiv);
        });

        document.getElementById('currentPage').textContent = `Page ${currentPage}`;
    }

    displayUsers();

    document.getElementById('nextPage').addEventListener('click', () => {
        if (currentPage * usersPerPage < allUsers.length) {
            currentPage++;
            displayUsers();
        }
    });

    document.getElementById('prevPage').addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            displayUsers();
        }
    });

    const sortOrder = {
        userid: 'asc',
        lastname: 'asc',
        firstname: 'asc',
        creationdate: 'asc',
        note: 'asc',
    };

    function sortTable(column) {
        sortOrder[column] = sortOrder[column] === 'asc' ? 'desc' : 'asc';
        allUsers.sort((a, b) => {
            const aValue = a[column].toString().trim();
            const bValue = b[column].toString().trim();

            return sortOrder[column] === 'asc'
                ? aValue.localeCompare(bValue, undefined, { numeric: true })
                : bValue.localeCompare(aValue, undefined, { numeric: true });
        });

        displayUsers();
    }

    document.getElementById('sortUserid').addEventListener('click', () => sortTable('userid'));
    document.getElementById('sortLastname').addEventListener('click', () => sortTable('lastname'));
    document.getElementById('sortFirstname').addEventListener('click', () => sortTable('firstname'));
    document.getElementById('sortCreationDate').addEventListener('click', () => sortTable('creationdate'));
    document.getElementById('sortNote').addEventListener('click', () => sortTable('note'));
}


export async function employe() {
    let {addUserInfo} = initAdminVar();

    addUserInfo.subscribeButton.addEventListener('click', e => {
        e.preventDefault(); 

        let formData = {
            lastName: addUserInfo.lastName.value,
            firstName: addUserInfo.firstName.value,
            email: addUserInfo.email.value,
            username: addUserInfo.username.value,
            password: addUserInfo.password.value,
            confirmPassword:addUserInfo.confirmPassword.value,
            phone: addUserInfo.phone.value,
            road: addUserInfo.road.value,
            roadComplement: addUserInfo.roadComplement.value,
            zipCode: addUserInfo.zipCode.value,
            city: addUserInfo.city.value,
            userType: addUserInfo.role.value
        };

        fetchRequest(formData);
        console.log('Données envoyées par js :', formData)

        async function fetchRequest(formData) {
            try {
                let request = await fetch('/Ecoride/src/Router/subscribeRoute.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ formData: formData })
                });
        
                const responseText = await request.text();
                console.log(responseText)
        
                const responseData = JSON.parse(responseText);
        
                handleResponse(responseData);
    
            } catch (error) {
                console.error("Erreur lors de la récupération de la réponse :", error);
                handleResponse(error);
            }
        }
    });

        

        
    let employeeInfo = await fetchRequest('getEmployees')

    const employeContainer = document.getElementById('employeContainer');
    const model = employeContainer.querySelector('.employeRow');

    const allemploye = employeeInfo;
    let currentPage = 1;
    const employePerPage = 10;

    employeContainer.innerHTML = '';

    function displayUsers() {
        employeContainer.innerHTML = '';

        const startIndex = (currentPage - 1) * employePerPage;
        const endIndex = currentPage * employePerPage;
        const employeToDisplay = allemploye.slice(startIndex, endIndex);

        employeToDisplay.forEach(employe => {
            const newDiv = model.cloneNode(true);
            newDiv.querySelector('.EmployeRef').textContent = employe.employeid;
            newDiv.querySelector('.employefirstname').textContent = employe.firstname;
            newDiv.querySelector('.employelastname').textContent = employe.lastname;
            newDiv.querySelector('.employePhone').textContent = employe.phone;
            newDiv.querySelector('.employeRoad').textContent = employe.road;
            newDiv.querySelector('.employezipcode').textContent = employe.zipcode;
            newDiv.querySelector('.employeCity').textContent = employe.city;
            newDiv.querySelector('.employeCreationDate').textContent = employe.creationdate;

            const actionCell = newDiv.querySelector('.employeAction');

            const existingBanButton = actionCell.querySelector('.ban-user');
            if (!existingBanButton) {
                const banButton = document.createElement('button');
                banButton.textContent = 'Bannir';
                banButton.classList.add("ban-user");
                banButton.dataset.banId = employe.idlogin;
                actionCell.appendChild(banButton);
                setupButton({ banUser: banButton });
            }

            employeContainer.appendChild(newDiv);
        });

        document.getElementById('employeCurrentPage').textContent = `Page ${currentPage}`;
    }

    displayUsers();

    document.getElementById('employeNextPage').addEventListener('click', () => {
        if (currentPage * employePerPage < allemploye.length) {
            currentPage++;
            displayUsers();
        }
    });

    document.getElementById('employePrevPage').addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            displayUsers();
        }
    });

    const sortOrder = {
        employeid: 'asc',
        lastname: 'asc',
        firstname: 'asc',
        creationdate: 'asc',
    };

    function sortTable(column) {
        sortOrder[column] = sortOrder[column] === 'asc' ? 'desc' : 'asc';
        allemploye.sort((a, b) => {
            const aValue = a[column].toString().trim();
            const bValue = b[column].toString().trim();

            return sortOrder[column] === 'asc'
                ? aValue.localeCompare(bValue, undefined, { numeric: true })
                : bValue.localeCompare(aValue, undefined, { numeric: true });
        });

        displayUsers();
    }

    document.getElementById('sortEmployeid').addEventListener('click', () => sortTable('employeid'));
    document.getElementById('sortEmployeLastname').addEventListener('click', () => sortTable('lastname'));
    document.getElementById('sortEmployeFirstname').addEventListener('click', () => sortTable('firstname'));
    document.getElementById('sortEmployeCreationDate').addEventListener('click', () => sortTable('creationdate'));





    };


















export async function tripStat() {
    let trendsInfo = await fetchRequest('getTrends');
    let totalInfo = document.getElementById('totalOffer');

    let dailyData = {};
    let total = 0;

    const currentYear = new Date().getFullYear();

    trendsInfo.forEach(item => {
        const date = new Date(item.datedepart);

        if (date.getFullYear() === currentYear) {
            const dayKey = `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()}`;
            total ++;
            if (dailyData[dayKey]) {
                dailyData[dayKey] += 1;
            } else {
                dailyData[dayKey] = 1;
                
            }
        }
    });

    totalInfo.textContent = `Total de covoiturages sur l'année : ${total}`;

    function formatDate(dateStr) {
        const [year, month, day] = dateStr.split('-');
        return `${parseInt(day)}-${parseInt(month)}-${year}`;
    }

    const labels = Object.keys(dailyData).map(date => formatDate(date)); 
    const data = Object.values(dailyData);

    const config = {
        type: 'bar', 
        data: {
            labels: labels, 
            datasets: [{
                label: `Covoiturages en ${currentYear}`,
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    const ctx = document.getElementById('offerChart').getContext('2d');
    new Chart(ctx, config);
}



export async function moneyStat() {
    let profitInfo = await fetchRequest('getProfit');
    let totalInfo = document.getElementById('totalCA');
    let dailyData = {};
    let total = 0;

    const currentYear = new Date().getFullYear();

    if (window.myChart) {
        window.myChart.destroy(); 
    }

    profitInfo.forEach(item => {
        const date = new Date(item.creationdate);

        if (date.getFullYear() === currentYear) {
            const isoDate = date.toISOString().split('T')[0];
            const credit = parseFloat(item.credit);
            total += credit;

            if (!dailyData[isoDate]) {
                dailyData[isoDate] = 0;
            }
            dailyData[isoDate] += credit;
        }
    });

    totalInfo.textContent = `Total CA : ${total.toFixed(2)} €`;

    function formatDate(dateStr) {
        const [year, month, day] = dateStr.split('-');
        return `${parseInt(day)}-${parseInt(month)}-${year}`;
    }

    const labels = Object.keys(dailyData).map(date => formatDate(date)); 
    const data = Object.values(dailyData);

    const config = {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: `Crédits gagnés par jour en ${currentYear}`,
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                },
                x: {
                    ticks: {
                        maxRotation: 90,
                        minRotation: 45
                    }
                }
            }
        }
    };

    const ctx = document.getElementById('profitChart').getContext('2d');
    new Chart(ctx, config);
}






export async function errorLog() {
    const logInfo = await fetchRequest('getLogs');
    const logContainer = document.getElementById('logContainer');
    const model = logContainer.querySelector('.logRow');

    model.style.display = 'none';

    const allLog = logInfo;
    let currentPage = 1;
    const logPerPage = 10;

    const sortOrder = {
        timestamp: 'asc',
        loglevel: 'asc',
    };

    function displayLog() {
        logContainer.innerHTML = '';

        const startIndex = (currentPage - 1) * logPerPage;
        const endIndex = currentPage * logPerPage;
        const logToDisplay = allLog.slice(startIndex, endIndex);

        logToDisplay.forEach(log => {
            const newDiv = model.cloneNode(true);
            newDiv.style.display = 'flex';
            newDiv.querySelector('.logDate').textContent = log.timestamp;
            newDiv.querySelector('.message').textContent = log.message;
            newDiv.querySelector('.loglevel').textContent = log.loglevel;
            logContainer.appendChild(newDiv);
        });

        const currentPageLabel = document.getElementById('currentLogPage');
        if (currentPageLabel) {
            currentPageLabel.textContent = `Page ${currentPage}`;
        }
    }

    function sortTable(column) {
        const key = column === 'date' ? 'timestamp' : column;
        sortOrder[key] = sortOrder[key] === 'asc' ? 'desc' : 'asc';

        allLog.sort((a, b) => {
            const aValue = a[key].toString().trim();
            const bValue = b[key].toString().trim();
            return sortOrder[key] === 'asc'
                ? aValue.localeCompare(bValue, undefined, { numeric: true })
                : bValue.localeCompare(aValue, undefined, { numeric: true });
        });

        displayLog();
    }

    // Navigation
    const nextBtn = document.getElementById('nextLogPage');
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (currentPage * logPerPage < allLog.length) {
                currentPage++;
                displayLog();
            }
        });
    }

    const prevBtn = document.getElementById('prevLogPage');
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                displayLog();
            }
        });
    }

    // Tri
    const sortDateBtn = document.getElementById('sortLogDate');
    if (sortDateBtn) {
        sortDateBtn.addEventListener('click', () => sortTable('date'));
    }

    const sortLevelBtn = document.getElementById('sortLogLevel');
    if (sortLevelBtn) {
        sortLevelBtn.addEventListener('click', () => sortTable('loglevel'));
    }

    displayLog();
}
