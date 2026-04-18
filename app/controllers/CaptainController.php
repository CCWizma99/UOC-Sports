<?php

class CaptainController {

    /* ===================== DASHBOARD ===================== */
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /uoc-sports/public/sign-in");
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Models
        $userModel = new User();
        $sportModel = new Sport();
        $sportTeamModel = new SportTeam();
        $scheduleModel = new Schedule();

        require_once __DIR__ . '/../models/TournamentParticipant.php';
        require_once __DIR__ . '/../models/Tournament.php';

        $tournamentPartModel = new TournamentParticipant();
        $tournamentModel = new Tournament();

        // User name
        $profile = $userModel->getUserProfile($userId);
        $username = $profile['full_name'] ?? 
            (trim(($profile['fname'] ?? '') . ' ' . ($profile['lname'] ?? '')) ?: 'Captain');

        // Sport
        $sport = $sportModel->getSportByCaptain($userId);
        $sportId = $sport['sport_id'] ?? null;
        $sportName = $sport['sport_name'] ?? null;

        // Stats
        $practiceSessions = $scheduleModel->getSessionsBySport($sportId);
        $memberCount = $sportTeamModel->getTeamMemberCount($sportId);
        $sessionCount = $scheduleModel->getSessionCountById($sportId);

        /* ===== EVENTS ===== */
        $today = date('Y-m-d');
        $allTournaments = $tournamentModel->getTournamentsBySportId($sportId);

        $upcomingTournaments = array_filter($allTournaments, function($t) use ($today) {
            return (isset($t['start_date']) && $t['start_date'] >= $today) || (isset($t['end_date']) && $t['end_date'] >= $today);
        });

        $events = [];

        foreach ($upcomingTournaments as $tour) {
            $events[] = [
                'start_date' => $tour['start_date'],
                'end_date' => $tour['end_date'],
                'name' => $tour['tournament_name'] ?? 'Tournament',
            ];
        }

        // Sort by start date
        usort($events, function($a, $b) {
            return strtotime($a['start_date']) <=> strtotime($b['start_date']);
        });

        $attendanceModel = new Attendance();
        $attendanceRate = $attendanceModel->getOverallAttendanceRate($sportId);

