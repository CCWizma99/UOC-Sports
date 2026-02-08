<?php
require_once '../config/config.php';
require_once '../core/Database.php';

$db = Database::getConnection();

// Check sport table data
echo "=== SPORT TABLE ===\n";
$stmt = $db->query("SELECT sport_id, sport_name FROM sport ORDER BY sport_id");
$sports = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($sports as $sport) {
    echo $sport['sport_id'] . " => " . $sport['sport_name'] . "\n";
}

echo "\n=== SPORT_EXPENSES TABLE (DISTINCT SPORTS) ===\n";
$stmt = $db->query("SELECT DISTINCT sport FROM sport_expenses ORDER BY sport");
$expenseSports = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($expenseSports as $sport) {
    echo "'" . $sport['sport'] . "'\n";
}

echo "\n=== CHECKING EXACT MATCHES ===\n";
foreach($sports as $sport) {
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM sport_expenses WHERE sport = ?");
    $stmt->execute([$sport['sport_name']]);
    $count = $stmt->fetchColumn();
    echo $sport['sport_name'] . " (" . $sport['sport_id'] . "): " . $count . " expenses\n";
}
