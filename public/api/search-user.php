<?php
require_once '../../config/config.php';
require_once '../../core/Database.php';

header('Content-Type: application/json');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$faculty = isset($_GET['faculty']) ? trim($_GET['faculty']) : '';
$year = isset($_GET['year']) ? trim($_GET['year']) : '';
$sport = isset($_GET['sport']) ? trim($_GET['sport']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';

try {
    $db = Database::getConnection();

    $sql = "
        SELECT u.user_id, u.fname, u.lname, u.type
        FROM user u
        LEFT JOIN faculty f ON u.faculty_id = f.faculty_id
        LEFT JOIN `sports-team` st ON u.student_id = st.student_id
        LEFT JOIN sport s ON st.sport_id = s.sport_id
        WHERE 1
    ";
    $params = [];

    if ($query !== '') {
        $sql .= " AND (u.user_id LIKE ? OR u.fname LIKE ? OR u.lname LIKE ?)";
        $like = "%$query%";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }
    if ($faculty !== '') {
        $sql .= " AND f.faculty_name = ?";
        $params[] = $faculty;
    }
    if ($year !== '') {
        $sql .= " AND u.year = ?";
        $params[] = $year;
    }
    if ($sport !== '') {
        $sql .= " AND s.sport_name = ?";
        $params[] = $sport;
    }
    if ($type !== '') {
        $sql .= " AND u.type = ?";
        $params[] = $type;
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error occurred',
        'message' => $e->getMessage()
    ]);
}
