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

    // JOIN with users table to get user names
    $stmt = $db->prepare("
        SELECT 
            fb.*, 
            CONCAT(u.fname, ' ', u.lname) AS user_name
        FROM `facility-booking` fb
        JOIN user u ON fb.user_id = u.user_id
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
