<?php
require_once '../config/config.php';
require_once '../core/Database.php';

$db = Database::getConnection();
$stmt = $db->query("SELECT request_id, sport_id, category_name, status, equipment_items, reserved_location FROM `equipment-requests` WHERE status = 'ACTIVE' LIMIT 20");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($results, JSON_PRETTY_PRINT);
