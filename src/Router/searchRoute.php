<?php
if (file_exists(__DIR__ . '/../Search/Filtres.php')) {
    require_once __DIR__ . '/../Search/Filtres.php';
} else {
    die('Le fichier Filtres.php est introuvable');
}
require_once __DIR__ . '/../Search/Extraction.php';


$inputData = json_decode(file_get_contents('php://input'), true);

// Vérification que des données sont présentes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($inputData['data'])) {

    // Assignation des valeurs à des variables
    $inseeDepart = $inputData['data']['inseeDepart'] ?? null;
    $inseeArrival = $inputData['data']['inseeArrival'] ?? null;
    $departDate = $inputData['data']['departDate'] ?? null;
    $placeAvailable = $inputData['data']['placeAvailable'] ?? null;
    $animal = $inputData['data']['animal'] ?? null;
    $smoke = $inputData['data']['smoke'] ?? null;
    $eco = $inputData['data']['eco'] ?? null;
    $duration = $inputData['data']['duration'] ?? null;
    $note = $inputData['data']['note'] ?? null;
    $zone = $inputData['data']['zone'] ?? null;

    try {
        // Envoi à Filtres
        $filtre = new Filtres(
            $inseeDepart,
            $inseeArrival,
            $departDate,
            $placeAvailable,
            $animal,
            $smoke,
            $eco,
            $duration,
            $note,
            $zone
        );

        // appel d'Extraction avec filtre
        $extraction = new Extraction($filtre, $pdo);
        $offres = $extraction->filterOffers();

        // Vérifie si la réponse contient un type spécial pour ma fonction handleResponse
        if (isset($offres['type'])) {
            echo json_encode($offres);
            exit; // Stoppe le script ici
        }

        // Sinon traitement normal des offres
        foreach ($offres as &$offre) {
            if (!empty($offre['photo'])) {
                if (is_resource($offre['photo'])) {
                    $photoData = stream_get_contents($offre['photo']);
                } else {
                    $photoData = false;
                }

                if ($photoData !== false) {
                    $offre['photo'] = 'data:image/jpeg;base64,' . base64_encode($photoData);
                } else {
                    $offre['photo'] = null;
                }
            }
        }
        unset($offre); //supression de la derniere référence enregistré dans la boucle

        echo json_encode([
            'status' => 'success',
            'data' => $offres
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'type' => 'dev',
            'message' => 'Router : Erreur lors de la récupération de l\'offre'
        ]);
    }
} else {
    echo json_encode([
        'type' => 'dev',
        'message' => 'Routeur : Aucune donnée reçue'
    ]);
}
