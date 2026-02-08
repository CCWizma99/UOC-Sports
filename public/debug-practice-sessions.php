<?php
require_once '../core/autoload.php';
require_once '../config/config.php';

$db = Database::getConnection();

echo "<h2>Sports in Database:</h2>";
$stmt = $db->query('SELECT sport_id, sport_name FROM sport ORDER BY sport_name');
echo "<table border='1'><tr><th>Sport ID</th><th>Sport Name</th></tr>";
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr><td>{$row['sport_id']}</td><td>{$row['sport_name']}</td></tr>";
}
echo "</table>";

echo "<h2>Practice Sessions in Database:</h2>";
$stmt = $db->query('SELECT ps.id, ps.sport_id, s.sport_name, ps.location, ps.session_date, ps.start_time, ps.end_time 
                    FROM practice_sessions ps 
                    LEFT JOIN sport s ON ps.sport_id = s.sport_id 
                    ORDER BY ps.session_date DESC');
echo "<table border='1'><tr><th>ID</th><th>Sport ID</th><th>Sport Name</th><th>Location</th><th>Date</th><th>Start</th><th>End</th></tr>";
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr><td>{$row['id']}</td><td>{$row['sport_id']}</td><td>{$row['sport_name']}</td><td>{$row['location']}</td><td>{$row['session_date']}</td><td>{$row['start_time']}</td><td>{$row['end_time']}</td></tr>";
}
echo "</table>";

// Check Badminton specifically
echo "<h2>Badminton Practice Sessions:</h2>";
$stmt = $db->prepare('SELECT ps.*, s.sport_name 
                      FROM practice_sessions ps 
                      LEFT JOIN sport s ON ps.sport_id = s.sport_id 
                      WHERE LOWER(s.sport_name) LIKE ?');
$stmt->execute(['%badminton%']);
echo "<table border='1'><tr><th>ID</th><th>Sport ID</th><th>Sport Name</th><th>Location</th><th>Date</th><th>Start</th><th>End</th></tr>";
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr><td>{$row['id']}</td><td>{$row['sport_id']}</td><td>{$row['sport_name']}</td><td>{$row['location']}</td><td>{$row['session_date']}</td><td>{$row['start_time']}</td><td>{$row['end_time']}</td></tr>";
}
echo "</table>";

// Test the filter
if (isset($_GET['sport'])) {
    $sportId = $_GET['sport'];
    echo "<h2>Testing filter for sport_id: {$sportId}</h2>";
    $stmt = $db->prepare('SELECT ps.*, s.sport_name 
                          FROM practice_sessions ps 
                          LEFT JOIN sport s ON ps.sport_id = s.sport_id 
                          WHERE ps.sport_id = ?');
    $stmt->execute([$sportId]);
    echo "<table border='1'><tr><th>ID</th><th>Sport ID</th><th>Sport Name</th><th>Location</th><th>Date</th></tr>";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr><td>{$row['id']}</td><td>{$row['sport_id']}</td><td>{$row['sport_name']}</td><td>{$row['location']}</td><td>{$row['session_date']}</td></tr>";
    }
    echo "</table>";
}
?>
