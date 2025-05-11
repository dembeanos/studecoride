"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.inseeCity = void 0;
//Définition des inputs et variables 
var arrivalLat;
var arrivalLong;
var departLat;
var departLong;
var cityArrival = document.getElementById('cityArrival');
var cityDepart = document.getElementById('cityDepart'); //Ecoute du chargement complet de la page

window.addEventListener('load', function () {
  //Ecoute des input a chaque frappe une requete est envoyé
  cityArrival.addEventListener('input', function _callee() {
    var citySearch, cityList;
    return regeneratorRuntime.async(function _callee$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            citySearch = cityArrival.value;
            _context.next = 3;
            return regeneratorRuntime.awrap(fetchRequest(citySearch));

          case 3:
            cityList = _context.sent;
            arrivalCitySuggestion(cityList, 'arrivalSuggestionBox');

          case 5:
          case "end":
            return _context.stop();
        }
      }
    });
  });
  cityDepart.addEventListener('input', function _callee2() {
    var citySearch, cityList;
    return regeneratorRuntime.async(function _callee2$(_context2) {
      while (1) {
        switch (_context2.prev = _context2.next) {
          case 0:
            citySearch = cityDepart.value;
            _context2.next = 3;
            return regeneratorRuntime.awrap(fetchRequest(citySearch));

          case 3:
            cityList = _context2.sent;
            departCitySuggestion(cityList, 'departSuggestionBox');

          case 5:
          case "end":
            return _context2.stop();
        }
      }
    });
  });
}); // Méthode fetch d'envoi reception des données

function fetchRequest(data) {
  var request, responseText;
  return regeneratorRuntime.async(function fetchRequest$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          _context3.next = 2;
          return regeneratorRuntime.awrap(fetch("/Ecoride/src/Router/cityRoute.php", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              data: data
            })
          }));

        case 2:
          request = _context3.sent;

          if (request.ok) {
            _context3.next = 5;
            break;
          }

          throw new Error("Erreur HTTP : ".concat(request.status));

        case 5:
          _context3.next = 7;
          return regeneratorRuntime.awrap(request.text());

        case 7:
          responseText = _context3.sent;
          _context3.prev = 8;
          return _context3.abrupt("return", JSON.parse(responseText));

        case 12:
          _context3.prev = 12;
          _context3.t0 = _context3["catch"](8);
          console.error("Erreur de parsing JSON:", _context3.t0);
          throw _context3.t0;

        case 16:
        case "end":
          return _context3.stop();
      }
    }
  }, null, null, [[8, 12]]);
} //Variables importées dans user-update pour l'ajout d'offre de covaoiturage


var inseeCity = {
  inseeDepart: null,
  inseeArrival: null
};
exports.inseeCity = inseeCity;

function arrivalCitySuggestion(cityList) {
  var suggestionContainer = document.getElementById('arrivalSuggestionBox');
  suggestionContainer.innerHTML = '';

  if (cityList && cityList.data && cityList.data.length > 0) {
    console.log('Liste des villes :', cityList.data);
    cityList.data.forEach(function (city) {
      var div = document.createElement('div');
      div.textContent = city.city_code + ' - ' + city.department_name;
      div.addEventListener('mousedown', function (event) {
        event.preventDefault();
        cityArrival.value = city.city_code + ' - ' + city.department_name;
        suggestionContainer.style.display = 'none';
        arrivalLat = city.latitude;
        arrivalLong = city.longitude;
        inseeCity.inseeArrival = city.insee_code; // c'est ici que je renseigne inseeArrival

        if (arrivalLat && arrivalLong && departLat && departLong) {
          updateRoute(arrivalLat, arrivalLong, departLat, departLong);
        }
      });
      suggestionContainer.appendChild(div);
    });
    suggestionContainer.style.display = 'block';
  } else {
    suggestionContainer.style.display = 'none';
  }
}

cityArrival.addEventListener('blur', function () {
  var inputValue = this.value;
  var suggestionContainer = document.getElementById('arrivalSuggestionBox');
  var validSuggestions = Array.from(suggestionContainer.children).map(function (div) {
    return div.textContent;
  });

  if (!validSuggestions.includes(inputValue)) {
    this.value = '';
  }
});

function departCitySuggestion(cityList) {
  var suggestionContainer = document.getElementById('departSuggestionBox');
  suggestionContainer.innerHTML = '';

  if (cityList && cityList.data && cityList.data.length > 0) {
    console.log('Liste des villes :', cityList.data);
    cityList.data.forEach(function (city) {
      var div = document.createElement('div');
      div.textContent = city.city_code + ' - ' + city.department_name;
      div.addEventListener('mousedown', function (event) {
        event.preventDefault();
        cityDepart.value = city.city_code + ' - ' + city.department_name;
        suggestionContainer.style.display = 'none';
        departLat = city.latitude;
        departLong = city.longitude;
        inseeCity.inseeDepart = city.insee_code; // c'est ici que je renseigne inseeDepart

        if (arrivalLat && arrivalLong && departLat && departLong) {
          updateRoute(arrivalLat, arrivalLong, departLat, departLong);
        }
      });
      suggestionContainer.appendChild(div);
    });
    suggestionContainer.style.display = 'block';
  } else {
    suggestionContainer.style.display = 'none';
  }
}

cityDepart.addEventListener('blur', function () {
  var inputValue = this.value;
  var suggestionContainer = document.getElementById('departSuggestionBox');
  var validSuggestions = Array.from(suggestionContainer.children).map(function (div) {
    return div.textContent;
  });

  if (!validSuggestions.includes(inputValue)) {
    this.value = '';
  }
});