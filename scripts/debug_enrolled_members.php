<?php
require_once __DIR__ . '/../core/Database.php';
$db = Database::getConnection();

// Get all sports
$stmt = $db->query("SELECT sport_id, sport_name FROM sport");
$sports = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Sports and their Enrolled Student Counts:\n";
echo str_repeat("-", 50) . "\n";

foreach ($sports as $sport) {
    $sid = $sport['sport_id'];
    $sname = $sport['sport_name'];
    
    // Students in User table for this sport
    $stmt = $db->prepare("SELECT COUNT(*) FROM user WHERE type = 'STUDENT' AND sport_id = ? AND status = 'ACTIVE'");
    $stmt->execute([$sid]);
    $countUserTable = $stmt->fetchColumn();
    
    // Students in sports-team table for this sport
    $stmt = $db->prepare("SELECT COUNT(*) FROM `sports-team` WHERE sport_id = ?");
    $stmt->execute([$sid]);
    $countTeamTable = $stmt->fetchColumn();
    
    echo sprintf("%-15s | User Table: %-3d | Team Table: %-3d\n", $sname, $countUserTable, $countTeamTable);
}

echo "\nChecking a few students status:\n";
$stmt = $db->query("SELECT user_id, fname, lname, sport_id, type, status FROM user WHERE type = 'STUDENT' LIMIT 5");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
