window.addEventListener('load', () => {
    
    const buttons = document.querySelectorAll('.tab-button');
    const tabs = {
        'receive-message': document.querySelector('.standard-message'),
        'send-message': document.querySelector('.send-message'),
        'messagerie-visiteurs': document.querySelector('.public-message')
    };
    const tabKeys = Object.keys(tabs);
    const functions = [receptionBt, null, visitorMessage];

    const hideTabs = () => {
        Object.values(tabs).forEach(tab => {
            if (tab) tab.style.display = "none";
        });
    };

    const showTab = (index) => {
        hideTabs();

        const key = tabKeys[index];
        const tab = tabs[key];

        if (tab) {
            tab.style.display = "block";

            const forms = tab.querySelectorAll("form");
            forms.forEach(form => form.reset());

            if (typeof functions[index] === "function") {
                functions[index]();
            }

            localStorage.setItem('activeTab', index);
        } else {
            console.error("La tab avec l'index", index, "n'existe pas.");
        }
    };

    const activeTab = localStorage.getItem('activeTab');
    const initialTabIndex = (activeTab !== null && !isNaN(activeTab) && activeTab < tabKeys.length)
        ? parseInt(activeTab)
        : 0;

    showTab(initialTabIndex);

    buttons.forEach((button, index) => {
        button.addEventListener("click", (event) => {
            event.preventDefault();
            showTab(index);
        });
    });
});


async function fetchRequest(action) {
    try {
        let request = await fetch(`/src/Router/messageRouter.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ action: action })
        });
        const response = await request.text();
        console.log(response)

        if (response.type) {
            handleResponse(response);
            return;
        }
        const responseData = JSON.parse(response);
        return responseData;
    } catch (error) {

        console.error("Erreur lors de la récupération de la réponse :", error);
    }
}


/*
// En cours de developpement --------------------

async function deleteMessage(selectedMessageId) {
    try {
        const response = await fetchRequest('deleteMessage', {
            method: 'POST',
            body: JSON.stringify({ data: selectedMessageId }),
            headers: { 'Content-Type': 'application/json' },
        });
        const result = await response.json();
        console.log(result);
        if (result.success) {
            alert("Message supprimé avec succès !");
        } else {
            alert("Erreur : " + result.message);
        }
    } catch (error) {
        console.error('Erreur lors de la suppression du message:', error);
    }
}

*/

async function receptionBt() {
    const messageReceived = await fetchRequest('getMessages');
    const messageList = document.getElementById('ourMessage-list');
    messageList.innerHTML = '';

    messageReceived.forEach((message) => {
        const listItem = document.createElement('li');
        listItem.classList.add('message-item');

        const image = document.createElement('img');
        image.src = message.senderPhoto;
        image.alt = message.senderUsername;
        image.style.width = '30px';

        listItem.innerHTML = `${message.senderUsername} - ${message.object} `;
        listItem.insertBefore(image, listItem.firstChild);
        messageList.appendChild(listItem);

        listItem.addEventListener('click', () => {
            document.getElementById('username').value = message.senderUsername;
            document.getElementById('objet').value = message.object;
            document.getElementById('message-received').value = message.messageText;

            const avatarImg = document.getElementById('avatar-img');
            avatarImg.style.display ='flex'
            avatarImg.src = message.senderPhoto;
            avatarImg.style.width = '50px';

            //const deleteBtn = document.getElementById('deleteReceived');
            //deleteBtn.onclick = () => deleteMessage(message.id);
        });
    });
}



async function visitorMessage() {
    let publicMessage = await fetchRequest('getPublicMessages');

    const messageList = document.getElementById('message-list-visiteurs');
    messageList.innerHTML = '';

    let selectedMessageId = null;

    publicMessage.forEach((message) => {
        const listItem = document.createElement('li');
        listItem.classList.add('message-item');
        
        listItem.innerHTML = `${message.senderId} - ${message.object}`;
        
        messageList.appendChild(listItem);

        listItem.addEventListener('click', () => {
            document.getElementById('nom').value = message.lastName;
            document.getElementById('prenom').value = message.firstName;
            document.getElementById('email').value = message.email;
            document.getElementById('objet-visiteur').value = message.object;
            document.getElementById('message-recu-visiteur').innerText = message.messageText;

            selectedMessageId = message._id.$oid;
        });
    });

   /* const deleteBtn = document.getElementById('deletePublicReceived');
    deleteBtn.onclick = () => {
        if (selectedMessageId) {
            deleteMessage(selectedMessageId);
        } else {
            alert("Aucun message sélectionné.");
        }
    };*/
}
