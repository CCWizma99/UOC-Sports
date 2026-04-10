<?php
// scripts/generate_mock_sql.php

$faculties = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];
$sports = ['BAD', 'VOL', 'FOO', 'TEN', 'BAS', 'HOC', 'NET', 'CRI', 'RUG', 'SWI', 'TT', 'WL', 'ROW', 'WRE', 'CHE', 'ATH', 'BOX', 'TKD', 'KRT', 'RR', 'SCR', 'ELL', 'BB', 'KBD', 'CRM'];
$facilities = range(1, 32); 

$mockUsers = [];
$mockAthletes = [];

$targetFile = __DIR__ . '/../database/SQL_FILES/Main.sql';

// Let's truncate the file safely to remove the previous bad appends (up to line 1769 max)
$lines = file($targetFile);
if (count($lines) > 1769) {
    $lines = array_slice($lines, 0, 1769);
    file_put_contents($targetFile, implode("", $lines));
}

$output = "\n\n-- ==========================================\n";
$output .= "-- DASHBOARD MOCK DATA GENERATED ON " . date('Y-m-d H:i:s') . "\n";
$output .= "-- ==========================================\n\n";

// 1. GENERATE USERS
$output .= "-- === 1. USERS ===\n";
for ($i = 1; $i <= 300; $i++) {
    $userId = "MOCKU" . str_pad($i, 3, "0", STR_PAD_LEFT);
    $facultyId = $faculties[array_rand($faculties)];
    $mockUsers[] = $userId;
    
    $output .= "INSERT IGNORE INTO `user` (`user_id`, `fname`, `lname`, `type`, `email`, `password`, `must_change_pass`, `profile_img`, `sport_id`, `status`, `faculty_id`) VALUES ";
    $output .= "('{$userId}', 'MockFirst{$i}', 'MockLast{$i}', 'STUDENT', 'mock{$i}@example.com', 'mockpass', 0, 'default.png', 'BAD', 'ACTIVE', '{$facultyId}');\n";
}
$output .= "\n";

// 2. GENERATE ATHLETES (50 Users become athletes)
$output .= "-- === 2. SPORTS TEAMS (ATHLETES) ===\n";
$athleteCandidates = array_slice($mockUsers, 0, 100);
foreach ($athleteCandidates as $uid) {
    $sportId = $sports[array_rand($sports)];
    $mockAthletes[] = $uid;
    $date = date('Y-m-d', strtotime("-60 days"));
    $output .= "INSERT IGNORE INTO `sports-team` (`student_id`, `sport_id`, `joined_date`, `in_team`) VALUES ";
    $output .= "('{$uid}', '{$sportId}', '{$date}', 'YES');\n";
}
$output .= "\n";

// 3. GENERATE FACILITY RESERVATIONS
$output .= "-- === 3. FACILITY BOOKINGS ===\n";
$statuses = ['BOOKED', 'ACCEPTED', 'REJECTED', 'COMPLETED'];
for ($i = 1; $i <= 100; $i++) {
    $bookingId = "MOCKB" . str_pad($i, 4, "0", STR_PAD_LEFT);
    $facilityId = $facilities[array_rand($facilities)];
    $userId = $mockUsers[array_rand($mockUsers)];
    $status = $statuses[array_rand($statuses)];
    
    // Spread dates between -30 and +15 days from today
    $dayOffset = rand(-30, 15);
    $date = date('Y-m-d', strtotime("{$dayOffset} days"));
    
    $output .= "INSERT IGNORE INTO `facility-booking` (`booking_id`, `user_id`, `facility_id`, `date`, `slot`, `purpose`, `status`, `payment_status`, `rejection_reason`) VALUES ";
    $output .= "('{$bookingId}', '{$userId}', '{$facilityId}', '{$date}', 'FULL', 'Mock Practice', '{$status}', 'INCOMPLETE', '');\n";
}
$output .= "\n";

// 4. GENERATE POSTS (COMMUNITY)
$output .= "-- === 4. COMMUNITY POSTS ===\n";
for ($i = 1; $i <= 30; $i++) {
    $postId = "MOCKP" . str_pad($i, 3, "0", STR_PAD_LEFT);
    $dayOffset = rand(-15, 0);
    $date = date('Y-m-d', strtotime("{$dayOffset} days"));
    
    $output .= "INSERT IGNORE INTO `newsfeed_post` (`post_id`, `title`, `description`, `commenting`, `date_posted`, `status`) VALUES ";
    $output .= "('{$postId}', 'Mock Topic {$i}', 'This is a mock discussion generated for the dashboard.', 'YES', '{$date}', 'ACTIVE');\n";
}
$output .= "\n";

// 5. GENERATE EQUIPMENT INVENTORY
$output .= "-- === 5. EQUIPMENT INVENTORY ===\n";
$equipment = [
    ['eq' => 'EQ001', 'sp' => 'BAD'], ['eq' => 'EQ002', 'sp' => 'BAD'],
    ['eq' => 'EQ004', 'sp' => 'VOL'], ['eq' => 'EQ007', 'sp' => 'FOO'],
    ['eq' => 'EQ011', 'sp' => 'TEN'], ['eq' => 'EQ014', 'sp' => 'BAS'],
    ['eq' => 'EQ022', 'sp' => 'CRI'], ['eq' => 'EQ023', 'sp' => 'CRI']
];

foreach ($equipment as $index => $eq) {
    if ($index > 20) break; // Limit to 20 random equipments
    
    $stockId = "MOCKS" . str_pad($index, 3, "0", STR_PAD_LEFT);
    $eqId = $eq['eq'];
    $spId = $eq['sp'];
    $qty = rand(10, 100);
    $usable = rand(5, $qty);
    $date = date('Y-m-d', strtotime("-60 days"));
    
    $output .= "INSERT IGNORE INTO `equipment_inventory` (`stock_id`, `equipment_id`, `sport_id`, `quantity`, `usable`, `added_date`, `remarks`) VALUES ";
    $output .= "('{$stockId}', '{$eqId}', '{$spId}', {$qty}, {$usable}, '{$date}', 'Mock Stock');\n";
}
$output .= "\n";

// 6. ACHIEVEMENTS
// Skipping achievements to avoid table missmatches since I don't know the schema perfectly.



$targetFile = __DIR__ . '/../database/SQL_FILES/Main.sql';
if (file_put_contents($targetFile, $output, FILE_APPEND) !== false) {
    echo "SQL generation complete! MOCK dashboard SQL appended successfully to Main.sql\n";
} else {
    echo "Error: Failed to write to $targetFile\n";
}

?>
