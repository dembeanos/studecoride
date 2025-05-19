export async function sendOpinion(reservationId, trajet) {
    const avisHTML = `
    <fieldset class="sent-opinion">
        <legend>Votre Avis sur le trajet ${trajet}</legend>
        <div id="opinionSection">
            <form id="opinionForm">
                <label for="note">Note :</label>
                <select id="note" required>
                    <option value="1">⭐</option>
                    <option value="2">⭐⭐</option>
                    <option value="3">⭐⭐⭐</option>
                    <option value="4">⭐⭐⭐⭐</option>
                    <option value="5">⭐⭐⭐⭐⭐</option>
                </select>
                <label for="containOpinion">Votre avis :</label>
                <textarea id="containOpinion" name="containOpinion" required></textarea>
                <button id="addOpinion" type="submit">Envoyer l'avis</button>
            </form>
        </div>
    </fieldset>
    `;

    sendInteractivePopup(avisHTML);
    await opinion(reservationId);
}

async function opinion(reservationId) {
    const form = document.getElementById('opinionForm');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const note = document.getElementById('note').value;
        const text = document.getElementById('containOpinion').value;

        const data = {
            note: note,
            opinionText: text,
            srcOpinion: reservationId
        };

        try {
            const response = await fetch(`/src/Router/userRoute.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ action: 'addOpinion', data })
            });

            const json = await response.json();
            if (json.type) {
                handleResponse(json);
            }
        } catch (error) {
            console.error('Erreur lors de l\'envoi de l\'avis :', error);
        }
    });
}
