<?php
require_once '../../core/Database.php';
require_once '../../config/config.php';
require_once '../../app/models/SportPracticeSession.php';
require_once '../../app/models/TournamentParticipant.php';

header('Content-Type: application/json');

$sportId = $_GET['sport_id'] ?? null;
$month = $_GET['month'] ?? date('m');

$practiceModel = new SportPracticeSession();
$tournamentModel = new TournamentParticipant();

// Get today's practice sessions
$todaySessions = $practiceModel->getTodaySessions($sportId ?: null);

// Get upcoming tournaments (filtered by sport if provided)
$upcomingTournaments = $tournamentModel->getUpcomingTournaments($sportId ?: null, 10);

echo json_encode([
    'success' => true,
    'todaySessions' => $todaySessions,
    'upcomingCompetitions' => $upcomingTournaments // Keep key for frontend compatibility
]);
