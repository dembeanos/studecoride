export function showResult(results) {
    let resultContainer = document.getElementById('resultContainer');

    if (!resultContainer) {
        console.error("Erreur : le conteneur de résultats n'a pas été trouvé.");
        return;
    }

    if (!Array.isArray(results)) {
        results = results ? [results] : [];
    }

    resultContainer.innerHTML = '';

    if (results.length === 0) {
        resultContainer.innerHTML = '<p>Aucun résultat trouvé.</p>';
        return;
    }

    results.forEach(result => {
        let resultDiv = document.createElement('div');
        resultDiv.classList.add('result-item');

        resultDiv.innerHTML = `
  <div class="result-item">
    <div>
      <img src="${result.photo || 'default.jpg'}" alt="Photo de ${result.username}">
      <div class="result-avatar">${result.username || 'Anonyme'}</div>
    </div>
    <div class="result-content">
      <h3>Détails du covoiturage</h3>
      <p><strong>Note :</strong> ${result.note ?? 'Non noté'}</p>
      <p><strong>Places restantes :</strong> ${result.placeavailable ?? 'Inconnu'}</p>
      <p><strong>Prix :</strong> ${result.price ?? '?'} €</p>
      <p><strong>Départ :</strong> ${result.citydepart || '?'}</p>
      <p><strong>Date départ :</strong> ${result.datedepart ? new Date(result.datedepart).toLocaleDateString() : '?'}</p>
      <p><strong>Arrivée :</strong> ${result.arrivalcity || '?'}</p>
      <p><strong>Date arrivée :</strong> ${result.datearrival ? new Date(result.datearrival).toLocaleDateString() : '?'}</p>
      <p><strong>Durée :</strong> ${result.duration || '?'}</p>
      <p><strong>Énergie :</strong> ${
        result.energy === 'Electric'
          ? '<span style="padding:2px 6px; border-radius:6px; background:#d4edda; color:#155724;">Électrique</span>'
          : result.energy || 'Non précisée'
      }</p>
    </div>
    <button class="select-button" data-offer='${JSON.stringify(result)}'>Détail…</button>
  </div>
`;

        resultContainer.appendChild(resultDiv);
    });

    document.querySelectorAll('.select-button').forEach(button => {
        button.addEventListener('click', (event) => {
            const selectedOffer = JSON.parse(event.target.getAttribute('data-offer'));
            loadOfferDetailsPopup(selectedOffer);
        });
    });
}

function loadOfferDetailsPopup(offer) {
    const popup = document.getElementById('popupDetail');
    const popupContent = document.getElementById('popupDetailsContent');

    if (!offer) {
        console.error("Erreur : aucune offre n'a été sélectionnée.");
        return;
    }

    popupContent.innerHTML = `
        <p><strong>Départ :</strong> ${offer.citydepart}</p>
        <p><strong>Arrivée :</strong> ${offer.arrivalcity}</p>
        <p><strong>Date départ :</strong> ${new Date(offer.datedepart).toLocaleDateString()}</p>
        <p><strong>Date arrivée :</strong> ${new Date(offer.datearrival).toLocaleDateString()}</p>
        <p><strong>Places disponibles :</strong> ${offer.placeavailable}</p>
        <p><strong>Prix :</strong> ${offer.price} €</p>

        <h3>Infos conducteur</h3>
        <p><strong>Nom :</strong> ${offer.username}</p>
        <p><strong>Notes :</strong> ${offer.note}</p>
        <h3>Préférences :</h3>
        <p><strong>Cigarette Autorisée :</strong> ${offer.smoke === true ? "Oui" : offer.smoke === false ? "Non" : "Non précisées"}</p>
        <p><strong>Animaux Autorisée :</strong> ${offer.animal === true ? "Oui" : offer.animal === false ? "Non" : "Non précisées"}</p>
        <p><strong>Autres Préférences :</strong> ${offer.other === true ? "Oui" : offer.other === false ? "Non" : "Non précisées"}</p>

        <h3>Véhicule</h3>
        <p><strong>Marque :</strong> ${offer.marque}</p>
        <p><strong>Modèle :</strong> ${offer.modele}</p>
        <p><strong>Énergie :</strong> ${offer.energy === 'Electric'
            ? '<span style="background-color: #d4edda; color: #155724; padding: 2px 6px; border-radius: 6px;">Écologique (Électrique)</span>'
            : `<span style="background-color: #f8f9fa; padding: 2px 6px; border-radius: 6px;">${offer.energy || "Non précisée"}</span>`
        }</p>

        <button id="participateButton">Participer</button>
    `;

    popup.style.display = 'block';

    document.getElementById('participateButton').addEventListener('click', () => {
        handleParticipation(offer);
    });
}

document.getElementById('closePopup').addEventListener('click', () => {
    document.getElementById('popupDetail').style.display = 'none';
});


async function handleParticipation(offer) {
    if (!await isUserLoggedIn()) {
        if (confirm("Vous devez être connecté pour participer. Voulez-vous vous connecter ?")) {
            window.location.href = "login.html";
        }
        return;
    }

    const placesInput = document.getElementById('places');
    const places = placesInput ? parseInt(placesInput.value) || 1 : 1;

    const totalCost = offer.price * places;

    const confirmation = confirm(
        `⚠️ Vous êtes sur le point de réserver ${places} place(s).\n` +
        `Montant total : ${totalCost} €.\n\nSouhaitez-vous valider cette réservation ?`
    );

    if (!confirmation) return;

    await addReservation(offer, places);
    alert("Bravo, tu es inscrit à ce covoiturage !");
}

async function addReservation(offer, places) {
    try {

        console.log(offer.offerid, places)
        const response = await fetch(`/Ecoride/src/Router/userRoute.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'addReservation',
                data: {
                    offerid: offer.offerid,
                    reservedPlaces: places
                }
            })
        });

        const text = await response.text();
        console.log('Réponse brute (addReservation) :', text);

        try {
            const data = JSON.parse(text);
            if (data.status === 'success') {
                console.log('Réservation ajoutée avec succès');
            } else {
                console.error('Erreur lors de l\'ajout de la réservation :', data.message);
            }
        } catch (parseError) {
            console.error('Réponse non-JSON :', text);
        }

    } catch (error) {
        console.error('Erreur réseau lors de l\'ajout de la réservation :', error);
    }
}

async function isUserLoggedIn() {
    try {
        const response = await fetch(`/Ecoride/src/Router/userRoute.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ action: 'checkSession' })
        });

        const text = await response.text();
        console.log('Réponse brute (isUserLoggedIn) :', text);

        try {
            const data = JSON.parse(text);
            if (data.isLoggedIn) {
                console.log('Utilisateur connecté');
                return true;
            } else {
                console.log('Utilisateur non connecté');
                return false;
            }
        } catch (parseError) {
            console.error('Réponse non-JSON :', text);
            return false;
        }

    } catch (error) {
        console.error('Erreur réseau (isUserLoggedIn) :', error);
        return false;
    }
}

