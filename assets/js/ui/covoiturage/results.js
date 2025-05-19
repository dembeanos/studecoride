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
  <div class="result-item" style="
      display: flex;
      align-items: flex-start;
      gap: 1rem;
      padding: 1rem;
      font-size: 1.5rem;
    ">
    <div style="flex-shrink: 0; text-align: center;">
      <img
        src="${result.photo || 'default.jpg'}"
        alt="Photo de ${result.username}"
        style="
          width: 160px;
          height: 160px;
          border-radius: 0.5rem;
          object-fit: cover;
        "
      >
      <div class="result-avatar" style="
          font-size: 2rem;
          margin-top: 0.25rem;
          font-weight: bold;
        ">
        ${result.username || 'Anonyme'}
      </div>
    </div>
    <div class="result-content" style="flex: 1;">
      <h3 style="
          font-size: 2rem;
          margin-bottom: 0.5rem;
        ">
        Détails du covoiturage
      </h3>
      <p style="font-size: 1.50rem; margin: 0.25rem 0;">
        <strong>Note :</strong> ${result.note ?? 'Non noté'}
      </p>
      <p style="font-size: 1.50rem; margin: 0.25rem 0;">
        <strong>Places restantes :</strong> ${result.placeavailable ?? 'Inconnu'}
      </p>
      <p style="font-size: 1.50rem; margin: 0.25rem 0;">
        <strong>Prix :</strong> ${result.price ?? '?'} €
      </p>
      <p style="font-size: 1.50rem; margin: 0.25rem 0;">
        <strong>Départ :</strong> ${result.citydepart || '?'}
      </p>
      <p style="font-size: 1.50rem; margin: 0.25rem 0;">
        <strong>Date départ :</strong> ${result.datedepart ? new Date(result.datedepart).toLocaleDateString() : '?'}
      </p>
      <p style="font-size: 1.50rem; margin: 0.25rem 0;">
        <strong>Arrivée :</strong> ${result.arrivalcity || '?'}
      </p>
      <p style="font-size: 1.50rem; margin: 0.25rem 0;">
        <strong>Date arrivée :</strong> ${result.datearrival ? new Date(result.datearrival).toLocaleDateString() : '?'}
      </p>
      <p style="font-size: 1.50rem; margin: 0.25rem 0;">
        <strong>Durée :</strong> ${result.duration || '?'}
      </p>
      <p style="font-size: 1.50rem; margin: 0.25rem 0;">
        <strong>Énergie :</strong>
        ${result.energy === 'Electric'
        ? '<span style=\"padding:4px 8px; border-radius:6px; background:#d4edda; color:#155724; font-size:1rem;\">Électrique</span>'
        : result.energy || 'Non précisée'
      }
      </p>
    </div>
    <button
      class="select-button"
      data-offer='${JSON.stringify(result)}'
      style="
        font-size: 1.5rem;
        padding: 0.75rem 1.5rem;
        margin-left: auto;
        align-self: center;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: transform 0.3s;
        color:black;
      "
    >
      Détail…
    </button>
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
  <div style="
      font-size: 1.4rem;
      line-height: 1.6;
      padding: 1rem;
      max-width: 300px;
    ">
    
    <h3 style="font-size: 1.6rem; margin-bottom: 0.5rem;">Trajet</h3>
    <p><strong>Départ :</strong> ${offer.citydepart || '?'}</p>
    <p><strong>Arrivée :</strong> ${offer.arrivalcity || '?'}</p>
    <p><strong>Date départ :</strong> ${offer.datedepart ? new Date(offer.datedepart).toLocaleDateString() : '?'}</p>
    <p><strong>Date arrivée :</strong> ${offer.datearrival ? new Date(offer.datearrival).toLocaleDateString() : '?'}</p>
    <p><strong>Places disponibles :</strong> ${offer.placeavailable ?? '?'}</p>
    <p><strong>Prix :</strong> ${offer.price ?? '?'} €</p>

    <h3 style="font-size: 1.6rem; margin: 1rem 0 0.5rem;">Conducteur</h3>
    <p><strong>Nom :</strong> ${offer.username || 'Anonyme'}</p>
    <p><strong>Notes :</strong> ${offer.note ?? 'Non noté'}</p>

    <h3 style="font-size: 1.6rem; margin: 1rem 0 0.5rem;">Préférences</h3>
    <p><strong>Cigarette :</strong> ${offer.smoke === true ? "Oui" : offer.smoke === false ? "Non" : "Non précisé"}</p>
    <p><strong>Animaux :</strong> ${offer.animal === true ? "Oui" : offer.animal === false ? "Non" : "Non précisé"}</p>
    <p><strong>Autres :</strong> ${offer.other === true ? "Oui" : offer.other === false ? "Non" : "Non précisé"}</p>

    <h3 style="font-size: 1.6rem; margin: 1rem 0 0.5rem;">Véhicule</h3>
    <p><strong>Marque :</strong> ${offer.marque || '?'}</p>
    <p><strong>Modèle :</strong> ${offer.modele || '?'}</p>
    <p><strong>Énergie :</strong> 
      ${offer.energy === 'Electric'
      ? '<span style="padding:4px 8px; border-radius:6px; background:#d4edda; color:#155724;">Électrique</span>'
      : `<span style="padding:4px 8px; border-radius:6px; background:#f1f1f1;">${offer.energy || "Non précisée"}</span>`
    }
    </p>

    <div style="text-align: center; margin-top: 1rem;">
      <button id="participateButton" style="
          padding: 0.5rem 1rem;
          font-size: 1.4rem;
          border-radius: 0.5rem;
          cursor: pointer;
          background-color:rgba(112, 193, 61, 1);
          color: white;
          border: none;
        ">
        Participer
      </button>
    </div>
  </div>
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
  const isLoggedIn = await isUserLoggedIn();
  if (!isLoggedIn) return;

  const placesInput = document.getElementById('places');
  const places = placesInput ? parseInt(placesInput.value, 10) || 1 : 1;
  const totalCost = offer.price * places;

  // Popup de confirmation
  sendInteractivePopup(
    `⚠️ Vous êtes sur le point de réserver ${places} place(s).<br>` +
    `Montant total : ${totalCost} €.<br>` +
    `<button id="popup-ok" class="popup-btn popup-btn-ok">Valider</button>` +
    `<button id="popup-cancel2" class="popup-btn popup-btn-cancel">Annuler</button>`
  );

  // Attendre que le DOM soit prêt
  setTimeout(() => {
    document.getElementById('popup-ok').addEventListener('click', async () => {
      await addReservation(offer, places);
    });

    document.getElementById('popup-cancel2').addEventListener('click', () => {
      const overlay = document.querySelector('.popup-overlay-inter');
      if (overlay) overlay.remove();
    });
  }, 10);
}

