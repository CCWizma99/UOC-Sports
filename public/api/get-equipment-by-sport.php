<?php
require_once '../../config/config.php';
require_once '../../core/Database.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['sport_id'])) {
        echo json_encode(['success' => false, 'message' => 'Sport ID is required']);
        exit;
    }

    $sportId = $_GET['sport_id'];
    $db = Database::getConnection();

    // Get equipment for the sport
    $query = "SELECT 
                e.equipment_id,
                e.equipment_name,
                COALESCE(SUM(ei.usable), 0) as available_count
              FROM equipment e
              LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id
              WHERE e.sport_id = ?
              GROUP BY e.equipment_id, e.equipment_name
              HAVING available_count > 0
              ORDER BY e.equipment_name";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$sportId]);
    $equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'equipment' => $equipment
    ]);

} catch (Exception $e) {
    error_log("Error fetching equipment: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching equipment data'
    ]);
}
