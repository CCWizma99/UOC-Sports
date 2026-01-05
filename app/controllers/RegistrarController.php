<?php

class RegistrarController {
    
    public function RegistrarDashboard() {
        // Get pending verifications for dashboard
        $verificationModel = new VerificationRequest();
        $userId = $_SESSION['user_id'] ?? null;
        
        $pendingVerifications = [];
        if ($userId) {
            $pendingVerifications = $verificationModel->getPendingForRegistrar($userId);
        }
        
        $data = [
            'verifications' => $pendingVerifications,
            'pendingCount' => count($pendingVerifications)
        ];
        
        view('registrar/registrar-dashboard', $data);
    }

    public function VerifyStudents() {
        $verificationModel = new VerificationRequest();
        $userId = $_SESSION['user_id'] ?? null;
        
        $pendingVerifications = [];
        if ($userId) {
            $pendingVerifications = $verificationModel->getPendingForRegistrar($userId);
        }
        
        $data = [
            'verifications' => $pendingVerifications
        ];
        
        view('registrar/verify-students', $data);
    }

    public function VerifyStaff() {
        view('registrar/verify-staff');
    }

    public function VerifyBookings() {
        view('registrar/verify-bookings');
    }

    /**
     * API: Get student details including ID card image
     */
    public function getStudentDetails() {
        header('Content-Type: application/json');
        
        $studentId = $_GET['student_id'] ?? null;
        
        if (!$studentId) {
            echo json_encode(['status' => 'error', 'message' => 'Student ID required']);
            return;
        }

        $userModel = new User();
        $verificationModel = new VerificationRequest();
        
        $student = $userModel->findByStudentId($studentId);
        $idCardImage = $verificationModel->getStudentIdCard($studentId);
        
        if ($student) {
            echo json_encode([
                'status' => 'success',
                'student' => $student,
                'id_card_image' => $idCardImage ? '/uoc-sports/app/student_id/' . $idCardImage : null
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Student not found']);
        }
    }

    /**
     * API: Verify a student (approve or reject)
     */
    public function verifyStudent() {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $requestId = $input['request_id'] ?? null;
        $studentId = $input['student_id'] ?? null;
        $status = $input['status'] ?? null; // 'VERIFIED' or 'REJECTED'
        $reason = $input['reason'] ?? null;
        $registrarId = $_SESSION['user_id'] ?? null;
        
        if (!$requestId || !$studentId || !$status || !$registrarId) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            return;
        }
        
        if (!in_array($status, ['VERIFIED', 'REJECTED'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid status']);
            return;
        }
        
        $verificationModel = new VerificationRequest();
        $result = $verificationModel->verifyStudent($requestId, $studentId, $registrarId, $status, $reason);
        
        if ($result) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Student ' . ($status === 'VERIFIED' ? 'verified' : 'rejected') . ' successfully'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update verification status']);
        }
    }

    /**
     * API: Get pending verifications count
     */
    public function getPendingCount() {
        header('Content-Type: application/json');
        
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            echo json_encode(['status' => 'error', 'count' => 0]);
            return;
        }
        
        $verificationModel = new VerificationRequest();
        $pending = $verificationModel->getPendingForRegistrar($userId);
        
        echo json_encode([
            'status' => 'success',
            'count' => count($pending)
        ]);
    }
}