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
                st.joined_date,
                f.faculty_name
            FROM `sports-team` st
            INNER JOIN user u ON st.student_id = u.user_id
            LEFT JOIN faculty f ON u.faculty_id = f.faculty_id
            WHERE st.sport_id = :sport_id 
            AND u.status = 'ACTIVE'
            AND st.status = 'ACTIVE'
            ORDER BY u.lname, u.fname
        ");
        
        $stmt->execute(['sport_id' => $sportId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get members not in the team for a specific sport (but enrolled in the sport)
     * @param string $sportId - Sport ID
     * @return array - Available members with user details
     */
    public function getMembersNotInTeam($sportId) {
        $stmt = $this->db->prepare("
            SELECT 
                u.user_id,
                u.fname,
                u.lname,
                u.student_id,
                u.email,
                u.contact_no,
                st.joined_date,
                f.faculty_name
            FROM `sports-team` st
            INNER JOIN user u ON st.student_id = u.user_id
            LEFT JOIN faculty f ON u.faculty_id = f.faculty_id
            WHERE st.sport_id = :sport_id 
            AND st.in_team = 'NO'
            AND st.status = 'ACTIVE'
            AND u.status = 'ACTIVE'
            ORDER BY u.lname, u.fname
        ");
        
        $stmt->execute(['sport_id' => $sportId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get team member count for a sport
     * @param string $sportId - Sport ID
     * @return int - Number of team members (confirmed in team)
     */
    public function getTeamMemberCount($sportId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM `sports-team` st
            INNER JOIN user u ON st.student_id = u.user_id
            WHERE st.sport_id = :sport_id
            AND u.status = 'ACTIVE'
            AND st.status = 'ACTIVE'
        ");
        
        $stmt->execute(['sport_id' => $sportId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['count'] ?? 0);
    }

    /**
     * Check if a student is a confirmed member of a sport team
     * @param string $sportId - Sport ID
     * @param string $userId - User ID
     * @return bool
     */
    public function isMember($sportId, $userId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM `sports-team`
            WHERE sport_id = :sport_id 
            AND student_id = :user_id 
            AND in_team = 'YES'
            AND status = 'ACTIVE'
        ");
        
        $stmt->execute([
            'sport_id' => $sportId,
            'user_id' => $userId
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['count'] ?? 0) > 0;
    }

    /**
     * Add a member to a sport team
     * @param string $sportId - Sport ID
     * @param string $userId - User ID
     * @return bool - Success status
     */
    public function addMember($sportId, $userId) {
        try {
            // Check if record exists
            $checkSql = "SELECT count(*) as count FROM `sports-team` WHERE sport_id = :sport_id AND student_id = :user_id";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute(['sport_id' => $sportId, 'user_id' => $userId]);
            $exists = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
            
            if ($exists) {
                // Update if exists - ensure both flags are set correctly
                $updateSql = "UPDATE `sports-team` SET status = 'ACTIVE', in_team = 'YES', joined_date = CURDATE() WHERE sport_id = :sport_id AND student_id = :user_id";
                return $this->db->prepare($updateSql)->execute(['sport_id' => $sportId, 'user_id' => $userId]);
            } else {
                // Insert new - default in_team to 'YES'
                $insertSql = "INSERT INTO `sports-team` (sport_id, student_id, joined_date, in_team, status) VALUES (:sport_id, :user_id, CURDATE(), 'YES', 'ACTIVE')";
                return $this->db->prepare($insertSql)->execute(['sport_id' => $sportId, 'user_id' => $userId]);
            }
        } catch (Exception $e) {
            error_log("Error adding team member: " . $e->getMessage());
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
            UPDATE `sports-team`
            SET status = 'INACTIVE', in_team = 'NO'
            WHERE sport_id = :sport_id AND student_id = :user_id
        ");
        
        return $stmt->execute([
            'sport_id' => $sportId,
            'user_id' => $userId
        ]);
    }

    /**
     * Get all students enrolled in a sport (both in-team and available)
     * @param string $sportId - Sport ID
     * @return array - All active enrolled students
     */
    public function getAllEnrolledStudents($sportId) {
        $stmt = $this->db->prepare("
            SELECT 
                u.user_id,
                u.fname,
                u.lname,
                u.student_id,
                u.email,
                u.contact_no,
                st.joined_date,
                st.in_team,
                f.faculty_name
            FROM `sports-team` st
            INNER JOIN user u ON st.student_id = u.user_id
            LEFT JOIN faculty f ON u.faculty_id = f.faculty_id
            WHERE st.sport_id = :sport_id 
            AND st.status = 'ACTIVE'
            AND u.status = 'ACTIVE'
            ORDER BY u.lname, u.fname
        ");
        
        $stmt->execute(['sport_id' => $sportId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
