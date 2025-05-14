window.addEventListener('load', function () {
    let objectPurpose = document.getElementById('object');
    let objectPerso = document.getElementById('customTitle');
    let divOther = document.getElementById('otherObject')
    let firstName = document.getElementById('firstName');
    let lastName = document.getElementById('lastName');
    let email = document.getElementById('email');
    let message = document.getElementById('message');
    let sendButton = document.getElementById('send');

    objectPurpose.addEventListener('change', function () {
        if (objectPurpose.value === 'autre') {
            divOther.style.display = 'block';
            objectPurpose.style.display = 'none';
        } else {
            objectPerso.style.display = 'none';
        }
    });

    sendButton.addEventListener('click', async function (event) {
        event.preventDefault();

        let finalObject = objectPurpose.value === 'autre'
            ? objectPerso.value
            : objectPurpose.value;

        let data = {
            object: finalObject,
            lastName: lastName.value,
            firstName: firstName.value,
            email: email.value,
            messageText: message.value
        };

        try {
            const response = await fetch(`/src/Router/messageRouter.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ action: 'sendPublicMessage', data: data })
            });

            const json = await response.text();
            console.log(json)
            if (json.type) {
                handleResponse(json);
            }

        } catch (error) {
            console.error("Erreur lors de la récupération de la réponse :", error);
        }

        objectPerso.style.display = 'none';
        divOther.style.display = 'none';
        objectPerso.value = '';
        objectPurpose.value = '';
        objectPurpose.style.display = 'block';

        firstName.value = '';
        lastName.value = '';
        email.value = '';
        message.value = '';


    });
});
