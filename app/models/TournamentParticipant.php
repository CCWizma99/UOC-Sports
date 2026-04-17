<?php

class TournamentParticipant {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Get all tournaments with optional filters
     */
    public function getAll($filters = []) {
        $query = "SELECT 
                    t.tournament_id,
                    t.tournament_name,
                    t.sport_id,
                    s.sport_name,
                    t.start_date as date,
                    t.status
                  FROM tournament t
                  LEFT JOIN sport s ON t.sport_id = s.sport_id
                  WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['sport_id'])) {
            $query .= " AND t.sport_id = ?";
            $params[] = $filters['sport_id'];
        }
        
        if (!empty($filters['status'])) {
            $query .= " AND t.status = ?";
            $params[] = $filters['status'];
        }
        
        $query .= " ORDER BY t.start_date DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single tournament by ID
     */
    public function getById($id) {
        $query = "SELECT 
                    t.*,
                    t.start_date as date,
                    s.sport_name
                  FROM tournament t
                  LEFT JOIN sport s ON t.sport_id = s.sport_id
                  WHERE t.tournament_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get participants for a tournament
     */
    public function getParticipants($tournamentId) {
        $query = "SELECT 
                    tp.user_id,
                    u.fname as first_name,
                    u.lname as last_name,
                    u.student_id,
                    tp.added_at
                  FROM tournament_participants tp
                  JOIN user u ON tp.user_id = u.user_id
                  WHERE tp.tournament_id = ? AND tp.status = 'ACTIVE'
                  ORDER BY u.fname, u.lname";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$tournamentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add multiple participants to a tournament
     */
    public function addParticipants($tournamentId, $userIds, $addedBy) {
        if (empty($userIds)) return true;

        $this->db->beginTransaction();
        try {
            $query = "INSERT INTO tournament_participants (tournament_id, user_id, added_by) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($query);

            foreach ($userIds as $userId) {
                // Ignore duplicates if they somehow bypass the UI
                try {
                    $stmt->execute([$tournamentId, $userId, $addedBy]);
                } catch (PDOException $e) {
                    if ($e->getCode() != 23000) { // 23000 is Integrity constraint violation (duplicate key)
                        throw $e;
                    }
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error adding tournament participants: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync participants for a tournament (remove missing, add new)
     */
    public function syncParticipants($tournamentId, $selectedUserIds, $addedBy) {
        $this->db->beginTransaction();
        try {
            // 1. Mark participants not in the new list as INACTIVE (Soft Delete)
            if (empty($selectedUserIds)) {
                $query = "UPDATE tournament_participants SET status = 'INACTIVE' WHERE tournament_id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$tournamentId]);
            } else {
                $placeholders = implode(',', array_fill(0, count($selectedUserIds), '?'));
                $query = "UPDATE tournament_participants SET status = 'INACTIVE' WHERE tournament_id = ? AND user_id NOT IN ($placeholders)";
                $stmt = $this->db->prepare($query);
                $params = array_merge([$tournamentId], $selectedUserIds);
                $stmt->execute($params);
            }

            // 2. Add new ones or re-activate existing ones
            if (!empty($selectedUserIds)) {
                foreach ($selectedUserIds as $userId) {
                    // Check if record exists
                    $checkSql = "SELECT id FROM tournament_participants WHERE tournament_id = ? AND user_id = ?";
                    $checkStmt = $this->db->prepare($checkSql);
                    $checkStmt->execute([$tournamentId, $userId]);
                    
                    if ($checkStmt->rowCount() > 0) {
                        // Reactivate if exists
                        $updateSql = "UPDATE tournament_participants SET status = 'ACTIVE', added_by = ? WHERE tournament_id = ? AND user_id = ?";
                        $this->db->prepare($updateSql)->execute([$addedBy, $tournamentId, $userId]);
                    } else {
                        // Insert new
                        $insertSql = "INSERT INTO tournament_participants (tournament_id, user_id, added_by, status) VALUES (?, ?, ?, 'ACTIVE')";
                        $this->db->prepare($insertSql)->execute([$tournamentId, $userId, $addedBy]);
                    }
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error syncing tournament participants: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get sport_id from sport_name (Migration helper)
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
     * Get upcoming tournaments for a sport manager
     */
    public function getUpcomingTournaments($sportId = null, $limit = 5) {
        $today = date('Y-m-d');
        
        $query = "SELECT 
                    t.tournament_id,
                    t.tournament_name,
                    t.sport_id,
                    s.sport_name,
                    t.start_date as date,
                    t.status
                  FROM tournament t
                  LEFT JOIN sport s ON t.sport_id = s.sport_id
                  WHERE t.start_date >= ?";
        
        $params = [$today];
        
        if ($sportId) {
            $query .= " AND t.sport_id = ?";
            $params[] = $sportId;
        }
        
        $query .= " ORDER BY t.start_date ASC LIMIT " . (int)$limit;
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get tournaments by month for a sport (Compatibility method)
     */
    public function getTournamentsByMonth($sportId, $month, $limit = 5) {
        $year = date('Y'); // Default to current year
        
        $query = "SELECT 
                    t.tournament_id,
                    t.tournament_name,
                    t.start_date as date,
                    t.status
                  FROM tournament t
                  WHERE t.sport_id = ? 
                    AND MONTH(t.start_date) = ? 
                    AND YEAR(t.start_date) = ?
                  ORDER BY t.start_date ASC
                  LIMIT " . (int)$limit;
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sportId, $month, $year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
