<?php
require_once 'core/Database.php';
require_once 'config/config.php';

try {
    $db = Database::getConnection();
    $sql = "ALTER TABLE `facility-booking` ADD COLUMN `payment_slip` VARCHAR(255) DEFAULT NULL AFTER `payment_id`";
    $db->exec($sql);
    echo "SUCCESS: Column 'payment_slip' added to 'facility-booking' table.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
