<?php
require_once '../../config/config.php';
require_once '../../core/Database.php';

header('Content-Type: application/json');

if (!isset($_GET['q']) || empty(trim($_GET['q']))) {
    echo json_encode([]);
    exit;
}

$query = trim($_GET['q']);

try {
    $db = Database::getConnection();

    $stmt = $db->prepare("SELECT user_id, fname, lname FROM user WHERE user_id LIKE ? OR fname LIKE ? OR lname LIKE ?");
    $like = "%$query%";
    $stmt->execute([$like, $like, $like]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error occurred',
        'message' => $e->getMessage()
    ]);
}
