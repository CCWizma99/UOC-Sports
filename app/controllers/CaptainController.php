<?php

class CaptainController {
    public function index() {
        // Ensure user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: /uoc-sports/public/sign-in");
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Load models
        $userModel = new User();
        $sportModel = new Sport();
        $sportTeamModel = new SportTeam();
        $scheduleModel = new Schedule();
        
        // Get user display name
        $profile = $userModel->getUserProfile($userId);
        $username = isset($profile['full_name']) ? $profile['full_name'] : (trim(($profile['fname'] ?? '') . ' ' . ($profile['lname'] ?? '')) ?: 'Captain');

        // Get sport assigned to this captain (if any)
        $sport = $sportModel->getSportByCaptain($userId);
        $sportName = $sport && isset($sport['sport_name']) ? $sport['sport_name'] : null;

        $practiceSessions = $scheduleModel->getSessionsBySport($sport['sport_id'] ?? null);

        $memberCount = $sportTeamModel->getTeamMemberCount($sport['sport_id'] ?? null);

        $sessionCount = $scheduleModel->getSessionCountById($sport['sport_id'] ?? null);

        view('captain/index', ['username' => $username, 'sport_name' => $sportName, 'member_count' => $memberCount, 'practice_sessions' => $practiceSessions, 'session_count' => $sessionCount]);
    }
    public function MarkAttendance() {
        view('captain/mark-attendance');
    }
    public function AddMembers() {
        view('captain/add-members');
    }
    public function SchedulePractice() {

    if (!isset($_SESSION['user_id'])) {
        header("Location: /uoc-sports/public/login");
        exit;
    }

    $userId = $_SESSION['user_id'];
    $scheduleModel = new Schedule();
    $pdo = Database::getConnection(); // ✅ Define once here

    // Get captain sport id
    if (!isset($_SESSION['captain_sport_id'])) {
        $stmt = $pdo->prepare("SELECT sport_id FROM sport WHERE captain_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && !empty($result['sport_id'])) {
            $_SESSION['captain_sport_id'] = $result['sport_id'];
        } else {
            die("Error: You are not assigned as a captain for any sport.");
        }
    }

    $sportId = $_SESSION['captain_sport_id'];

    // ✅ Get sport name ONCE (important fix)
    $stmt = $pdo->prepare("SELECT sport_name FROM sport WHERE sport_id = ?");
    $stmt->execute([$sportId]);
    $sportData = $stmt->fetch(PDO::FETCH_ASSOC);
    $sportName = $sportData['sport_name'] ?? '';

    /* ===================== CREATE ===================== */
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {

        $facility = $sportName; // Always secure

        $date = $_POST['date'] ?? '';
        $startTime = $_POST['start_time'] ?? '';
        $endTime = $_POST['end_time'] ?? '';
        $equipment = $_POST['need_equipment'] ?? '';
        $location = $_POST['location'] ?? '';
        $notes = $_POST['notes'] ?? '';

        $scheduleModel->create(
            $facility,
            $date,
            $startTime,
            $endTime,
            $equipment,
            $location,
            $notes,
            $sportId
        );

        header("Location: /uoc-sports/public/captain/schedule-practice");
        exit;
    }

    /* ===================== UPDATE ===================== */
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {

        $id = $_POST['id'] ?? null;

        $facility = $sportName; // Always secure

        $date = $_POST['date'] ?? '';
        $startTime = $_POST['start_time'] ?? '';
        $endTime = $_POST['end_time'] ?? '';
        $equipment = $_POST['need_equipment'] ?? '';
        $location = $_POST['location'] ?? '';
        $notes = $_POST['notes'] ?? '';

        if ($id) {
            $scheduleModel->update(
                $id,
                $facility,
                $date,
                $startTime,
                $endTime,
                $equipment,
                $location,
                $notes
            );
        }

        header("Location: /uoc-sports/public/captain/schedule-practice");
        exit;
    }

    /* ===================== DELETE ===================== */
    if (isset($_GET['delete'])) {
        $scheduleModel->delete($_GET['delete']);
        header("Location: /uoc-sports/public/captain/schedule-practice");
        exit;
    }

    /* ===================== VIEW LOAD ===================== */
    $schedules = $scheduleModel->getAll($sportId);

    view('captain/schedule-practice', [
        'schedules' => $schedules,
        'sport_name' => $sportName
    ]);
}

}