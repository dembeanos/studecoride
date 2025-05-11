"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.initUserVar = initUserVar;
exports.setupButton = setupButton;
exports.userInfo = userInfo;
exports.cars = cars;
exports.credit = credit;
exports.reservation = reservation;
exports.setTrip = setTrip;

var _userUpdate = require("./user-update.js");

var _opinion = require("./opinion.js");

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance"); }

function _iterableToArrayLimit(arr, i) { if (!(Symbol.iterator in Object(arr) || Object.prototype.toString.call(arr) === "[object Arguments]")) { return; } var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

window.addEventListener("load", function () {
  var onglets = document.querySelectorAll(".onglets");
  var pages = document.querySelectorAll("section");
  var functions = [userInfo, cars, credit, reservation, setTrip, tripManager];

  var hidePages = function hidePages() {
    pages.forEach(function (page) {
      return page.style.display = "none";
    });
  };

  var showPage = function showPage(index) {
    hidePages();
    pages[index].style.display = "block";
    var forms = pages[index].querySelectorAll("form");
    forms.forEach(function (form) {
      return form.reset();
    });

    if (typeof functions[index] === "function") {
      functions[index]();
    }

    localStorage.setItem('activePage', index);
  };

  var activePage = localStorage.getItem('activePage');
  var initialPage = activePage ? parseInt(activePage) : 0;
  hidePages();
  showPage(initialPage);
  onglets.forEach(function (onglet, index) {
    onglet.addEventListener("click", function (event) {
      event.preventDefault();
      showPage(index);
      ;
    });
  });
});

function initUserVar() {
  var inputInfo = {
    firstname: document.getElementById("firstName"),
    lastname: document.getElementById("lastName"),
    phone: document.getElementById("phone"),
    email: document.getElementById("email"),
    road: document.getElementById("road"),
    roadcomplement: document.getElementById("roadComplement"),
    zipcode: document.getElementById("zipCode"),
    city: document.getElementById("city")
  };
  var inputRole = document.querySelectorAll('input[name="role"]');
  var inputPhoto = document.getElementById('photoUpload');
  var inputPass = {
    backPassword: document.getElementById("backPassword"),
    newPassword: document.getElementById("newPassword"),
    confirmPassword: document.getElementById("confirmPassword")
  };
  var inputCar = {
    marque: document.getElementById("marque"),
    modele: document.getElementById("modele"),
    immatriculation: document.getElementById("immatriculation"),
    firstImmatriculation: document.getElementById("firstImmatriculation"),
    color: document.getElementById("color"),
    energy: document.getElementById("energy"),
    places: document.getElementById("places"),
    carLine: document.querySelector("[data-immatriculation=\"".concat(immatriculation, "\"]"))
  };
  var inputPref = {
    animal: document.getElementById('allowAnimals'),
    smoke: document.getElementById('allowSmoke'),
    other: document.getElementById('otherPreference')
  };
  var creditVar = {
    movementsContainer: document.querySelector('.movementsCredits'),
    movementLine: document.querySelector('.movementRow'),
    totalCreditElem: document.getElementById('totalAmountCredits')
  };
  var reservationVar = {
    reservationContainer: document.querySelector('.reservation-container'),
    templateLine: document.querySelector('.reservation-container .reservation-line')
  };
  var inputTrip = {
    cityDepart: document.getElementById('cityDepart'),
    roadDepart: document.getElementById('roadDepart'),
    dateDepart: document.getElementById('tripDateDepart'),
    hourDepart: document.getElementById('tripHourDepart'),
    arrivalCity: document.getElementById('cityArrival'),
    arrivalRoad: document.getElementById('arrivalRoad'),
    dateArrival: document.getElementById('tripArrivalDate'),
    hourArrival: document.getElementById('tripArrivalHour'),
    price: document.getElementById('tripPrice'),
    duration: document.getElementById('tripDuration'),
    placeAvailable: document.getElementById('tripPlaces')
  };
  var tripVar = {
    autoTripSelect: document.getElementById('autoTrip'),
    tripButton: document.getElementById("addTrip"),
    prefId: null,
    carId: null,
    carPlaces: null
  };
  var inputOpinion = {
    opinionText: document.getElementById('containOpinion'),
    note: document.getElementById('note')
  };
  var inputReclam = {
    reclamText: document.getElementById('containReclamation')
  };
  var opinionVar = {
    tripOpinion: document.getElementById('tripOpinion'),
    tripReclamation: document.getElementById('tripReclamation'),
    userOpinionContainer: document.querySelector('.userOpinionContainer'),
    container: document.querySelector('.opinionContainer'),
    reclamationContainer: document.querySelector('.reclamationContainer'),
    addOpinionButton: document.getElementById('addOpinion'),
    addClaimButton: document.getElementById('addClaim')
  };
  return {
    inputInfo: inputInfo,
    inputPhoto: inputPhoto,
    inputRole: inputRole,
    inputPass: inputPass,
    inputCar: inputCar,
    inputPref: inputPref,
    creditVar: creditVar,
    reservationVar: reservationVar,
    inputTrip: inputTrip,
    tripVar: tripVar,
    inputOpinion: inputOpinion,
    inputReclam: inputReclam,
    opinionVar: opinionVar
  };
}

var carLine = document.getElementById('carLine');
var carTemplate = carLine.querySelector('.car-template'); //Méthode Importé d'autres fichiers

function fetchRequest(action) {
  var request, responseText, responseData;
  return regeneratorRuntime.async(function fetchRequest$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          _context.prev = 0;
          _context.next = 3;
          return regeneratorRuntime.awrap(fetch("/Ecoride/src/Router/userRoute.php", {
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
          responseText = _context.sent;
          console.log(responseText);
          responseData = JSON.parse(responseText);
          return _context.abrupt("return", responseData);

        case 12:
          _context.prev = 12;
          _context.t0 = _context["catch"](0);
          handleResponse(_context.t0);
          console.error("Erreur lors de la récupération de la réponse :", _context.t0);

        case 16:
        case "end":
          return _context.stop();
      }
    }
  }, null, null, [[0, 12]]);
}

function setupButton(buttons) {
  Object.entries(buttons).forEach(function (_ref) {
    var _ref2 = _slicedToArray(_ref, 2),
        action = _ref2[0],
        button = _ref2[1];

    if (button && !button.dataset.listenerAttached) {
      button.addEventListener("click", function (event) {
        event.preventDefault();
        var functionToCall;
        var immatriculation = button.dataset.immatriculation;
        var carId = button.dataset.carId;
        var prefId = button.dataset.prefId;
        var tripId = button.dataset.tripId;
        var reservationId = button.dataset.reservationId;
        var srcClaim = button.dataset.srcClaim;
        var srcOpinion = button.dataset.srcOpinion;

        switch (action) {
          case "updateUserInfo":
            functionToCall = _userUpdate.updateUserInfo;
            break;

          case "updatePassword":
            functionToCall = _userUpdate.updatePassword;
            break;

          case "updatePhoto":
            functionToCall = _userUpdate.updatePhoto;
            break;

          case "updateRole":
            functionToCall = _userUpdate.updateRole;
            break;

          case "addCar":
            functionToCall = _userUpdate.addCar;
            break;

          case "addPreference":
            functionToCall = _userUpdate.addPreference;
            break;

          case "addTrip":
            functionToCall = function functionToCall() {
              return (0, _userUpdate.addTrip)(carId, prefId);
            };

            break;

          case "addOpinion":
            functionToCall = function functionToCall() {
              return (0, _userUpdate.addOpinion)(srcOpinion);
            };

            break;

          case "addClaim":
            functionToCall = function functionToCall() {
              return (0, _userUpdate.addClaim)(srcClaim);
            };

            break;

          case "deleteCar":
            functionToCall = function functionToCall() {
              return (0, _userUpdate.deleteCar)(immatriculation);
            };

            break;

          case "cancelTrip":
            functionToCall = function functionToCall() {
              return (0, _userUpdate.cancelTrip)(tripId);
            };

            break;

          case 'startTrip':
            functionToCall = function functionToCall() {
              return (0, _userUpdate.startTrip)(tripId);
            };

            break;

          case 'endTrip':
            functionToCall = function functionToCall() {
              return (0, _userUpdate.endTrip)(tripId);
            };

            break;

          case 'cancelReservation':
            functionToCall = function functionToCall() {
              return (0, _userUpdate.cancelReservation)(parseInt(reservationId));
            };

            break;

          case 'validateReservation':
            (0, _opinion.sendOpinion)(reservationId);

            functionToCall = function functionToCall() {
              return (0, _userUpdate.validateReservation)(reservationId);
            };

            break;

          default:
            console.error("La fonction ".concat(action, " n'est pas d\xE9finie."));
        }

        if (functionToCall) {
          functionToCall();
        }
      });
      button.dataset.listenerAttached = "true";
    }
  });
}

function userInfo() {
  var requests, _ref3, _ref4, _userInfo, userRole, userPhoto, userInfoData, _initUserVar, inputInfo, key, _initUserVar2, inputRole, photoOnglet, photoProfile, buttons;

  return regeneratorRuntime.async(function userInfo$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          _context2.prev = 0;
          requests = [fetchRequest('getUserInfo'), fetchRequest('getRole'), fetchRequest('getPhoto')];
          _context2.next = 4;
          return regeneratorRuntime.awrap(Promise.all(requests));

        case 4:
          _ref3 = _context2.sent;
          _ref4 = _slicedToArray(_ref3, 3);
          _userInfo = _ref4[0];
          userRole = _ref4[1];
          userPhoto = _ref4[2];

          if (_userInfo && _userInfo.status === 'success') {
            userInfoData = _userInfo.data;
            _initUserVar = initUserVar(), inputInfo = _initUserVar.inputInfo;

            for (key in userInfoData) {
              if (inputInfo[key]) {
                inputInfo[key].value = userInfoData[key];
              }
            }
          } else {
            console.error('js : Erreur lors de la récupération des données utilisateur');
          }

          if (userRole) {
            _initUserVar2 = initUserVar(), inputRole = _initUserVar2.inputRole;
            inputRole.forEach(function (radio) {
              if (radio.value === userRole.userrole) {
                radio.checked = true;
              }
            });
          }

          if (userPhoto) {
            photoOnglet = document.getElementById('userPhotoOnglet');
            photoProfile = document.getElementById('userPhotoProfil');

            if (photoOnglet || photoProfile) {
              photoOnglet.src = userPhoto;
              photoProfile.src = userPhoto;
            }
          } else {
            console.log('Erreur : Photo manquante dans la réponse');
          }

          buttons = {
            updateUserInfo: document.getElementById("sendInfo"),
            updatePassword: document.getElementById("sendPassword"),
            updatePhoto: document.getElementById("updatePhoto"),
            updateRole: document.getElementById("updateRole")
          };
          setupButton(buttons);
          _context2.next = 19;
          break;

        case 16:
          _context2.prev = 16;
          _context2.t0 = _context2["catch"](0);
          console.error('Erreur lors de la requête:', _context2.t0);

        case 19:
        case "end":
          return _context2.stop();
      }
    }
  }, null, null, [[0, 16]]);
}

