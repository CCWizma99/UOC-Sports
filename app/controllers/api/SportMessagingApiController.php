<?php
require_once __DIR__ . '/../../services/EmailService.php';

class SportMessagingApiController {
    
    public function sendMassEmail() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $subject = $data['subject'] ?? '';
        $message = $data['message'] ?? '';
        $sportId = $data['sport_id'] ?? '';

        if (empty($subject) || empty($message) || empty($sportId)) {
            echo json_encode(['status' => 'error', 'message' => 'Subject, message, and sport ID are required']);
            return;
        }

        try {
            // Get all students for this sport
            require_once __DIR__ . '/../../models/TournamentParticipant.php';
            $tpModel = new TournamentParticipant();
            $students = $tpModel->getStudentsBySport($sportId);

            if (empty($students)) {
                echo json_encode(['status' => 'error', 'message' => 'No active students found for this sport']);
                return;
            }

            $emailService = new EmailService();
            $successCount = 0;
            $failCount = 0;

            foreach ($students as $student) {
                if (!empty($student['email'])) {
                    $result = $emailService->sendMassEmail($student['email'], $student['first_name'], $subject, $message);
                    if ($result) {
                        $successCount++;
                    } else {
                        $failCount++;
                    }
                }
            }

            echo json_encode([
                'status' => 'success', 
                'message' => "Email sent to $successCount students." . ($failCount > 0 ? " Failed to send to $failCount students." : ""),
                'success_count' => $successCount,
                'fail_count' => $failCount
            ]);

        } catch (Exception $e) {
            error_log("Error in sendMassEmail: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Internal server error: ' . $e->getMessage()]);
        }
    }
}