async function addReservation(offer, places) {
  try {
    const response = await fetch('/src/Router/userRoute.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: 'addReservation',
        data: {
          offerid: offer.offerid,
          reservedPlaces: places
        }
      })
    });

    const json = await response.json(); // Récupère tout, même si pas du JSON


    if (json.type) {
      handleResponse(json);
    }

  } catch (error) {
    console.error('Erreur (addReservation) :', error);
    sendInteractivePopup("Une erreur est survenue. Veuillez réessayer plus tard.");
  }
}


async function isUserLoggedIn() {

    const response = await fetch(`/src/Authentification/auth.php`, {
      method: 'POST',
      credentials: 'same-origin', 
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'checkSession' })
    });

    const data = await response.json();
    if (data.isLoggedIn === true ) {
      return true;
    } else if (data.isLoggedIn === false) {
      // Popup de connexion
      sendInteractivePopup(
        "Vous devez vous connecter pour réserver.<br>" +
        `<button id="popup-login" class="popup-btn popup-btn-ok">Se connecter</button>` +
        `<button id="popup-cancel" class="popup-btn popup-btn-cancel">Annuler</button>`
      );

      setTimeout(() => {
        document.getElementById('popup-login').addEventListener('click', () => {
          window.location.href = "/pages/connexion/connexion.php";
        });

        document.getElementById('popup-cancel').addEventListener('click', () => {
          const overlay = document.querySelector('.popup-overlay-inter');
          if (overlay) overlay.remove();
        });
      }, 10);

      return false;
    } else{
      console.log('Status indescriptible')
    }

}


