<?php

class EventResultPermission {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Grant permission to a captain for a specific tournament
     * Returns the new permission ID or false on failure
     */
    public function grantPermission($tournamentId, $captainId, $sportId, $grantedBy) {
        try {
            $this->ensureTableExists();
            // Check if permission already exists (active)
            $existing = $this->getPermission($tournamentId, $captainId);
            if ($existing && $existing['status'] === 'ACTIVE') {
                return ['exists' => true, 'id' => $existing['id']];
            }

            // If revoked, reactivate it
            if ($existing && $existing['status'] === 'REVOKED') {
                $stmt = $this->db->prepare(
                    "UPDATE event_result_permissions 
                     SET status = 'ACTIVE', granted_by = :granted_by, granted_at = NOW(), email_sent = 0
                     WHERE id = :id"
                );
                $stmt->execute(['granted_by' => $grantedBy, 'id' => $existing['id']]);
                return ['exists' => false, 'id' => $existing['id']];
            }

            // Insert new permission
            $stmt = $this->db->prepare(
                "INSERT INTO event_result_permissions 
                 (tournament_id, captain_id, sport_id, granted_by)
                 VALUES (:tournament_id, :captain_id, :sport_id, :granted_by)"
            );
            $stmt->execute([
                'tournament_id' => $tournamentId,
                'captain_id'    => $captainId,
                'sport_id'      => $sportId,
                'granted_by'    => $grantedBy,
            ]);
            return ['exists' => false, 'id' => $this->db->lastInsertId()];
        } catch (PDOException $e) {
            error_log("Grant permission error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark email as sent for a permission
     */
    public function markEmailSent($id) {
        $stmt = $this->db->prepare("UPDATE event_result_permissions SET email_sent = 1 WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    /**
     * Revoke a permission
     */
    public function revokePermission($id) {
        try {
            $this->ensureTableExists();
            $stmt = $this->db->prepare("UPDATE event_result_permissions SET status = 'REVOKED' WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return true;
        } catch (PDOException $e) {
            error_log("Revoke permission error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get a specific permission record
     */
    public function getPermission($tournamentId, $captainId) {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM event_result_permissions 
                 WHERE tournament_id = :tournament_id AND captain_id = :captain_id"
            );
            $stmt->execute(['tournament_id' => $tournamentId, 'captain_id' => $captainId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get permission error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all granted permissions (with tournament, sport, captain details) for admin view
     */
    public function getAllGrantedPermissions() {
        try {
            $this->ensureTableExists();
            $sql = "
                SELECT 
                    erp.id,
                    erp.tournament_id,
                    erp.captain_id,
                    erp.sport_id,
                    erp.status,
                    erp.granted_at,
                    erp.email_sent,
                    t.tournament_name,
                    t.start_date,
                    t.end_date,
                    s.sport_name,
                    CONCAT(u.fname, ' ', u.lname) AS captain_name,
                    u.email AS captain_email,
                    CONCAT(a.fname, ' ', a.lname) AS granted_by_name
                FROM event_result_permissions erp
                JOIN tournament t ON erp.tournament_id = t.tournament_id
                JOIN sport s ON erp.sport_id = s.sport_id
                JOIN user u ON erp.captain_id = u.user_id
                JOIN user a ON erp.granted_by = a.user_id
                ORDER BY erp.granted_at DESC
            ";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all permissions error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all ACTIVE permissions for a captain (for captain's view)
     * Only shows tournaments that have already started
     */
    public function getActivePermissionsForCaptain($captainId) {
        try {
            $this->ensureTableExists();
            $today = date('Y-m-d');
            $sql = "
                SELECT 
                    erp.id,
                    erp.tournament_id,
                    erp.sport_id,
                    erp.granted_at,
                    t.tournament_name,
                    t.start_date,
                    t.end_date,
                    s.sport_name,
                    s.sport_category
                FROM event_result_permissions erp
                JOIN tournament t ON erp.tournament_id = t.tournament_id
                JOIN sport s ON erp.sport_id = s.sport_id
                WHERE erp.captain_id = :captain_id
                  AND erp.status = 'ACTIVE'
                  AND t.start_date <= :today
                ORDER BY erp.granted_at DESC
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['captain_id' => $captainId, 'today' => $today]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get captain permissions error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verify a captain has active permission for a specific tournament
     */
    public function hasPermission($captainId, $tournamentId) {
        try {
            $today = date('Y-m-d');
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM event_result_permissions erp
                JOIN tournament t ON erp.tournament_id = t.tournament_id
                WHERE erp.captain_id = :captain_id
                  AND erp.tournament_id = :tournament_id
                  AND erp.status = 'ACTIVE'
                  AND t.start_date <= :today
            ");
            $stmt->execute([
                'captain_id'    => $captainId,
                'tournament_id' => $tournamentId,
                'today'         => $today,
            ]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Has permission check error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ensure the event_result_permissions table exists (called before queries that need it)
     */
    private function ensureTableExists() {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS `event_result_permissions` (
              `id` int NOT NULL AUTO_INCREMENT,
              `tournament_id` varchar(24) NOT NULL,
              `captain_id` varchar(12) NOT NULL,
              `sport_id` varchar(4) NOT NULL,
              `granted_by` varchar(12) NOT NULL,
              `granted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `status` enum('ACTIVE','REVOKED') NOT NULL DEFAULT 'ACTIVE',
              `email_sent` tinyint(1) NOT NULL DEFAULT 0,
              PRIMARY KEY (`id`),
              UNIQUE KEY `tournament_captain` (`tournament_id`, `captain_id`),
              KEY `captain_id` (`captain_id`),
              KEY `tournament_id` (`tournament_id`),
              KEY `sport_id` (`sport_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    /**
     * Get all tournaments that have started (start_date <= today) for admin grant panel
     * Includes captain info from the sport table
     */
    public function getStartedTournaments() {
        try {
            $this->ensureTableExists();
            $today = date('Y-m-d');
            $sql = "
                SELECT 
                    t.tournament_id,
                    t.tournament_name,
                    t.sport_id,
                    t.start_date,
                    t.end_date,
                    t.status,
                    s.sport_name,
                    s.sport_category,
                    s.captain_id,
                    CASE WHEN s.captain_id != '' THEN CONCAT(u.fname, ' ', u.lname) ELSE '' END AS captain_name,
                    CASE WHEN s.captain_id != '' THEN u.email ELSE '' END AS captain_email,
                    erp.id AS permission_id,
                    erp.status AS permission_status
                FROM tournament t
                JOIN sport s ON t.sport_id = s.sport_id
                LEFT JOIN user u ON s.captain_id = u.user_id AND s.captain_id != ''
                LEFT JOIN event_result_permissions erp 
                       ON erp.tournament_id = t.tournament_id AND erp.captain_id = s.captain_id AND s.captain_id != ''
                WHERE t.start_date <= :today
                ORDER BY t.start_date DESC
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['today' => $today]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get started tournaments error: " . $e->getMessage());
            return [];
        }
    }
}
