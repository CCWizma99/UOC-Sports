<?php

class StudentController {
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /uoc-sports/public/sign-in');
            exit;
        }
        
        $userModel = new User();
        $user = $userModel->getUserById($_SESSION['user_id']);
        
        $data = [
            'user' => $user
        ];
        
        view('student/overview', $data);
    }

    public function sports() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /uoc-sports/public/sign-in');
            exit;
        }
        view('student/sports');
    }

    public function equipment() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /uoc-sports/public/sign-in');
            exit;
        }
        // Get student ID and sports list for the view
        $userModel = new User();
        $student = $userModel->getStudentId($_SESSION['user_id']);
        
        require_once '../app/models/EquipmentBookigRequest.php';
        $bookingModel = new EquipmentBookigRequest();
        $sports = $bookingModel->getAllSports();
        
        $data = [
            'student_id' => $student['student_id'],
            'sports' => $sports
        ];
        
        view('student/equipment', $data);
    }

    public function facilities() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /uoc-sports/public/sign-in');
            exit;
        }
        view('student/facilities');
    }


    /**
     * Get dashboard statistics (API)
     */
    public function dashboardStats() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $studentModel = new Student();
        
        // Get Student ID
        $userModel = new User();
        $student = $userModel->getStudentId($userId);
        
        if (!$student) {
            echo json_encode(['status' => 'error', 'message' => 'Student not found']);
            return;
        }
        $studentId = $student['student_id'];

        // Fetch Stats
        $stats = $studentModel->getDashboardStats($userId, $studentId);
        $upcoming = $studentModel->getUpcomingActivities($userId, $studentId);

        echo json_encode([
            'status' => 'success', 
            'stats' => $stats, 
            'upcoming' => $upcoming
        ]);
    }

    /**
     * Get available sports for enrollment (API endpoint)
     */
    public function getAvailableSports() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
            return;
        }

        $userModel = new User();
        $studentData = $userModel->getStudentId($_SESSION['user_id']);
        
        if (!$studentData) {
            echo json_encode(['status' => 'error', 'message' => 'Student not found']);
            return;
        }

        $studentModel = new Student();
        $sports = $studentModel->getAvailableSports($studentData['student_id'], $_SESSION['user_id']);
        
        echo json_encode(['status' => 'success', 'data' => $sports]);
    }

    /**
     * Get enrolled sports (API endpoint)
     */
    public function getEnrolledSports() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
            return;
        }

        $studentModel = new Student();
        // Needs student_id from user_id first? 
        // original code passed user_id to getEnrolledSports but query used student_id? 
        // Let's check original Student model. 
        // Original Student model getEnrolledSports used :student_id param.
        // Wait, original controller passed $_SESSION['user_id'] to getEnrolledSports.
        // If the model expects student_id but controller passed user_id, that would be a bug unless they are same or handled.
        // I should fix this to be safe.
        
        $userModel = new User();
        $studentData = $userModel->getStudentId($_SESSION['user_id']);
        
        if (!$studentData) {
             echo json_encode(['status' => 'error', 'message' => 'Student not found']);
             return;
        }

        $sports = $studentModel->getEnrolledSports($studentData['student_id'], $_SESSION['user_id']);
        
        echo json_encode(['status' => 'success', 'data' => $sports]);
    }

    /**
     * Enroll in a sport (API endpoint)
     */
    public function enrollSport() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }

        $sportId = $_POST['sport_id'] ?? null;
        
        if (!$sportId) {
            echo json_encode(['status' => 'error', 'message' => 'Sport ID is required']);
            return;
        }

        $userModel = new User();
        $studentData = $userModel->getStudentId($_SESSION['user_id']);

        $studentModel = new Student();
        $result = $studentModel->enrollInSport($studentData['student_id'], $sportId);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Successfully enrolled in sport']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to enroll. You may already be enrolled in this sport.']);
        }
    }

    public function getStudentPracticeSessions() {
        header('Content-Type: application/json');

        $month = $_GET['month'] ?? date('m');
        $year  = $_GET['year']  ?? date('Y');

        // Strip any stray characters (e.g. a trailing semicolon from the old JS bug)
        $month = preg_replace('/[^0-9]/', '', $month);
        $year  = preg_replace('/[^0-9]/', '', $year);

        try {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                echo json_encode(['success' => false, 'message' => 'Not authenticated']);
                return;
            }

            $userModel = new User();
            $studentData = $userModel->getStudentId($userId);
            $studentId = $studentData ? $studentData['student_id'] : null;

            $model = new SportPracticeSession();
            $sessions = $model->getStudentPracticeSessions($month, $year, $userId, $studentId);

            // Group by date
            $grouped = [];
            foreach ($sessions as $session) {
                $date = $session['session_date'];
                $grouped[$date][] = $session;
            }

            echo json_encode(['success' => true, 'data' => $grouped]);
        } catch (Exception $e) {
            error_log('getStudentPracticeSessions error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    /**
     * Unenroll from a sport (API endpoint)
     */
    public function unenrollSport() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }

        $sportId = $_POST['sport_id'] ?? null;
        
        if (!$sportId) {
            echo json_encode(['status' => 'error', 'message' => 'Sport ID is required']);
            return;
        }

        $userModel = new User();
        $studentData = $userModel->getStudentId($_SESSION['user_id']);

        $studentModel = new Student();
        $result = $studentModel->unenrollFromSport($studentData['student_id'], $sportId);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Successfully unenrolled from sport']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to unenroll from sport']);
        }
    }
}