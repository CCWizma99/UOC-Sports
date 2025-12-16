<?php
require_once '../../config/config.php';
require_once '../../core/Database.php';
require_once '../../app/models/Facility.php';

header('Content-Type: application/json');

$booking_id = isset($_GET['id']) ? trim($_GET['id']) : '';

if (empty($booking_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Booking ID is required']);
    exit;
}

try {
    $facility = new Facility();
    $reservation = $facility->getReservationDetails($booking_id);

    if (!$reservation) {
        http_response_code(404);
        echo json_encode(['error' => 'Reservation not found']);
        exit;
    }

    echo json_encode($reservation);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error occurred',
        'message' => $e->getMessage()
    ]);
}
