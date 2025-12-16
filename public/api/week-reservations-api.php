<?php
require_once '../../config/config.php';
require_once '../../core/Database.php';
require_once '../../app/models/Facility.php';

header('Content-Type: application/json');

$date = isset($_GET['date']) ? trim($_GET['date']) : date('Y-m-d');

try {
    $facility = new Facility();
    $reservations = $facility->getWeekReservations($date);

    echo json_encode($reservations);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error occurred',
        'message' => $e->getMessage()
    ]);
}
