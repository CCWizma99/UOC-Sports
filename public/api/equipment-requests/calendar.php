<?php
require_once '../../../config/config.php';
require_once '../../../core/Database.php';

header('Content-Type: application/json');

try {
    $categoryName = $_GET['category_name'] ?? null;
    $sportId = $_GET['sport_id'] ?? null;
    $month = $_GET['month'] ?? date('m');
    $year = $_GET['year'] ?? date('Y');
    
    $db = Database::getConnection();
    
    // Build query to get accepted, pending, and completed equipment reservations
    $query = "SELECT 
                er.request_id as id,
                er.student_id,
                er.category_name,
                er.equipment_items,
                er.sport_id,
                COALESCE(s.sport_name, er.sport_id) as sport_name,
                er.request_date,
                er.start_time,
                er.end_time,
                COALESCE(er.reserved_location, '') as location,
                COALESCE(er.requester_name, CONCAT(u.fname, ' ', u.lname), 'N/A') as student_name,
                COALESCE(er.notes, '') as notes,
                er.status
              FROM `equipment-requests` er
              LEFT JOIN user u ON er.student_id = u.user_id
              LEFT JOIN sport s ON er.sport_id = s.sport_id
              WHERE er.status IN ('ACCEPTED', 'PENDING', 'COMPLETED')
                AND MONTH(er.request_date) = ?
                AND YEAR(er.request_date) = ?";
    
    $params = [$month, $year];
    
    // Filter by category if provided
    if ($categoryName) {
        $query .= " AND er.category_name = ?";
        $params[] = $categoryName;
    }
    
    // Filter by sport if provided
    if ($sportId) {
        $query .= " AND er.sport_id = ?";
        $params[] = $sportId;
    }
    
    $query .= " ORDER BY er.request_date ASC, er.start_time ASC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group reservations by date
    $groupedReservations = [];
    foreach ($reservations as $reservation) {
        $date = $reservation['request_date'];
        if (!isset($groupedReservations[$date])) {
            $groupedReservations[$date] = [];
        }
        $groupedReservations[$date][] = $reservation;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $groupedReservations
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching equipment reservations: ' . $e->getMessage()
    ]);
}