function cars() {
  var savedCar, cars, savedPref, _initUserVar3, inputPref, buttons;

  return regeneratorRuntime.async(function cars$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          _context3.next = 2;
          return regeneratorRuntime.awrap(fetchRequest('getCar'));

        case 2:
          savedCar = _context3.sent;
          carLine.innerHTML = "";
          cars = [];

          if (Array.isArray(savedCar.data)) {
            cars = savedCar.data;
          }

          cars.forEach(function (car) {
            var newCarDiv = carTemplate.cloneNode(true);
            newCarDiv.style.display = "block";
            var fields = {
              marque: newCarDiv.querySelector('[data-field="marque"]'),
              modele: newCarDiv.querySelector('[data-field="modele"]'),
              immatriculation: newCarDiv.querySelector('[data-field="immatriculation"]'),
              couleur: newCarDiv.querySelector('[data-field="color"]'),
              energie: newCarDiv.querySelector('[data-field="energy"]'),
              deleteAuto: newCarDiv.querySelector('[data-field="deleteAuto"]')
            };
            if (fields.marque) fields.marque.textContent = car.marque;
            if (fields.modele) fields.modele.textContent = car.modele;
            if (fields.immatriculation) fields.immatriculation.textContent = car.immatriculation;
            if (fields.couleur) fields.couleur.textContent = car.color;
            if (fields.energie) fields.energie.textContent = car.energy;
            var deleteButton = document.createElement("button");
            deleteButton.textContent = "🗑️ Supprimer";
            deleteButton.classList.add("delete-car");
            deleteButton.dataset.immatriculation = car.immatriculation;
            deleteButton.type = "submit";
            setupButton({
              deleteCar: deleteButton
            });

            if (fields.deleteAuto) {
              fields.deleteAuto.innerHTML = "";
              fields.deleteAuto.appendChild(deleteButton);
            }

            carLine.appendChild(newCarDiv);
          });
          _context3.next = 9;
          return regeneratorRuntime.awrap(fetchRequest('getPreference'));

        case 9:
          savedPref = _context3.sent;
          _initUserVar3 = initUserVar(), inputPref = _initUserVar3.inputPref;

          if (savedPref) {
            inputPref.animal.checked = savedPref.animal;
            inputPref.smoke.checked = savedPref.smoke;
            inputPref.other.value = savedPref.other;
          }

          buttons = {
            addCar: document.getElementById("addCar"),
            addPreference: document.getElementById("addPref")
          };
          setupButton(buttons);

        case 14:
        case "end":
          return _context3.stop();
      }
    }
  });
}

