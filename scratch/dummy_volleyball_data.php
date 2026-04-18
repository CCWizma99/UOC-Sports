<?php
// Include config and core files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';

try {
    $db = Database::getConnection();
    
    echo "Using Database: uoc-sports\n";

    // 1. Clear existing volleyball achievements for clean demo
    $db->exec("DELETE FROM achievement WHERE sport_id = 'VOL'");
    echo "Cleared old volleyball achievements.\n";

    // 2. Create a Dummy Tournament if it doesn't exist
    $tournament_id = 'T-VOL-2026';
    $tournament_name = 'UOC Premier Volleyball League 2026';
    
    $checkTournament = $db->prepare("SELECT tournament_id FROM tournament WHERE tournament_id = ?");
    $checkTournament->execute([$tournament_id]);
    
    if (!$checkTournament->fetch()) {
        $stmt = $db->prepare("INSERT INTO tournament (tournament_id, tournament_name, sport_id, start_date, end_date, status) VALUES (?, ?, 'VOL', '2026-03-01', '2026-03-10', 'COMPLETE')");
        $stmt->execute([$tournament_id, $tournament_name]);
        echo "Created Tournament: $tournament_name\n";
    } else {
        echo "Tournament already exists.\n";
    }

    // 3. Add Achievements for Volleyball players
    // Player IDs found in sports-team table: '5Q1XZO2Y', 'L3NCL2J4', 'STU005'
    $achievements = [
        ['user_id' => '5Q1XZO2Y', 'achievement' => 'Best Setter', 'points' => 10],
        ['user_id' => 'L3NCL2J4', 'achievement' => '1st place', 'points' => 5],
        ['user_id' => 'STU005', 'achievement' => 'Best Attacker', 'points' => 8],
        ['user_id' => 'TEAM', 'achievement' => 'Finalist Trophy', 'points' => 15], 
    ];

    $stmtAch = $db->prepare("INSERT INTO achievement (user_id, sport_id, tournament_id, achievement, points) VALUES (?, 'VOL', ?, ?, ?)");
    
    foreach ($achievements as $ach) {
        $stmtAch->execute([$ach['user_id'], $tournament_id, $ach['achievement'], $ach['points']]);
        echo "Added Achievement: {$ach['achievement']} for Player: {$ach['user_id']}\n";
    }

    echo "\nDummy data insertion complete!\n";

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
