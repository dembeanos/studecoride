"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.sendOpinion = sendOpinion;

function sendOpinion(reservationId, trajet) {
  var avisHTML;
  return regeneratorRuntime.async(function sendOpinion$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          avisHTML = "\n    <fieldset class=\"sent-opinion\">\n        <legend>Votre Avis sur le trajet ".concat(trajet, "</legend>\n        <div id=\"opinionSection\">\n            <form id=\"opinionForm\">\n                <label for=\"note\">Note :</label>\n                <select id=\"note\" required>\n                    <option value=\"1\">\u2B50</option>\n                    <option value=\"2\">\u2B50\u2B50</option>\n                    <option value=\"3\">\u2B50\u2B50\u2B50</option>\n                    <option value=\"4\">\u2B50\u2B50\u2B50\u2B50</option>\n                    <option value=\"5\">\u2B50\u2B50\u2B50\u2B50\u2B50</option>\n                </select>\n                <label for=\"containOpinion\">Votre avis :</label>\n                <textarea id=\"containOpinion\" name=\"containOpinion\" required></textarea>\n                <button id=\"addOpinion\" type=\"submit\">Envoyer l'avis</button>\n            </form>\n        </div>\n    </fieldset>\n    ");
          sendInteractivePopup(avisHTML);
          _context.next = 4;
          return regeneratorRuntime.awrap(opinion(reservationId));

        case 4:
        case "end":
          return _context.stop();
      }
    }
  });
}

function opinion(reservationId) {
  var form;
  return regeneratorRuntime.async(function opinion$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          form = document.getElementById('opinionForm');
          form.addEventListener('submit', function _callee(event) {
            var note, text, data, response, json;
            return regeneratorRuntime.async(function _callee$(_context2) {
              while (1) {
                switch (_context2.prev = _context2.next) {
                  case 0:
                    event.preventDefault();
                    note = document.getElementById('note').value;
                    text = document.getElementById('containOpinion').value;
                    data = {
                      note: note,
                      opinionText: text,
                      srcOpinion: reservationId
                    };
                    _context2.prev = 4;
                    _context2.next = 7;
                    return regeneratorRuntime.awrap(fetch("/Ecoride/src/Router/userRoute.php", {
                      method: 'POST',
                      headers: {
                        'Content-Type': 'application/json'
                      },
                      body: JSON.stringify({
                        action: 'addOpinion',
                        data: data
                      })
                    }));

                  case 7:
                    response = _context2.sent;
                    _context2.next = 10;
                    return regeneratorRuntime.awrap(response.json());

                  case 10:
                    json = _context2.sent;
                    console.log(json);

                    if (json.type) {
                      handleResponse(json);
                    }

                    _context2.next = 18;
                    break;

                  case 15:
                    _context2.prev = 15;
                    _context2.t0 = _context2["catch"](4);
                    console.error('Erreur lors de l\'envoi de l\'avis :', _context2.t0);

                  case 18:
                  case "end":
                    return _context2.stop();
                }
              }
            }, null, null, [[4, 15]]);
          });

        case 2:
        case "end":
          return _context3.stop();
      }
    }
  });
}