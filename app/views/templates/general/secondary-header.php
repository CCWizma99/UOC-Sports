<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userType = $_SESSION['user_type'] ?? null;
$currentPage = $_SERVER['REQUEST_URI'];

// Exclude Admin and Executive
if (!$userType || in_array($userType, ['ADMIN', 'EXECUTIVE'])) {
    return;
}

// Map user types to human-readable names and home links
$roleMap = [
    'STUDENT' => ['name' => 'Student Portal', 'url' => '/uoc-sports/public/student'],
    'CAPTAIN' => ['name' => 'Captain Portal', 'url' => '/uoc-sports/public/captain'],
    'COACH'   => ['name' => 'Coach Portal', 'url' => '/uoc-sports/public/coach'],
    'EQP'     => ['name' => 'Equipment Manager', 'url' => '/uoc-sports/public/equipment-manager'],
    'SPT'     => ['name' => 'Sport Manager', 'url' => '/uoc-sports/public/sport-manager'],
    'REG'     => ['name' => 'Registrar', 'url' => '/uoc-sports/public/registrar'],
];

// Check if the current page belongs to the user's specific portal
$userPortalUrl = $roleMap[$userType]['url'] ?? '';
if (strpos($currentPage, $userPortalUrl) === false) {
    return;
}

$roleInfo = $roleMap[$userType] ?? ['name' => 'User Portal', 'url' => '#'];

// Define navigation links for each role
$navLinks = [];

switch ($userType) {
    case 'STUDENT':
        $navLinks = [
            ['name' => 'Overview', 'url' => '/uoc-sports/public/student/'],
            ['name' => 'Sports', 'url' => '/uoc-sports/public/student/sports/'],
            ['name' => 'Equipments', 'url' => '/uoc-sports/public/student/equipment/'],
            ['name' => 'My Bookings', 'url' => '/uoc-sports/public/student/bookings/'],
        ];
        break;
    case 'CAPTAIN':
        $navLinks = [
            ['name' => 'Home', 'url' => '/uoc-sports/public/captain'],
            ['name' => 'Team', 'url' => '/uoc-sports/public/captain/add-members'],
            ['name' => 'Schedule', 'url' => '/uoc-sports/public/captain/schedule-practice'],
            ['name' => 'Attendance', 'url' => '/uoc-sports/public/captain/mark-attendance'],
            ['name' => 'Add Result', 'url' => '/uoc-sports/public/captain/add-result'],
            ['name' => 'Communication', 'url' => '/uoc-sports/public/captain/communication']
        ];
        break;
    case 'COACH':
        $navLinks = [
            ['name' => 'Home', 'url' => '/uoc-sports/public/coach'],
            ['name' => 'Injuries', 'url' => '/uoc-sports/public/coach/report-injury'],
            ['name' => 'Communication', 'url' => '/uoc-sports/public/coach/coach-communicate'],
        ];
        break;
    case 'EQP':
        $navLinks = [
            ['name' => 'Home', 'url' => '/uoc-sports/public/equipment-manager'],
            ['name' => 'Equipments', 'url' => '/uoc-sports/public/equipment-manager/equipments'],
            ['name' => 'Lost Items', 'url' => '/uoc-sports/public/equipment-manager/lostitem'],
            ['name' => 'Booking Requests', 'url' => '/uoc-sports/public/equipment-manager/bookingrequests'],
        ];
        break;
    case 'SPT':
        // Sport manager needs managed sports for the selector
        $userId = $_SESSION['user_id'] ?? null;
        $managedSports = [];
        $selectedSportId = $_GET['sport'] ?? null;
        if ($userId) {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT s.sport_id, s.sport_name 
                                  FROM manager_sport ms
                                  JOIN sport s ON ms.sport_id = s.sport_id
                                  WHERE ms.user_id = ?
                                  ORDER BY s.sport_name");
            $stmt->execute([$userId]);
            $managedSports = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$selectedSportId && !empty($managedSports)) {
                $selectedSportId = $managedSports[0]['sport_id'];
            }
        }
        $sportParam = $selectedSportId ? '?sport=' . urlencode($selectedSportId) : '';
        $navLinks = [
            ['name' => 'Home', 'url' => '/uoc-sports/public/sport-manager'],
            ['name' => 'Expenses', 'url' => '/uoc-sports/public/sport-manager/expenses' . $sportParam],
            ['name' => 'Practice Sessions', 'url' => '/uoc-sports/public/sport-manager/practicesessions' . $sportParam],
            ['name' => 'Sport Events', 'url' => '/uoc-sports/public/sport-manager/tournaments' . $sportParam],
            ['name' => 'Achievements', 'url' => '/uoc-sports/public/sport-manager/team' . $sportParam],
            ['name' => 'Messages', 'url' => '/uoc-sports/public/sport-manager/messages' . $sportParam],
        ];
        break;
    case 'REG':
        $navLinks = [
            ['name' => 'Home', 'url' => '/uoc-sports/public/registrar'],
            ['name' => 'Verify Students', 'url' => '/uoc-sports/public/registrar/verify-students'],
            ['name' => 'Verify Staff', 'url' => '/uoc-sports/public/registrar/verify-staff'],
            ['name' => 'Verify Bookings', 'url' => '/uoc-sports/public/registrar/verify-bookings'],
        ];
        break;
}
?>

<link rel="stylesheet" href="/uoc-sports/public/css/general/secondary-header.css">

<div class="secondary-header single-bar">
    <div class="container">
        <div class="header-nav-wrapper">
            <ul class="nav-tabs">
                <?php foreach ($navLinks as $link): ?>
                    <?php 
                        $isActive = false;
                        if ($link['name'] === 'Home' || $link['name'] === 'Overview') {
                            $isActive = ($currentPage === $link['url'] || $currentPage === rtrim($link['url'], '/'));
                        } else {
                            $isActive = strpos($currentPage, $link['url']) !== false;
                        }
                    ?>
                    <li>
                        <a href="<?= htmlspecialchars($link['url']) ?>" class="<?= $isActive ? 'active' : '' ?>">
                            <?= htmlspecialchars($link['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if ($userType === 'SPT' && !empty($managedSports)): ?>
            <div class="sport-selector">
                <label for="secondary-sport-selector" class="sport-selector-label">Sport:</label>
                <div class="sport-select-wrap">
                    <select id="secondary-sport-selector" onchange="switchSport(this.value)">
                        <?php foreach ($managedSports as $sport): ?>
                            <option value="<?= htmlspecialchars($sport['sport_id']) ?>" 
                                    <?= $sport['sport_id'] == $selectedSportId ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sport['sport_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <script>
                function switchSport(sportId) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('sport', sportId);
                    window.location.href = url.toString();
                }
            </script>
            <?php endif; ?>
        </div>
    </div>
</div>