function credit() {
  var _ref5, _ref6, creditHistory, getTotalCredit, _initUserVar4, creditVar, items, parsedtotal, total;

  return regeneratorRuntime.async(function credit$(_context4) {
    while (1) {
      switch (_context4.prev = _context4.next) {
        case 0:
          _context4.prev = 0;
          _context4.next = 3;
          return regeneratorRuntime.awrap(Promise.all([fetchRequest('getCredit'), fetchRequest('getTotalCredit')]));

        case 3:
          _ref5 = _context4.sent;
          _ref6 = _slicedToArray(_ref5, 2);
          creditHistory = _ref6[0];
          getTotalCredit = _ref6[1];
          _initUserVar4 = initUserVar(), creditVar = _initUserVar4.creditVar;

          if (!(!creditVar.movementsContainer || !creditVar.movementLine)) {
            _context4.next = 11;
            break;
          }

          console.error("L'élément .movementsCredits ou .movementRow est introuvable !");
          return _context4.abrupt("return");

        case 11:
          creditVar.movementsContainer.innerHTML = "";

          if (creditHistory && creditHistory.data) {
            items = creditHistory.data;
            items.forEach(function (item) {
              var newHistCreditDiv = creditVar.movementLine.cloneNode(true);
              newHistCreditDiv.querySelector('#historyDate').textContent = item.creationdate;
              newHistCreditDiv.querySelector('#historyLabel').textContent = item.label;
              newHistCreditDiv.querySelector('#historyCredit').textContent = item.credit;
              newHistCreditDiv.querySelector('#historyDebit').textContent = item.debit;
              creditVar.movementsContainer.appendChild(newHistCreditDiv);
            });
          } else {
            creditVar.movementLine.innerHTML = "<p>Aucun mouvement enregistré.</p>";
          }

          if (getTotalCredit) {
            parsedtotal = JSON.parse(getTotalCredit);
            total = parsedtotal.data.credit;
            creditVar.totalCreditElem.textContent = Number(total).toFixed(2);
          } else {
            creditVar.totalCreditElem.textContent = "0";
          }

          _context4.next = 19;
          break;

        case 16:
          _context4.prev = 16;
          _context4.t0 = _context4["catch"](0);
          console.error('Erreur lors de la récupération des crédits:', _context4.t0);

        case 19:
        case "end":
          return _context4.stop();
      }
    }
  }, null, null, [[0, 16]]);
}

