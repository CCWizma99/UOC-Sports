<?php
require_once '../../config/config.php';
require_once '../../core/Database.php';
require_once '../../app/models/Facility.php';

header('Content-Type: application/json');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$filters = [
    'date' => isset($_GET['date']) ? trim($_GET['date']) : '',
    'location' => isset($_GET['location']) ? trim($_GET['location']) : '',
    'user_type' => isset($_GET['user_type']) ? trim($_GET['user_type']) : ''
];

try {
    $facilityModel = new Facility();
    $results = $facilityModel->searchReservations($query, $filters);
    echo json_encode($results);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error occurred',
        'message' => $e->getMessage()
    ]);
}

