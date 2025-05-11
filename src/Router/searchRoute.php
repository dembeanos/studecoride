<?php
if (file_exists(__DIR__ . '/../Search/Filtres.php')) {
    require_once __DIR__ . '/../Search/Filtres.php';
} else {
    die('Le fichier Filtres.php est introuvable');
}
require_once __DIR__ .'/../Search/Extraction.php';

//Json decode
$inputData = json_decode(file_get_contents('php://input'), true);

//Verification que la méthod et POST et que des données sont présentes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($inputData['data'])) {

    //Assignation des valeurs à des variables 
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
        // 1ere etape : Envoi a Filtres
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

        // 2eme étape une fois réponse filtre appel d'Extraction
        $extraction = new Extraction($filtre, $pdo);
        $offres = $extraction->filterOffers();

        foreach ($offres as &$offre) { // Utilisation de & pour modifier les éléments de $offres en place (pas des copies)
            if (!empty($offre['photo'])) {
                // Si la donnée est déjà une ressource, récupère son contenu
                if (is_resource($offre['photo'])) {
                    $photoData = stream_get_contents($offre['photo']);  // Pour lire une ressource de type fichier
                } else {
                    // Si ce n'est ni une ressource ni une chaîne, gère l'erreur
                    $photoData = false;
                }
        
                // Vérifier si la lecture a réussi
                if ($photoData !== false) {
                    // Encoder en base64
                    $offre['photo'] = 'data:image/jpeg;base64,' . base64_encode($photoData);
                } else {
                    // Si la lecture échoue, gérer l'erreur
                    $offre['photo'] = null;  // Ou une valeur par défaut
                }
            }
        }
        unset($offre); // unset $offre pour défaire la derniere valeur vers qui il pointait en fin de boucle
        
        echo json_encode([
            'status' => 'success',
            'data' => $offres
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'type' => 'dev',
            'message' => 'Router : Erreur lors de la recuperation de l\'offre'
        ]);
    }

} else {
    echo json_encode([
        'type' => 'dev',
        'message' => 'Routeur : Aucune donnée reçue'
    ]);
}
