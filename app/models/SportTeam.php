<?php
// app/models/SportTeam.php
require_once __DIR__ . '/../../core/Database.php';

class SportTeam {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Search sports by ID or name
     */
    public function search($query) {
        $sql = "SELECT * FROM sport
                WHERE sport_id LIKE :query OR sport_name LIKE :query
                LIMIT 4";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['query' => "%$query%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all team members for a specific sport
     * @param string $sportId - Sport ID
     * @return array - Team members with user details
     */
    public function getTeamMembers($sportId) {
        $stmt = $this->db->prepare("
            SELECT 
                u.user_id,
                u.fname,
                u.lname,
                u.student_id,
                u.email,
                u.contact_no,
                st.joined_date
            FROM `sports-team` st
            INNER JOIN user u ON st.student_id = u.user_id
            WHERE st.sport_id = :sport_id
            AND u.status = 'ACTIVE'
            ORDER BY u.lname, u.fname
        ");
        
        $stmt->execute(['sport_id' => $sportId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get team member count for a sport
     * @param string $sportId - Sport ID
     * @return int - Number of team members
     */
    public function getTeamMemberCount($sportId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM `sports-team` st
            INNER JOIN user u ON st.student_id = u.user_id
            WHERE st.sport_id = :sport_id
            AND u.status = 'ACTIVE'
        ");
        
        $stmt->execute(['sport_id' => $sportId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    /**
     * Check if a student is a member of a sport team
     * @param string $sportId - Sport ID
     * @param string $userId - User ID
     * @return bool
     */
    public function isMember($sportId, $userId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM `sports-team`
            WHERE sport_id = :sport_id AND student_id = :user_id
        ");
        
        $stmt->execute([
            'sport_id' => $sportId,
            'user_id' => $userId
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Add a member to a sport team
     * @param string $sportId - Sport ID
     * @param string $userId - User ID
     * @return bool - Success status
     */
    public function addMember($sportId, $userId) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO `sports-team` (sport_id, student_id, joined_date)
                VALUES (:sport_id, :user_id, CURDATE())
            ");
            
            return $stmt->execute([
                'sport_id' => $sportId,
                'user_id' => $userId
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Remove a member from a sport team
     * @param string $sportId - Sport ID
     * @param string $userId - User ID
     * @return bool - Success status
     */
    public function removeMember($sportId, $userId) {
        $stmt = $this->db->prepare("
            DELETE FROM `sports-team`
            WHERE sport_id = :sport_id AND student_id = :user_id
        ");
        
        return $stmt->execute([
            'sport_id' => $sportId,
            'user_id' => $userId
        ]);
    }
}
