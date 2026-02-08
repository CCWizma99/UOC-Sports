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
        
        // Get all competitions for the sport
        $competitionsQuery = "SELECT competition_id, competition_name, date 
                              FROM competition";
        $competitionParams = [];
        
        if ($sportId) {
            $competitionsQuery .= " WHERE sport_id = ?";
            $competitionParams[] = $sportId;
        }
        
        $competitionsQuery .= " ORDER BY date DESC";
        $stmt = $db->prepare($competitionsQuery);
        $stmt->execute($competitionParams);
        $competitions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        view('sports-manager/add-achievement', [
            'students' => $students,
            'competitions' => $competitions,
            'selectedSport' => $sportId
        ]);
    }
    
    /**
     * Store new achievement
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /uoc-sports/public/sport-manager/team');
            exit;
        }
        
        $achievementModel = new SportAchievements();
        
        $data = [
            'user_id' => $_POST['user_id'] ?? null,
            'sport_id' => $_POST['sport_id'] ?? null,
            'competition_id' => $_POST['competition_id'] ?? null,
            'achievement' => $_POST['achievement'] ?? null
        ];
        
        // Validate required fields
        if (!$data['user_id'] || !$data['sport_id'] || !$data['competition_id'] || !$data['achievement']) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: /uoc-sports/public/sport-manager/team/create?sport=' . $data['sport_id']);
            exit;
        }
        
        try {
            $achievementModel->create($data);
            $_SESSION['success'] = 'Achievement added successfully! Points have been automatically assigned.';
            header('Location: /uoc-sports/public/sport-manager/team?sport=' . $data['sport_id']);
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error adding achievement: ' . $e->getMessage();
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
            $_SESSION['error'] = 'Invalid achievement ID';
            header('Location: /uoc-sports/public/sport-manager/team?sport=' . $sportId);
            exit;
        }
        
        $achievementModel = new SportAchievements();
        
        try {
            $achievementModel->delete($id);
            $_SESSION['success'] = 'Achievement deleted successfully!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error deleting achievement: ' . $e->getMessage();
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
