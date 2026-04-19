<?php
// app/controllers/api/AttendanceApiController.php
require_once __DIR__ . '/../../models/Attendance.php';
require_once __DIR__ . '/../../models/SportTeam.php';
require_once __DIR__ . '/../../models/Schedule.php';

class AttendanceApiController {
    private $attendanceModel;
    private $teamModel;
    private $scheduleModel;

    public function __construct() {
        $this->attendanceModel = new Attendance();
        $this->teamModel = new SportTeam();
        $this->scheduleModel = new Schedule();
    }

    /**
     * Save attendance for a practice session
     * POST /api/attendance/save
     * Body: { practice_id, attendance: { user_id: status, ... } }
     */
    public function saveAttendance() {
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['practice_id']) || !isset($input['attendance'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Missing required fields: practice_id and attendance'
                ]);
                return;
            }

            $practiceId = $input['practice_id'];
            $attendanceData = $input['attendance'];

            $result = $this->attendanceModel->saveAttendance($practiceId, $attendanceData);

            if (isset($result['status']) && $result['status'] === 'success') {
                // Mark the practice session status as MARKED
                try {
                    $this->scheduleModel->setStatus($practiceId, 'MARKED');
                    $result['practice_status'] = 'MARKED';
                } catch (Exception $e) {
                    // If status update fails, include a warning but still return success for attendance save
                    $result['practice_status'] = 'failed_to_mark';
                    $result['practice_status_message'] = $e->getMessage();
                }
            }

            echo json_encode($result);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get attendance for a specific session
     * GET /api/attendance/session/{id}
     */
    public function getAttendanceBySession($sessionId) {
        header('Content-Type: application/json');
        
        try {
            $attendance = $this->attendanceModel->getAttendanceBySession($sessionId);
            
            echo json_encode([
                'status' => 'success',
                'data' => $attendance
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get team members with attendance percentages
     * GET /api/attendance/team-members/{sport_id}
     */
    public function getTeamMembersWithPercentages($sportId) {
        header('Content-Type: application/json');
        
        try {
            $members = $this->teamModel->getAllEnrolledStudents($sportId);
            $percentages = $this->attendanceModel->getTeamAttendancePercentages($sportId);
            
            // Merge percentages into members array
            foreach ($members as &$member) {
                $member['attendance_percentage'] = $percentages[$member['user_id']] ?? 0;
            }
            
            echo json_encode([
                'status' => 'success',
                'members' => $members
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get attendance history for a sport
     * GET /api/attendance/history/{sport_id}?limit=10
     */
    public function getAttendanceHistory($sportId) {
        header('Content-Type: application/json');
        
        try {
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $history = $this->attendanceModel->getAttendanceHistory($sportId, $limit);
            
            echo json_encode([
                'status' => 'success',
                'data' => $history
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get attendance history for the logged-in student in a specific sport
     * GET /api/attendance/student-history/{sport_id}
     */
    public function getStudentHistory($sportId) {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
            return;
        }

        try {
            $history = $this->attendanceModel->getStudentAttendanceHistory($_SESSION['user_id'], $sportId);
            
            echo json_encode([
                'status' => 'success',
                'data' => $history
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get last session attendance
     * GET /api/attendance/last-session/{sport_id}
     */
    public function getLastSessionAttendance($sportId) {
        header('Content-Type: application/json');
        
        try {
            $lastSession = $this->attendanceModel->getLastSessionAttendance($sportId);
            
            if ($lastSession) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $lastSession
                ]);
            } else {
                echo json_encode([
                    'status' => 'empty',
                    'message' => 'No previous attendance records found'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

  public function getSessionsByDate($sportId)
{
    $input = json_decode(file_get_contents('php://input'), true);
    $date = $input['date'] ?? null;

    $query = "
        SELECT id, start_time AS session_time, location
        FROM practice_sessions
        WHERE sport_id = :sport_id
    ";

    if ($date) {
        $query .= " AND session_date = :session_date";
    }

    $query .= " ORDER BY start_time ASC";

    $pdo = Database::getConnection();
    $stmt = $pdo->prepare($query);

    $params = ['sport_id' => $sportId];
    if ($date) {
        $params['session_date'] = $date;
    }

    $stmt->execute($params);
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'sessions' => $sessions
    ]);
}

    /**
     * Get upcoming practice sessions for a sport
     * GET /api/attendance/upcoming-sessions/{sport_id}
     */
    public function getUpcomingSessions($sportId) {
        header('Content-Type: application/json');
        
        try {
            $sessions = $this->scheduleModel->getUpcomingSessions($sportId, 20);
            
            echo json_encode([
                'status' => 'success',
                'sessions' => $sessions
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Check if attendance exists for a session
     * GET /api/attendance/exists/{practice_id}
     */
    public function checkAttendanceExists($practiceId) {
        header('Content-Type: application/json');
        
        try {
            $exists = $this->attendanceModel->attendanceExists($practiceId);
            
            echo json_encode([
                'status' => 'success',
                'exists' => $exists
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    public function getPreviousSessions($sportId)
{
    $scheduleModel = new Schedule();
    $sessions = $scheduleModel->getPreviousSessions($sportId);

    echo json_encode([
        'status' => 'success',
        'sessions' => $sessions
    ]);
}
}
