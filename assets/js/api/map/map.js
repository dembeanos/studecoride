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
        waypoints: [
            L.latLng(departLat, departLong),
            L.latLng(arrivalLat, arrivalLong)
        ],
        routeWhileDragging: true,
        showAlternatives: false,
        createMarkers: false,
    }).addTo(window.map);
    let leafletInfo = document.querySelector(".leaflet-routing-alternatives-container")
    let leaftletContainer = document.querySelector('.leaflet-routing-container')
    leaftletContainer.style.display = 'none'
    leafletInfo.style.display = 'none'

    window.routingControl.on('routesfound', function (e) {
        const route = e.routes[0];
        const distance = route.summary.totalDistance / 1000;
        const timeInMinutes = route.summary.totalTime / 60;
        const timeInHours = Math.floor(timeInMinutes / 60)
        const finalMinutes = Math.floor(timeInMinutes - (timeInHours * 60))
        console.log(`Distance: ${distance} km`);
        console.log(`Temps de trajet: ${timeInHours} heures et ${finalMinutes} minutes`);

        let graph = document.getElementById('graph')

        if (graph) {
            graph.style.display = "block"
            document.getElementById('graphDepart').textContent = document.getElementById('cityDepart').value
            document.getElementById('graphDest').textContent = document.getElementById('cityArrival').value
            document.getElementById('travelTime').textContent = `Temps de trajet: ${timeInHours} heures et ${finalMinutes} minutes`;
            document.getElementById('distance').textContent = `Distance: ${distance.toFixed(2)} km`;
        }
    });
}
