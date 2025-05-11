"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.updateUserInfo = updateUserInfo;
exports.updateRole = updateRole;
exports.updatePassword = updatePassword;
exports.updatePhoto = updatePhoto;
exports.addCar = addCar;
exports.addPreference = addPreference;
exports.addTrip = addTrip;
exports.addOpinion = addOpinion;
exports.addClaim = addClaim;
exports.deleteCar = deleteCar;
exports.cancelReservation = cancelReservation;
exports.validateReservation = validateReservation;
exports.cancelTrip = cancelTrip;
exports.startTrip = startTrip;
exports.endTrip = endTrip;

var _userManager = require("./user-manager.js");

var _autocomplete = require("../../ui/menu/autocomplete.js");

function fetchRequestUpdate(action, data) {
  var request, json;
  return regeneratorRuntime.async(function fetchRequestUpdate$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          console.log(data);
          _context.prev = 1;
          _context.next = 4;
          return regeneratorRuntime.awrap(fetch("/Ecoride/src/Router/userRoute.php", {
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
          request = _context.sent;

          if (request.ok) {
            _context.next = 8;
            break;
          }

          console.log("La requ\xEAte a \xE9chou\xE9 pour l'action : ".concat(action, " avec le statut ").concat(request.status));
          return _context.abrupt("return");

        case 8:
          _context.next = 10;
          return regeneratorRuntime.awrap(request.text());

        case 10:
          json = _context.sent;
          console.log(json);

          if (typeof json.data === 'string') {
            try {
              json.data = JSON.parse(json.data);
            } catch (e) {
              console.warn("Données déjà parsées ou mal formées :", json.data);
            }
          }

          handleResponse(json);
          _context.next = 19;
          break;

        case 16:
          _context.prev = 16;
          _context.t0 = _context["catch"](1);
          console.error("Erreur lors de la récupération de la réponse :", _context.t0.text);

        case 19:
        case "end":
          return _context.stop();
      }
    }
  }, null, null, [[1, 16]]);
}

function updateUserInfo() {
  var _initUserVar, inputInfo, data;

  return regeneratorRuntime.async(function updateUserInfo$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          _initUserVar = (0, _userManager.initUserVar)(), inputInfo = _initUserVar.inputInfo;
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
          return regeneratorRuntime.awrap(fetchRequestUpdate('updateUserInfo', data));

        case 4:
        case "end":
          return _context2.stop();
      }
    }
  });
}

function updateRole() {
  var _initUserVar2, inputRole, selectedRole, updateData, updateResponse;

  return regeneratorRuntime.async(function updateRole$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          _initUserVar2 = (0, _userManager.initUserVar)(), inputRole = _initUserVar2.inputRole;
          selectedRole = Array.from(inputRole).find(function (role) {
            return role.checked;
          });
          updateData = {
            role: selectedRole.value
          };
          _context3.next = 5;
          return regeneratorRuntime.awrap(fetchRequestUpdate('updateRole', updateData));

        case 5:
          updateResponse = _context3.sent;

          if (!updateResponse) {
            _context3.next = 8;
            break;
          }

          return _context3.abrupt("return", updatemessage(updateResponse));

        case 8:
        case "end":
          return _context3.stop();
      }
    }
  });
}

function updatePassword() {
  var _initUserVar3, inputPass, data, updateResponse;

  return regeneratorRuntime.async(function updatePassword$(_context4) {
    while (1) {
      switch (_context4.prev = _context4.next) {
        case 0:
          _initUserVar3 = (0, _userManager.initUserVar)(), inputPass = _initUserVar3.inputPass;
          data = {
            backPassword: inputPass.backPassword.value,
            newPassword: inputPass.newPassword.value,
            confirmPassword: inputPass.confirmPassword.value
          };
          _context4.next = 4;
          return regeneratorRuntime.awrap(fetchRequestUpdate('updatePassword', data));

        case 4:
          updateResponse = _context4.sent;

          if (!updateResponse) {
            _context4.next = 7;
            break;
          }

          return _context4.abrupt("return", updatemessage(updateResponse));

        case 7:
        case "end":
          return _context4.stop();
      }
    }
  });
}

