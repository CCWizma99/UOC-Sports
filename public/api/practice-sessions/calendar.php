<?php
require_once '../../../config/config.php';
require_once '../../../core/Database.php';

header('Content-Type: application/json');

try {
    $sportId = $_GET['sport_id'] ?? null;
    $month = $_GET['month'] ?? date('m');
    $year = $_GET['year'] ?? date('Y');
    
    $db = Database::getConnection();
    
    // Build query to get accepted and completed practice sessions
    $query = "SELECT 
                ps.id,
                ps.sport_id,
                s.sport_name,
                ps.location,
                ps.session_date,
                ps.start_time,
                ps.end_time,
                ps.status,
                ps.facility,
                ps.notes
              FROM practice_sessions ps
              LEFT JOIN sport s ON ps.sport_id = s.sport_id
              WHERE ps.status IN ('ACCEPTED', 'COMPLETED')
                AND MONTH(ps.session_date) = ?
                AND YEAR(ps.session_date) = ?";
    
    $params = [$month, $year];
    
    // Filter by sport if provided
    if ($sportId) {
        $query .= " AND ps.sport_id = ?";
        $params[] = $sportId;
    }
    
    $query .= " ORDER BY ps.session_date ASC, ps.start_time ASC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group sessions by date
    $groupedSessions = [];
    foreach ($sessions as $session) {
        $date = $session['session_date'];
        if (!isset($groupedSessions[$date])) {
            $groupedSessions[$date] = [];
        }
        $groupedSessions[$date][] = $session;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $groupedSessions
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching practice sessions: ' . $e->getMessage()
    ]);
}
