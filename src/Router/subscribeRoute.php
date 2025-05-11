<?php

require_once __DIR__ .'/../Registration/Subscribe.php';

//Décode du json
$inputData = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($inputData['formData'])) {
    
    $formData = $inputData['formData'];
    
    //Mise en tableau des champs
    $requiredFields = ['firstName', 'lastName', 'email', 'username', 'password', 'confirmPassword'];
    //Creation du boucle pour verification des champs à js au format handleResponse
    foreach ($requiredFields as $field) {
        if (empty($formData[$field])) {
            echo json_encode([
                "type" => "user_error",
                "message" => "Le champ $field doit être renseigné",
                "target" => $field
            ]);
            exit;
        }
    }

    // Set du crédit par défaut
    $credit = 20;
    //si le type d'user est spécifié sinon défaut user
    //si ce routeur est appelé par l'admin usertype aura une valeur
    //sinon c'est qu'il est appelé par la page d'inscription publique donc user
    $userType = $formData['userType'] ?? 'user';
    $response = null;

    try {
        $subscribe = new Subscribe($pdo);

        $response = $subscribe->registerUser(
            $formData['firstName'],
            $formData['lastName'],
            $formData['email'],
            $formData['username'],
            $formData['password'],
            $formData['confirmPassword'],
            $formData['phone'],
            $formData['road'],
            $formData['roadComplement'],
            $formData['zipCode'],
            $formData['city'],
            $credit,
            $userType
        );

        if (empty($response)) {
            echo json_encode([
                "type" => "dev",
                "message" => "Aucune réponse recu"
            ]);
        }

        echo trim(json_encode($response));

    } catch (Exception $e) {
        echo json_encode([
            "type" => "dev",
            "message" => "Erreur du router"
        ]);
    }
}

?>
