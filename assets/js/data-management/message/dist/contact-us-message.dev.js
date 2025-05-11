"use strict";

window.addEventListener('load', function () {
  var objectPurpose = document.getElementById('object');
  var objectPerso = document.getElementById('customTitle');
  var divOther = document.getElementById('otherObject');
  var firstName = document.getElementById('firstName');
  var lastName = document.getElementById('lastName');
  var email = document.getElementById('email');
  var message = document.getElementById('message');
  var sendButton = document.getElementById('send'); // Affiche le champ custom si "autre" est sélectionné

  objectPurpose.addEventListener('change', function () {
    if (objectPurpose.value === 'autre') {
      divOther.style.display = 'block';
      objectPurpose.style.display = 'none';
    } else {
      objectPerso.style.display = 'none';
    }
  });
  sendButton.addEventListener('click', function _callee(event) {
    var finalObject, data, response, json;
    return regeneratorRuntime.async(function _callee$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            event.preventDefault(); // On choisit l'objet final selon la sélection

            finalObject = objectPurpose.value === 'autre' ? objectPerso.value : objectPurpose.value;
            data = {
              object: finalObject,
              lastName: lastName.value,
              firstName: firstName.value,
              email: email.value,
              messageText: message.value
            };
            _context.prev = 3;
            _context.next = 6;
            return regeneratorRuntime.awrap(fetch("Ecoride/src/Router/routeurMessage.php", {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                action: 'sendPublicMessage',
                data: data
              })
            }));

          case 6:
            response = _context.sent;
            _context.next = 9;
            return regeneratorRuntime.awrap(response.json());

          case 9:
            json = _context.sent;

            if (json.type) {
              handleResponse(json);
            }

            _context.next = 16;
            break;

          case 13:
            _context.prev = 13;
            _context.t0 = _context["catch"](3);
            console.error("Erreur lors de la récupération de la réponse :", _context.t0);

          case 16:
            objectPerso.style.display = 'none';
            divOther.style.display = 'none';
            objectPerso.value = '';
            objectPurpose.value = '';
            objectPurpose.style.display = 'block';
            firstName.value = '';
            lastName.value = '';
            email.value = '';
            message.value = '';

          case 25:
          case "end":
            return _context.stop();
        }
      }
    }, null, null, [[3, 13]]);
  });
});