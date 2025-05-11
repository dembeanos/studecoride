"use strict";

var _autocomplete = require("../../ui/menu/autocomplete.js");

var _results = require("../../ui/covoiturage/results.js");

// Importation des modules nécessaires
// ------------------ 1. Récupération des éléments DOM ------------------
var zone = document.getElementById('zone');
var cityDepart = document.getElementById('cityDepart');
var arrivalCity = document.getElementById('cityArrival');
var departDate = document.getElementById('departureDate');
var arrivalDate = document.getElementById('arrivalDate');
var places = document.getElementById('places');
var smoke = document.getElementById('smoke');
var animal = document.getElementById('animal');
var eco = document.getElementById('eco');
var duration = document.getElementById('tripDuration');
var note = document.getElementById('note');
var kmRange = document.getElementById('kmRange'); // Boutons

var resetFilterButton = document.getElementById('resetFilter');
var getResultButton = document.getElementById('getResult'); // Sélection de tri

var sortBy = document.getElementById('sortby');
var currentResults = []; // ------------------ 2. Gestion des événements ------------------

sortBy.addEventListener('change', function () {
  if (currentResults.length > 0) {
    var sortedData = SortResult(sortBy.value, currentResults);
    (0, _results.showResult)(sortedData);
  }
});
resetFilterButton.addEventListener('click', function (event) {
  event.preventDefault();
  kmRange.value = 10;
  zone.textContent = "10";
  cityDepart.value = '';
  arrivalCity.value = '';
  departDate.value = '';
  arrivalDate.value = '';
  places.value = '';
  smoke.checked = false;
  animal.checked = false;
  eco.checked = false;
  duration.value = '';
  note.value = '';
});
kmRange.addEventListener('input', function () {
  zone.textContent = kmRange.value;
});
getResultButton.addEventListener('click', function _callee(event) {
  var today, data, results, request, resultsData;
  return regeneratorRuntime.async(function _callee$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          event.preventDefault();
          today = new Date().toISOString().split('T')[0];
          data = {
            inseeDepart: _autocomplete.inseeCity.inseeDepart,
            inseeArrival: _autocomplete.inseeCity.inseeArrival,
            zone: kmRange.value || 10,
            departDate: departDate.value || today,
            arrivalDate: arrivalDate.value,
            places: places.value || 1,
            smoke: smoke.checked,
            animal: animal.checked,
            eco: eco.checked,
            duration: duration.value || '99h00',
            note: note.value || 1
          }; // ------------------ 3. Envoi de la requête ------------------

          _context.prev = 3;
          console.log(data);
          _context.next = 7;
          return regeneratorRuntime.awrap(fetch("/Ecoride/src/Router/searchRoute.php", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              data: data
            })
          }));

        case 7:
          request = _context.sent;

          if (request.ok) {
            _context.next = 11;
            break;
          }

          console.log("La requ\xEAte a \xE9chou\xE9 avec le statut ".concat(request.status));
          return _context.abrupt("return");

        case 11:
          _context.next = 13;
          return regeneratorRuntime.awrap(request.json());

        case 13:
          results = _context.sent;
          console.log(results);

          if (results.type) {
            handleResponse(results);
          }

          if (results.status === 'success') {
            currentResults = results.data;
            resultsData = SortResult(sortBy.value, currentResults);
            (0, _results.showResult)(resultsData);
          }

          _context.next = 22;
          break;

        case 19:
          _context.prev = 19;
          _context.t0 = _context["catch"](3);
          console.error("Une erreur s'est produite lors de l'envoi de la requête :", _context.t0);

        case 22:
        case "end":
          return _context.stop();
      }
    }
  }, null, null, [[3, 19]]);
}); // ------------------ 4. Fonction de tri des résultats ------------------

function SortResult(sortBy, results) {
  switch (sortBy) {
    case 'priceasc':
      return results.slice().sort(function (a, b) {
        return parseFloat(a.price) - parseFloat(b.price);
      });

    case 'pricedesc':
      return results.slice().sort(function (a, b) {
        return parseFloat(b.price) - parseFloat(a.price);
      });

    case 'datedepartasc':
      return results.slice().sort(function (a, b) {
        return new Date(a.datedepart) - new Date(b.datedepart);
      });

    case 'datedepartdesc':
      return results.slice().sort(function (a, b) {
        return new Date(b.datedepart) - new Date(a.datedepart);
      });

    default:
      return results;
  }
}