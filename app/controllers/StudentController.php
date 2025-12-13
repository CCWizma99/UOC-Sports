<?php

class StudentController {
    public function index() {
        view('student/student-portal');
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
        $sports = $studentModel->getAvailableSports($studentData['student_id']);
        
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
        $sports = $studentModel->getEnrolledSports($_SESSION['user_id']);
        
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

        $studentModel = new Student();
        $result = $studentModel->enrollInSport($_SESSION['user_id'], $sportId);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Successfully enrolled in sport']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to enroll. You may already be enrolled in this sport.']);
        }
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

        $studentModel = new Student();
        $result = $studentModel->unenrollFromSport($_SESSION['user_id'], $sportId);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Successfully unenrolled from sport']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to unenroll from sport']);
        }
    }
}