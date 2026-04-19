<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
$db = Database::getConnection();

$userId = 'CAP_67b360ea10a11'; // I need to find a valid captain ID or just check all sports

function checkSport($sid, $db) {
    echo "Sport: $sid\n";
    
    // Count in user table
    $stmt = $db->prepare("SELECT COUNT(*) FROM user WHERE sport_id = ? AND type = 'STUDENT'");
    $stmt->execute([$sid]);
    echo "  In User table: " . $stmt->fetchColumn() . "\n";
    
    // Count in sports-team table
    $stmt = $db->prepare("SELECT COUNT(*) FROM `sports-team` WHERE sport_id = ?");
    $stmt->execute([$sid]);
    echo "  In sports-team table: " . $stmt->fetchColumn() . "\n";
    
    // Details of a few
    $stmt = $db->prepare("SELECT u.fname, u.lname, st.in_team, st.status FROM `sports-team` st JOIN user u ON st.student_id = u.user_id WHERE st.sport_id = ? LIMIT 3");
    $stmt->execute([$sid]);
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
}

$sports = $db->query("SELECT sport_id FROM sport")->fetchAll(PDO::FETCH_COLUMN);
foreach ($sports as $sid) {
    checkSport($sid, $db);
}
