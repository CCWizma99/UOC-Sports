<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';

try {
    $db = Database::getConnection();
    
    // Check if column exists
    $stmt = $db->query("SHOW COLUMNS FROM `match_players` LIKE 'external_id'");
    $exists = $stmt->fetch();

    if (!$exists) {
        echo "Adding 'external_id' column to 'match_players' table...\n";
        $db->exec("ALTER TABLE `match_players` ADD COLUMN `external_id` VARCHAR(50) NULL AFTER `student_id`");
        echo "Column added successfully.\n";
    } else {
        echo "'external_id' column already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
