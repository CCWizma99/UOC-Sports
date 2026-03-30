<?php
session_start();
$_SESSION['user_id'] = 1; // Mock user

$_GET['facility_id'] = 1;
$_GET['date'] = '2026-02-17';

echo "=== Testing Chart API ===\n\n";
echo "URL: /uoc-sports/public/reserve-facilities/chart?facility_id=1&date=2026-02-17\n\n";

// Simulate the API call
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/autoload.php';
require_once __DIR__ . '/../app/controllers/api/FacilityApiController.php';
require_once __DIR__ . '/../app/models/Facility.php';

try {
    $controller = new FacilityApiController();
    echo "Calling getReservationChart()...\n\n";
    echo "OUTPUT:\n";
    ob_start();
    $controller->getReservationChart();
    $output = ob_get_clean();
    echo $output . "\n\n";
    
    echo "JSON Validation:\n";
    $json = json_decode($output, true);
    if ($json === null) {
        echo "ERROR: Invalid JSON - " . json_last_error_msg() . "\n";
    } else {
        echo "SUCCESS: Valid JSON\n";
        echo "Keys: " . implode(', ', array_keys($json)) . "\n";
        if (isset($json['chart_data'])) {
            echo "Chart data count: " . count($json['chart_data']) . "\n";
        }
        if (isset($json['parallel_booking'])) {
            echo "Parallel booking: " . ($json['parallel_booking'] ? 'true' : 'false') . "\n";
        }
    }
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
