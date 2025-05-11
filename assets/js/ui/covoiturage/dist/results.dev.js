"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.showResult = showResult;

function showResult(results) {
  // Sélectionner l'élément de conteneur des résultats
  var resultContainer = document.getElementById('resultContainer'); // Vider le conteneur avant d'afficher de nouveaux résultats

  resultContainer.innerHTML = ''; // Vérifier s'il y a des résultats

  if (results.length === 0) {
    resultContainer.innerHTML = '<p>Aucun résultat trouvé.</p>';
    return;
  } // Créer une carte pour chaque résultat


  results.forEach(function (result) {
    // Créer un élément div pour chaque "carte"
    var resultDiv = document.createElement('div');
    resultDiv.classList.add('result-item'); // Ajouter une classe CSS pour le style
    // Ajouter du contenu spécifique à chaque emplacement de la carte

    resultDiv.innerHTML = "\n            <h3>".concat(result.title, "</h3>\n            <p>Prix: ").concat(result.price, " \u20AC</p>\n            <p>Date de d\xE9part: ").concat(new Date(result.departureDate).toLocaleDateString(), "</p>\n            <p>Note: ").concat(result.note, " / 5</p>\n            <button class=\"select-button\" data-id=\"").concat(result.id, "\">S\xE9lectionner</button>\n        "); // Ajouter cette carte au conteneur principal

    resultContainer.appendChild(resultDiv);
  }); // Ajouter une action au bouton "Sélectionner" (si tu veux)

  var selectButtons = document.querySelectorAll('.select-button');
  selectButtons.forEach(function (button) {
    button.addEventListener('click', function (event) {
      var resultId = event.target.getAttribute('data-id');
      handleSelection(resultId);
    });
  });
} // Exemple de gestion du bouton "Sélectionner"


function handleSelection(id) {
  console.log("Le r\xE9sultat avec l'ID ".concat(id, " a \xE9t\xE9 s\xE9lectionn\xE9")); // Implémenter ici ce que tu veux faire après avoir sélectionné un résultat
}