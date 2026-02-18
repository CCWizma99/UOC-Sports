<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../core/Database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$coachId = $_SESSION['user_id'];
$sportId = $_GET['sport_id'] ?? null;

if (!$sportId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'sport_id required']);
    exit;
}

try {
    $pdo = Database::getConnection();

    // Fetch all team members for the given sport
    $stmt = $pdo->prepare("
        SELECT 
            u.user_id,
            u.fname,
            u.lname
        FROM user u
        INNER JOIN sport_team st ON u.user_id = st.user_id
        WHERE st.sport_id = ?
        ORDER BY u.fname, u.lname
    ");
    $stmt->execute([$sportId]);
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'players' => $players
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
