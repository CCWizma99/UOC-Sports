<?php
header('Content-Type: application/json');

require_once '../../config/config.php';
require_once '../../core/Database.php';
require_once '../../core/Model.php';
require_once '../../app/models/SportAchievements.php';

try {
    // Get user_id from query parameter
    $userId = $_GET['user_id'] ?? null;
    
    if (!$userId) {
        throw new Exception('User ID is required');
    }
    
    // Create model instance
    $achievementsModel = new SportAchievements();
    
    // Get student's achievements
    $achievements = $achievementsModel->getByStudent($userId);
    
    echo json_encode([
        'success' => true,
        'achievements' => $achievements
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
