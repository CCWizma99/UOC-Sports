<?php

require_once '../../config/config.php';
require_once '../../core/Database.php';

header('Content-Type: application/json');

if (!isset($_GET['month']) || !isset($_GET['year'])) {
    echo json_encode(['error' => 'Month and year required']);
    exit;
}

$month = str_pad($_GET['month'], 2, '0', STR_PAD_LEFT);
$year = $_GET['year'];

try {
    $db = Database::getConnection();

    $stmt = $db->prepare("
        SELECT DISTINCT `date` 
        FROM `facility-booking` 
        WHERE MONTH(`date`) = ? AND YEAR(`date`) = ?
    ");
    $stmt->execute([$month, $year]);

    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode(['dates' => $dates]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'DB Error: ' . $e->getMessage()]);
}
