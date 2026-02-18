<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/autoload.php';
require_once __DIR__ . '/../app/controllers/api/FacilityApiController.php';
require_once __DIR__ . '/../app/models/Facility.php';

session_start();
$_SESSION['user_id'] = 1;

$_GET['facility_id'] = 1;
$_GET['date'] = date('Y-m-d');

try {
    $controller = new FacilityApiController();
    echo "Output from getReservationChart:\n";
    $controller->getReservationChart();
    echo "\n\nOutput from getReservedSlots:\n";
    $controller->getReservedSlots();
    echo "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
