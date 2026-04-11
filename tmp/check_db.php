<?php
require_once 'core/Database.php';
require_once 'config/config.php';

try {
    $db = Database::getConnection();
    $stmt = $db->query("DESCRIBE `facility-booking` ");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($columns, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
