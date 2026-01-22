<?php
require_once '../app/core/Database.php';
require_once '../app/models/SportTeam.php';

// Simulate Coach ID (Suresh Kumara from the header?)
// We need to find the user_id for the coach. 
// Let's just list all sports and their members count.

$db = Database::getConnection();
$stmt = $db->query("SELECT sport_id, sport_name, coach_id FROM sport");
$sports = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h1>Debug Sports Teams</h1>";

$teamModel = new SportTeam();

foreach ($sports as $sport) {
    echo "<h2>Sport: " . htmlspecialchars($sport['sport_name']) . " (ID: " . $sport['sport_id'] . ")</h2>";
    echo "Coach ID: " . $sport['coach_id'] . "<br>";
    
    $members = $teamModel->getTeamMembers($sport['sport_id']);
    echo "Members Count: " . count($members) . "<br>";
    
    if (!empty($members)) {
        echo "<ul>";
        foreach ($members as $m) {
            echo "<li>" . htmlspecialchars($m['fname'] . ' ' . $m['lname']) . " (ID: " . ($m['student_id'] ?? $m['user_id']) . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "No members found.<br>";
    }
    echo "<hr>";
}