        view('captain/index', [
            'username' => $username,
            'sport_name' => $sportName,
            'member_count' => $memberCount,
            'practice_sessions' => $practiceSessions,
            'session_count' => $sessionCount,
            'attendance_rate' => $attendanceRate,
            'upcoming_events' => $events
        ]);
    }

    /* ===================== ATTENDANCE ===================== */
    public function MarkAttendance() {
        view('captain/mark-attendance');
    }

    /* ===================== ADD MEMBERS ===================== */
    public function AddMembers() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /uoc-sports/public/sign-in");
            exit;
        }

        $userId = $_SESSION['user_id'];

        $sportModel = new Sport();
        $sportTeamModel = new SportTeam();
        $userModel = new User();
        
        require_once __DIR__ . '/../models/Tournament.php';
        require_once __DIR__ . '/../models/TournamentParticipant.php';
        $tournamentModel = new Tournament();
        $tpModel = new TournamentParticipant();

        $sport = $sportModel->getSportByCaptain($userId);
        $sportId = $sport['sport_id'] ?? null;
        
        $selectedTournamentId = $_GET['tournament_id'] ?? null;

        /* ===== HANDLE ACTIONS ===== */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tid = $_POST['tournament_id'] ?? null;
            
            if (!$tid) {
                header("Location: /uoc-sports/public/captain/add-members");
                exit;
            }

            if (isset($_POST['add_member_id'])) {
                $tpModel->addParticipants($tid, [$_POST['add_member_id']], $userId);
            }

            if (isset($_POST['remove_member_id'])) {
                $tpModel->removeParticipant($tid, $_POST['remove_member_id']);
            }

            header("Location: /uoc-sports/public/captain/add-members?tournament_id=" . $tid);
            exit;
        }

        /* ===== DATA ===== */
        // Only active tournaments (Eradicating completed ones)
        $allTournaments = $tournamentModel->getTournamentsBySportId($sportId);
        $activeTournaments = array_filter($allTournaments, function($t) {
            return ($t['status'] ?? '') !== 'COMPLETED';
        });
        
        $team_members = [];
        $available_members = [];
        $is_tournament_mode = false;
        $current_tournament = null;

        if ($selectedTournamentId) {
            // Manage Tournament Squad (Team Card)
            $squad = $tpModel->getParticipants($selectedTournamentId);
            $team_members = array_map(function($p) {
                return [
                    'user_id' => $p['user_id'],
                    'fname' => $p['first_name'],
                    'lname' => $p['last_name'],
                    'student_id' => $p['student_id'],
                    'faculty_name' => ''
                ];
            }, $squad);
            
            // Available members: All students enrolled in this sport but not in this squad
            $all_students = $userModel->getAllEnrolledStudents($sportId);
            $squadUserIds = array_column($team_members, 'user_id');
            
            $available_members = array_filter($all_students, function($m) use ($squadUserIds) {
                return !in_array($m['user_id'], $squadUserIds);
            });
            
            $is_tournament_mode = true;
            $current_tournament = $tournamentModel->getTournamentById($selectedTournamentId);
        } else {
            // No tournament selected -> Available members is the full pool for selection UI
            $available_members = $userModel->getAllEnrolledStudents($sportId);
        }

        view('captain/add-members', [
            'team_members' => $team_members,
            'available_members' => $available_members,
            'available_total' => count($available_members),
            'selected_total' => count($team_members),
            'available_slots' => 20 - count($team_members),
            'tournaments' => array_values($activeTournaments),
            'selected_tournament_id' => $selectedTournamentId,
            'is_tournament_mode' => $is_tournament_mode,
            'current_tournament_name' => $current_tournament['tournament_name'] ?? '',
            'sport_name' => $sport['sport_name'] ?? ''
        ]);
    }

    /* ===================== SCHEDULE PRACTICE ===================== */
    public function SchedulePractice() {

        if (!isset($_SESSION['user_id'])) {
            header("Location: /uoc-sports/public/login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $pdo = Database::getConnection();
        $scheduleModel = new Schedule();
        
        require_once __DIR__ . '/../models/Facility.php';
        $facilityModel = new Facility();
        $locations = $facilityModel->getPhysicalFacilities();

        // Get sport ID
        if (!isset($_SESSION['captain_sport_id'])) {
            $stmt = $pdo->prepare("SELECT sport_id FROM sport WHERE captain_id = ?");
            $stmt->execute([$userId]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$res) die("No sport assigned");

            $_SESSION['captain_sport_id'] = $res['sport_id'];
        }

        $sportId = $_SESSION['captain_sport_id'];

        // Get sport name
        $stmt = $pdo->prepare("SELECT sport_name FROM sport WHERE sport_id = ?");
        $stmt->execute([$sportId]);
        $sportName = $stmt->fetchColumn();

        /* ===== CREATE ===== */
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {

            $date = $_POST['date'];
            $start = $_POST['start_time'];
            $end = $_POST['end_time'];

            // Validate 30-minute increments
            $start_min = date('i', strtotime($start));
            $end_min = date('i', strtotime($end));
            if ($start_min % 30 !== 0 || $end_min % 30 !== 0) {
                $_SESSION['error'] = "Invalid time format! Practice sessions must start and end at 30-minute intervals (e.g., :00 or :30).";
                header("Location: /uoc-sports/public/captain/schedule-practice");
                exit;
            }

        // Check conflict
        if ($scheduleModel->hasTimeConflict($sportId, $date, $start, $end)) {
         $_SESSION['error'] = "Time conflict! Keep at least 10 minutes gap between sessions.";
        header("Location: /uoc-sports/public/captain/schedule-practice");
        exit;
}

            $scheduleModel->create(
                $sportName,
                $_POST['date'],
                $_POST['start_time'],
                $_POST['end_time'],
                $_POST['need_equipment'],
                $_POST['location'],
                $_POST['notes'],
                $sportId
            );

            header("Location: /uoc-sports/public/captain/schedule-practice");
            exit;
        }

        /* ===== UPDATE ===== */
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        $date = $_POST['date'];
        $start = $_POST['start_time'];
        $end = $_POST['end_time'];
        $id = $_POST['id'];

        // Validate 30-minute increments
        $start_min = date('i', strtotime($start));
        $end_min = date('i', strtotime($end));
        if ($start_min % 30 !== 0 || $end_min % 30 !== 0) {
            $_SESSION['error'] = "Invalid time format! Practice sessions must start and end at 30-minute intervals (e.g., :00 or :30).";
            header("Location: /uoc-sports/public/captain/schedule-practice");
            exit;
        }

        // Check conflict (exclude current session)
        if ($scheduleModel->hasTimeConflict($sportId, $date, $start, $end, $id)) {
        $_SESSION['error'] = "Time conflict! Keep at least 10 minutes gap between sessions.";
        header("Location: /uoc-sports/public/captain/schedule-practice");
        exit;
}
            $scheduleModel->update(
                $_POST['id'],
                $sportName,
                $_POST['date'],
                $_POST['start_time'],
                $_POST['end_time'],
                $_POST['need_equipment'],
                $_POST['location'],
                $_POST['notes']
            );

            header("Location: /uoc-sports/public/captain/schedule-practice");
            exit;
        }

        /* ===== DELETE ===== */
        if (isset($_GET['delete'])) {
            $scheduleModel->delete($_GET['delete']);
            header("Location: /uoc-sports/public/captain/schedule-practice");
            exit;
        }

        $schedules = $scheduleModel->getAll($sportId);

        view('captain/schedule-practice', [
            'schedules' => $schedules,
            'sport_name' => $sportName,
            'locations' => $locations
        ]);
    }

    /* ===================== COMMUNICATION ===================== */
    public function Communication() {
        view('captain/communication');
    }

    /* ===================== ACHIEVEMENTS ===================== */
    public function Achievements() {

        if (!isset($_SESSION['user_id'])) {
            header("Location: /uoc-sports/public/sign-in");
            exit;
        }

        $userId = $_SESSION['user_id'];

        $sportModel = new Sport();
        $sport = $sportModel->getSportByCaptain($userId);
        $sportId = $sport['sport_id'] ?? null;

        require_once __DIR__ . '/../models/Achievements.php';
        $achievementsModel = new Achievements();

        $teamAchievements = $achievementsModel->getTeamAchievements($sportId);

        $teamDetails = [];

        foreach ($teamAchievements as $teamAch) {
            $tid = $teamAch['tournament_id'];

            $teamDetails[$tid] = [
                'players' => $achievementsModel->getPlayersByCompetition($sportId, $tid),
                'individual_achievements' => $achievementsModel->getIndividualAchievementsByCompetition($tid)
            ];
        }

        view('captain/achievements', [
            'sport_name' => $sport['sport_name'] ?? '',
            'team_achievements' => $teamAchievements,
            'team_details' => $teamDetails
        ]);
    }

    /* ===================== ADD RESULT ===================== */
    public function AddResult() {

        if (!isset($_SESSION['user_id'])) {
            header("Location: /uoc-sports/public/sign-in");
            exit;
        }

        require_once __DIR__ . '/../models/EventResultPermission.php';

        $permModel = new EventResultPermission();

        $tournaments = $permModel->getActivePermissionsForCaptain($_SESSION['user_id']);

        view('captain/add-result', [
            'permitted_tournaments' => $tournaments
        ]);
    }
}