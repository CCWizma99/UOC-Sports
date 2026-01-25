<?php
// Test the equipment API
$sportId = 'ATH'; // Test with Athletics

$url = 'http://localhost/uoc-sports/public/api/get-equipment-by-sport.php?sport_id=' . $sportId;

echo "Testing URL: " . $url . "\n\n";

$response = file_get_contents($url);
echo "Response:\n";
print_r($response);
echo "\n\n";

$data = json_decode($response, true);
echo "Decoded Data:\n";
print_r($data);
