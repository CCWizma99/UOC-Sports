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
        // Generate a unique ID that fits within 12 characters
        // Format: ATD + microtime-based hash (9 chars)
        usleep(1); // Ensure microsecond difference
        $microtime = microtime(true);
        $hash = substr(md5($microtime . random_bytes(4)), 0, 9);
        return 'ATD' . strtoupper($hash);
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

            // Mark existing attendance for this session as SUPERSEDED (Soft Delete for audit)
            $supersedeStmt = $this->db->prepare("UPDATE attendance SET record_status = 'SUPERSEDED' WHERE practice_id = :practice_id AND record_status = 'ACTIVE'");
            $supersedeStmt->execute(['practice_id' => $practiceId]);

            // Insert new attendance records
            $insertStmt = $this->db->prepare("
                INSERT INTO attendance (attendance_id, practice_id, user_id, status, record_status)
                VALUES (:attendance_id, :practice_id, :user_id, :status, 'ACTIVE')
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
    u.student_id,
    'STUDENT' AS participant_type
FROM attendance a
INNER JOIN user u ON a.user_id = u.user_id
WHERE a.practice_id = :practice_id AND a.record_status = 'ACTIVE'

ORDER BY lname, fname
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
            AND ps.sport_id = :sport_id
            AND a.record_status = 'ACTIVE'
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
            LEFT JOIN practice_sessions ps ON ps.sport_id = st.sport_id
            LEFT JOIN attendance a ON a.practice_id = ps.id AND a.user_id = st.student_id AND a.record_status = 'ACTIVE'
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
    $limit = (int)$limit; // Ensure integer

    // Step 1: Fetch last N session IDs for the sport
    $sessionStmt = $this->db->prepare("
        SELECT id, location, session_date, start_time, notes
        FROM practice_sessions
        WHERE sport_id = :sport_id
        ORDER BY session_date DESC, start_time DESC
        LIMIT :limit
    ");
    $sessionStmt->bindValue(':sport_id', $sportId, PDO::PARAM_STR);
    $sessionStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $sessionStmt->execute();
    $sessions = $sessionStmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$sessions) {
        return [];
    }

    // Step 2: Fetch attendance for these session IDs
    $sessionIds = array_column($sessions, 'id');
    $in  = str_repeat('?,', count($sessionIds) - 1) . '?'; // Prepare placeholders

    $attendanceStmt = $this->db->prepare("
        SELECT a.practice_id, a.user_id, a.status, u.fname, u.lname, u.student_id
        FROM attendance a
        LEFT JOIN user u ON a.user_id = u.user_id
        WHERE a.practice_id IN ($in) AND a.record_status = 'ACTIVE'
        ORDER BY u.lname, u.fname
    ");
    $attendanceStmt->execute($sessionIds);
    $attendanceRows = $attendanceStmt->fetchAll(PDO::FETCH_ASSOC);

    // Step 3: Group attendance by session
    $grouped = [];
    foreach ($sessions as $s) {
        $grouped[$s['id']] = [
            'practice_id' => $s['id'],
            'location' => $s['location'],
            'session_date' => $s['session_date'],
            'start_time' => $s['start_time'],
            'description' => $s['notes'],
            'attendance' => []
        ];
    }

    foreach ($attendanceRows as $row) {
        $grouped[$row['practice_id']]['attendance'][] = [
            'user_id' => $row['user_id'],
            'fname' => $row['fname'],
            'lname' => $row['lname'],
            'student_id' => $row['student_id'],
            'status' => $row['status']
        ];
    }

    return array_values($grouped);
}

    /**
     * Get last session attendance for a sport
     * @param string $sportId - Sport ID
     * @return array|null - Last session attendance or null
     */
    public function getLastSessionAttendance($sportId) {
        // Get the most recent practice session before today for the sport
        $stmt = $this->db->prepare("
            SELECT id, location, session_date, start_time, notes
            FROM practice_sessions
            WHERE sport_id = :sport_id
              AND session_date < CURDATE()
            ORDER BY session_date DESC, start_time DESC
            LIMIT 1
        ");
        $stmt->execute(['sport_id' => $sportId]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$session) {
            return null;
        }

        // Get attendance for this session (may be empty)
        $attendance = $this->getAttendanceBySession($session['id']);
        $session['attendance'] = $attendance;
        return $session;
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
            WHERE practice_id = :practice_id AND record_status = 'ACTIVE'
        ");
        $stmt->execute(['practice_id' => $practiceId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
public function getSessionsByDate($sportId)
{
     $stmt = $this->db->prepare("
        SELECT id, session_date, start_time, location, notes AS description
        FROM practice_sessions
        WHERE sport_id = :sport_id
          AND session_date <= CURDATE()
        ORDER BY session_date DESC, start_time DESC
        LIMIT 5
    ");

    $stmt->execute(['sport_id' => $sportId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



    /**
     * Get overall attendance rate for a sport
     * @param string $sportId
     * @return int - Percentage (0-100)
     */
    public function getOverallAttendanceRate($sportId) {
        if (!$sportId) return 0;
        
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_records,
                SUM(CASE WHEN a.status = 'PRESENT' THEN 1 ELSE 0 END) as present_count
            FROM attendance a
            JOIN practice_sessions ps ON a.practice_id = ps.id
            WHERE ps.sport_id = :sport_id AND a.record_status = 'ACTIVE'
        ");
        $stmt->execute(['sport_id' => $sportId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result || $result['total_records'] == 0) return 0;
        
        return (int)round(($result['present_count'] / $result['total_records']) * 100);
    }
}