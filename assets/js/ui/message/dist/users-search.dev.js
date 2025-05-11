"use strict";

document.addEventListener('DOMContentLoaded', function () {
  var search = document.getElementById('dest-username');
  var userSuggestionBox = document.getElementById('userSuggestionBox');
  var selectedUsername = '';
  search.addEventListener('input', function _callee() {
    var userSearch, result, users;
    return regeneratorRuntime.async(function _callee$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            userSearch = search.value.trim();

            if (!(userSearch === '')) {
              _context.next = 5;
              break;
            }

            userSuggestionBox.innerHTML = '';
            userSuggestionBox.style.display = 'none';
            return _context.abrupt("return");

          case 5:
            _context.next = 7;
            return regeneratorRuntime.awrap(fetchRequest('searchUser', userSearch));

          case 7:
            result = _context.sent;

            if (result) {
              _context.next = 10;
              break;
            }

            return _context.abrupt("return");

          case 10:
            if (!result.type) {
              _context.next = 12;
              break;
            }

            return _context.abrupt("return", handleResponse(result));

          case 12:
            users = typeof result === 'string' ? JSON.parse(result) : result;
            displaySuggestions(users);

          case 14:
          case "end":
            return _context.stop();
        }
      }
    });
  });

  function displaySuggestions(users) {
    userSuggestionBox.innerHTML = '';

    if (users.length === 0) {
      userSuggestionBox.style.display = 'none';
      return;
    }

    userSuggestionBox.style.display = 'block';
    users.forEach(function (user) {
      var div = document.createElement('div');
      div.classList.add('suggestion-item');

      if (user.photo) {
        var img = document.createElement('img');
        img.src = 'data:image/jpeg;base64,' + user.photo;

        img.onerror = function () {
          return img.src = '';
        };

        div.appendChild(img);
      }

      var username = document.createElement('span');
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
  } //---------------------------------Mehode appel backend---------------------------------


  function fetchRequest(action, data) {
    var response, json;
    return regeneratorRuntime.async(function fetchRequest$(_context2) {
      while (1) {
        switch (_context2.prev = _context2.next) {
          case 0:
            _context2.prev = 0;
            console.log('Données envoyées :', data);
            _context2.next = 4;
            return regeneratorRuntime.awrap(fetch('/Ecoride/src/Router/messageRouter.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                action: action,
                data: data
              })
            }));

          case 4:
            response = _context2.sent;

            if (response.ok) {
              _context2.next = 7;
              break;
            }

            throw new Error("Erreur HTTP : ".concat(response.status));

          case 7:
            _context2.next = 9;
            return regeneratorRuntime.awrap(response.json());

          case 9:
            json = _context2.sent;

            if (json.type) {
              handleResponse(json);
            }

            return _context2.abrupt("return", json);

          case 14:
            _context2.prev = 14;
            _context2.t0 = _context2["catch"](0);
            console.error('Erreur fetch:', _context2.t0);
            return _context2.abrupt("return", null);

          case 18:
          case "end":
            return _context2.stop();
        }
      }
    }, null, null, [[0, 14]]);
  } //------------------------------------------------Fonction gestion d'envoi du message-----------------------------------------------


  var sendButton = document.getElementById('send-button');
  sendButton.addEventListener('click', function _callee2(event) {
    var object, message, data, updatedata;
    return regeneratorRuntime.async(function _callee2$(_context3) {
      while (1) {
        switch (_context3.prev = _context3.next) {
          case 0:
            event.preventDefault();
            object = document.getElementById('send-objet');
            message = document.getElementById('send-message');

            if (selectedUsername) {
              _context3.next = 6;
              break;
            }

            handleResponse({
              type: 'user_error',
              message: 'Veuillez sélectionner un destinataire avant d\'envoyer un message.',
              target: 'dest-username'
            });
            return _context3.abrupt("return");

          case 6:
            data = {
              username: selectedUsername,
              object: object.value,
              messageText: message.value
            };
            _context3.next = 9;
            return regeneratorRuntime.awrap(fetchRequest('sendMessage', data));

          case 9:
            updatedata = _context3.sent;

            if (updatedata && updatedata.success) {
              handleResponse({
                type: 'popup',
                message: 'Message envoyé avec succès'
              });
            } else {
              console.log('Échec de l\'envoi du message');
            }

          case 11:
          case "end":
            return _context3.stop();
        }
      }
    });
  });
});