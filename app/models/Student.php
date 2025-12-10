<?php
class Student {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Get all sports that a student is NOT enrolled in
     */
    public function getAvailableSports($studentId) {
        $sql = "SELECT s.sport_id, s.sport_name 
                FROM sport s
                WHERE s.sport_id NOT IN (
                    SELECT st.sport_id 
                    FROM `sports-team` st 
                    WHERE st.student_id = :student_id
                )
                ORDER BY s.sport_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all sports that a student is enrolled in
     */
    public function getEnrolledSports($studentId) {
        $sql = "SELECT s.sport_id, s.sport_name, st.joined_date
                FROM `sports-team` st
                INNER JOIN sport s ON st.sport_id = s.sport_id
                WHERE st.student_id = :student_id
                ORDER BY st.joined_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Enroll a student in a sport
     */
    public function enrollInSport($studentId, $sportId) {
        try {
            $sql = "INSERT INTO `sports-team` (sport_id, student_id, joined_date) 
                    VALUES (:sport_id, :student_id, :joined_date)";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                'sport_id' => $sportId,
                'student_id' => $studentId,
                'joined_date' => date('Y-m-d')
            ]);
            
            return $result;
        } catch (PDOException $e) {
            // Handle duplicate entry error
            if ($e->getCode() == 23000) {
                return false; // Already enrolled
            }
            throw $e;
        }
    }

    /**
     * Unenroll a student from a sport
     */
    public function unenrollFromSport($studentId, $sportId) {
        $sql = "DELETE FROM `sports-team` 
                WHERE sport_id = :sport_id AND student_id = :student_id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'sport_id' => $sportId,
            'student_id' => $studentId
        ]);
    }

    /**
     * Check if a student is enrolled in a specific sport
     */
    public function isEnrolled($studentId, $sportId) {
        $sql = "SELECT COUNT(*) as count FROM `sports-team` 
                WHERE sport_id = :sport_id AND student_id = :student_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'sport_id' => $sportId,
            'student_id' => $studentId
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}
