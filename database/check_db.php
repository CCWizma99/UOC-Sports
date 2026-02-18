<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';

try {
    $db = Database::getConnection();
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in " . DB_NAME . ":\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }
    
    if (in_array('active_booking_attempts', $tables)) {
        echo "\nSchema for active_booking_attempts:\n";
        $stmt = $db->query("DESCRIBE active_booking_attempts");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
    } else {
        echo "\nERROR: table active_booking_attempts NOT FOUND!\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
