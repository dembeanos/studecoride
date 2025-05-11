"use strict";

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

window.addEventListener('load', function (event) {
  var lastName = document.getElementById('lastName');
  var firstName = document.getElementById('firstName');
  var email = document.getElementById('email');
  var username = document.getElementById('username');
  var password = document.getElementById('password');
  var confirmPassword = document.getElementById('confirmPassword');
  var phone = document.getElementById('phone');
  var road = document.getElementById('road');
  var roadComplement = document.getElementById('roadComplement');
  var zipCode = document.getElementById('zipCode');
  var city = document.getElementById('city');
  var userTypeElement = document.getElementById('userType');
  var userType = userTypeElement ? userTypeElement.value : undefined;
  var button = document.getElementById('subscribe');
  button.addEventListener('click', function (e) {
    e.preventDefault();

    var formData = _objectSpread({
      lastName: lastName.value,
      firstName: firstName.value,
      email: email.value,
      username: username.value,
      password: password.value,
      confirmPassword: confirmPassword.value,
      phone: phone.value,
      road: road.value,
      roadComplement: roadComplement.value,
      zipCode: zipCode.value,
      city: city.value
    }, userType && {
      userType: userType
    });

    fetchRequest(formData);
    console.log('Données envoyées par js :', formData);
  });

  function fetchRequest(formData) {
    var request, responseText, responseData;
    return regeneratorRuntime.async(function fetchRequest$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            _context.prev = 0;
            _context.next = 3;
            return regeneratorRuntime.awrap(fetch("/Ecoride/src/Router/subscribeRoute.php", {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                formData: formData
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
            handleResponse(responseData);
            _context.next = 16;
            break;

          case 12:
            _context.prev = 12;
            _context.t0 = _context["catch"](0);
            console.error("Erreur lors de la récupération de la réponse :", _context.t0);
            handleResponse(_context.t0);

          case 16:
          case "end":
            return _context.stop();
        }
      }
    }, null, null, [[0, 12]]);
  }
});