"use strict";

window.addEventListener('load', function () {
  window.map = L.map('map').setView([46.603354, 1.888334], 5);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  }).addTo(window.map);
});

window.updateRoute = function (arrivalLat, arrivalLong, departLat, departLong) {
  if (!arrivalLat || !arrivalLong || !departLat || !departLong) return;

  if (!window.map) {
    console.error("La carte Leaflet n'est pas initialisée.");
    return;
  }

  if (window.routingControl) {
    window.map.removeControl(window.routingControl);
  }

  window.routingControl = L.Routing.control({
    waypoints: [L.latLng(departLat, departLong), L.latLng(arrivalLat, arrivalLong)],
    routeWhileDragging: true,
    showAlternatives: false,
    createMarkers: false
  }).addTo(window.map);
  var leafletInfo = document.querySelector(".leaflet-routing-alternatives-container");
  var leaftletContainer = document.querySelector('.leaflet-routing-container');
  leaftletContainer.style.display = 'none';
  leafletInfo.style.display = 'none';
  window.routingControl.on('routesfound', function (e) {
    var route = e.routes[0];
    var distance = route.summary.totalDistance / 1000;
    var timeInMinutes = route.summary.totalTime / 60;
    var timeInHours = Math.floor(timeInMinutes / 60);
    var finalMinutes = Math.floor(timeInMinutes - timeInHours * 60);
    console.log("Distance: ".concat(distance, " km"));
    console.log("Temps de trajet: ".concat(timeInHours, " heures et ").concat(finalMinutes, " minutes"));
    var graph = document.getElementById('graph');

    if (graph) {
      graph.style.display = "block";
      document.getElementById('graphDepart').textContent = document.getElementById('cityDepart').value;
      document.getElementById('graphDest').textContent = document.getElementById('cityArrival').value;
      document.getElementById('travelTime').textContent = "Temps de trajet: ".concat(timeInHours, " heures et ").concat(finalMinutes, " minutes");
      document.getElementById('distance').textContent = "Distance: ".concat(distance.toFixed(2), " km");
    }
  });
};