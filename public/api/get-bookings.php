<?php

require_once '../../config/config.php';
require_once '../../core/Database.php';

header('Content-Type: application/json');

if (!isset($_GET['date']) || empty($_GET['date'])) {
    echo json_encode(['error' => 'Date is required']);
    exit;
}

$date = $_GET['date'];

try {
    $db = Database::getConnection();

    // JOIN with users table and facility_rates
    $stmt = $db->prepare("
        SELECT 
            fb.*, 
            CONCAT(u.fname, ' ', u.lname) AS user_name,
            fr.facility_name,
            CASE 
                WHEN fb.slot = 'MORNING' THEN '08:00 AM'
                WHEN fb.slot = 'AFTERNOON' THEN '01:00 PM'
                WHEN fb.slot = 'FULL' THEN '08:00 AM'
                ELSE 'N/A'
            END as start_time,
            CASE 
                WHEN fb.slot = 'MORNING' THEN '12:00 PM'
                WHEN fb.slot = 'AFTERNOON' THEN '05:00 PM'
                WHEN fb.slot = 'FULL' THEN '05:00 PM'
                ELSE 'N/A'
            END as end_time
        FROM `facility-booking` fb
        JOIN user u ON fb.user_id = u.user_id
        LEFT JOIN facility_rates fr ON fb.facility_id = fr.id
        WHERE fb.date = ?
    ");
    $stmt->execute([$date]);

    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($bookings) {
        echo json_encode([
            'booked' => true,
            'count' => count($bookings),
            'data' => $bookings
        ]);
    } else {
        echo json_encode([
            'booked' => false,
            'count' => 0,
            'data' => []
        ]);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
