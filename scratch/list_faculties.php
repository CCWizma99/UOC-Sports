<?php
require_once 'config/config.php';
require_once 'core/Database.php';

$db = Database::getConnection();
$stmt = $db->query("SELECT faculty_id, faculty_name FROM faculty");
$faculties = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($faculties, JSON_PRETTY_PRINT);
