<?php

class CoachController {
    public function TeamSchedules() {
        // Ensure user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: /uoc-sports/public/sign-in");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $sportId = null;

        // Verify/Get coach's sport_id directly from DB to avoid session sync issues
        require_once __DIR__ . '/../../core/Database.php';
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT sport_id FROM sport WHERE coach_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && !empty($result['sport_id'])) {
            $sportId = $result['sport_id'];
            $_SESSION['coach_sport_id'] = $sportId; // Update session just in case
        }

        $data = [
            'schedules' => [],
            'members' => [],
            'upcoming_matches_count' => 0,
            'debug_sport_id' => $sportId,
            'debug_user_id' => $userId
        ];

        if ($sportId) {
            // Get Schedules
            require_once __DIR__ . '/../models/Schedule.php';
            $scheduleModel = new Schedule();
            $data['schedules'] = $scheduleModel->getRecentAndUpcomingSessions($sportId, 10);

            // Get Team Members
            require_once __DIR__ . '/../models/SportTeam.php';
            $teamModel = new SportTeam();
            $data['members'] = $teamModel->getTeamMembers($sportId);

            // Get Upcoming Matches (Tournaments)
            require_once __DIR__ . '/../models/Sport.php';
            $sportModel = new Sport();
            // Assuming getTournaments fetches all incomplete tournaments. It doesn't filter by sport ID though?
            // Sport::getTournaments() returns all.
            // Let's rely on fake data for matches or 0 if we can't filter easily. 
            // Actually Sport.php: getTournaments() query: SELECT ... FROM tournament t JOIN sport s ...
            // It doesn't enable filtering by ID.
            // I'll leave fake match count or set to 0. 
            // Wait, I can try to filter the result array.
            $tournaments = $sportModel->getTournaments();
            $data['upcoming_matches_count'] = 0;
            foreach ($tournaments as $t) {
                if ($t['sport_id'] === $sportId) {
                    $data['upcoming_matches_count']++;
                }
            }
        }

        view('coach/team-schedules', $data);
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