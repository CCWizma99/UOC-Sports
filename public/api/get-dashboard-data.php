<?php
require_once '../../core/Database.php';
require_once '../../config/config.php';
require_once '../../app/models/SportPracticeSession.php';
require_once '../../app/models/SportCompetition.php';

header('Content-Type: application/json');

$sportId = $_GET['sport_id'] ?? null;
$month = $_GET['month'] ?? date('m');

$practiceModel = new SportPracticeSession();
$competitionModel = new SportCompetition();

// Get today's practice sessions
$todaySessions = $practiceModel->getTodaySessions($sportId ?: null);

// Get competitions for selected month
$upcomingCompetitions = $competitionModel->getCompetitionsByMonth($sportId ?: null, $month, 10);

echo json_encode([
    'success' => true,
    'todaySessions' => $todaySessions,
    'upcomingCompetitions' => $upcomingCompetitions
]);
