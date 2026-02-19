<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../app/models/Facility.php';

session_start();
$_SESSION['user_id'] = 1; // Mock user ID

try {
    $model = new Facility();
    $facilityId = 1;
    $date = date('Y-m-d');
    
    echo "Testing trackBookingAttempt...\n";
    $res1 = $model->trackBookingAttempt(1, $facilityId, $date);
    var_dump($res1);
    
    echo "Testing getParallelBookingCount...\n";
    $res2 = $model->getParallelBookingCount($facilityId, $date, 1);
    var_dump($res2);
    
    echo "Testing getReservationChartData...\n";
    $startDate = date('Y-m-01', strtotime($date));
    $endDate = date('Y-m-t', strtotime($date));
    $res3 = $model->getReservationChartData($facilityId, $startDate, $endDate);
    echo "Count: " . count($res3) . "\n";
    
    echo "SUCCESS\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
