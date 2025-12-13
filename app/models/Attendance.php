<?php
// app/models/Attendance.php
require_once __DIR__ . '/../../core/Database.php';

class Attendance {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Generate unique attendance ID
     */
    private function generateAttendanceId() {
        return 'ATD' . strtoupper(uniqid());
    }

    /**
     * Save attendance for a practice session
     * @param string $practiceId - Practice session ID
     * @param array $attendanceData - Array of ['user_id' => 'status']
     * @return array - Result with status and message
     */
    public function saveAttendance($practiceId, $attendanceData) {
        try {
            $this->db->beginTransaction();

            // First, delete existing attendance for this session (to allow re-marking)
            $deleteStmt = $this->db->prepare("DELETE FROM attendance WHERE practice_id = :practice_id");
            $deleteStmt->execute(['practice_id' => $practiceId]);

            // Insert new attendance records
            $insertStmt = $this->db->prepare("
                INSERT INTO attendance (attendance_id, practice_id, user_id, status)
                VALUES (:attendance_id, :practice_id, :user_id, :status)
            ");

            $presentCount = 0;
            $absentCount = 0;

            foreach ($attendanceData as $userId => $status) {
                $attendanceId = $this->generateAttendanceId();
                $insertStmt->execute([
                    'attendance_id' => $attendanceId,
                    'practice_id' => $practiceId,
                    'user_id' => $userId,
                    'status' => $status
                ]);

                if ($status === 'PRESENT') {
                    $presentCount++;
                } else {
                    $absentCount++;
                }
            }

            $this->db->commit();

            return [
                'status' => 'success',
                'message' => 'Attendance saved successfully',
                'data' => [
                    'practice_id' => $practiceId,
                    'total_marked' => count($attendanceData),
                    'present' => $presentCount,
                    'absent' => $absentCount
                ]
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                'status' => 'error',
                'message' => 'Failed to save attendance: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get attendance for a specific practice session
     * @param string $practiceId - Practice session ID
     * @return array - Attendance records
     */
    public function getAttendanceBySession($practiceId) {
        $stmt = $this->db->prepare("
            SELECT 
                a.attendance_id,
                a.user_id,
                a.status,
                u.fname,
                u.lname,
                u.student_id
            FROM attendance a
            INNER JOIN user u ON a.user_id = u.user_id
            WHERE a.practice_id = :practice_id
            ORDER BY u.lname, u.fname
        ");
        $stmt->execute(['practice_id' => $practiceId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Calculate attendance percentage for a student in a specific sport
     * @param string $userId - Student user ID
     * @param string $sportId - Sport ID
     * @return float - Attendance percentage
     */
    public function calculateAttendancePercentage($userId, $sportId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_sessions,
                SUM(CASE WHEN a.status = 'PRESENT' THEN 1 ELSE 0 END) as present_count
            FROM attendance a
            INNER JOIN practice_sessions ps ON a.practice_id = ps.id
            WHERE a.user_id = :user_id 
            AND ps.facility LIKE CONCAT('%', :sport_id, '%')
        ");
        
        $stmt->execute([
            'user_id' => $userId,
            'sport_id' => $sportId
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['total_sessions'] == 0) {
            return 0;
        }
        
        return round(($result['present_count'] / $result['total_sessions']) * 100, 1);
    }

    /**
     * Get attendance percentages for all team members
     * @param string $sportId - Sport ID
     * @return array - Array of user_id => percentage
     */
    public function getTeamAttendancePercentages($sportId) {
        $stmt = $this->db->prepare("
            SELECT 
                st.student_id as user_id,
                COUNT(DISTINCT ps.id) as total_sessions,
                SUM(CASE WHEN a.status = 'PRESENT' THEN 1 ELSE 0 END) as present_count
            FROM `sports-team` st
            LEFT JOIN practice_sessions ps ON ps.facility LIKE CONCAT('%', st.sport_id, '%')
            LEFT JOIN attendance a ON a.practice_id = ps.id AND a.user_id = st.student_id
            WHERE st.sport_id = :sport_id
            GROUP BY st.student_id
        ");
        
        $stmt->execute(['sport_id' => $sportId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $percentages = [];
        foreach ($results as $row) {
            if ($row['total_sessions'] > 0) {
                $percentages[$row['user_id']] = round(($row['present_count'] / $row['total_sessions']) * 100, 1);
            } else {
                $percentages[$row['user_id']] = 0;
            }
        }
        
        return $percentages;
    }

    /**
     * Get attendance history for a sport (all sessions)
     * @param string $sportId - Sport ID
     * @param int $limit - Number of sessions to retrieve
     * @return array - Historical attendance records grouped by session
     */
    public function getAttendanceHistory($sportId, $limit = 10) {
        $stmt = $this->db->prepare("
            SELECT 
                ps.id as practice_id,
                ps.facility,
                ps.session_date,
                ps.session_time,
                ps.description,
                a.user_id,
                a.status,
                u.fname,
                u.lname,
                u.student_id
            FROM practice_sessions ps
            LEFT JOIN attendance a ON ps.id = a.practice_id
            LEFT JOIN user u ON a.user_id = u.user_id
            WHERE ps.facility LIKE CONCAT('%', :sport_id, '%')
            AND ps.session_date <= CURDATE()
            ORDER BY ps.session_date DESC, ps.session_time DESC
            LIMIT :limit
        ");
        
        $stmt->execute(['sport_id' => $sportId, 'limit' => $limit * 50]); // Multiply to get enough records
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Group by session
        $grouped = [];
        foreach ($results as $row) {
            $practiceId = $row['practice_id'];
            if (!isset($grouped[$practiceId])) {
                $grouped[$practiceId] = [
                    'practice_id' => $practiceId,
                    'facility' => $row['facility'],
                    'session_date' => $row['session_date'],
                    'session_time' => $row['session_time'],
                    'description' => $row['description'],
                    'attendance' => []
                ];
            }
            
            if ($row['user_id']) {
                $grouped[$practiceId]['attendance'][] = [
                    'user_id' => $row['user_id'],
                    'fname' => $row['fname'],
                    'lname' => $row['lname'],
                    'student_id' => $row['student_id'],
                    'status' => $row['status']
                ];
            }
        }
        
        return array_values(array_slice($grouped, 0, $limit));
    }

    /**
     * Get last session attendance for a sport
     * @param string $sportId - Sport ID
     * @return array|null - Last session attendance or null
     */
    public function getLastSessionAttendance($sportId) {
        $history = $this->getAttendanceHistory($sportId, 1);
        return !empty($history) ? $history[0] : null;
    }

    /**
     * Check if attendance exists for a practice session
     * @param string $practiceId - Practice session ID
     * @return bool
     */
    public function attendanceExists($practiceId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM attendance 
            WHERE practice_id = :practice_id
        ");
        $stmt->execute(['practice_id' => $practiceId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}
