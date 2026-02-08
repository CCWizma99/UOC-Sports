<?php
header('Content-Type: application/json');

require_once '../../config/config.php';
require_once '../../core/Database.php';
require_once '../../core/Model.php';
require_once '../../app/models/SportAchievements.php';

try {
    // Get sport_id from query parameter
    $sportId = $_GET['sport_id'] ?? null;
    
    // Create model instance
    $achievementsModel = new SportAchievements();
    
    // Get student rankings
    $rankings = $achievementsModel->getStudentRankings($sportId, 100);
    
    echo json_encode([
        'success' => true,
        'rankings' => $rankings
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching rankings: ' . $e->getMessage()
    ]);
}
