document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('dest-username');
    const userSuggestionBox = document.getElementById('userSuggestionBox');
    let selectedUsername = '';

    search.addEventListener('input', async function () {
        const userSearch = search.value.trim();

        if (userSearch === '') {
            userSuggestionBox.innerHTML = '';
            userSuggestionBox.style.display = 'none';
            return;
        }

        const result = await fetchRequest('searchUser', userSearch);
        if (!result) return;

        if (result.type) return handleResponse(result);

        const users = typeof result === 'string' ? JSON.parse(result) : result;
        displaySuggestions(users);
    });

    function displaySuggestions(users) {
        userSuggestionBox.innerHTML = '';

        if (users.length === 0) {
            userSuggestionBox.style.display = 'none';
            return;
        }

        userSuggestionBox.style.display = 'block';

        users.forEach(user => {
            const div = document.createElement('div');
            div.classList.add('suggestion-item');

            if (user.photo) {
                const img = document.createElement('img');
                img.src = 'data:image/jpeg;base64,' + user.photo;
                img.onerror = () => img.src = ''
                div.appendChild(img);
            }

            const username = document.createElement('span');
            username.textContent = user.username;
            div.appendChild(username);

            div.addEventListener('click', function () {
                selectedUsername = user.username;
                search.value = selectedUsername;
                userSuggestionBox.innerHTML = '';
                userSuggestionBox.style.display = 'none';
            });

            userSuggestionBox.appendChild(div);
        });
    }

   //---------------------------------Mehode appel backend---------------------------------
    async function fetchRequest(action, data) {
        try {
            console.log('Données envoyées :', data);
            const response = await fetch('/Ecoride/src/Router/messageRouter.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: action,
                    data: data
                })
            });
    
            if (!response.ok) throw new Error(`Erreur HTTP : ${response.status}`);
    
            const json = await response.json();
            if (json.type){
                handleResponse(json)
            }
            return json;
        } catch (error) {
            console.error('Erreur fetch:', error);
            return null;
        }
    }
    


  //------------------------------------------------Fonction gestion d'envoi du message-----------------------------------------------

    let sendButton = document.getElementById('send-button');

    sendButton.addEventListener('click', async function (event) {
        event.preventDefault();

        let object = document.getElementById('send-objet');
        let message = document.getElementById('send-message');
       

        if (!selectedUsername) {
            handleResponse({type: 'user_error', 
                            message:'Veuillez sélectionner un destinataire avant d\'envoyer un message.',
                            target:'dest-username'});
            return;
        }

        let data = {
            username: selectedUsername,
            object: object.value,
            messageText: message.value,
        };

            let updatedata = await fetchRequest('sendMessage', data);

            if (updatedata && updatedata.success) {
                handleResponse({type: 'popup', 
                    message:'Message envoyé avec succès'});
            } else {
                console.log('Échec de l\'envoi du message');
            }
        
    });
});
