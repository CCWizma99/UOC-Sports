<?php

class SportAchievementsController {
    
    /**
     * Display all achievements for a sport
     */
    public function index() {
        $sportId = $_GET['sport'] ?? null;
        $achievementModel = new SportAchievements();
        
        // Get achievements
        $achievements = $sportId ? 
            $achievementModel->getBySport($sportId) : 
            $achievementModel->getAll();
        
        // Get student rankings
        $rankings = $achievementModel->getStudentRankings($sportId, 10);
        
        // Get statistics
        $stats = $sportId ? $achievementModel->getStatsBySport($sportId) : null;
        
        view('sports-manager/team', [
            'achievements' => $achievements,
            'rankings' => $rankings,
            'stats' => $stats,
            'selectedSport' => $sportId
        ]);
    }
    
    /**
     * Show create achievement form
     */
    public function create() {
        $sportId = $_GET['sport'] ?? null;
        
        // Get all students for the sport
        $db = Database::getConnection();
        $studentsQuery = "SELECT user_id, fname, lname, email 
                          FROM user 
                          WHERE type = 'STUDENT'";
        
        $params = [];
        if ($sportId) {
            $studentsQuery .= " AND sport_id = ?";
            $params[] = $sportId;
        }
        
        $studentsQuery .= " ORDER BY fname, lname";
        $stmt = $db->prepare($studentsQuery);
        $stmt->execute($params);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get all tournaments for the sport
        $tournamentsQuery = "SELECT tournament_id, tournament_name, start_date as date 
                              FROM tournament";
        $tournamentParams = [];
        if ($sportId) {
            $tournamentsQuery .= " WHERE sport_id = ?";
            $tournamentParams[] = $sportId;
        }
        $tournamentsQuery .= " ORDER BY start_date DESC";
        $stmt = $db->prepare($tournamentsQuery);
        $stmt->execute($tournamentParams);
        $tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        view('sports-manager/achievements', [
            'achievements' => $achievements,
            'students' => $students,
            'tournaments' => $tournaments,
            'sport_id' => $sportId,
            'student_id' => $_POST['user_id'] ?? null,
            'tournament_id' => $_POST['tournament_id'] ?? null,
            'selected_sport' => $sportId
        ]);
    }
    
    /**
     * Store new achievement
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /uoc-sports/public/sport-manager/achievements");
            exit;
        }

        $achModel = new SportAchievements();
        $data = [
            'user_id' => $_POST['user_id'],
            'sport_id' => $_POST['sport_id'],
            'tournament_id' => $_POST['tournament_id'],
            'achievement' => $_POST['achievement']
        ];

        if (!$data['user_id'] || !$data['sport_id'] || !$data['tournament_id'] || !$data['achievement']) {
            $_SESSION['message'] = 'All fields are required';
            $_SESSION['color'] = 'red';
            header('Location: /uoc-sports/public/sport-manager/team/create?sport=' . $data['sport_id']);
            exit;
        }
        
        try {
            $achModel->create($data); // Note: Fix variable name achModel vs achievementModel if needed, but in the snippet it was achievementModel on line 102
            $_SESSION['message'] = 'Achievement added successfully! Points have been automatically assigned.';
            $_SESSION['color'] = 'green';
            header('Location: /uoc-sports/public/sport-manager/team?sport=' . $data['sport_id']);
        } catch (Exception $e) {
            $_SESSION['message'] = 'Error adding achievement: ' . $e->getMessage();
            $_SESSION['color'] = 'red';
            header('Location: /uoc-sports/public/sport-manager/team/create?sport=' . $data['sport_id']);
        }
        exit;
    }
    
    /**
     * Delete achievement
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /uoc-sports/public/sport-manager/team');
            exit;
        }
        
        $id = $_POST['achievement_id'] ?? null;
        $sportId = $_POST['sport_id'] ?? null;
        
        if (!$id) {
            $_SESSION['message'] = 'Invalid achievement ID';
            $_SESSION['color'] = 'red';
            header('Location: /uoc-sports/public/sport-manager/team?sport=' . $sportId);
            exit;
        }
        
        $achievementModel = new SportAchievements();
        
        try {
            $achievementModel->delete($id);
            $_SESSION['message'] = 'Achievement deleted successfully!';
            $_SESSION['color'] = 'green';
        } catch (Exception $e) {
            $_SESSION['message'] = 'Error deleting achievement: ' . $e->getMessage();
            $_SESSION['color'] = 'red';
        }
        
        header('Location: /uoc-sports/public/sport-manager/team?sport=' . $sportId);
        exit;
    }
    
    /**
     * Get student rankings (for AJAX)
     */
    public function getRankings() {
        header('Content-Type: application/json');
        
        $sportId = $_GET['sport_id'] ?? null;
        $limit = $_GET['limit'] ?? 10;
        
        $achievementModel = new SportAchievements();
        $rankings = $achievementModel->getStudentRankings($sportId, $limit);
        
        echo json_encode([
            'success' => true,
            'rankings' => $rankings
        ]);
    }
    
    /**
     * Get student details with achievements
     */
    public function getStudentDetails() {
        header('Content-Type: application/json');
        
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'User ID required']);
            exit;
        }
        
        $achievementModel = new SportAchievements();
        $achievements = $achievementModel->getByStudent($userId);
        $totalPoints = $achievementModel->getStudentPoints($userId);
        
        echo json_encode([
            'success' => true,
            'achievements' => $achievements,
            'total_points' => $totalPoints
        ]);
    }
}