function reservation() {
  var reservationInfo, _initUserVar5, reservationVar, infos;

  return regeneratorRuntime.async(function reservation$(_context5) {
    while (1) {
      switch (_context5.prev = _context5.next) {
        case 0:
          _context5.prev = 0;
          _context5.next = 3;
          return regeneratorRuntime.awrap(fetchRequest('getReservation'));

        case 3:
          reservationInfo = _context5.sent;
          _initUserVar5 = initUserVar(), reservationVar = _initUserVar5.reservationVar;

          if (reservationVar.templateLine) {
            _context5.next = 8;
            break;
          }

          console.error("Aucune ligne modèle (.reservation-line) trouvée !");
          return _context5.abrupt("return");

        case 8:
          reservationVar.reservationContainer.innerHTML = "";

          if (reservationInfo) {
            infos = Array.isArray(reservationInfo) ? reservationInfo : [reservationInfo];
            infos.forEach(function (info) {
              var newReservationLine = reservationVar.templateLine.cloneNode(true);
              newReservationLine.style.display = "block";
              newReservationLine.querySelector(".reservation-date").textContent = info.dateReservation;
              newReservationLine.querySelector(".reservation-ref").textContent = info.reservationId;
              newReservationLine.querySelector(".reservation-price").textContent = info.price;
              newReservationLine.querySelector(".reservation-depart-date").textContent = info.dateDepart;
              newReservationLine.querySelector(".city-depart").textContent = info.cityDepart;
              newReservationLine.querySelector(".reservation-arrival-date").textContent = info.dateArrival;
              newReservationLine.querySelector(".arrival-city").textContent = info.arrivalCity;
              newReservationLine.querySelector(".driver-name").textContent = info.driver;
              var action = newReservationLine.querySelector('.reservation-action');
              action.innerHTML = "";
              var currentDate = new Date();
              var dateDepart = new Date(info.dateDepart);

              if (info.status === 'canceled') {
                action.textContent = "❌ Annulée";
              } else if (currentDate < dateDepart) {
                var deleteButton = document.createElement("button");
                deleteButton.textContent = "❌ Annuler";
                deleteButton.classList.add("delete-reservation");
                deleteButton.dataset.reservationId = info.reservationId;
                action.appendChild(deleteButton);
                setupButton({
                  cancelReservation: deleteButton
                });
              } else if (currentDate >= dateDepart && info.status !== 'validated') {
                var validationButton = document.createElement("button");
                validationButton.textContent = "Valider Transaction";
                validationButton.classList.add("valid-reservation");
                validationButton.dataset.reservationId = info.reservationId;
                action.appendChild(validationButton);
                setupButton({
                  validateReservation: validationButton
                });
              } else {
                action.textContent = info.status === "validated" ? "✅ Validé" : info.status;
              }

              reservationVar.reservationContainer.appendChild(newReservationLine);
            });
          } else {
            reservationVar.reservationContainer.innerHTML = "<p>Aucune réservation pour le moment.</p>";
          }

          _context5.next = 15;
          break;

        case 12:
          _context5.prev = 12;
          _context5.t0 = _context5["catch"](0);
          console.error('Erreur lors de la récupération des réservations:', _context5.t0);

        case 15:
        case "end":
          return _context5.stop();
      }
    }
  }, null, null, [[0, 12]]);
}

