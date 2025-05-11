"use strict";

window.addEventListener('load', function () {
  var buttons = document.querySelectorAll('.tab-button');
  var tabs = {
    'receive-message': document.querySelector('.standard-message'),
    'send-message': document.querySelector('.send-message'),
    'messagerie-visiteurs': document.querySelector('.public-message')
  };
  var tabKeys = Object.keys(tabs);
  var functions = [receptionBt, null, visitorMessage];

  var hideTabs = function hideTabs() {
    Object.values(tabs).forEach(function (tab) {
      if (tab) tab.style.display = "none";
    });
  };

  var showTab = function showTab(index) {
    hideTabs();
    var key = tabKeys[index];
    var tab = tabs[key];

    if (tab) {
      tab.style.display = "block";
      var forms = tab.querySelectorAll("form");
      forms.forEach(function (form) {
        return form.reset();
      });

      if (typeof functions[index] === "function") {
        functions[index]();
      }

      localStorage.setItem('activeTab', index);
    } else {
      console.error("La tab avec l'index", index, "n'existe pas.");
    }
  };

  var activeTab = localStorage.getItem('activeTab');
  var initialTabIndex = activeTab !== null && !isNaN(activeTab) && activeTab < tabKeys.length ? parseInt(activeTab) : 0;
  showTab(initialTabIndex);
  buttons.forEach(function (button, index) {
    button.addEventListener("click", function (event) {
      event.preventDefault();
      showTab(index);
    });
  });
});

function fetchRequest(action) {
  var request, response, responseData;
  return regeneratorRuntime.async(function fetchRequest$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          _context.prev = 0;
          _context.next = 3;
          return regeneratorRuntime.awrap(fetch("/Ecoride/src/Router/messageRouter.php", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              action: action
            })
          }));

        case 3:
          request = _context.sent;
          _context.next = 6;
          return regeneratorRuntime.awrap(request.text());

        case 6:
          response = _context.sent;
          console.log(response);

          if (!response.type) {
            _context.next = 11;
            break;
          }

          handleResponse(response);
          return _context.abrupt("return");

        case 11:
          responseData = JSON.parse(response);
          return _context.abrupt("return", responseData);

        case 15:
          _context.prev = 15;
          _context.t0 = _context["catch"](0);
          console.error("Erreur lors de la récupération de la réponse :", _context.t0);

        case 18:
        case "end":
          return _context.stop();
      }
    }
  }, null, null, [[0, 15]]);
}
/*
// En cours de developpement 

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


function receptionBt() {
  var messageReceived, messageList;
  return regeneratorRuntime.async(function receptionBt$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          _context2.next = 2;
          return regeneratorRuntime.awrap(fetchRequest('getMessages'));

        case 2:
          messageReceived = _context2.sent;
          messageList = document.getElementById('ourMessage-list');
          messageList.innerHTML = '';
          messageReceived.forEach(function (message) {
            var listItem = document.createElement('li');
            listItem.classList.add('message-item');
            var image = document.createElement('img');
            image.src = message.senderPhoto;
            image.alt = message.senderUsername;
            image.style.width = '30px';
            listItem.innerHTML = "".concat(message.senderUsername, " - ").concat(message.object, " ");
            listItem.insertBefore(image, listItem.firstChild);
            messageList.appendChild(listItem);
            listItem.addEventListener('click', function () {
              document.getElementById('username').value = message.senderUsername;
              document.getElementById('objet').value = message.object;
              document.getElementById('message-received').value = message.messageText;
              var avatarImg = document.getElementById('avatar-img');
              avatarImg.style.display = 'flex';
              avatarImg.src = message.senderPhoto;
              avatarImg.style.width = '50px'; //const deleteBtn = document.getElementById('deleteReceived');
              //deleteBtn.onclick = () => deleteMessage(message.id);
            });
          });

        case 6:
        case "end":
          return _context2.stop();
      }
    }
  });
}

function visitorMessage() {
  var publicMessage, messageList, selectedMessageId;
  return regeneratorRuntime.async(function visitorMessage$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          _context3.next = 2;
          return regeneratorRuntime.awrap(fetchRequest('getPublicMessages'));

        case 2:
          publicMessage = _context3.sent;
          messageList = document.getElementById('message-list-visiteurs');
          messageList.innerHTML = '';
          selectedMessageId = null;
          publicMessage.forEach(function (message) {
            var listItem = document.createElement('li');
            listItem.classList.add('message-item');
            listItem.innerHTML = "".concat(message.senderId, " - ").concat(message.object);
            messageList.appendChild(listItem);
            listItem.addEventListener('click', function () {
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

        case 7:
        case "end":
          return _context3.stop();
      }
    }
  });
}