function updatePhoto() {
  var _initUserVar4, inputPhoto, photo, formData, response, responseText, updateResponse;

  return regeneratorRuntime.async(function updatePhoto$(_context5) {
    while (1) {
      switch (_context5.prev = _context5.next) {
        case 0:
          _initUserVar4 = (0, _userManager.initUserVar)(), inputPhoto = _initUserVar4.inputPhoto;
          photo = inputPhoto.files[0];

          if (!photo) {
            _context5.next = 22;
            break;
          }

          _context5.prev = 3;
          formData = new FormData();
          formData.append('action', 'updatePhoto');
          formData.append('updatePhoto', photo);
          _context5.next = 9;
          return regeneratorRuntime.awrap(fetch('/Ecoride/src/Router/userRoute.php', {
            method: 'POST',
            body: formData
          }));

        case 9:
          response = _context5.sent;
          _context5.next = 12;
          return regeneratorRuntime.awrap(response.text());

        case 12:
          responseText = _context5.sent;
          console.log("Réponse brute du serveur:", responseText); // Ajoute un log pour afficher la réponse

          try {
            updateResponse = JSON.parse(responseText); // Essaie de parser la réponse en JSON

            if (updateResponse.status === 'success') {
              console.dir(updateResponse); // Affiche le message si succès
            } else {
              console.error('Erreur:', updateResponse.message); // Affiche le message d'erreur si échec
            }

            (0, _userManager.userInfo)();
          } catch (error) {
            console.error('Erreur de parsing JSON:', error);
            console.log('Contenu de la réponse:', responseText); // Affiche la réponse brute pour déboguer
          }

          _context5.next = 20;
          break;

        case 17:
          _context5.prev = 17;
          _context5.t0 = _context5["catch"](3);
          console.error('Erreur de requête:', _context5.t0);

        case 20:
          _context5.next = 23;
          break;

        case 22:
          console.error("Aucun fichier sélectionné");

        case 23:
        case "end":
          return _context5.stop();
      }
    }
  }, null, null, [[3, 17]]);
}

function addCar() {
  var _initUserVar5 = (0, _userManager.initUserVar)(),
      inputCar = _initUserVar5.inputCar;

  var updateData = {
    marque: inputCar.marque.value,
    modele: inputCar.modele.value,
    immatriculation: inputCar.immatriculation.value,
    firstImmatriculation: inputCar.firstImmatriculation.value,
    color: inputCar.color.value,
    energy: inputCar.energy.value,
    places: inputCar.places.value
  };
  fetchRequestUpdate('addCar', updateData);
  setTimeout(function () {
    return (0, _userManager.cars)();
  }, 1000);
}

function addPreference() {
  var _initUserVar6, inputPref, updateData, updateResponse;

  return regeneratorRuntime.async(function addPreference$(_context6) {
    while (1) {
      switch (_context6.prev = _context6.next) {
        case 0:
          _initUserVar6 = (0, _userManager.initUserVar)(), inputPref = _initUserVar6.inputPref;
          updateData = {
            animal: inputPref.animal.checked,
            smoke: inputPref.smoke.checked,
            other: inputPref.other.value
          };
          _context6.next = 4;
          return regeneratorRuntime.awrap(fetchRequestUpdate('addPreference', updateData));

        case 4:
          updateResponse = _context6.sent;

          if (!updateResponse) {
            _context6.next = 7;
            break;
          }

          return _context6.abrupt("return", updatemessage(updateResponse));

        case 7:
        case "end":
          return _context6.stop();
      }
    }
  });
}

function addTrip(carId, prefId) {
  var _initUserVar7, inputTrip, updateData, updateResponse;

  return regeneratorRuntime.async(function addTrip$(_context7) {
    while (1) {
      switch (_context7.prev = _context7.next) {
        case 0:
          _initUserVar7 = (0, _userManager.initUserVar)(), inputTrip = _initUserVar7.inputTrip;
          updateData = {
            cityDepart: inputTrip.cityDepart.value,
            arrivalCity: inputTrip.arrivalCity.value,
            roadDepart: inputTrip.roadDepart.value,
            arrivalRoad: inputTrip.arrivalRoad.value,
            hourDepart: inputTrip.hourDepart.value,
            hourArrival: inputTrip.hourArrival.value,
            dateArrival: inputTrip.dateArrival.value,
            dateDepart: inputTrip.dateDepart.value,
            price: inputTrip.price.value,
            duration: inputTrip.duration.value,
            car: carId,
            preference: prefId,
            placeAvailable: inputTrip.placeAvailable.value,
            inseeArrival: _autocomplete.inseeCity.inseeArrival,
            inseeDepart: _autocomplete.inseeCity.inseeDepart
          };
          _context7.next = 4;
          return regeneratorRuntime.awrap(fetchRequestUpdate('addTrip', updateData));

        case 4:
          updateResponse = _context7.sent;

          if (!updateResponse) {
            _context7.next = 8;
            break;
          }

          setTimeout(function () {
            return trip();
          }, 1000);
          return _context7.abrupt("return", updatemessage(updateResponse));

        case 8:
        case "end":
          return _context7.stop();
      }
    }
  });
}

function addOpinion(srcOpinion) {
  var _initUserVar8, inputOpinion, updateData, updateResponse;

  return regeneratorRuntime.async(function addOpinion$(_context8) {
    while (1) {
      switch (_context8.prev = _context8.next) {
        case 0:
          _initUserVar8 = (0, _userManager.initUserVar)(), inputOpinion = _initUserVar8.inputOpinion;
          updateData = {
            srcOpinion: srcOpinion,
            opinionText: inputOpinion.opinionText.value,
            note: inputOpinion.note.value
          };
          _context8.next = 4;
          return regeneratorRuntime.awrap(fetchRequestUpdate('addOpinion', updateData));

        case 4:
          updateResponse = _context8.sent;

          if (!updateResponse) {
            _context8.next = 8;
            break;
          }

          setTimeout(function () {
            return opinion();
          }, 1000);
          return _context8.abrupt("return", updatemessage(updateResponse));

        case 8:
        case "end":
          return _context8.stop();
      }
    }
  });
}

