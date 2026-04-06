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
        // Get captain's user_id from session
        if (!isset($_SESSION['user_id'])) {
            header("Location: /uoc-sports/public/login");
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $scheduleModel = new Schedule();
        
        // Get captain's sport_id from database if not already in session
        if (!isset($_SESSION['captain_sport_id'])) {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT sport_id FROM sport WHERE captain_id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && !empty($result['sport_id'])) {
                $_SESSION['captain_sport_id'] = $result['sport_id'];
            } else {
                // User is not a captain of any sport
                die("Error: You are not assigned as a captain for any sport.");
            }
        }
        
        $sportId = $_SESSION['captain_sport_id'];

        // Handle Create
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
            // sanitize or validate as needed
            $facility = $_POST['facility'] ?? '';
            $date = $_POST['date'] ?? '';
            $time = $_POST['time'] ?? '';
            $description = $_POST['description'] ?? '';

            $scheduleModel->create($facility, $date, $time, $description, $sportId, 'CAPTAIN');
            header("Location: /uoc-sports/public/captain/schedule-practice");
            exit;
        }

        // Handle Update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
            $id = $_POST['id'] ?? null;
            $facility = $_POST['facility'] ?? '';
            $date = $_POST['date'] ?? '';
            $time = $_POST['time'] ?? '';
            $description = $_POST['description'] ?? '';

        if ($id) {
            $scheduleModel->update($id, $facility, $date, $time, $description);
        }
        header("Location: /uoc-sports/public/captain/schedule-practice");
        exit;
    }

        // Handle Delete
        if (isset($_GET['delete'])) {
            $scheduleModel->delete($_GET['delete']);
            header("Location: /uoc-sports/public/captain/schedule-practice");
            exit;
        }

        // Fetch all schedules for display (filtered by captain's sport)
        $schedules = $scheduleModel->getAll($sportId);
        view('captain/schedule-practice', ['schedules' => $schedules]);
    }   

    public function Communication() {
        view('captain/communication');
    }

    public function AddResult() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /uoc-sports/public/sign-in");
            exit;
        }

        $captainId = $_SESSION['user_id'];
        $permModel = new EventResultPermission();
        $permittedTournaments = $permModel->getActivePermissionsForCaptain($captainId);

        view('captain/add-result', [
            'permitted_tournaments' => $permittedTournaments,
        ]);
    }
}

