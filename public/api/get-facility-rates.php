<?php
require_once '../../config/config.php';
require_once '../../core/Database.php';

header('Content-Type: application/json');

try {
    $db = Database::getConnection();

    // If no query params, return all
    $sql = "SELECT * FROM facility_rates WHERE 1=1";
    $params = [];

    // Facility type
    if (isset($_GET['facility_type']) && !empty(trim($_GET['facility_type']))) {
        $sql .= " AND facility_type = :facility_type";
        $params[':facility_type'] = trim($_GET['facility_type']);
    }

    // Facility name
    if (isset($_GET['facility_name']) && !empty(trim($_GET['facility_name']))) {
        $sql .= " AND facility_name LIKE :facility_name";
        $params[':facility_name'] = "%" . trim($_GET['facility_name']) . "%";
    }

    $sql .= " ORDER BY facility_type, facility_name";

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
