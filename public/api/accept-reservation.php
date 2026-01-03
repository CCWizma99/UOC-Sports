<?php
require_once '../../config/config.php';
require_once '../../core/Database.php';
require_once '../../app/models/Facility.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$booking_id = isset($data['booking_id']) ? trim($data['booking_id']) : '';

if (empty($booking_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Booking ID is required']);
    exit;
}

try {
    $facility = new Facility();
    $result = $facility->acceptReservation($booking_id);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Reservation accepted successfully'
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to accept reservation. It may have already been processed.'
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error occurred',
        'message' => $e->getMessage()
    ]);
}
