<?php
// scripts/generate_more_mock.php

$sports = ['BAD', 'VOL', 'FOO', 'TEN', 'BAS', 'HOC', 'NET', 'CRI', 'RUG', 'SWI', 'TT', 'WL', 'ROW', 'WRE', 'CHE', 'ATH', 'BOX', 'TKD', 'KRT', 'RR', 'SCR', 'ELL', 'BB', 'KBD', 'CRM'];
$users = [];
for ($i=1; $i<=300; $i++) {
    $users[] = "MOCKU" . str_pad($i, 3, "0", STR_PAD_LEFT);
}

// 0. CLEANUP OLD BROKEN IMPORTS IF THEY EXIST IN MAIN.SQL
$targetFile = __DIR__ . '/../database/SQL_FILES/Main.sql';
if (file_exists($targetFile)) {
    $content = file_get_contents($targetFile);
    $pos = strpos($content, "-- ==========================================\n-- DASHBOARD EXTENDED MOCK DATA GENERATED ON");
    if ($pos !== false) {
        $content = substr($content, 0, $pos);
        file_put_contents($targetFile, rtrim($content) . "\n");
        echo "Cleaned up old broken mock data from Main.sql!\n";
    }
}

$output = "\n\n-- ==========================================\n";
$output .= "-- DASHBOARD EXTENDED MOCK DATA GENERATED ON " . date('Y-m-d H:i:s') . "\n";
$output .= "-- ==========================================\n\n";

// 1. BUDGETS
$output .= "-- === BUDGETS ===\n";
foreach(array_slice($sports, 0, 10) as $index => $sp) {
    $budgetId = "MOCKBG" . str_pad($index, 2, "0", STR_PAD_LEFT);
    $allocated = rand(100000, 500000);
    $spent = rand(10000, $allocated + 50000); // Sometimes overspent
    $date = date('Y-01-01');
    $output .= "INSERT IGNORE INTO `budget` (`budget_id`, `sport_id`, `year`, `allocated_amount`, `spent_amount`, `allocation_date`, `description`) VALUES ";
    $output .= "('{$budgetId}', '{$sp}', '2026', {$allocated}, {$spent}, '{$date}', 'Mock Budget');\n";
}
$output .= "\n";

// 2. TOURNAMENTS
$output .= "-- === TOURNAMENTS ===\n";
$tournaments = [];
foreach(array_slice($sports, 0, 15) as $index => $sp) {
    $tId = "MOCKT" . str_pad($index, 3, "0", STR_PAD_LEFT);
    $tournaments[] = $tId;
    $dateOffset = rand(-60, 60);
    $start = date('Y-m-d', strtotime("{$dateOffset} days"));
    $end = date('Y-m-d', strtotime("{$dateOffset} days + 5 days"));
    $status = $dateOffset < 0 ? 'COMPLETE' : 'INCOMPLETE';
    $output .= "INSERT IGNORE INTO `tournament` (`tournament_id`, `tournament_name`, `sport_id`, `start_date`, `end_date`, `status`) VALUES ";
    $output .= "('{$tId}', 'Mock Tournament {$sp}', '{$sp}', '{$start}', '{$end}', '{$status}');\n";
    
    // Add matches for this tournament
    for($m=1; $m<=5; $m++) {
        $mId = "MOCKM{$index}_{$m}";
        $mDate = date('Y-m-d', strtotime("{$start} + {$m} days"));
        $result = $dateOffset < 0 ? 'COMPLETED' : 'PENDING';
        $output .= "INSERT IGNORE INTO `tournament_match` (`match_id`, `tournament_id`, `sport_id`, `sport_category`, `match_name`, `match_date`, `result_status`, `is_published`) VALUES ";
        // Using team_goal as arbitrary default
        $output .= "('{$mId}', '{$tId}', '{$sp}', 'TEAM_GOAL', 'Match {$m}', '{$mDate}', '{$result}', 1);\n";
    }
}
$output .= "\n";

