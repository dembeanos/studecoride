"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.initEmployeVar = initEmployeVar;
exports.setupButton = setupButton;
exports.employeInfo = employeInfo;
exports.opinion = opinion;

var _employeUpdate = require("./employe-update.js");

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance"); }

function _iterableToArrayLimit(arr, i) { if (!(Symbol.iterator in Object(arr) || Object.prototype.toString.call(arr) === "[object Arguments]")) { return; } var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

window.addEventListener("load", function () {
  var onglets = document.querySelectorAll(".onglets");
  var pages = document.querySelectorAll("section.pages");
  var functions = [employeInfo, opinion];

  var hidePages = function hidePages() {
    pages.forEach(function (page) {
      return page.style.display = "none";
    });
  };

  var showPage = function showPage(index) {
    hidePages();

    if (pages[index]) {
      pages[index].style.display = "block";
      var forms = pages[index].querySelectorAll("form");
      forms.forEach(function (form) {
        return form.reset();
      });

      if (typeof functions[index] === "function") {
        functions[index]();
      }

      localStorage.setItem('activePage', index);
    } else {
      console.error("La page avec l'index", index, "n'existe pas.");
    }
  };

  var activePage = localStorage.getItem('activePage');
  var initialPage = activePage !== null && !isNaN(activePage) && activePage < pages.length ? parseInt(activePage) : 0;
  hidePages();
  showPage(initialPage);
  onglets.forEach(function (onglet, index) {
    onglet.addEventListener("click", function (event) {
      event.preventDefault();
      showPage(index);
    });
  });
});

function initEmployeVar() {
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
  var inputPhoto = document.getElementById('photoUpload');
  var inputPass = {
    backPassword: document.getElementById("backPassword"),
    newPassword: document.getElementById("newPassword"),
    confirmPassword: document.getElementById("confirmPassword")
  };
  return {
    inputInfo: inputInfo,
    inputPhoto: inputPhoto,
    inputPass: inputPass
  };
}

function fetchRequest(action) {
  var request, responseText, responseData;
  return regeneratorRuntime.async(function fetchRequest$(_context) {
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
        var validOpinion = button.dataset.validOpinion;
        var rejectOpinion = button.dataset.rejectOpinion;
        var tripInfo = button.dataset.tripOpinion;

        switch (action) {
          case "updateEmployeInfo":
            functionToCall = _employeUpdate.updateEmployeInfo;
            break;

          case "updatePassword":
            functionToCall = _employeUpdate.updatePassword;
            break;

          case "updatePhoto":
            functionToCall = _employeUpdate.updatePhoto;
            break;

          case 'validationOpinion':
            functionToCall = function functionToCall() {
              return (0, _employeUpdate.validationOpinion)(validOpinion);
            };

            break;

          case 'rejectOp':
            functionToCall = function functionToCall() {
              return (0, _employeUpdate.rejectOp)(rejectOpinion);
            };

            break;

          case 'getTripInfo':
            functionToCall = function functionToCall() {
              return (0, _employeUpdate.getTripInfo)(tripInfo);
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

function employeInfo() {
  var requests, _ref3, _ref4, _employeInfo, employePhoto, employeInfoData, _initEmployeVar, inputInfo, key, photoOnglet, photoProfile, buttons;

  return regeneratorRuntime.async(function employeInfo$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          _context2.prev = 0;
          requests = [fetchRequest('getEmployeeInfo'), fetchRequest('getPhoto')];
          _context2.next = 4;
          return regeneratorRuntime.awrap(Promise.all(requests));

        case 4:
          _ref3 = _context2.sent;
          _ref4 = _slicedToArray(_ref3, 2);
          _employeInfo = _ref4[0];
          employePhoto = _ref4[1];

          if (_employeInfo && _employeInfo.status === 'success') {
            employeInfoData = _employeInfo.data;
            _initEmployeVar = initEmployeVar(), inputInfo = _initEmployeVar.inputInfo;

            for (key in employeInfoData) {
              if (inputInfo[key]) {
                inputInfo[key].value = employeInfoData[key];
              }
            }
          } else {
            console.error('js : Erreur lors de la récupération des données administrateur');
          }

          if (employePhoto) {
            console.log(employePhoto);
            photoOnglet = document.getElementById('employePhotoOnglet');
            photoProfile = document.getElementById('employePhotoProfil');

            if (photoOnglet || photoProfile) {
              photoOnglet.src = employePhoto;
              photoProfile.src = employePhoto;
            }
          } else {
            console.log('Erreur : Photo manquante dans la réponse');
          }

          buttons = {
            updateEmployeInfo: document.getElementById("sendInfo"),
            updatePassword: document.getElementById("sendPassword"),
            updatePhoto: document.getElementById("updatePhoto")
          };
          setupButton(buttons);
          _context2.next = 17;
          break;

        case 14:
          _context2.prev = 14;
          _context2.t0 = _context2["catch"](0);
          console.error('Erreur lors de la requête:', _context2.t0);

        case 17:
        case "end":
          return _context2.stop();
      }
    }
  }, null, null, [[0, 14]]);
}

function opinion() {
  var opinionInfo, opinionContainer, model, allOpinion, currentPage, opinionPerPage, displayOpinion, sortOrder, sortTable;
  return regeneratorRuntime.async(function opinion$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          sortTable = function _ref6(column) {
            sortOrder[column] = sortOrder[column] === 'asc' ? 'desc' : 'asc';
            allOpinion.sort(function (a, b) {
              var aValue = a[column].toString().trim();
              var bValue = b[column].toString().trim();
              return sortOrder[column] === 'asc' ? aValue.localeCompare(bValue, undefined, {
                numeric: true
              }) : bValue.localeCompare(aValue, undefined, {
                numeric: true
              });
            });
            displayOpinion();
          };

          displayOpinion = function _ref5() {
            opinionContainer.innerHTML = '';
            var startIndex = (currentPage - 1) * opinionPerPage;
            var endIndex = currentPage * opinionPerPage;
            var opinionToDisplay = allOpinion.slice(startIndex, endIndex);
            opinionToDisplay.forEach(function (opinion) {
              var newDiv = model.cloneNode(true);
              newDiv.querySelector('.opinionRef').textContent = opinion.opinionid;
              newDiv.querySelector('.note').textContent = opinion.note;
              newDiv.querySelector('.message').textContent = opinion.comment;
              newDiv.querySelector('.opinionDate').textContent = opinion.creationdate;
              var validCell = newDiv.querySelector('.opinionvalidation');
              var rejectCell = newDiv.querySelector('.rejectOpinion');
              var tripInfo = newDiv.querySelector('.tripInfo');
              var existingValidButton = validCell.querySelector('.valid-opinion');

              if (!existingValidButton) {
                var validButton = document.createElement('button');
                validButton.textContent = 'Valider';
                validButton.classList.add("valid-opinion");
                validButton.dataset.validOpinion = opinion.opinionid;
                validCell.appendChild(validButton);
                setupButton({
                  validationOpinion: validButton
                });
              }

              var existingRejectButton = rejectCell.querySelector('.reject-opinion');

              if (!existingRejectButton) {
                var rejectButton = document.createElement('button');
                rejectButton.textContent = 'Rejeter';
                rejectButton.classList.add("reject-opinion");
                rejectButton.dataset.rejectOpinion = opinion.opinionid;
                rejectCell.appendChild(rejectButton);
                setupButton({
                  rejectOp: rejectButton
                });
              }

              var existingTripButton = tripInfo.querySelector('.trip-opinion');

              if (!existingTripButton) {
                var tripButton = document.createElement('button');
                tripButton.textContent = 'Détails';
                tripButton.classList.add("reject-opinion");
                tripButton.dataset.tripOpinion = opinion.opinionid;
                tripInfo.appendChild(tripButton);
                setupButton({
                  getTripInfo: tripButton
                });
              }

              opinionContainer.appendChild(newDiv);
            });
            document.getElementById('currentPage').textContent = "Page ".concat(currentPage);
          };

          _context3.next = 4;
          return regeneratorRuntime.awrap(fetchRequest('getOpinion'));

        case 4:
          opinionInfo = _context3.sent;
          opinionContainer = document.getElementById('opinionContainer');
          model = opinionContainer.querySelector('.opinionRow');
          allOpinion = opinionInfo;
          currentPage = 1;
          opinionPerPage = 10;
          opinionContainer.innerHTML = '';
          displayOpinion();
          document.getElementById('nextPage').addEventListener('click', function () {
            if (currentPage * opinionPerPage < allOpinion.length) {
              currentPage++;
              displayOpinion();
            }
          });
          document.getElementById('prevPage').addEventListener('click', function () {
            if (currentPage > 1) {
              currentPage--;
              displayOpinion();
            }
          });
          sortOrder = _defineProperty({
            opinionRef: 'asc',
            note: 'asc',
            firstname: 'asc',
            creationdate: 'asc'
          }, "note", 'asc');
          document.getElementById('sortOpinionId').addEventListener('click', function () {
            return sortTable('opinionRef');
          });
          document.getElementById('sortBynote').addEventListener('click', function () {
            return sortTable('note');
          });

        case 17:
        case "end":
          return _context3.stop();
      }
    }
  });
}