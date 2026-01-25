<?php
// Simple test to check if model works
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Equipment Booking Request Test</h2>";
echo "<pre>";

// Include required files
require_once '../config/config.php';
require_once '../core/Database.php';
require_once '../app/models/EquipmentBookigRequest.php';

echo "Step 1: Files loaded successfully\n\n";

try {
    // Test database connection
    echo "Step 2: Testing database connection...\n";
    $db = Database::getConnection();
    echo "✓ Database connected\n\n";
    
    // Test raw query
    echo "Step 3: Testing raw query on equipment-requests...\n";
    $stmt = $db->query("SELECT * FROM `equipment-requests` LIMIT 2");
    $rawResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Raw results count: " . count($rawResults) . "\n";
    if (!empty($rawResults)) {
        echo "First raw record:\n";
        print_r($rawResults[0]);
    } else {
        echo "WARNING: No records found in equipment-requests table!\n";
    }
    echo "\n";
    
    // Test model
    echo "Step 4: Testing EquipmentBookigRequest model...\n";
    $model = new EquipmentBookigRequest();
    echo "✓ Model instantiated\n\n";
    
    // Test getAllRequests
    echo "Step 5: Calling getAllRequests()...\n";
    $requests = $model->getAllRequests();
    echo "Results count: " . count($requests) . "\n";
    
    if (!empty($requests)) {
        echo "✓ Data retrieved successfully!\n\n";
        echo "First request:\n";
        print_r($requests[0]);
    } else {
        echo "✗ No results returned from getAllRequests()\n";
        
        // Debug: Check what the query returns
        echo "\nDirect query test:\n";
        $testQuery = "SELECT 
                        er.request_id,
                        er.student_id,
                        er.status
                    FROM `equipment-requests` er
                    LIMIT 2";
        $stmt = $db->query($testQuery);
        $testResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Direct query results: " . count($testResults) . "\n";
        print_r($testResults);
    }
    
    // Test statistics
    echo "\nStep 6: Testing getStatistics()...\n";
    $stats = $model->getStatistics();
    print_r($stats);
    
} catch (Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString();
}

echo "</pre>";
?>
