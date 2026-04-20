<?php
class Student {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Get all sports that a student is NOT enrolled in
     */
    public function getAvailableSports($studentId, $userId = null) {
        $sql = "SELECT s.sport_id, s.sport_name 
                FROM sport s
                WHERE s.sport_id NOT IN (
                    SELECT st.sport_id 
                    FROM `sports-team` st 
                    WHERE (st.student_id = :student_id OR st.student_id = :user_id)
                )
                ORDER BY s.sport_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'student_id' => $studentId,
            'user_id' => ($userId ?: '')
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all sports that a student is enrolled in
     */
    public function getEnrolledSports($studentId, $userId = null) {
        $sql = "SELECT s.sport_id, s.sport_name, st.joined_date
                FROM `sports-team` st
                INNER JOIN sport s ON st.sport_id = s.sport_id
                WHERE (st.student_id = :student_id OR st.student_id = :user_id)
                ORDER BY st.joined_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'student_id' => $studentId,
            'user_id' => ($userId ?: '')
        ]);
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
        // sports-team.student_id may store system user_id or alphanumeric university student_id
        $sql1 = "SELECT s.sport_name 
                 FROM `sports-team` st
                 JOIN sport s ON st.sport_id = s.sport_id
                 WHERE (st.student_id = :user_id OR st.student_id = :student_id)";
        $stmt1 = $this->db->prepare($sql1);
        $stmt1->execute([
            'user_id' => $userId, 
            'student_id' => ($studentId ?: '')
        ]);
        $sportsList = $stmt1->fetchAll(PDO::FETCH_COLUMN);

        // 2. Upcoming Practice Sessions (for enrolled sports) — used only for calendar
        // practice_sessions uses start_time (not session_time)
        $sql2 = "SELECT s.sport_name, ps.session_date, ps.start_time
                 FROM practice_sessions ps
                 JOIN sport s ON ps.sport_id = s.sport_id
                 WHERE ps.sport_id IN (SELECT sport_id FROM `sports-team` WHERE (student_id = :user_id OR student_id = :student_id))
                 AND ps.session_date >= CURDATE()
                 AND ps.status IN ('ACTIVE', 'ACCEPTED', 'PENDING')
                 ORDER BY ps.session_date ASC, ps.start_time ASC";
        $stmt2 = $this->db->prepare($sql2);
        $stmt2->execute([
            'user_id' => $userId, 
            'student_id' => ($studentId ?: '')
        ]);
        $sessionsList = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // 3. Reserved Equipment
        // equipment-requests.student_id may hold either user_id or university student_id
        // Query by both to cover all cases
        $sql3 = "SELECT COALESCE(e.equipment_name, r.category_name) as equipment_name, r.request_date 
                 FROM `equipment-requests` r 
                 LEFT JOIN equipment e ON r.equipment_id = e.equipment_id
                 WHERE (r.student_id = :user_id OR r.student_id = :student_id)
                 AND r.status IN ('ACTIVE', 'ACCEPTED', 'PENDING')
                 ORDER BY r.request_date DESC";
        $equipmentList = [];
        try {
            $stmt3 = $this->db->prepare($sql3);
            $stmt3->execute(['user_id' => $userId, 'student_id' => ($studentId ?: '')]);
            $equipmentList = $stmt3->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Dashboard Stats Error (Equipment): " . $e->getMessage());
        }

        // 4. Active Facility Reservations (upcoming or today)
        // facility-booking.facility_id is an integer matching facility_rates.id
        $sql4 = "SELECT fr.facility_name, fb.date, fb.slot 
                 FROM `facility-booking` fb 
                 JOIN facility_rates fr ON CAST(fb.facility_id AS UNSIGNED) = fr.id
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
            'sports_count'    => count($sportsList),
            'sports_list'     => $sportsList,
            'sessions_count'  => count($sessionsList),
            'sessions_list'   => $sessionsList,
            'equipment_count' => count($equipmentList),
            'equipment_list'  => $equipmentList,
            'facilities_count'=> count($facilityList),
            'facilities_list' => $facilityList
        ];
    }

    /**
     * Get upcoming activities (practice sessions) for enrolled sports
     */
    public function getUpcomingActivities($userId, $studentId, $limit = 5) {
        // NOTE: sports-team.student_id stores user_id values (system ID)
        $sql = "SELECT ps.*, s.sport_name 
                FROM practice_sessions ps
                JOIN sport s ON ps.sport_id = s.sport_id
                WHERE ps.sport_id IN (SELECT sport_id FROM `sports-team` WHERE student_id = :user_id)
                AND ps.session_date >= CURDATE()
                AND ps.status IN ('ACTIVE', 'ACCEPTED', 'PENDING')
                ORDER BY ps.session_date ASC, ps.start_time ASC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
