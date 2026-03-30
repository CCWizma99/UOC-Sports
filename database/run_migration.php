<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';

try {
    $db = Database::getConnection();
    $sql = file_get_contents(__DIR__ . '/../database/create_active_booking_attempts.sql');
    $db->exec($sql);
    echo "Table active_booking_attempts created successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
