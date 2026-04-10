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

        require_once __DIR__ . '/../models/SportCompetition.php';
        require_once __DIR__ . '/../models/Tournament.php';

        $competitionModel = new SportCompetition();
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
        $upcomingCompetitions = $competitionModel->getCompetitionsByMonth($sportId, date('m'), 5);

        $today = date('Y-m-d');
        $allTournaments = $tournamentModel->getTournamentsBySportId($sportId);

        $upcomingTournaments = array_filter($allTournaments, function($t) use ($today) {
            return isset($t['end_date']) && $t['end_date'] >= $today;
        });

        $events = [];

        foreach ($upcomingCompetitions as $comp) {
            $events[] = [
                'date' => $comp['date'] ?? $comp['created_at'],
                'time' => isset($comp['date']) ? date('H:i', strtotime($comp['date'])) : '',
                'name' => $comp['competition_name'] ?? 'Competition',
            ];
        }

        foreach ($upcomingTournaments as $tour) {
            $events[] = [
                'start_date' => $tour['start_date'],
                'end_date' => $tour['end_date'],
                'name' => $tour['tournament_name'] ?? 'Tournament',
            ];
        }

        usort($events, function($a, $b) {
            return strtotime($a['date']) <=> strtotime($b['date']);
        });

        view('captain/index', [
            'username' => $username,
            'sport_name' => $sportName,
            'member_count' => $memberCount,
            'practice_sessions' => $practiceSessions,
            'session_count' => $sessionCount,
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

        $sport = $sportModel->getSportByCaptain($userId);
        $sportId = $sport['sport_id'] ?? null;

        /* ===== HANDLE ACTIONS ===== */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['add_member_id'])) {
                $sportTeamModel->addMember($sportId, $_POST['add_member_id']);
            }

            if (isset($_POST['remove_member_id'])) {
                $sportTeamModel->removeMember($sportId, $_POST['remove_member_id']);
            }

            header("Location: /uoc-sports/public/captain/add-members");
            exit;
        }

        /* ===== DATA ===== */
        $team_members = $sportTeamModel->getTeamMembers($sportId);
        $team_ids = array_column($team_members, 'user_id');

        $all_students = $userModel->getEligibleStudents($sportId);

        $available_members = array_filter($all_students, function($s) use ($team_ids) {
            return !in_array($s['user_id'], $team_ids);
        });

        view('captain/add-members', [
            'team_members' => $team_members,
            'available_members' => $available_members,
            'available_total' => count($available_members),
            'selected_total' => count($team_members),
            'available_slots' => 15 - count($team_members)
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
            'sport_name' => $sportName
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
            $cid = $teamAch['competition_id'];

            $teamDetails[$cid] = [
                'players' => $achievementsModel->getPlayersByCompetition($sportId, $cid),
                'individual_achievements' => $achievementsModel->getIndividualAchievementsByCompetition($cid)
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