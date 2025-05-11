<?php
require_once __DIR__ .'/../Search/City.php';

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (isset($data['data']) && !empty($data['data'])) {
    $citySearch = $data['data'];

    $send = new City($pdo, $citySearch);
    $city = $send->getCity($citySearch);

    if ($city) {
        $response = [
            'status' => 'success',
            'data' => $city
        ];
    } else {
        $response = [
            'type' => 'dev',
            'message' => 'Aucune ville trouvée.'
        ];
    }
} else {
    $response = [
        'type' => 'dev',
        'message' => 'Aucune ville recherchée.'
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
