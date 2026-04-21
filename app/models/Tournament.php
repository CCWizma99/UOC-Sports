<?php

class Tournament {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Create a new tournament
     */
    public function createTournament($name, $sportId, $startDate, $endDate, $matchLevel = 'UNIVERSITY') {
        try {
            // Ensure match_level column exists
            try {
                $this->db->exec("ALTER TABLE tournament ADD COLUMN `match_level` ENUM('UNIVERSITY','NATIONAL','INTERNATIONAL') NOT NULL DEFAULT 'UNIVERSITY' AFTER `status`");
            } catch (PDOException $e) {
                // Column already exists, ignore
            }

            $tournamentId = 'TOUR_' . uniqid();
            
            $sql = "INSERT INTO tournament (tournament_id, tournament_name, sport_id, start_date, end_date, status, match_level) 
                    VALUES (:tournament_id, :tournament_name, :sport_id, :start_date, :end_date, 'INCOMPLETE', :match_level)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'tournament_id' => $tournamentId,
                'tournament_name' => $name,
                'sport_id' => $sportId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'match_level' => $matchLevel
            ]);
            
            return $tournamentId;
        } catch (PDOException $e) {
            error_log("Tournament creation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all saved email recipients
     */
    public function getSavedEmails() {
        try {
            $sql = "SELECT email, recepient_name FROM saved_emails ORDER BY recepient_name";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get saved emails error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Save a new email recipient
     */
    public function saveEmail($email, $recipientName) {
        try {
            // Check if email already exists
            if ($this->emailExists($email)) {
                return false;
            }

            $sql = "INSERT INTO saved_emails (email, recepient_name) VALUES (:email, :recepient_name)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'email' => $email,
                'recepient_name' => $recipientName
            ]);
            
            return true;
        } catch (PDOException $e) {
            error_log("Save email error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if email already exists in saved emails
     */
    public function emailExists($email) {
        try {
            $sql = "SELECT COUNT(*) FROM saved_emails WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['email' => $email]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Email exists check error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a saved email recipient
     */
    public function deleteEmail($email) {
        try {
            $sql = "DELETE FROM saved_emails WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['email' => $email]);
            return true;
        } catch (PDOException $e) {
            error_log("Delete email error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get tournament by ID
     */
    public function getTournamentById($tournamentId) {
        try {
            $sql = "SELECT t.*, s.sport_name 
                    FROM tournament t 
                    LEFT JOIN sport s ON t.sport_id = s.sport_id 
                    WHERE t.tournament_id = :tournament_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['tournament_id' => $tournamentId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get tournament error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all active tournaments (not completed)
     */
    public function getAllTournaments() {
        try {
            $sql = "SELECT t.*, s.sport_name 
                    FROM tournament t 
                    LEFT JOIN sport s ON t.sport_id = s.sport_id 
                    WHERE t.status != 'COMPLETE'
                    ORDER BY t.start_date DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all tournaments error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get past tournaments with pagination and search
     */
    public function getPastTournaments($limit, $offset, $search = '') {
        try {
            $sql = "SELECT t.*, s.sport_name 
                    FROM tournament t 
                    LEFT JOIN sport s ON t.sport_id = s.sport_id 
                    WHERE t.status = 'COMPLETE'";
            
            if ($search) {
                $sql .= " AND (t.tournament_name LIKE :search OR s.sport_name LIKE :search)";
            }
            
            $sql .= " ORDER BY t.end_date DESC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            if ($search) {
                $stmt->bindValue(':search', '%' . $search . '%');
            }
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get past tournaments error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get total count of past tournaments for pagination
     */
    public function getPastTournamentsCount($search = '') {
        try {
            $sql = "SELECT COUNT(*) FROM tournament t 
                    LEFT JOIN sport s ON t.sport_id = s.sport_id 
                    WHERE t.status = 'COMPLETE'";
            
            if ($search) {
                $sql .= " AND (t.tournament_name LIKE :search OR s.sport_name LIKE :search)";
            }
            
            $stmt = $this->db->prepare($sql);
            if ($search) {
                $stmt->bindValue(':search', '%' . $search . '%');
            }
            
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Get past tournaments count error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Update tournament status
     */
    public function updateStatus($tournamentId, $status) {
        try {
            $sql = "UPDATE tournament SET status = :status WHERE tournament_id = :tournament_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'status' => $status,
                'tournament_id' => $tournamentId
            ]);
        } catch (PDOException $e) {
            error_log("Update tournament status error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get count of active events (ongoing tournaments) for dashboard stats
     * @return int
     */
    public function getActiveEventsCount() {
        try {
            $today = date('Y-m-d');
            $sql = "SELECT COUNT(*) as total FROM tournament 
                    WHERE status = 'INCOMPLETE' 
                    AND (end_date >= :today OR end_date IS NULL)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['today' => $today]);
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Get active events count error: " . $e->getMessage());
            return 0;
        }
    }

    public function getTournamentsBySportId($sportId) {
        try {
            $sql = "SELECT t.*, s.sport_name 
                    FROM tournament t 
                    LEFT JOIN sport s ON t.sport_id = s.sport_id 
                    WHERE t.sport_id = :sport_id 
                    ORDER BY t.start_date DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['sport_id' => $sportId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get tournaments by sport ID error: " . $e->getMessage());
            return [];
        }
    }
}