function setTrip() {
  var _initUserVar6, tripVar, _ref7, _ref8, savedCar, savedPref, tripInfo, _cars, autoTripSelect, addCarOption;

  return regeneratorRuntime.async(function setTrip$(_context6) {
    while (1) {
      switch (_context6.prev = _context6.next) {
        case 0:
          _context6.prev = 0;
          _initUserVar6 = initUserVar(), tripVar = _initUserVar6.tripVar;
          _context6.next = 4;
          return regeneratorRuntime.awrap(Promise.all([fetchRequest('getCar'), fetchRequest('getPreference'), fetchRequest('getTrip')]));

        case 4:
          _ref7 = _context6.sent;
          _ref8 = _slicedToArray(_ref7, 3);
          savedCar = _ref8[0];
          savedPref = _ref8[1];
          tripInfo = _ref8[2];
          carLine.innerHTML = "";
          _cars = Array.isArray(savedCar.data) ? savedCar.data : [];
          tripVar.prefId = savedPref.preferenceid;
          tripVar.tripButton.dataset.prefId = tripVar.prefId;
          autoTripSelect = tripVar.autoTripSelect;

          if (autoTripSelect) {
            autoTripSelect.innerHTML = "";

            _cars.forEach(function (car) {
              var option = document.createElement('option');
              option.value = car.carid;
              option.textContent = "".concat(car.marque, " ").concat(car.modele, " ").concat(car.color, " ").concat(car.immatriculation);
              autoTripSelect.appendChild(option);
            });

            addCarOption = document.createElement('option');
            addCarOption.value = "addCar";
            addCarOption.textContent = "+ Ajouter un véhicule";
            autoTripSelect.appendChild(addCarOption);
            autoTripSelect.addEventListener('click', function () {
              if (autoTripSelect.value === "addCar") {
                document.getElementById('pageCar').style.display = 'block';
                document.getElementById('pageTrajet').style.display = 'none';
                getCar();
              } else {
                var selectedCar = _cars.find(function (car) {
                  return car.carid == autoTripSelect.value;
                });

                if (selectedCar) {
                  tripVar.carId = selectedCar.carid;
                  tripVar.carPlaces = selectedCar.places;
                  tripVar.tripButton.dataset.carId = tripVar.carId;
                  var tripPlacesSelect = document.getElementById('tripPlaces');
                  tripPlacesSelect.innerHTML = "";
                  var defaultOption = document.createElement('option');
                  defaultOption.value = "0";
                  defaultOption.textContent = "Sélectionnez...";
                  tripPlacesSelect.appendChild(defaultOption);
                  var availablePlaces = tripVar.carPlaces - 1;

                  for (var i = 1; i <= availablePlaces; i++) {
                    var option = document.createElement('option');
                    option.value = i;
                    option.textContent = i + (i > 1 ? " places" : " place");
                    tripPlacesSelect.appendChild(option);
                  }
                }
              }
            });
          }

          _context6.next = 20;
          break;

        case 17:
          _context6.prev = 17;
          _context6.t0 = _context6["catch"](0);
          console.error("Erreur dans setTrip :", _context6.t0);

        case 20:
        case "end":
          return _context6.stop();
      }
    }
  }, null, null, [[0, 17]]);
}

