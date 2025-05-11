"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.initAdminVar = initAdminVar;
exports.setupButton = setupButton;
exports.adminInfo = adminInfo;
exports.employe = employe;
exports.tripStat = tripStat;
exports.moneyStat = moneyStat;
exports.errorLog = errorLog;

var _adminUpdate = require("./admin-update.js");

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance"); }

function _iterableToArrayLimit(arr, i) { if (!(Symbol.iterator in Object(arr) || Object.prototype.toString.call(arr) === "[object Arguments]")) { return; } var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

window.addEventListener("load", function () {
  var onglets = document.querySelectorAll(".onglets");
  var pages = document.querySelectorAll("section");
  var functions = [adminInfo, users, employe, tripStat, moneyStat, errorLog];

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

function initAdminVar() {
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
  var addUserInfo = {
    firstName: document.getElementById("addUserFirstName"),
    lastName: document.getElementById("addUserLastName"),
    username: document.getElementById("addUserUsername"),
    password: document.getElementById("addUserPassword"),
    confirmPassword: document.getElementById("addUserConfirmPassword"),
    phone: document.getElementById("addUserPhone"),
    email: document.getElementById("addUserEmail"),
    road: document.getElementById("addUserRoad"),
    roadComplement: document.getElementById("addUserRoadComplement"),
    zipCode: document.getElementById("addUserZipCode"),
    city: document.getElementById("addUserCity"),
    role: document.getElementById("role"),
    subscribeButton: document.getElementById("subscribe")
  };
  var inputPhoto = document.getElementById('photoUpload');
  var inputPass = {
    backPassword: document.getElementById("backPassword"),
    newPassword: document.getElementById("newPassword"),
    confirmPassword: document.getElementById("confirmPassword")
  };
  return {
    inputInfo: inputInfo,
    addUserInfo: addUserInfo,
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
          return regeneratorRuntime.awrap(fetch("/Ecoride/src/Router/adminRoute.php", {
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
        var banId = button.dataset.banId;
        var employeId = button.dataset.employeId;

        switch (action) {
          case "updateAdminInfo":
            functionToCall = _adminUpdate.updateAdminInfo;
            break;

          case "updatePassword":
            functionToCall = _adminUpdate.updatePassword;
            break;

          case "updatePhoto":
            functionToCall = _adminUpdate.updatePhoto;
            break;

          case "updateUser":
            functionToCall = function functionToCall() {
              return (0, _adminUpdate.updateUser)(banId);
            };

            break;

          case "updateEmploye":
            functionToCall = function functionToCall() {
              return (0, _adminUpdate.updateEmploye)(employeId);
            };

            break;

          case "banUser":
            functionToCall = function functionToCall() {
              return (0, _adminUpdate.banUser)(banId);
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

function adminInfo() {
  var requests, _ref3, _ref4, _adminInfo, adminPhoto, adminInfoData, _initAdminVar, inputInfo, key, photoOnglet, photoProfile, buttons;

  return regeneratorRuntime.async(function adminInfo$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          _context2.prev = 0;
          requests = [fetchRequest('getAdminInfo'), fetchRequest('getPhoto')];
          _context2.next = 4;
          return regeneratorRuntime.awrap(Promise.all(requests));

        case 4:
          _ref3 = _context2.sent;
          _ref4 = _slicedToArray(_ref3, 2);
          _adminInfo = _ref4[0];
          adminPhoto = _ref4[1];

          if (_adminInfo && _adminInfo.status === 'success') {
            adminInfoData = _adminInfo.data;
            _initAdminVar = initAdminVar(), inputInfo = _initAdminVar.inputInfo;

            for (key in adminInfoData) {
              if (inputInfo[key]) {
                inputInfo[key].value = adminInfoData[key];
              }
            }
          } else {
            console.error('js : Erreur lors de la récupération des données administrateur');
          }

          if (adminPhoto) {
            console.log(adminPhoto);
            photoOnglet = document.getElementById('adminPhotoOnglet');
            photoProfile = document.getElementById('adminPhotoProfil');

            if (photoOnglet || photoProfile) {
              photoOnglet.src = adminPhoto;
              photoProfile.src = adminPhoto;
            }
          } else {
            console.log('Erreur : Photo manquante dans la réponse');
          }

          buttons = {
            updateAdminInfo: document.getElementById("sendInfo"),
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

function users() {
  var usersInfo, userContainer, model, allUsers, currentPage, usersPerPage, displayUsers, sortOrder, sortTable;
  return regeneratorRuntime.async(function users$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          sortTable = function _ref6(column) {
            sortOrder[column] = sortOrder[column] === 'asc' ? 'desc' : 'asc';
            allUsers.sort(function (a, b) {
              var aValue = a[column].toString().trim();
              var bValue = b[column].toString().trim();
              return sortOrder[column] === 'asc' ? aValue.localeCompare(bValue, undefined, {
                numeric: true
              }) : bValue.localeCompare(aValue, undefined, {
                numeric: true
              });
            });
            displayUsers();
          };

          displayUsers = function _ref5() {
            userContainer.innerHTML = '';
            var startIndex = (currentPage - 1) * usersPerPage;
            var endIndex = currentPage * usersPerPage;
            var usersToDisplay = allUsers.slice(startIndex, endIndex);
            usersToDisplay.forEach(function (user) {
              var newDiv = model.cloneNode(true);
              newDiv.querySelector('.usersRef').textContent = user.userid;
              newDiv.querySelector('.firstname').textContent = user.firstname;
              newDiv.querySelector('.lastname').textContent = user.lastname;
              newDiv.querySelector('.usersPhone').textContent = user.phone;
              newDiv.querySelector('.usersRoad').textContent = user.road;
              newDiv.querySelector('.zipcode').textContent = user.zipcode;
              newDiv.querySelector('.usersCity').textContent = user.city;
              newDiv.querySelector('.usersCreationDate').textContent = user.creationdate;
              newDiv.querySelector('.usersNote').textContent = user.note;
              newDiv.querySelector('.usersRole').textContent = user.userrole;
              newDiv.querySelector('.usersCredit').textContent = user.credit;
              var actionCell = newDiv.querySelector('.usersAction');
              var existingBanButton = actionCell.querySelector('.ban-user');

              if (!existingBanButton) {
                var banButton = document.createElement('button');
                banButton.textContent = 'Bannir';
                banButton.classList.add("ban-user");
                banButton.dataset.banId = user.idlogin;
                actionCell.appendChild(banButton);
                setupButton({
                  banUser: banButton
                });
              }

              userContainer.appendChild(newDiv);
            });
            document.getElementById('currentPage').textContent = "Page ".concat(currentPage);
          };

          _context3.next = 4;
          return regeneratorRuntime.awrap(fetchRequest('getUsers'));

        case 4:
          usersInfo = _context3.sent;
          userContainer = document.getElementById('usersContainer');
          model = userContainer.querySelector('.userRow');
          allUsers = usersInfo;
          currentPage = 1;
          usersPerPage = 10;
          userContainer.innerHTML = '';
          displayUsers();
          document.getElementById('nextPage').addEventListener('click', function () {
            if (currentPage * usersPerPage < allUsers.length) {
              currentPage++;
              displayUsers();
            }
          });
          document.getElementById('prevPage').addEventListener('click', function () {
            if (currentPage > 1) {
              currentPage--;
              displayUsers();
            }
          });
          sortOrder = {
            userid: 'asc',
            lastname: 'asc',
            firstname: 'asc',
            creationdate: 'asc',
            note: 'asc'
          };
          document.getElementById('sortUserid').addEventListener('click', function () {
            return sortTable('userid');
          });
          document.getElementById('sortLastname').addEventListener('click', function () {
            return sortTable('lastname');
          });
          document.getElementById('sortFirstname').addEventListener('click', function () {
            return sortTable('firstname');
          });
          document.getElementById('sortCreationDate').addEventListener('click', function () {
            return sortTable('creationdate');
          });
          document.getElementById('sortNote').addEventListener('click', function () {
            return sortTable('note');
          });

        case 20:
        case "end":
          return _context3.stop();
      }
    }
  });
}

function employe() {
  var _initAdminVar2, addUserInfo, employeeInfo, employeContainer, model, allemploye, currentPage, employePerPage, displayUsers, sortOrder, sortTable;

  return regeneratorRuntime.async(function employe$(_context5) {
    while (1) {
      switch (_context5.prev = _context5.next) {
        case 0:
          sortTable = function _ref8(column) {
            sortOrder[column] = sortOrder[column] === 'asc' ? 'desc' : 'asc';
            allemploye.sort(function (a, b) {
              var aValue = a[column].toString().trim();
              var bValue = b[column].toString().trim();
              return sortOrder[column] === 'asc' ? aValue.localeCompare(bValue, undefined, {
                numeric: true
              }) : bValue.localeCompare(aValue, undefined, {
                numeric: true
              });
            });
            displayUsers();
          };

          displayUsers = function _ref7() {
            employeContainer.innerHTML = '';
            var startIndex = (currentPage - 1) * employePerPage;
            var endIndex = currentPage * employePerPage;
            var employeToDisplay = allemploye.slice(startIndex, endIndex);
            employeToDisplay.forEach(function (employe) {
              var newDiv = model.cloneNode(true);
              newDiv.querySelector('.EmployeRef').textContent = employe.employeid;
              newDiv.querySelector('.employefirstname').textContent = employe.firstname;
              newDiv.querySelector('.employelastname').textContent = employe.lastname;
              newDiv.querySelector('.employePhone').textContent = employe.phone;
              newDiv.querySelector('.employeRoad').textContent = employe.road;
              newDiv.querySelector('.employezipcode').textContent = employe.zipcode;
              newDiv.querySelector('.employeCity').textContent = employe.city;
              newDiv.querySelector('.employeCreationDate').textContent = employe.creationdate;
              var actionCell = newDiv.querySelector('.employeAction');
              var existingBanButton = actionCell.querySelector('.ban-user');

              if (!existingBanButton) {
                var banButton = document.createElement('button');
                banButton.textContent = 'Bannir';
                banButton.classList.add("ban-user");
                banButton.dataset.banId = employe.idlogin;
                actionCell.appendChild(banButton);
                setupButton({
                  banUser: banButton
                });
              }

              employeContainer.appendChild(newDiv);
            });
            document.getElementById('employeCurrentPage').textContent = "Page ".concat(currentPage);
          };

          _initAdminVar2 = initAdminVar(), addUserInfo = _initAdminVar2.addUserInfo;
          addUserInfo.subscribeButton.addEventListener('click', function (e) {
            e.preventDefault();
            var formData = {
              lastName: addUserInfo.lastName.value,
              firstName: addUserInfo.firstName.value,
              email: addUserInfo.email.value,
              username: addUserInfo.username.value,
              password: addUserInfo.password.value,
              confirmPassword: addUserInfo.confirmPassword.value,
              phone: addUserInfo.phone.value,
              road: addUserInfo.road.value,
              roadComplement: addUserInfo.roadComplement.value,
              zipCode: addUserInfo.zipCode.value,
              city: addUserInfo.city.value,
              userType: addUserInfo.role.value
            };
            fetchRequest(formData);
            console.log('Données envoyées par js :', formData);

            function fetchRequest(formData) {
              var request, responseText, responseData;
              return regeneratorRuntime.async(function fetchRequest$(_context4) {
                while (1) {
                  switch (_context4.prev = _context4.next) {
                    case 0:
                      _context4.prev = 0;
                      _context4.next = 3;
                      return regeneratorRuntime.awrap(fetch('/Ecoride/src/Router/subscribeRoute.php', {
                        method: 'POST',
                        headers: {
                          'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                          formData: formData
                        })
                      }));

                    case 3:
                      request = _context4.sent;
                      _context4.next = 6;
                      return regeneratorRuntime.awrap(request.text());

                    case 6:
                      responseText = _context4.sent;
                      console.log(responseText);
                      responseData = JSON.parse(responseText);
                      handleResponse(responseData);
                      _context4.next = 16;
                      break;

                    case 12:
                      _context4.prev = 12;
                      _context4.t0 = _context4["catch"](0);
                      console.error("Erreur lors de la récupération de la réponse :", _context4.t0);
                      handleResponse(_context4.t0);

                    case 16:
                    case "end":
                      return _context4.stop();
                  }
                }
              }, null, null, [[0, 12]]);
            }
          });
          _context5.next = 6;
          return regeneratorRuntime.awrap(fetchRequest('getEmployees'));

        case 6:
          employeeInfo = _context5.sent;
          employeContainer = document.getElementById('employeContainer');
          model = employeContainer.querySelector('.employeRow');
          allemploye = employeeInfo;
          currentPage = 1;
          employePerPage = 10;
          employeContainer.innerHTML = '';
          displayUsers();
          document.getElementById('employeNextPage').addEventListener('click', function () {
            if (currentPage * employePerPage < allemploye.length) {
              currentPage++;
              displayUsers();
            }
          });
          document.getElementById('employePrevPage').addEventListener('click', function () {
            if (currentPage > 1) {
              currentPage--;
              displayUsers();
            }
          });
          sortOrder = {
            employeid: 'asc',
            lastname: 'asc',
            firstname: 'asc',
            creationdate: 'asc'
          };
          document.getElementById('sortEmployeid').addEventListener('click', function () {
            return sortTable('employeid');
          });
          document.getElementById('sortEmployeLastname').addEventListener('click', function () {
            return sortTable('lastname');
          });
          document.getElementById('sortEmployeFirstname').addEventListener('click', function () {
            return sortTable('firstname');
          });
          document.getElementById('sortEmployeCreationDate').addEventListener('click', function () {
            return sortTable('creationdate');
          });

        case 21:
        case "end":
          return _context5.stop();
      }
    }
  });
}

;

function tripStat() {
  var trendsInfo, totalInfo, dailyData, total, currentYear, formatDate, labels, data, config, ctx;
  return regeneratorRuntime.async(function tripStat$(_context6) {
    while (1) {
      switch (_context6.prev = _context6.next) {
        case 0:
          formatDate = function _ref9(dateStr) {
            var _dateStr$split = dateStr.split('-'),
                _dateStr$split2 = _slicedToArray(_dateStr$split, 3),
                year = _dateStr$split2[0],
                month = _dateStr$split2[1],
                day = _dateStr$split2[2];

            return "".concat(parseInt(day), "-").concat(parseInt(month), "-").concat(year);
          };

          _context6.next = 3;
          return regeneratorRuntime.awrap(fetchRequest('getTrends'));

        case 3:
          trendsInfo = _context6.sent;
          totalInfo = document.getElementById('totalOffer');
          dailyData = {};
          total = 0;
          currentYear = new Date().getFullYear();
          trendsInfo.forEach(function (item) {
            var date = new Date(item.datedepart);

            if (date.getFullYear() === currentYear) {
              var dayKey = "".concat(date.getFullYear(), "-").concat(date.getMonth() + 1, "-").concat(date.getDate());
              total++;

              if (dailyData[dayKey]) {
                dailyData[dayKey] += 1;
              } else {
                dailyData[dayKey] = 1;
              }
            }
          });
          totalInfo.textContent = "Total de covoiturages sur l'ann\xE9e : ".concat(total);
          labels = Object.keys(dailyData).map(function (date) {
            return formatDate(date);
          });
          data = Object.values(dailyData);
          config = {
            type: 'bar',
            data: {
              labels: labels,
              datasets: [{
                label: "Covoiturages en ".concat(currentYear),
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
              }]
            },
            options: {
              scales: {
                y: {
                  beginAtZero: true
                }
              }
            }
          };
          ctx = document.getElementById('offerChart').getContext('2d');
          new Chart(ctx, config);

        case 15:
        case "end":
          return _context6.stop();
      }
    }
  });
}

function moneyStat() {
  var profitInfo, totalInfo, dailyData, total, currentYear, formatDate, labels, data, config, ctx;
  return regeneratorRuntime.async(function moneyStat$(_context7) {
    while (1) {
      switch (_context7.prev = _context7.next) {
        case 0:
          formatDate = function _ref10(dateStr) {
            var _dateStr$split3 = dateStr.split('-'),
                _dateStr$split4 = _slicedToArray(_dateStr$split3, 3),
                year = _dateStr$split4[0],
                month = _dateStr$split4[1],
                day = _dateStr$split4[2];

            return "".concat(parseInt(day), "-").concat(parseInt(month), "-").concat(year);
          };

          _context7.next = 3;
          return regeneratorRuntime.awrap(fetchRequest('getProfit'));

        case 3:
          profitInfo = _context7.sent;
          totalInfo = document.getElementById('totalCA');
          dailyData = {};
          total = 0;
          currentYear = new Date().getFullYear();

          if (window.myChart) {
            window.myChart.destroy();
          }

          profitInfo.forEach(function (item) {
            var date = new Date(item.creationdate);

            if (date.getFullYear() === currentYear) {
              var isoDate = date.toISOString().split('T')[0];
              var credit = parseFloat(item.credit);
              total += credit;

              if (!dailyData[isoDate]) {
                dailyData[isoDate] = 0;
              }

              dailyData[isoDate] += credit;
            }
          });
          totalInfo.textContent = "Total CA : ".concat(total.toFixed(2), " \u20AC");
          labels = Object.keys(dailyData).map(function (date) {
            return formatDate(date);
          });
          data = Object.values(dailyData);
          config = {
            type: 'bar',
            data: {
              labels: labels,
              datasets: [{
                label: "Cr\xE9dits gagn\xE9s par jour en ".concat(currentYear),
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
              }]
            },
            options: {
              scales: {
                y: {
                  beginAtZero: true
                },
                x: {
                  ticks: {
                    maxRotation: 90,
                    minRotation: 45
                  }
                }
              }
            }
          };
          ctx = document.getElementById('profitChart').getContext('2d');
          new Chart(ctx, config);

        case 16:
        case "end":
          return _context7.stop();
      }
    }
  });
}

function errorLog() {
  var logInfo, logContainer, model, allLog, currentPage, logPerPage, sortOrder, displayLog, sortTable, nextBtn, prevBtn, sortDateBtn, sortLevelBtn;
  return regeneratorRuntime.async(function errorLog$(_context8) {
    while (1) {
      switch (_context8.prev = _context8.next) {
        case 0:
          sortTable = function _ref12(column) {
            var key = column === 'date' ? 'timestamp' : column;
            sortOrder[key] = sortOrder[key] === 'asc' ? 'desc' : 'asc';
            allLog.sort(function (a, b) {
              var aValue = a[key].toString().trim();
              var bValue = b[key].toString().trim();
              return sortOrder[key] === 'asc' ? aValue.localeCompare(bValue, undefined, {
                numeric: true
              }) : bValue.localeCompare(aValue, undefined, {
                numeric: true
              });
            });
            displayLog();
          };

          displayLog = function _ref11() {
            logContainer.innerHTML = '';
            var startIndex = (currentPage - 1) * logPerPage;
            var endIndex = currentPage * logPerPage;
            var logToDisplay = allLog.slice(startIndex, endIndex);
            logToDisplay.forEach(function (log) {
              var newDiv = model.cloneNode(true);
              newDiv.style.display = 'flex';
              newDiv.querySelector('.logDate').textContent = log.timestamp;
              newDiv.querySelector('.message').textContent = log.message;
              newDiv.querySelector('.loglevel').textContent = log.loglevel;
              logContainer.appendChild(newDiv);
            });
            var currentPageLabel = document.getElementById('currentLogPage');

            if (currentPageLabel) {
              currentPageLabel.textContent = "Page ".concat(currentPage);
            }
          };

          _context8.next = 4;
          return regeneratorRuntime.awrap(fetchRequest('getLogs'));

        case 4:
          logInfo = _context8.sent;
          logContainer = document.getElementById('logContainer');
          model = logContainer.querySelector('.logRow');
          model.style.display = 'none';
          allLog = logInfo;
          currentPage = 1;
          logPerPage = 10;
          sortOrder = {
            timestamp: 'asc',
            loglevel: 'asc'
          };
          // Navigation
          nextBtn = document.getElementById('nextLogPage');

          if (nextBtn) {
            nextBtn.addEventListener('click', function () {
              if (currentPage * logPerPage < allLog.length) {
                currentPage++;
                displayLog();
              }
            });
          }

          prevBtn = document.getElementById('prevLogPage');

          if (prevBtn) {
            prevBtn.addEventListener('click', function () {
              if (currentPage > 1) {
                currentPage--;
                displayLog();
              }
            });
          } // Tri


          sortDateBtn = document.getElementById('sortLogDate');

          if (sortDateBtn) {
            sortDateBtn.addEventListener('click', function () {
              return sortTable('date');
            });
          }

          sortLevelBtn = document.getElementById('sortLogLevel');

          if (sortLevelBtn) {
            sortLevelBtn.addEventListener('click', function () {
              return sortTable('loglevel');
            });
          }

          displayLog();

        case 21:
        case "end":
          return _context8.stop();
      }
    }
  });
}