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
    /**
     * Get dashboard statistics for summary cards
     */
    public function getDashboardStats($userId, $studentId) {
        // 1. Enrolled Sports
        $sql1 = "SELECT s.sport_name 
                 FROM `sports-team` st
                 JOIN sport s ON st.sport_id = s.sport_id
                 WHERE st.student_id = :student_id";
        $stmt1 = $this->db->prepare($sql1);
        $stmt1->execute(['student_id' => $studentId]);
        $sportsList = $stmt1->fetchAll(PDO::FETCH_COLUMN);

        // 2. Upcoming Practice Sessions (for enrolled sports)
        $sql2 = "SELECT s.sport_name, ps.session_date, ps.session_time
                 FROM practice_sessions ps
                 JOIN sport s ON ps.sport_id = s.sport_id
                 WHERE ps.sport_id IN (SELECT sport_id FROM `sports-team` WHERE student_id = :student_id)
                 AND ps.session_date >= CURDATE()
                 ORDER BY ps.session_date ASC, ps.session_time ASC";
        $stmt2 = $this->db->prepare($sql2);
        $stmt2->execute(['student_id' => $studentId]);
        $sessionsList = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // 3. Reserved Equipment
        // Adjusted table name and columns based on Equipment model research
        $sql3 = "SELECT e.equipment_name, r.request_date 
                 FROM `equipment-requests` r 
                 JOIN equipment e ON r.equipment_id = e.equipment_id
                 WHERE r.student_id = :student_id 
                 ORDER BY r.request_date DESC";
        $equipmentList = [];
        try {
            $stmt3 = $this->db->prepare($sql3);
            $stmt3->execute(['student_id' => $studentId]);
            $equipmentList = $stmt3->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Dashboard Stats Error (Equipment): " . $e->getMessage());
        }

        // 4. Active Facility Reservations
        // Adjusted table names and columns based on Facility model research
        $sql4 = "SELECT fr.facility_name, fb.date, fb.slot 
                 FROM `facility-booking` fb 
                 JOIN facility_rates fr ON fb.facility_id = fr.id
                 WHERE fb.user_id = :user_id 
                 AND fb.status IN ('BOOKED', 'ACCEPTED') 
                 AND fb.date >= CURDATE()
                 ORDER BY fb.date ASC";
        $facilityList = [];
        try {
            $stmt4 = $this->db->prepare($sql4);
            $stmt4->execute(['user_id' => $userId]);
            $facilityList = $stmt4->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Dashboard Stats Error (Facilities): " . $e->getMessage());
        }

        return [
            'sports_count' => count($sportsList),
            'sports_list' => array_slice($sportsList, 0, 3), // Show first 3 for summary
            'sessions_count' => count($sessionsList),
            'sessions_list' => array_slice($sessionsList, 0, 3), // Show first 3 for summary
            'equipment_count' => count($equipmentList),
            'equipment_list' => array_slice($equipmentList, 0, 3), // Show first 3 for summary
            'facilities_count' => count($facilityList),
            'facilities_list' => array_slice($facilityList, 0, 3)  // Show first 3 for summary
        ];
    }

    /**
     * Get upcoming activities (practice sessions) for enrolled sports
     */
    public function getUpcomingActivities($userId, $studentId, $limit = 5) {
        $sql = "SELECT ps.*, s.sport_name 
                FROM practice_sessions ps
                JOIN sport s ON ps.sport_id = s.sport_id
                WHERE ps.sport_id IN (SELECT sport_id FROM `sports-team` WHERE student_id = :student_id)
                AND ps.session_date >= CURDATE()
                ORDER BY ps.session_date ASC, ps.session_time ASC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':student_id', $studentId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
