<?php
/**
 * Migration script to add status columns for soft deletes.
 * Run this once.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';

try {
    $db = Database::getConnection();
    echo "Connected to database.\n";

    $tablesToUpdate = [
        'equipment',
        'injury_report',
        'inquiry',
        'message',
        'achievement',
        'sport_expenses',
        'comment',
        'playing_teams',
        'equipment_inventory',
        'tournament_participants',
        'saved_emails'
    ];

    foreach ($tablesToUpdate as $table) {
        try {
            // Check if column exists first
            $check = $db->query("SHOW COLUMNS FROM `$table` LIKE 'status'")->fetch();
            if (!$check) {
                $db->exec("ALTER TABLE `$table` ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'ACTIVE'");
                echo "Added 'status' to $table.\n";
            } else {
                echo "'status' already exists in $table.\n";
            }
        } catch (Exception $e) {
            echo "Error on table $table: " . $e->getMessage() . "\n";
        }
    }

    // Special cases
    // sports-team
    try {
        $check = $db->query("SHOW COLUMNS FROM `sports-team` LIKE 'status'")->fetch();
        if (!$check) {
            $db->exec("ALTER TABLE `sports-team` ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'ACTIVE'");
            echo "Added 'status' to sports-team.\n";
        }
    } catch (Exception $e) {
        echo "Error on sports-team: " . $e->getMessage() . "\n";
    }

    // attendance
    try {
        $check = $db->query("SHOW COLUMNS FROM `attendance` LIKE 'record_status'")->fetch();
        if (!$check) {
            $db->exec("ALTER TABLE `attendance` ADD COLUMN `record_status` VARCHAR(20) NOT NULL DEFAULT 'ACTIVE'");
            echo "Added 'record_status' to attendance.\n";
        }
    } catch (Exception $e) {
        echo "Error on attendance: " . $e->getMessage() . "\n";
    }

    echo "Migration completed successfully.\n";

} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
