<?php
class Sport {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection(); // Your PDO connection
    }

    /**
     * Get all tournaments with sport info
     */
    public function getTournaments() {
        $stmt = $this->db->query("
            SELECT t.tournament_id, t.tournament_name, t.sport_id, s.sport_name, s.sport_category
            FROM tournament t
            JOIN sport s ON t.sport_id = s.sport_id
            WHERE t.status = 'INCOMPLETE' 
            ORDER BY t.tournament_name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all sports with category
     */
    public function getSports() {
        $stmt = $this->db->query("SELECT sport_id, sport_name, sport_category FROM sport ORDER BY sport_name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get sport by ID with category
     */
    public function getSportById($sportId) {
        $stmt = $this->db->prepare("SELECT sport_id, sport_name, sport_category FROM sport WHERE sport_id = :sport_id");
        $stmt->execute(['sport_id' => $sportId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all students (to pick winners)
     */
    public function getStudents() {
        $stmt = $this->db->query("SELECT user_id, CONCAT(fname,' ',lname) AS name FROM user WHERE type='STUDENT' ORDER BY fname");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get form configuration for a sport
     * Uses the new sport-specific model classes
     */
    public function getFormConfig($sportId) {
        require_once __DIR__ . '/MatchResultFactory.php';
        
        $sport = $this->getSportById($sportId);
        if (!$sport) return null;
        
        $model = MatchResultFactory::getModel($sport['sport_category']);
        return $model->getFormConfig($sportId);
    }

    /**
     * Get sport by captain user id
     */
    public function getSportByCaptain($captainId) {
        $stmt = $this->db->prepare("SELECT sport_id, sport_name FROM sport WHERE captain_id = :captain_id LIMIT 1");
        $stmt->execute(['captain_id' => $captainId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Add a match result using sport-specific tables
     * 
     * @param array $matchData Common match data (tournament_id, sport_id, match_name, match_date, winner_id, result_status)
     * @param array $details Sport-specific details
     * @return string|false The match_id or false on failure
     */
    public function addMatchResult(array $matchData, array $details = []) {
        require_once __DIR__ . '/MatchResultFactory.php';
        require_once __DIR__ . '/BaseMatchModel.php';
        
        // Get sport category
        $sport = $this->getSportById($matchData['sport_id']);
        if (!$sport) return false;
        
        $matchData['sport_category'] = $sport['sport_category'];
        
        try {
            $this->db->beginTransaction();
            
            // Get the appropriate model for this sport
            $model = MatchResultFactory::getModel($sport['sport_category']);
            
            // Create the central match record
            $matchId = $model->createMatch($matchData);
            
            // Add sport-specific details
            if (!empty($details)) {
                $model->addDetails($matchId, $details);
            }
            
            $this->db->commit();
            return $matchId;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error adding match result: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add a participant to a match
     */
    public function addMatchParticipant($matchId, $userId, $team = 'A', $score = null, $performanceData = [], $notes = null) {
        $stmt = $this->db->prepare("
            INSERT INTO match_participant (match_id, user_id, team, score, performance_data, notes)
            VALUES (:match_id, :user_id, :team, :score, :performance_data, :notes)
        ");
        
        return $stmt->execute([
            'match_id' => $matchId,
            'user_id' => $userId,
            'team' => $team,
            'score' => $score,
            'performance_data' => json_encode($performanceData),
            'notes' => $notes
        ]);
    }

    /**
     * Get player match history
     * Updated for sport-specific tables schema
     */
    public function getPlayerMatchHistory($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                tm.match_id,
                tm.match_name,
                tm.match_date,
                tm.result_status,
                tm.sport_category,
                t.tournament_name,
                s.sport_name,
                mp.team,
                mp.score AS player_score,
                mp.performance_data,
                mp.notes,
                CASE WHEN tm.winner_id = :user_id THEN 'WON' ELSE 'PARTICIPATED' END AS outcome
            FROM match_participant mp
            JOIN tournament_match tm ON mp.match_id = tm.match_id
            JOIN tournament t ON tm.tournament_id = t.tournament_id
            JOIN sport s ON tm.sport_id = s.sport_id
            WHERE mp.user_id = :user_id2
            ORDER BY tm.match_date DESC
        ");
        
        $stmt->execute(['user_id' => $userId, 'user_id2' => $userId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Decode JSON fields and optionally fetch sport-specific details
        foreach ($results as &$row) {
            $row['performance_data'] = json_decode($row['performance_data'], true);
            
            // Fetch sport-specific details if needed
            $row['match_details'] = $this->getSportSpecificDetails($row['match_id'], $row['sport_category']);
        }
        
        return $results;
    }
    
    /**
     * Get sport-specific match details from the appropriate detail table
     */
    public function getSportSpecificDetails($matchId, $sportCategory) {
        require_once __DIR__ . '/MatchResultFactory.php';
        
        try {
            $model = MatchResultFactory::getModel($sportCategory);
            return $model->getDetails($matchId);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Search matches
     */
    public function searchMatches($query = '', $sportId = '', $tournamentId = '', $limit = 20) {
        $sql = "
            SELECT 
                tm.match_id,
                tm.match_name,
                tm.match_date,
                tm.result_status,
                tm.sport_category,
                t.tournament_name,
                s.sport_name,
                CONCAT(w.fname, ' ', w.lname) AS winner_name
            FROM tournament_match tm
            JOIN tournament t ON tm.tournament_id = t.tournament_id
            JOIN sport s ON tm.sport_id = s.sport_id
            LEFT JOIN user w ON tm.winner_id = w.user_id
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($query)) {
            $sql .= " AND (tm.match_name LIKE :query OR t.tournament_name LIKE :query2)";
            $params['query'] = "%$query%";
            $params['query2'] = "%$query%";
        }
        
        if (!empty($sportId)) {
            $sql .= " AND tm.sport_id = :sport_id";
            $params['sport_id'] = $sportId;
        }
        
        if (!empty($tournamentId)) {
            $sql .= " AND tm.tournament_id = :tournament_id";
            $params['tournament_id'] = $tournamentId;
        }
        
        $sql .= " ORDER BY tm.match_date DESC LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get match details with participants
     * Updated for sport-specific tables schema
     */
    public function getMatchDetails($matchId) {
        // Get match info
        $stmt = $this->db->prepare("
            SELECT 
                tm.*,
                t.tournament_name,
                s.sport_name,
                CONCAT(w.fname, ' ', w.lname) AS winner_name
            FROM tournament_match tm
            JOIN tournament t ON tm.tournament_id = t.tournament_id
            JOIN sport s ON tm.sport_id = s.sport_id
            LEFT JOIN user w ON tm.winner_id = w.user_id
            WHERE tm.match_id = :match_id
        ");
        $stmt->execute(['match_id' => $matchId]);
        $match = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$match) return null;
        
        // Fetch sport-specific details from the appropriate table
        $match['details'] = $this->getSportSpecificDetails($matchId, $match['sport_category']);
        
        // Get participants
        $stmt2 = $this->db->prepare("
            SELECT 
                mp.*,
                CONCAT(u.fname, ' ', u.lname) AS player_name,
                u.student_id
            FROM match_participant mp
            JOIN user u ON mp.user_id = u.user_id
            WHERE mp.match_id = :match_id
            ORDER BY mp.team, mp.score DESC
        ");
        $stmt2->execute(['match_id' => $matchId]);
        $participants = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($participants as &$p) {
            $p['performance_data'] = json_decode($p['performance_data'], true);
        }
        
        $match['participants'] = $participants;
        
        return $match;
    }

    /**
     * Get sport with staff details (coach, captain, manager)
     * @param string $sportId
     * @return array|null
     */
    public function getSportWithStaff($sportId) {
        $sql = "
            SELECT 
                s.sport_id,
                s.sport_name,
                s.coach_id,
                s.captain_id,
                s.manager_id,
                coach.fname AS coach_fname,
                coach.lname AS coach_lname,
                coach.email AS coach_email,
                coach.contact_no AS coach_contact,
                captain.fname AS captain_fname,
                captain.lname AS captain_lname,
                captain.email AS captain_email,
                captain.contact_no AS captain_contact,
                manager.fname AS manager_fname,
                manager.lname AS manager_lname,
                manager.email AS manager_email,
                manager.contact_no AS manager_contact
            FROM sport s
            LEFT JOIN user coach ON s.coach_id = coach.user_id
            LEFT JOIN user captain ON s.captain_id = captain.user_id
            LEFT JOIN user manager ON s.manager_id = manager.user_id
            WHERE s.sport_id = :sport_id
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['sport_id' => $sportId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
