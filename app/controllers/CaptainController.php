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
        require_once __DIR__ . '/../models/SportCompetition.php';
        require_once __DIR__ . '/../models/Tournament.php';
        $competitionModel = new SportCompetition();
        $tournamentModel = new Tournament();

        // Get user display name
        $profile = $userModel->getUserProfile($userId);
        $username = isset($profile['full_name']) ? $profile['full_name'] : (trim(($profile['fname'] ?? '') . ' ' . ($profile['lname'] ?? '')) ?: 'Captain');

        // Get sport assigned to this captain (if any)
        $sport = $sportModel->getSportByCaptain($userId);
        $sportName = $sport && isset($sport['sport_name']) ? $sport['sport_name'] : null;
        $sportId = $sport['sport_id'] ?? null;

        $practiceSessions = $scheduleModel->getSessionsBySport($sportId);
        $memberCount = $sportTeamModel->getTeamMemberCount($sportId);
        $sessionCount = $scheduleModel->getSessionCountById($sportId);

        // Fetch upcoming competitions (next 30 days)
        $upcomingCompetitions = $competitionModel->getCompetitionsByMonth($sportId, date('m'), 5);
        // Fetch upcoming tournaments (end_date >= today)
        $today = date('Y-m-d');
        $allTournaments = $tournamentModel->getAllTournaments();
        $upcomingTournaments = array_filter($allTournaments, function($t) use ($today) {
            return isset($t['end_date']) && $t['end_date'] >= $today;
        });

        // Merge and sort by date
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
                'date' => $tour['start_date'],
                'time' => isset($tour['start_date']) ? date('H:i', strtotime($tour['start_date'])) : '',
                'name' => $tour['tournament_name'] ?? 'Tournament',
            ];
        }
        // Sort events by date
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
    public function MarkAttendance() {
        view('captain/mark-attendance');
    }
    public function AddMembers() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /uoc-sports/public/sign-in");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $sportModel = new Sport();
        $sportTeamModel = new SportTeam();
        $userModel = new User();

        // Get sport assigned to this captain
        $sport = $sportModel->getSportByCaptain($userId);
        $sportId = $sport['sport_id'] ?? null;

        // Handle add/remove actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['add_member_id'])) {
                $addId = $_POST['add_member_id'];
                $sportTeamModel->addMember($sportId, $addId);
            }
            if (isset($_POST['remove_member_id'])) {
                $removeId = $_POST['remove_member_id'];
                $sportTeamModel->removeMember($sportId, $removeId);
            }
            // Redirect to avoid form resubmission
            header("Location: /uoc-sports/public/captain/add-members");
            exit;
        }

        // Get current team members
        $team_members = $sportTeamModel->getTeamMembers($sportId);
        $team_member_ids = array_column($team_members, 'user_id');

        // Get all eligible students for this sport (not in team)
        $all_students = $userModel->getEligibleStudents($sportId); // You may need to implement this method
        $available_members = array_filter($all_students, function($student) use ($team_member_ids) {
            return !in_array($student['user_id'], $team_member_ids);
        });

        // Stats
        $available_total = count($available_members);
        $selected_total = count($team_members);
        $available_slots = 15 - $selected_total;

        view('captain/add-members', [
            'team_members' => $team_members,
            'available_members' => $available_members,
            'available_total' => $available_total,
            'selected_total' => $selected_total,
            'available_slots' => $available_slots
        ]);
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

 public function Communication() {
        view('captain/communication');
    }

    public function Achievements() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /uoc-sports/public/sign-in");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $sportModel = new Sport();
        $sport = $sportModel->getSportByCaptain($userId);
        $sportId = $sport['sport_id'] ?? null;
        $sportName = $sport['sport_name'] ?? '';

        require_once __DIR__ . '/../models/Achievements.php';
        $achievementsModel = new Achievements();
        $teamAchievements = $achievementsModel->getTeamAchievements($sportId);

        // For each team achievement, get players and their individual achievements for that competition
        $teamDetails = [];
        foreach ($teamAchievements as $teamAch) {
            $competitionId = $teamAch['competition_id'];
            $players = $achievementsModel->getPlayersByCompetition($sportId, $competitionId);
            $individuals = $achievementsModel->getIndividualAchievementsByCompetition($competitionId);
            $teamDetails[$competitionId] = [
                'players' => $players,
                'individual_achievements' => $individuals
            ];
        }

        view('captain/achievements', [
            'sport_name' => $sportName,
            'team_achievements' => $teamAchievements,
            'team_details' => $teamDetails
        ]);
    }
}