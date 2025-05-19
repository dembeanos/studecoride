window.addEventListener('load', event => {

    let lastName = document.getElementById('lastName');
    let firstName = document.getElementById('firstName');
    let email = document.getElementById('email');
    let username = document.getElementById('username');
    let password = document.getElementById('password');
    let confirmPassword = document.getElementById('confirmPassword');
    let phone = document.getElementById('phone');
    let road = document.getElementById('road');
    let roadComplement = document.getElementById('roadComplement');
    let zipCode = document.getElementById('zipCode');
    let city = document.getElementById('city');
    let userTypeElement = document.getElementById('userType');
    let userType = userTypeElement ? userTypeElement.value : undefined;


    let button = document.getElementById('subscribe');

    button.addEventListener('click', e => {
        e.preventDefault(); 

        let data = {
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
            city: city.value,
            ...(userType && { userType })
        };

        fetchRequest(data);
        console.log('Données envoyées par js :', data)
    });

    async function fetchRequest(data) {
        try {
            let request = await fetch(`/src/Router/subscribeRoute.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ data: data })
            });
    
            const response = await request.json();
             if (response.type){
            handleResponse(response)
        }
        return response

        } catch (error) {
            console.error("Erreur lors de la récupération de la réponse :", error);
        }
    }
    
});
