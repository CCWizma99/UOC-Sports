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

        // Get coach name and sport name
        $coachName = '';
        $sportName = '';
        
        $userStmt = $pdo->prepare("SELECT fname, lname FROM user WHERE user_id = ?");
        $userStmt->execute([$userId]);
        $userData = $userStmt->fetch(PDO::FETCH_ASSOC);
        if ($userData) {
            $coachName = htmlspecialchars($userData['fname'] . ' ' . $userData['lname']);
        }

        if ($sportId) {
            $sportStmt = $pdo->prepare("SELECT sport_name FROM sport WHERE sport_id = ?");
            $sportStmt->execute([$sportId]);
            $sportData = $sportStmt->fetch(PDO::FETCH_ASSOC);
            if ($sportData) {
                $sportName = htmlspecialchars($sportData['sport_name']);
            }
        }

        $data = [
            'schedules' => [],
            'members' => [],
            'upcoming_matches_count' => 0,
            'coach_name' => $coachName,
            'sport_name' => $sportName,
            'debug_sport_id' => $sportId,
            'debug_user_id' => $userId
        ];

        if ($sportId) {
            // Get Schedules
            require_once __DIR__ . '/../models/Schedule.php';
            $scheduleModel = new Schedule();
            $data['schedules'] = $scheduleModel->getUpcomingSessions($sportId, 10);

            // Get Team Members
            require_once __DIR__ . '/../models/SportTeam.php';
            $teamModel = new SportTeam();
            $data['members'] = $teamModel->getTeamMembers($sportId);

            // Get Upcoming Tournaments (Matches)
            require_once __DIR__ . '/../models/Tournament.php';
            $tournamentModel = new Tournament();
            $today = date('Y-m-d');
            $allTournaments = $tournamentModel->getTournamentsBySportId($sportId);

            $data['upcoming_matches'] = [];
            foreach ($allTournaments as $tour) {
                if ((isset($tour['start_date']) && $tour['start_date'] >= $today) || 
                    (isset($tour['end_date']) && $tour['end_date'] >= $today)) {
                    $data['upcoming_matches'][] = [
                        'start_date' => $tour['start_date'],
                        'end_date' => $tour['end_date'],
                        'name' => $tour['tournament_name'] ?? 'Tournament'
                    ];
                }
            }

            // Sort by start date
            usort($data['upcoming_matches'], function($a, $b) {
                return strtotime($a['start_date'] ?? 0) <=> strtotime($b['start_date'] ?? 0);
            });
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
        $sportId = null;

        // Get coach's sport_id and store in session if not present
        require_once __DIR__ . '/../../core/Database.php';
        $pdo = Database::getConnection();
        
        if (!isset($_SESSION['coach_sport_id'])) {
            $stmt = $pdo->prepare("SELECT sport_id FROM sport WHERE coach_id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && !empty($result['sport_id'])) {
                $_SESSION['coach_sport_id'] = $result['sport_id'];
                $sportId = $result['sport_id'];
            }
        } else {
            $sportId = $_SESSION['coach_sport_id'];
        }

        $data = [
            'members' => []
        ];

        // Get Team Members for the sport
        if ($sportId) {
            require_once __DIR__ . '/../models/SportTeam.php';
            $teamModel = new SportTeam();
            $data['members'] = $teamModel->getTeamMembers($sportId);
        }

        view('coach/injuries', $data);
    }
    public function CoachCommunicate() {
        view('coach/communications');
    }
}