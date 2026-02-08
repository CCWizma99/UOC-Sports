<?php
require_once '../config/config.php';
require_once '../core/Database.php';

$sportId = $_GET['sport'] ?? 'BAD';

$db = Database::getConnection();

echo "<h2>Debug Sport Selection</h2>";
echo "<p>Sport ID from URL: <strong>" . htmlspecialchars($sportId) . "</strong></p>";

// Get sport name from sport_id
$stmt = $db->prepare("SELECT sport_id, sport_name FROM sport WHERE sport_id = ?");
$stmt->execute([$sportId]);
$sport = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<h3>Sport from Database:</h3>";
echo "<pre>";
print_r($sport);
echo "</pre>";

echo "<h3>All Sports in Database:</h3>";
$allSports = $db->query("SELECT sport_id, sport_name FROM sport ORDER BY sport_name")->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($allSports);
echo "</pre>";

echo "<h3>Form Options Match Test:</h3>";
$formOptions = [
    'Athletics', 'Rugby', 'Tennis', 'Weightlifting', 'Basketball', 
    'Carrom', 'Scrabble', 'Chess', 'Football', 'Baseball', 
    'Rowing', 'Netball', 'Teakwondo', 'Hockey', 'Elle', 
    'Cricket', 'Kabaddi', 'Wrestling', 'Badminton', 'Table Tennis', 
    'Volleyball', 'Boxing', 'Karate', 'Swimming'
];

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>DB Sport Name</th><th>In Form Options?</th><th>Exact Match</th></tr>";
foreach ($allSports as $dbSport) {
    $inOptions = in_array($dbSport['sport_name'], $formOptions);
    $exactMatch = '';
    foreach ($formOptions as $option) {
        if (strcasecmp($dbSport['sport_name'], $option) === 0) {
            $exactMatch = $option;
            break;
        }
    }
    echo "<tr>";
    echo "<td>" . htmlspecialchars($dbSport['sport_name']) . "</td>";
    echo "<td>" . ($inOptions ? 'YES' : 'NO') . "</td>";
    echo "<td>" . ($exactMatch ? htmlspecialchars($exactMatch) : '<span style="color:red">NO MATCH</span>') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>Selected Sport Would Be:</h3>";
if ($sport) {
    echo "<p style='background: #d1fae5; padding: 10px;'>";
    echo "Sport Name: <strong>" . htmlspecialchars($sport['sport_name']) . "</strong><br>";
    echo "Would match option: <strong>" . (in_array($sport['sport_name'], $formOptions) ? 'YES' : 'NO') . "</strong>";
    echo "</p>";
} else {
    echo "<p style='background: #fee2e2; padding: 10px;'>No sport found with ID: " . htmlspecialchars($sportId) . "</p>";
}
?>
