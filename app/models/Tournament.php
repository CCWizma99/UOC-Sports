<?php

class Tournament {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Create a new tournament
     */
    public function createTournament($name, $sportId, $startDate, $endDate) {
        try {
            $tournamentId = 'TOUR_' . uniqid();
            
            $sql = "INSERT INTO tournament (tournament_id, tournament_name, sport_id, start_date, end_date, status) 
                    VALUES (:tournament_id, :tournament_name, :sport_id, :start_date, :end_date, 'INCOMPLETE')";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'tournament_id' => $tournamentId,
                'tournament_name' => $name,
                'sport_id' => $sportId,
                'start_date' => $startDate,
                'end_date' => $endDate
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
     * Get all tournaments
     */
    public function getAllTournaments() {
        try {
            $sql = "SELECT t.*, s.sport_name 
                    FROM tournament t 
                    LEFT JOIN sport s ON t.sport_id = s.sport_id 
                    ORDER BY t.start_date DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all tournaments error: " . $e->getMessage());
            return [];
        }
    }
}
