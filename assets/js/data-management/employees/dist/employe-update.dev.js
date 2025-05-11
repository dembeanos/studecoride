"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.updateEmployeInfo = updateEmployeInfo;
exports.updatePassword = updatePassword;
exports.updatePhoto = updatePhoto;
exports.validationOpinion = validationOpinion;
exports.rejectOp = rejectOp;
exports.getTripInfo = getTripInfo;

var _employeManager = require("./employe-manager.js");

function fetchRequestUpdate(action, data) {
  var response, json;
  return regeneratorRuntime.async(function fetchRequestUpdate$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          _context.prev = 0;
          _context.next = 3;
          return regeneratorRuntime.awrap(fetch("/Ecoride/src/Router/employeRoute.php", {
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
          _context.next = 6;
          return regeneratorRuntime.awrap(response.json());

        case 6:
          json = _context.sent;

          if (!json.type) {
            _context.next = 10;
            break;
          }

          handleResponse(json);
          return _context.abrupt("return");

        case 10:
          if (typeof json.data === 'string') {
            try {
              json.data = JSON.parse(json.data);
            } catch (e) {
              console.warn("Données déjà parsées ou mal formées :", json.data);
            }
          }

          _context.next = 16;
          break;

        case 13:
          _context.prev = 13;
          _context.t0 = _context["catch"](0);
          console.error("Erreur lors de la récupération de la réponse :", _context.t0);

        case 16:
        case "end":
          return _context.stop();
      }
    }
  }, null, null, [[0, 13]]);
}

function updateEmployeInfo() {
  var _initEmployeVar, inputInfo, data;

  return regeneratorRuntime.async(function updateEmployeInfo$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          _initEmployeVar = (0, _employeManager.initEmployeVar)(), inputInfo = _initEmployeVar.inputInfo;
          data = {
            firstName: inputInfo.firstname.value,
            lastName: inputInfo.lastname.value,
            phone: inputInfo.phone.value,
            email: inputInfo.email.value,
            road: inputInfo.road.value,
            complement: inputInfo.roadcomplement.value,
            zipCode: inputInfo.zipcode.value,
            city: inputInfo.city.value
          };
          _context2.next = 4;
          return regeneratorRuntime.awrap(fetchRequestUpdate('updateEmployeInfo', data));

        case 4:
        case "end":
          return _context2.stop();
      }
    }
  });
}

function updatePassword() {
  var _initEmployeVar2, inputPass, data, updateResponse;

  return regeneratorRuntime.async(function updatePassword$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          _initEmployeVar2 = (0, _employeManager.initEmployeVar)(), inputPass = _initEmployeVar2.inputPass;
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
  var _initEmployeVar3, inputPhoto, photo, formData, response, responseText, updateResponse;

  return regeneratorRuntime.async(function updatePhoto$(_context4) {
    while (1) {
      switch (_context4.prev = _context4.next) {
        case 0:
          _initEmployeVar3 = (0, _employeManager.initEmployeVar)(), inputPhoto = _initEmployeVar3.inputPhoto;
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
          return regeneratorRuntime.awrap(fetch('/Ecoride/src/Profile/Employee/EmployeRequestRoute.php', {
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

function validationOpinion(validOpinion) {
  var updateData, updateResponse;
  return regeneratorRuntime.async(function validationOpinion$(_context5) {
    while (1) {
      switch (_context5.prev = _context5.next) {
        case 0:
          updateData = {
            opinionId: validOpinion
          };
          console.log(updateData.opinionId);
          _context5.next = 4;
          return regeneratorRuntime.awrap(fetchRequestUpdate('validateOpinion', updateData));

        case 4:
          updateResponse = _context5.sent;

          if (updateResponse) {
            setTimeout(function () {
              return (0, _employeManager.opinion)();
            }, 1000);
          }

        case 6:
        case "end":
          return _context5.stop();
      }
    }
  });
}

function rejectOp(rejectOpinion) {
  var updateData, updateResponse;
  return regeneratorRuntime.async(function rejectOp$(_context6) {
    while (1) {
      switch (_context6.prev = _context6.next) {
        case 0:
          updateData = {
            opinionId: rejectOpinion
          };
          console.log(updateData.opinionId);
          _context6.next = 4;
          return regeneratorRuntime.awrap(fetchRequestUpdate('rejectedOpinion', updateData));

        case 4:
          updateResponse = _context6.sent;

          if (updateResponse) {
            setTimeout(function () {
              return (0, _employeManager.opinion)();
            }, 1000);
          }

        case 6:
        case "end":
          return _context6.stop();
      }
    }
  });
}

function getTripInfo(tripInfo) {
  var updateData, response, json, tripDetails, tripMessage;
  return regeneratorRuntime.async(function getTripInfo$(_context7) {
    while (1) {
      switch (_context7.prev = _context7.next) {
        case 0:
          updateData = {
            opinionId: tripInfo
          };
          console.log(updateData.opinionId);
          _context7.prev = 2;
          _context7.next = 5;
          return regeneratorRuntime.awrap(fetch('/Ecoride/src/Profile/Employee/EmployeRequestRoute.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              action: 'getTripDetail',
              data: updateData
            })
          }));

        case 5:
          response = _context7.sent;
          _context7.next = 8;
          return regeneratorRuntime.awrap(response.json());

        case 8:
          json = _context7.sent;

          if (!json.type) {
            _context7.next = 14;
            break;
          }

          handleResponse(json);
          return _context7.abrupt("return");

        case 14:
          if (Array.isArray(json) && json.length > 0) {
            tripDetails = json[0]; // On prend le premier objet de l'array
            // Construction du message à afficher dans l'alert

            tripMessage = "\n                <div class=\"trip-details\">\n        <p class=\"title\">Offre ID: <span class=\"value\">".concat(tripDetails.offerid, "</span></p>\n        <p class=\"title\">Date d\xE9part: <span class=\"value\">").concat(tripDetails.datedepart, "</span></p>\n        <p class=\"title\">Ville d\xE9part: <span class=\"value\">").concat(tripDetails.citydepart, "</span></p>\n        <p class=\"title\">Route d\xE9part: <span class=\"value\">").concat(tripDetails.roaddepart, "</span></p>\n        <p class=\"title\">Date arriv\xE9e: <span class=\"value\">").concat(tripDetails.datearrival, "</span></p>\n        <p class=\"title\">Ville arriv\xE9e: <span class=\"value\">").concat(tripDetails.arrivalcity, "</span></p>\n        <p class=\"title\">Route arriv\xE9e: <span class=\"value\">").concat(tripDetails.arrivalroad, "</span></p>\n        <p class=\"title\">Passager: <span class=\"value\">").concat(tripDetails.passengerusername, "</span></p>\n        <p class=\"title\">Email Passager: <span class=\"value\">").concat(tripDetails.passengeremail, "</span></p>\n        <p class=\"title\">Conducteur: <span class=\"value\">").concat(tripDetails.driverusername, "</span></p>\n        <p class=\"title\">Email Conducteur: <span class=\"value\">").concat(tripDetails.driveremail, "</span></p>\n    </div>\n            "); // Affichage dans l'alert

            sendInteractivePopup(tripMessage);
          } else {
            console.error("Réponse du serveur mal formée ou vide", json);
          }

        case 15:
          _context7.next = 20;
          break;

        case 17:
          _context7.prev = 17;
          _context7.t0 = _context7["catch"](2);
          console.error("Erreur lors de la requête:", _context7.t0);

        case 20:
        case "end":
          return _context7.stop();
      }
    }
  }, null, null, [[2, 17]]);
}