function tripManager() {
  var _initUserVar7, tripVar, tripInfo, tripLineTemplate, trips, fragment, _parentElement, noTripMessage;

  return regeneratorRuntime.async(function tripManager$(_context7) {
    while (1) {
      switch (_context7.prev = _context7.next) {
        case 0:
          _context7.prev = 0;
          _initUserVar7 = initUserVar(), tripVar = _initUserVar7.tripVar;
          _context7.next = 4;
          return regeneratorRuntime.awrap(fetchRequest('getTrip'));

        case 4:
          tripInfo = _context7.sent;
          tripLineTemplate = document.querySelector('.tripLine');

          if (tripInfo.data && Array.isArray(tripInfo.data) && tripInfo.data.length > 0) {
            trips = tripInfo.data;
            fragment = document.createDocumentFragment();
            _parentElement = tripLineTemplate.parentElement;

            _parentElement.querySelectorAll('.tripLine').forEach(function (line) {
              return line.remove();
            });

            trips.forEach(function (trip) {
              var newTripLine = tripLineTemplate.cloneNode(true);
              newTripLine.querySelector('.trip-date').textContent = trip.datedepart || 'Date inconnue';
              newTripLine.querySelector('.trip-ref').textContent = trip.offerid;
              newTripLine.querySelector('.trip-price').textContent = trip.price || 'Prix inconnu';
              newTripLine.querySelector('.trip-hour').textContent = trip.hourdepart || 'Heure inconnue';
              newTripLine.querySelector('.trip-depart').textContent = trip.citydepart || 'Lieu inconnu';
              newTripLine.querySelector('.trip-arrival').textContent = trip.arrivalcity || 'Arrivée inconnue';
              newTripLine.querySelector('.trip-participation').textContent = "".concat(trip.totalreservations, " / ").concat(trip.placeavailable);
              newTripLine.querySelector('.trip-status').textContent = trip.status;
              var currentDate = new Date();
              var dateDepart = new Date(trip.datedepart);
              var action = newTripLine.querySelector('.trip-action');

              if (trip.status === 'active' && currentDate < dateDepart) {
                var deleteButton = document.createElement('button');
                deleteButton.textContent = '❌ Annuler';
                deleteButton.classList.add('delete-trip');
                deleteButton.dataset.tripId = trip.offerid; // Ici, offerid est garanti d'exister

                deleteButton.type = 'submit';
                action.innerHTML = '';
                action.appendChild(deleteButton);
                setupButton({
                  cancelTrip: deleteButton
                });
              } // Si le trajet est déjà en cours, afficher directement "Arrivée"


              if (trip.status === 'in process') {
                var _endTrip = document.createElement('button');

                _endTrip.textContent = 'Arrivée';

                _endTrip.classList.add('end-trip');

                _endTrip.dataset.tripId = trip.offerid; // Ici, offerid est garanti d'exister

                _endTrip.type = 'submit';
                action.innerHTML = '';
                action.appendChild(_endTrip);
                console.log(trip.offerid);
                setupButton({
                  endTrip: _endTrip
                });
              }

              function isSameDay(date1, date2) {
                return date1.toISOString().split('T')[0] === date2.toISOString().split('T')[0];
              } // Si on est le jour J, afficher le bouton "Démarrer"


              if (trip.status === 'active' && isSameDay(currentDate, dateDepart)) {
                var _startTrip = document.createElement('button');

                _startTrip.textContent = 'Démarrer';

                _startTrip.classList.add('start-trip');

                _startTrip.dataset.tripId = trip.offerid; // Ici, offerid est garanti d'exister

                _startTrip.type = 'submit';
                action.innerHTML = '';
                action.appendChild(_startTrip);
                setupButton({
                  startTrip: _startTrip
                });

                _startTrip.addEventListener('click', function () {
                  _startTrip.disabled = true;
                  var processTrip = document.createElement('button');
                  processTrip.textContent = 'Processing...';
                  processTrip.classList.add('process-trip');
                  processTrip.disabled = true;
                  action.innerHTML = '';
                  action.appendChild(processTrip);
                  setTimeout(function () {
                    var endTrip = document.createElement('button');
                    endTrip.textContent = 'Arrivée';
                    endTrip.classList.add('end-trip');
                    endTrip.dataset.tripId = trip.offerid; // Ici, offerid est garanti d'exister

                    endTrip.type = 'submit';
                    action.innerHTML = '';
                    action.appendChild(endTrip);
                    setupButton({
                      endTrip: endTrip
                    });
                  }, 10000);
                });
              }

              fragment.appendChild(newTripLine);
            });

            _parentElement.appendChild(fragment);
          } else {
            // Supprimer anciennes lignes et afficher "Aucun trajet"
            parentElement.querySelectorAll('.tripLine').forEach(function (line) {
              return line.remove();
            });
            noTripMessage = document.createElement('p');
            noTripMessage.textContent = "Aucun trajet pour le moment.";
            parentElement.appendChild(noTripMessage);
          }

          setupButton({
            addTrip: tripVar.tripButton
          });
          _context7.next = 13;
          break;

        case 10:
          _context7.prev = 10;
          _context7.t0 = _context7["catch"](0);
          console.error('Erreur lors de la récupération des trajets ou des véhicules :', _context7.t0);

        case 13:
        case "end":
          return _context7.stop();
      }
    }
  }, null, null, [[0, 10]]);
}