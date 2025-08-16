<?php
require_once '../../config/config.php';
require_once '../../core/Database.php';

header('Content-Type: application/json');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$date = isset($_GET['date']) ? trim($_GET['date']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$user_type = isset($_GET['user_type']) ? trim($_GET['user_type']) : '';

try {
    $db = Database::getConnection();

    $sql = "
        SELECT r.booking_id,
               CONCAT(u.fname, ' ', u.lname) AS user_name,
               r.date,
               r.facility_id,
               u.type AS user_type,
               r.payment_status
        FROM `facility-booking` r
        INNER JOIN user u ON r.user_id = u.user_id
        WHERE 1
    ";

    $params = [];

    if ($query !== '') {
        $sql .= " AND (r.booking_id LIKE ? OR u.fname LIKE ? OR u.lname LIKE ?)";
        $params[] = "%$query%";
        $params[] = "%$query%";
        $params[] = "%$query%";
    }
    if ($date !== '') {
        $sql .= " AND r.date = ?";
        $params[] = $date;
    }
    if ($location !== '') {
        $sql .= " AND r.location = ?";
        $params[] = $location;
    }
    if ($user_type !== '') {
        $sql .= " AND u.type = ?";
        $params[] = $user_type;
    }

    $sql .= " ORDER BY r.date DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error occurred',
        'message' => $e->getMessage()
    ]);
}
