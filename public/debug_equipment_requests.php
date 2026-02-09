<?php
// Debug script to test equipment requests query
require_once '../config/config.php';
require_once '../core/Database.php';

echo "<h2>Equipment Requests Debug</h2>";
echo "<pre>";

try {
    $db = Database::getConnection();
    echo "✓ Database connection successful\n\n";
    
    // Test 1: Check if table exists
    echo "=== TEST 1: Check if equipment-requests table exists ===\n";
    $stmt = $db->query("SHOW TABLES LIKE 'equipment-requests'");
    $tableExists = $stmt->fetch();
    echo $tableExists ? "✓ Table exists\n\n" : "✗ Table does NOT exist\n\n";
    
    // Test 2: Count records
    echo "=== TEST 2: Count records in equipment-requests ===\n";
    $stmt = $db->query("SELECT COUNT(*) as count FROM `equipment-requests`");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total records: " . $count['count'] . "\n\n";
    
    // Test 3: Get raw data
    echo "=== TEST 3: Get raw data from equipment-requests ===\n";
    $stmt = $db->query("SELECT * FROM `equipment-requests` LIMIT 5");
    $rawData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($rawData);
    echo "\n";
    
    // Test 4: Test the full query with joins
    echo "=== TEST 4: Test full query with joins ===\n";
    $query = "SELECT 
                er.request_id,
                er.student_id,
                er.category_id,
                COALESCE(ec.category_name, e.equipment_name, er.category_id) as category_name,
                er.sport_id,
                COALESCE(s.sport_name, er.sport_id) as sport_name,
                er.request_date,
                er.start_time,
                er.end_time,
                COALESCE(er.reserved_location, '') as reserved_location,
                COALESCE(er.requester_name, CONCAT(u.fname, ' ', u.lname), 'N/A') as student_name,
                COALESCE(er.purpose, '') as purpose,
                er.status,
                COALESCE(er.notes, '') as notes,
                u.email as student_email
            FROM `equipment-requests` er
            LEFT JOIN user u ON er.student_id = u.user_id
            LEFT JOIN equipment e ON er.category_id = e.equipment_id
            LEFT JOIN equipment_categories ec ON e.category_id = ec.category_id
            LEFT JOIN sport s ON er.sport_id = s.sport_id
            WHERE 1=1
            ORDER BY er.request_date DESC, er.start_time DESC";
    
    echo "Query:\n$query\n\n";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Results count: " . count($results) . "\n";
    echo "Results:\n";
    print_r($results);
    echo "\n";
    
    // Test 5: Check each table
    echo "=== TEST 5: Check related tables ===\n";
    
    // Check user table
    $stmt = $db->query("SELECT COUNT(*) as count FROM user");
    $userCount = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Users: " . $userCount['count'] . "\n";
    
    // Check equipment table
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM equipment");
        $equipCount = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Equipment: " . $equipCount['count'] . "\n";
    } catch (Exception $e) {
        echo "Equipment table error: " . $e->getMessage() . "\n";
    }
    
    // Check equipment_categories table
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM equipment_categories");
        $catCount = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Equipment Categories: " . $catCount['count'] . "\n";
    } catch (Exception $e) {
        echo "Equipment Categories table error: " . $e->getMessage() . "\n";
    }
    
    // Check sport table
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM sport");
        $sportCount = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Sports: " . $sportCount['count'] . "\n";
    } catch (Exception $e) {
        echo "Sport table error: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== TEST 6: Check specific student_id from equipment-requests ===\n";
    $stmt = $db->query("SELECT DISTINCT student_id FROM `equipment-requests`");
    $studentIds = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Student IDs in requests:\n";
    print_r($studentIds);
    
    // Check if these student_ids exist in user table
    foreach ($studentIds as $sid) {
        $stmt = $db->prepare("SELECT user_id, fname, lname FROM user WHERE user_id = ?");
        $stmt->execute([$sid['student_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "\nStudent ID: " . $sid['student_id'] . " -> ";
        if ($user) {
            echo "Found: " . $user['fname'] . " " . $user['lname'];
        } else {
            echo "NOT FOUND in user table";
        }
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString();
}

echo "</pre>";
?>