function addClaim(srcClaim) {
  var _initUserVar9, inputReclam, updateData, updateResponse;

  return regeneratorRuntime.async(function addClaim$(_context9) {
    while (1) {
      switch (_context9.prev = _context9.next) {
        case 0:
          _initUserVar9 = (0, _userManager.initUserVar)(), inputReclam = _initUserVar9.inputReclam;
          updateData = {
            srcClaim: srcClaim,
            reclamText: inputReclam.reclamText.value
          };
          _context9.next = 4;
          return regeneratorRuntime.awrap(fetchRequestUpdate('addClaim', updateData));

        case 4:
          updateResponse = _context9.sent;

          if (!updateResponse) {
            _context9.next = 8;
            break;
          }

          setTimeout(function () {
            return opinion();
          }, 1000);
          return _context9.abrupt("return", updatemessage(updateResponse));

        case 8:
        case "end":
          return _context9.stop();
      }
    }
  });
}

function deleteCar(immatriculation) {
  var deleteData;
  return regeneratorRuntime.async(function deleteCar$(_context10) {
    while (1) {
      switch (_context10.prev = _context10.next) {
        case 0:
          deleteData = {
            immatriculation: immatriculation
          };
          _context10.next = 3;
          return regeneratorRuntime.awrap(fetchRequestUpdate('deleteCar', deleteData));

        case 3:
          return _context10.abrupt("return", (0, _userManager.cars)());

        case 4:
        case "end":
          return _context10.stop();
      }
    }
  });
}

function cancelReservation(reservationId) {
  var updateData, updateResponse;
  return regeneratorRuntime.async(function cancelReservation$(_context11) {
    while (1) {
      switch (_context11.prev = _context11.next) {
        case 0:
          updateData = {
            reservationId: reservationId
          };
          console.log(updateData);
          _context11.next = 4;
          return regeneratorRuntime.awrap(fetchRequestUpdate('cancelReservation', updateData));

        case 4:
          updateResponse = _context11.sent;

          if (!updateResponse) {
            _context11.next = 8;
            break;
          }

          setTimeout(function () {
            return (0, _userManager.reservation)();
          }, 1000);
          return _context11.abrupt("return", updatemessage(updateResponse));

        case 8:
        case "end":
          return _context11.stop();
      }
    }
  });
}

function validateReservation(reservationId) {
  var updateData, updateResponse;
  return regeneratorRuntime.async(function validateReservation$(_context12) {
    while (1) {
      switch (_context12.prev = _context12.next) {
        case 0:
          updateData = {
            reservationId: reservationId
          };
          console.log(updateData);
          _context12.next = 4;
          return regeneratorRuntime.awrap(fetchRequestUpdate('validateReservation', updateData));

        case 4:
          updateResponse = _context12.sent;

          if (!updateResponse) {
            _context12.next = 8;
            break;
          }

          setTimeout(function () {
            return (0, _userManager.reservation)();
          }, 1000);
          return _context12.abrupt("return", updatemessage(updateResponse));

        case 8:
        case "end":
          return _context12.stop();
      }
    }
  });
}

function cancelTrip(tripId) {
  var updateData, updateResponse;
  return regeneratorRuntime.async(function cancelTrip$(_context13) {
    while (1) {
      switch (_context13.prev = _context13.next) {
        case 0:
          updateData = {
            tripId: tripId
          };
          console.log(updateData);
          _context13.next = 4;
          return regeneratorRuntime.awrap(fetchRequestUpdate('cancelTrip', updateData));

        case 4:
          updateResponse = _context13.sent;

          if (updateResponse) {
            setTimeout(function () {
              return trip();
            }, 1000);
          }

        case 6:
        case "end":
          return _context13.stop();
      }
    }
  });
}

function startTrip(tripId) {
  var updateData, updateResponse;
  return regeneratorRuntime.async(function startTrip$(_context14) {
    while (1) {
      switch (_context14.prev = _context14.next) {
        case 0:
          updateData = {
            tripId: tripId
          };
          console.log(updateData);
          _context14.next = 4;
          return regeneratorRuntime.awrap(fetchRequestUpdate('startTrip', updateData));

        case 4:
          updateResponse = _context14.sent;

          if (updateResponse) {
            setTimeout(function () {
              return trip();
            }, 1000);
          }

        case 6:
        case "end":
          return _context14.stop();
      }
    }
  });
}

function endTrip(tripId) {
  var updateData, updateResponse;
  return regeneratorRuntime.async(function endTrip$(_context15) {
    while (1) {
      switch (_context15.prev = _context15.next) {
        case 0:
          updateData = {
            tripId: tripId
          };
          console.log(updateData);
          _context15.next = 4;
          return regeneratorRuntime.awrap(fetchRequestUpdate('endTrip', updateData));

        case 4:
          updateResponse = _context15.sent;

          if (updateResponse) {
            setTimeout(function () {
              return trip();
            }, 1000);
          }

        case 6:
        case "end":
          return _context15.stop();
      }
    }
  });
}