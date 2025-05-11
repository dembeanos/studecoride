"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.updateAdminInfo = updateAdminInfo;
exports.updatePassword = updatePassword;
exports.updatePhoto = updatePhoto;
exports.updateUser = updateUser;
exports.updateEmploye = updateEmploye;
exports.updateOpinion = updateOpinion;
exports.banUser = banUser;

var _adminManager = require("./admin-manager.js");

function fetchRequestUpdate(action, data) {
  var response, json;
  return regeneratorRuntime.async(function fetchRequestUpdate$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          _context.prev = 0;
          _context.next = 3;
          return regeneratorRuntime.awrap(fetch("/Ecoride/src/Router/adminRoute.php", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              action: action,
              data: data
            })
          }));

        case 3:
          response = _context.sent;

          if (response.ok) {
            _context.next = 6;
            break;
          }

          throw new Error("Erreur HTTP: ".concat(response.status));

        case 6:
          _context.next = 8;
          return regeneratorRuntime.awrap(response.json());

        case 8:
          json = _context.sent;

          if (typeof json.data === 'string') {
            try {
              json.data = JSON.parse(json.data);
            } catch (e) {
              console.warn("Données déjà parsées ou mal formées :", json.data);
            }
          }

          handleResponse(json);
          _context.next = 16;
          break;

        case 13:
          _context.prev = 13;
          _context.t0 = _context["catch"](0);
          console.error("Erreur lors de la récupération de la réponse :", _context.t0.text);

        case 16:
        case "end":
          return _context.stop();
      }
    }
  }, null, null, [[0, 13]]);
}

function updateAdminInfo() {
  var _initAdminVar, inputInfo, data;

  return regeneratorRuntime.async(function updateAdminInfo$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          _initAdminVar = (0, _adminManager.initAdminVar)(), inputInfo = _initAdminVar.inputInfo;
          data = {
            firstName: inputInfo.firstname.value,
            lastName: inputInfo.lastname.value,
            phone: inputInfo.phone.value,
            email: inputInfo.email.value,
            road: inputInfo.road.value,
            roadComplement: inputInfo.roadcomplement.value,
            zipCode: inputInfo.zipcode.value,
            city: inputInfo.city.value
          };
          _context2.next = 4;
          return regeneratorRuntime.awrap(fetchRequestUpdate('updateAdminInfo', data));

        case 4:
        case "end":
          return _context2.stop();
      }
    }
  });
}

function updatePassword() {
  var _initAdminVar2, inputPass, data, updateResponse;

  return regeneratorRuntime.async(function updatePassword$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          _initAdminVar2 = (0, _adminManager.initAdminVar)(), inputPass = _initAdminVar2.inputPass;
          data = {
            backPassword: inputPass.backPassword.value,
            newPassword: inputPass.newPassword.value,
            confirmPassword: inputPass.confirmPassword.value
          };
          _context3.next = 4;
          return regeneratorRuntime.awrap(fetchRequestUpdate('updatePassword', data));

        case 4:
          updateResponse = _context3.sent;

          if (!updateResponse) {
            _context3.next = 7;
            break;
          }

          return _context3.abrupt("return", updatemessage(updateResponse));

        case 7:
        case "end":
          return _context3.stop();
      }
    }
  });
}

function updatePhoto() {
  var _initAdminVar3, inputPhoto, photo, formData, response, responseText, updateResponse;

  return regeneratorRuntime.async(function updatePhoto$(_context4) {
    while (1) {
      switch (_context4.prev = _context4.next) {
        case 0:
          _initAdminVar3 = (0, _adminManager.initAdminVar)(), inputPhoto = _initAdminVar3.inputPhoto;
          photo = inputPhoto.files[0];

          if (!photo) {
            _context4.next = 22;
            break;
          }

          _context4.prev = 3;
          formData = new FormData();
          formData.append('action', 'updatePhoto');
          formData.append('updatePhoto', photo);
          _context4.next = 9;
          return regeneratorRuntime.awrap(fetch('/Ecoride/src/Profile/Admin/adminRequestRoute.php', {
            method: 'POST',
            body: formData
          }));

        case 9:
          response = _context4.sent;
          _context4.next = 12;
          return regeneratorRuntime.awrap(response.text());

        case 12:
          responseText = _context4.sent;
          console.log("Réponse brute du serveur:", responseText); // Ajoute un log pour afficher la réponse

          try {
            updateResponse = JSON.parse(responseText); // Essaie de parser la réponse en JSON

            if (updateResponse.status === 'success') {
              console.dir(updateResponse); // Affiche le message si succès
            } else {
              console.error('Erreur:', updateResponse.message); // Affiche le message d'erreur si échec
            }

            userInfo();
          } catch (error) {
            console.error('Erreur de parsing JSON:', error);
            console.log('Contenu de la réponse:', responseText); // Affiche la réponse brute pour déboguer
          }

          _context4.next = 20;
          break;

        case 17:
          _context4.prev = 17;
          _context4.t0 = _context4["catch"](3);
          console.error('Erreur de requête:', _context4.t0);

        case 20:
          _context4.next = 23;
          break;

        case 22:
          console.error("Aucun fichier sélectionné");

        case 23:
        case "end":
          return _context4.stop();
      }
    }
  }, null, null, [[3, 17]]);
}

function updateUser() {
  console.log('ok');
}

function updateEmploye() {
  console.log('ok');
}

function updateOpinion() {
  console.log('ok');
}

function banUser(banId) {
  var banData;
  return regeneratorRuntime.async(function banUser$(_context5) {
    while (1) {
      switch (_context5.prev = _context5.next) {
        case 0:
          banData = {
            banId: banId
          };
          _context5.prev = 1;
          _context5.next = 4;
          return regeneratorRuntime.awrap(fetchRequestUpdate('banUser', banData));

        case 4:
          _context5.next = 9;
          break;

        case 6:
          _context5.prev = 6;
          _context5.t0 = _context5["catch"](1);
          console.error('Erreur js lors du bannissement de l\'utilisateur :', _context5.t0);

        case 9:
        case "end":
          return _context5.stop();
      }
    }
  }, null, null, [[1, 6]]);
}