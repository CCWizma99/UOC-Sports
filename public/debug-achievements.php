<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/config.php';
require_once '../core/Database.php';
require_once '../core/Model.php';
require_once '../app/models/SportAchievements.php';

echo "<h1>Debug Achievements Data</h1>";

try {
    $db = Database::getConnection();
    
    // Check if tables exist
    echo "<h2>1. Checking Tables</h2>";
    
    $tables = ['achievement', 'user_points', 'user', 'sport', 'competition'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✓ Table '$table' exists</p>";
            
            // Count rows
            $count = $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "<p>   → Has $count rows</p>";
        } else {
            echo "<p style='color: red;'>✗ Table '$table' NOT FOUND</p>";
        }
    }
    
    // Check achievement data
    echo "<h2>2. Sample Achievement Data</h2>";
    $stmt = $db->query("SELECT * FROM achievement LIMIT 5");
    $achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($achievements) {
        echo "<pre>";
        print_r($achievements);
        echo "</pre>";
    } else {
        echo "<p style='color: orange;'>No achievements in database</p>";
    }
    
    // Check user_points data
    echo "<h2>3. Sample User Points Data</h2>";
    $stmt = $db->query("SELECT * FROM user_points LIMIT 5");
    $userPoints = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($userPoints) {
        echo "<pre>";
        print_r($userPoints);
        echo "</pre>";
    } else {
        echo "<p style='color: orange;'>No user points in database</p>";
    }
    
    // Try getStudentRankings
    echo "<h2>4. Test getStudentRankings()</h2>";
    $model = new SportAchievements();
    $rankings = $model->getStudentRankings(null, 10);
    echo "<p>Found " . count($rankings) . " students</p>";
    if ($rankings) {
        echo "<pre>";
        print_r($rankings);
        echo "</pre>";
    }
    
    // Check with specific sport
    echo "<h2>5. Test with Sport ID</h2>";
    $stmt = $db->query("SELECT sport_id, sport_name FROM sport LIMIT 5");
    $sports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>Available sports:</p><pre>";
    print_r($sports);
    echo "</pre>";
    
    if (!empty($sports)) {
        $sportId = $sports[0]['sport_id'];
        echo "<p>Testing with sport_id: $sportId</p>";
        $rankings = $model->getStudentRankings($sportId, 10);
        echo "<p>Found " . count($rankings) . " students for this sport</p>";
        if ($rankings) {
            echo "<pre>";
            print_r($rankings);
            echo "</pre>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>ERROR: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
