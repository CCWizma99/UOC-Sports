<?php
require_once '../core/Database.php';
require_once '../config/config.php';

$db = Database::getConnection();
$stmt = $db->query('DESCRIBE `equipment-requests`');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Current table structure:\n\n";
foreach($columns as $col) {
    echo $col['Field'] . ' | ' . $col['Type'] . ' | Null: ' . $col['Null'] . ' | Key: ' . $col['Key'] . "\n";
}
