<?php
/**
 * ResultModel - Handles generic cross-sport match result querying and publishing
 */
class ResultModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Get all match results for admin panel
     */
    public function getAllResults() {
        $sql = "
            SELECT 
                tm.match_id, 
                tm.tournament_id,
                t.tournament_name,
                tm.sport_id,
                s.sport_name,
                tm.sport_category,
                tm.match_name,
                tm.match_date,
                tm.result_status,
                tm.is_published,
                tm.submitted_by,
                tm.created_at,
                tm.winner_id,
                tm.winner_type,
                CASE 
                    WHEN tm.winner_type = 'INTERNAL' THEN CONCAT(u.fname, ' ', u.lname)
                    WHEN tm.winner_type = 'INVITATIONAL' THEN CONCAT(ip.fname, ' ', ip.lname)
                    WHEN tm.winner_type = 'TEAM' THEN tm.winner_name
                    WHEN tm.winner_type = 'DRAW' THEN 'DRAW'
                    ELSE COALESCE(tm.winner_name, CONCAT(u.fname, ' ', u.lname))
                END as winner_display_name
            FROM tournament_match tm
            JOIN tournament t ON tm.tournament_id = t.tournament_id
            JOIN sport s ON tm.sport_id = s.sport_id
            LEFT JOIN user u ON tm.winner_id = u.user_id
            LEFT JOIN invitational_players ip ON tm.winner_invitational_id = ip.inv_player_id
            ORDER BY tm.created_at DESC
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Toggle the publish status of a single match match
     */
    public function togglePublish($matchId, $isPublished) {
        $sql = "UPDATE tournament_match SET is_published = :is_published WHERE match_id = :match_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':is_published' => $isPublished,
            ':match_id' => $matchId
        ]);
    }

    /**
     * Publish all currently pending results for an entire tournament
     * and revoke active captain permissions
     */
    public function publishEntireTournament($tournamentId) {
        try {
            $this->db->beginTransaction();

            // Publish all matches
            $sql1 = "UPDATE tournament_match SET is_published = 1 WHERE tournament_id = :tid";
            $stmt1 = $this->db->prepare($sql1);
            $stmt1->execute([':tid' => $tournamentId]);

            // Revoke captain permissions
            $sql2 = "UPDATE event_result_permissions SET status = 'REVOKED' WHERE tournament_id = :tid AND status = 'ACTIVE'";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([':tid' => $tournamentId]);

            // Mark tournament as COMPLETE
            $sql3 = "UPDATE tournament SET status = 'COMPLETE' WHERE tournament_id = :tid";
            $stmt3 = $this->db->prepare($sql3);
            $stmt3->execute([':tid' => $tournamentId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Publish Tournament Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get published match results for the public page
     */
    public function getPublishedResults() {
        $sql = "
            SELECT 
                tm.match_id, 
                t.tournament_name,
                s.sport_name,
                tm.sport_category,
                tm.match_name,
                tm.match_date,
                tm.result_status,
                tm.winner_id,
                tm.winner_type,
                CASE 
                    WHEN tm.winner_type = 'INTERNAL' THEN CONCAT(u.fname, ' ', u.lname)
                    WHEN tm.winner_type = 'INVITATIONAL' THEN CONCAT(ip.fname, ' ', ip.lname)
                    WHEN tm.winner_type = 'TEAM' THEN tm.winner_name
                    WHEN tm.winner_type = 'DRAW' THEN 'DRAW'
                    ELSE COALESCE(tm.winner_name, CONCAT(u.fname, ' ', u.lname))
                END as winner_display_name
            FROM tournament_match tm
            JOIN tournament t ON tm.tournament_id = t.tournament_id
            JOIN sport s ON tm.sport_id = s.sport_id
            LEFT JOIN user u ON tm.winner_id = u.user_id
            LEFT JOIN invitational_players ip ON tm.winner_invitational_id = ip.inv_player_id
            WHERE tm.is_published = 1
            ORDER BY tm.match_date DESC, tm.created_at DESC
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
