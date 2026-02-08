<?php

class SportCompetition {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Get all competitions with optional filters
     */
    public function getAll($filters = []) {
        $query = "SELECT 
                    c.competition_id,
                    c.competition_name,
                    c.sport_id,
                    s.sport_name,
                    c.participant_pdf,
                    c.participants,
                    c.date,
                    c.created_at
                  FROM competition c
                  LEFT JOIN sport s ON c.sport_id = s.sport_id
                  WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['sport_id'])) {
            $query .= " AND c.sport_id = ?";
            $params[] = $filters['sport_id'];
        }
        
        if (!empty($filters['competition_name'])) {
            $query .= " AND c.competition_name LIKE ?";
            $params[] = '%' . $filters['competition_name'] . '%';
        }
        
        $query .= " ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single competition by ID
     */
    public function getById($id) {
        $query = "SELECT 
                    c.*,
                    s.sport_name
                  FROM competition c
                  LEFT JOIN sport s ON c.sport_id = s.sport_id
                  WHERE c.competition_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new competition
     */
    public function create($data) {
        $query = "INSERT INTO competition 
                  (competition_name, sport_id, participant_pdf, participants)
                  VALUES (?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        $result = $stmt->execute([
            $data['competition_name'],
            $data['sport_id'],
            $data['participant_pdf'] ?? null,
            $data['participants'] ?? null
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Update a competition
     */
    public function update($id, $data) {
        $query = "UPDATE competition 
                  SET competition_name = ?,
                      sport_id = ?,
                      participant_pdf = ?,
                      participants = ?
                  WHERE competition_id = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['competition_name'],
            $data['sport_id'],
            $data['participant_pdf'] ?? null,
            $data['participants'] ?? null,
            $id
        ]);
    }

    /**
     * Delete a competition
     */
    public function delete($id) {
        // Get competition details to delete file if exists
        $competition = $this->getById($id);
        
        if ($competition && !empty($competition['participant_pdf'])) {
            $filePath = '../app/internal/Sport_competitions/' . $competition['participant_pdf'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        $query = "DELETE FROM competition WHERE competition_id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * Get sport_id from sport_name
     */
    public function getSportIdByName($sportName) {
        $query = "SELECT sport_id FROM sport WHERE LOWER(sport_name) = LOWER(?)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sportName]);
        return $stmt->fetchColumn();
    }

    /**
     * Get all sports for dropdown
     */
    public function getAllSports() {
        $query = "SELECT sport_id, sport_name FROM sport ORDER BY sport_name";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get students by sport
     */
    public function getStudentsBySport($sportId) {
        $query = "SELECT DISTINCT 
                    u.user_id, 
                    u.fname as first_name, 
                    u.lname as last_name, 
                    u.email, 
                    u.student_id
                  FROM user u
                  WHERE u.type = 'STUDENT' 
                    AND u.sport_id = ?
                    AND u.status = 'ACTIVE'
                  ORDER BY u.fname, u.lname";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sportId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get participants as array
     */
    public function getParticipantsArray($participants) {
        if (empty($participants)) {
            return [];
        }
        return explode(',', $participants);
    }

    /**
     * Get upcoming competitions for a sport manager
     */
    public function getUpcomingCompetitions($sportId = null, $limit = 5) {
        $currentMonth = date('m');
        return $this->getCompetitionsByMonth($sportId, $currentMonth, $limit);
    }
    
    /**
     * Get competitions for a specific month
     */
    public function getCompetitionsByMonth($sportId = null, $month = null, $limit = 10) {
        if (!$month) {
            $month = date('m');
        }
        
        $currentYear = date('Y');
        $today = date('Y-m-d');
        
        $query = "SELECT 
                    c.competition_id,
                    c.competition_name,
                    c.sport_id,
                    s.sport_name,
                    c.date,
                    c.created_at
                  FROM competition c
                  LEFT JOIN sport s ON c.sport_id = s.sport_id
                  WHERE (
                    (c.date IS NOT NULL AND c.date >= ?)
                    OR (c.date IS NULL AND YEAR(c.created_at) = ? AND MONTH(c.created_at) = ?)
                  )";
        
        $params = [$today, $currentYear, $month];
        
        if ($sportId) {
            $query .= " AND c.sport_id = ?";
            $params[] = $sportId;
        }
        
        // Optional: filter by month for dated competitions
        if ($month) {
            $query .= " AND (c.date IS NULL OR (YEAR(c.date) = ? AND MONTH(c.date) = ?))";
            $params[] = $currentYear;
            $params[] = $month;
        }
        
        $query .= " ORDER BY 
                    CASE WHEN c.date IS NULL THEN 1 ELSE 0 END,
                    c.date ASC,
                    c.created_at DESC 
                    LIMIT " . (int)$limit;
        
        error_log("========================================");
        error_log("getCompetitionsByMonth called");
        error_log("Query: " . $query);
        error_log("Params: " . json_encode($params));
        error_log("Sport ID filter: " . ($sportId ?? 'NULL - NO FILTER'));
        error_log("Today's date: " . $today);
        error_log("========================================");
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Query returned " . count($results) . " competitions");
        foreach ($results as $i => $comp) {
            error_log("Competition $i: ID={$comp['competition_id']}, Name={$comp['competition_name']}, Sport={$comp['sport_name']} (ID={$comp['sport_id']}), Date={$comp['date']}");
        }
        
        return $results;
    }
}
