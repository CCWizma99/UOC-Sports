<?php
require_once 'core/Database.php';
require_once 'config/config.php';

try {
    $db = Database::getConnection();
    $stmt = $db->query("SHOW COLUMNS FROM `facility-booking` LIKE 'payment_slip'");
    $column = $stmt->fetch();
    echo $column ? "EXISTS" : "MISSING";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
