<?php

class CoachController {
    public function TeamSchedules() {
        view('coach/team-schedules');
    }
    public function ReportInjury() {
        // Ensure user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: /uoc-sports/public/sign-in");
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Get coach's sport_id and store in session if not present
        if (!isset($_SESSION['coach_sport_id'])) {
            require_once __DIR__ . '/../../core/Database.php';
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT sport_id FROM sport WHERE coach_id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && !empty($result['sport_id'])) {
                $_SESSION['coach_sport_id'] = $result['sport_id'];
            }
        }

        view('coach/injuries');
    }
    public function CoachCommunicate() {
        view('coach/communications');
    }
}