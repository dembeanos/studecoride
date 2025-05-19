    window.addEventListener('load', () => {
      const buttons = document.querySelectorAll('.tab-button');
      const tabs = {
        'receive-message': document.querySelector('.standard-message'),
        'send-message': document.querySelector('.send-message'),
        'messagerie-visiteurs': document.querySelector('.public-message')
      };
      const tabKeys = Object.keys(tabs);
      // On associe chaque onglet à sa fonction (null si pas besoin d'appeler)
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
          tab.style.display = "grid"; // on souhaite la mise en grille pour Boîte et Public
          // Réinitialiser les formulaires à chaque affichage
          const forms = tab.querySelectorAll("form");
          forms.forEach(form => form.reset());

          // Appeler la fonction si elle existe
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
        const request = await fetch(`/src/Router/messageRouter.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: action })
        });
        const responseText = await request.text();
        console.log('Réponse brute du serveur →', responseText);

        // Si le serveur renvoie un objet { type: ... }, on suppose que c'est une réponse d'erreur ou info
        const possibleJson = JSON.parse(responseText);
        if (possibleJson.type) {
          handleResponse(possibleJson);
          return;
        }
        // Sinon on retourne le JSON converti en tableau ou objet
        return possibleJson;
      } catch (error) {
        console.error("Erreur lors de la récupération de la réponse :", error);
      }
    }

    // 3. Récupérer et afficher les messages reçus (Boîte de réception)
    async function receptionBt() {
      const messageReceived = await fetchRequest('getMessages');
      const messageList = document.getElementById('ourMessage-list');
      messageList.innerHTML = '';

      if (!Array.isArray(messageReceived)) {
        console.error('La réponse de getMessages n’est pas un tableau :', messageReceived);
        return;
      }

      messageReceived.forEach((message) => {
        const listItem = document.createElement('li');
        listItem.classList.add('message-item');

        const image = document.createElement('img');
        image.src = message.senderPhoto;
        image.alt = message.senderUsername;
        image.style.width = '30px';
        image.style.height = '30px';
        image.style.borderRadius = '50%';
        image.style.objectFit = 'cover';
        image.style.marginRight = '0.5rem';

        listItem.innerHTML = `${message.senderUsername} - ${message.object}`;
        listItem.insertBefore(image, listItem.firstChild);
        messageList.appendChild(listItem);

        listItem.addEventListener('click', () => {

          document.getElementById('username').value = message.senderUsername;
          document.getElementById('objet').value = message.object;
          document.getElementById('message-received').value = message.messageText;

          const avatarImg = document.getElementById('avatar-img');
          avatarImg.style.display = 'block';
          avatarImg.src = message.senderPhoto;
          avatarImg.style.width = '50px';
          avatarImg.style.height = '50px';
        });
      });
    }

    // 4. Récupérer et afficher les messages visiteurs (pour admin/employé)
    async function visitorMessage() {
      const publicMessage = await fetchRequest('getPublicMessages');
      const messageList = document.getElementById('message-list-visiteurs');
      messageList.innerHTML = '';

      if (!Array.isArray(publicMessage)) {
        console.error('La réponse de getPublicMessages n’est pas un tableau :', publicMessage);
        return;
      }

      publicMessage.forEach((message) => {
        const listItem = document.createElement('li');
        listItem.classList.add('message-item');
        listItem.innerHTML = `${message.lastName} ${message.firstName} - ${message.object}`;
        messageList.appendChild(listItem);

        listItem.addEventListener('click', () => {
          document.getElementById('nom').value = message.lastName;
          document.getElementById('prenom').value = message.firstName;
          document.getElementById('email').value = message.email;
          document.getElementById('objet-visiteur').value = message.object;
          document.getElementById('message-recu-visiteur').value = message.messageText;
        });
      });
    }