// 3. INQUIRIES
$output .= "-- === INQUIRIES ===\n";
$statuses = ['RESOLVED', 'NOT-RESOLVED'];
for($i=1; $i<=20; $i++) {
    $inqId = "MOCKI" . str_pad($i, 3, "0", STR_PAD_LEFT);
    $uid = $users[array_rand($users)];
    $status = $statuses[array_rand($statuses)];
    $date = date('Y-m-d', strtotime("-" . rand(1, 30) . " days"));
    $output .= "INSERT IGNORE INTO `inquiry` (`inquiry_id`, `user_id`, `email`, `subject`, `message`, `date`, `status`) VALUES ";
    $output .= "('{$inqId}', '{$uid}', 'mock@example.com', 'Mock subject {$i}', 'Mock msg', '{$date}', '{$status}');\n";
}
$output .= "\n";

// 4. GRN
$output .= "-- === GOOD RECEIVED NOTES ===\n";
for($i=1; $i<=15; $i++) {
    $sp = $sports[array_rand($sports)];
    $date = date('Y-m-d', strtotime("-" . rand(1, 60) . " days"));
    $qty = rand(10, 50);
    $output .= "INSERT IGNORE INTO `good_received_notes` (`sport_id`, `equipment_id`, `description`, `date`, `po_number`, `supplier_id`, `invoice_no`, `quantity`, `unit`, `unit_price`, `stock_id`) VALUES ";
    $output .= "('{$sp}', 'EQ001', 'Mock GRN', '{$date}', 'PO123', 1, 'INV123', {$qty}, 'PCS', 500.00, 'MOCKS001');\n";
}
$output .= "\n";

// 5. EQUIPMENT REQUESTS
$output .= "-- === EQUIPMENT REQUESTS ===\n";
for($i=1; $i<=10; $i++) {
    $reqId = "MOCKE" . str_pad($i, 3, "0", STR_PAD_LEFT);
    $uid = $users[array_rand($users)];
    $date = date('Y-m-d', strtotime("+" . rand(1, 10) . " days"));
    $output .= "INSERT IGNORE INTO `equipment-requests` (`request_id`, `student_id`, `category_name`, `equipment_id`, `request_date`, `start_time`, `end_time`, `purpose`, `status`, `notes`) VALUES ";
    $output .= "('{$reqId}', '{$uid}', 'Mock Category', 'EQ001', '{$date}', '10:00:00', '12:00:00', 'Practice', 'ACTIVE', 'None');\n";
}
$output .= "\n";

// 6. COMMENTS
$output .= "-- === COMMENTS ===\n";
for($i=1; $i<=30; $i++) {
    $cmtId = "MOCKC" . str_pad($i, 3, "0", STR_PAD_LEFT);
    $pid = "MOCKP" . str_pad(rand(1, 20), 3, "0", STR_PAD_LEFT);
    $uid = $users[array_rand($users)];
    $output .= "INSERT IGNORE INTO `comment` (`comment_id`, `post_id`, `comment_from`, `reply_to`, `content`) VALUES ";
    $output .= "('{$cmtId}', '{$pid}', '{$uid}', '', 'Mock comment content {$i}');\n";
}
$output .= "\n";

// 7. ACHIEVEMENTS
$output .= "-- === ACHIEVEMENTS ===\n";
// Link achievements to tournaments created in section 2
$tournamentIds = $tournaments;


for($i=0; $i<count($tournamentIds); $i++) {
    $tId = $tournamentIds[$i];    
    for($a=1; $a<=3; $a++) {
        $sp = $sports[array_rand($sports)];
        $uid = $users[array_rand($users)];
        $pts = rand(1, 10);
        $output .= "INSERT IGNORE INTO `achievement` (`user_id`, `sport_id`, `tournament_id`, `achievement`, `points`) VALUES ";
        $output .= "('{$uid}', '{$sp}', '{$tId}', 'Achievement in Tournament', {$pts});\n";
    }
}
$output .= "\n";

if (file_put_contents($targetFile, $output, FILE_APPEND) !== false) {
    echo "Extended SQL generation complete! MOCK dashboard SQL appended successfully to Main.sql\n";
} else {
    echo "Error: Failed to write to $targetFile\n";
}

?>
