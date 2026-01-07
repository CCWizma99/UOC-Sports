<?php
/**
 * VerificationApiController
 * Handles API endpoints for student verification requests
 */
class VerificationApiController {
    
    private $verificationModel;
    private $userModel;

    public function __construct() {
        $this->verificationModel = new VerificationRequest();
        $this->userModel = new User();
    }

    /**
     * Create a new verification request
     * Called by sport manager
     */
    public function createRequest() {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        $sportId = $input['sport_id'] ?? null;
        $studentIds = $input['student_ids'] ?? [];
        $notes = $input['notes'] ?? null;
        $requestingUserId = $_SESSION['user_id'] ?? null;

        // Validation
        if (!$sportId || empty($studentIds) || !$requestingUserId) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Missing required fields'
            ]);
            return;
        }

        // Create the verification request
        $requestId = $this->verificationModel->create($requestingUserId, $sportId, $studentIds, $notes);

        if ($requestId) {
            // Send email notifications to registrars
            $this->sendNotifications($requestId, $requestingUserId, $sportId);

            echo json_encode([
                'status' => 'success',
                'message' => 'Verification request created successfully',
                'request_id' => $requestId
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to create verification request'
            ]);
        }
    }

    /**
     * Get verification requests for the current sport manager
     */
    public function getMyRequests() {
        header('Content-Type: application/json');

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            echo json_encode(['status' => 'error', 'requests' => []]);
            return;
        }

        $requests = $this->verificationModel->getRequestsBySportManager($userId);

        echo json_encode([
            'status' => 'success',
            'requests' => $requests
        ]);
    }

    /**
     * Get unverified students for a sport team
     */
    public function getUnverifiedStudents() {
        header('Content-Type: application/json');

        $sportId = $_GET['sport_id'] ?? null;

        if (!$sportId) {
            echo json_encode(['status' => 'error', 'students' => []]);
            return;
        }

        $db = Database::getConnection();
        
        // Get students in the team
        // Note: sports-team.student_id actually contains user_id values
        $sql = "SELECT 
                    u.user_id, u.student_id, u.fname, u.lname, u.email, u.faculty_id,
                    f.faculty_name,
                    CASE 
                        WHEN EXISTS (
                            SELECT 1 FROM verification_request_students vrs 
                            INNER JOIN verification_requests vr ON vrs.request_id = vr.request_id
                            WHERE vrs.student_id = u.student_id 
                            AND vr.sport_id = :sport_id
                            AND vrs.verification_status = 'VERIFIED'
                        ) THEN 'VERIFIED'
                        WHEN EXISTS (
                            SELECT 1 FROM verification_request_students vrs 
                            INNER JOIN verification_requests vr ON vrs.request_id = vr.request_id
                            WHERE vrs.student_id = u.student_id 
                            AND vr.sport_id = :sport_id2
                            AND vrs.verification_status = 'PENDING'
                        ) THEN 'PENDING'
                        ELSE 'UNVERIFIED'
                    END as verification_status
                FROM `sports-team` st
                INNER JOIN user u ON st.student_id = u.user_id
                LEFT JOIN faculty f ON u.faculty_id = f.faculty_id
                WHERE st.sport_id = :sport_id3
                ORDER BY u.fname, u.lname";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':sport_id' => $sportId,
            ':sport_id2' => $sportId,
            ':sport_id3' => $sportId
        ]);
        
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'students' => $students
        ]);
    }

    /**
     * Send email notifications to registrars
     */
    private function sendNotifications($requestId, $requestingUserId, $sportId) {
        global $smtpKey, $senderEmail;

        // Get faculties involved in this request
        $faculties = $this->verificationModel->getFacultiesForRequest($requestId);
        
        // Get requesting user's name
        $requester = $this->userModel->getUserById($requestingUserId);
        $requesterName = $requester ? $requester['fname'] . ' ' . $requester['lname'] : 'Sport Manager';

        // Get sport name
        $db = Database::getConnection();
        $sportStmt = $db->prepare("SELECT sport_name FROM sport WHERE sport_id = ?");
        $sportStmt->execute([$sportId]);
        $sport = $sportStmt->fetch(PDO::FETCH_ASSOC);
        $sportName = $sport ? $sport['sport_name'] : 'Unknown Sport';

        foreach ($faculties as $faculty) {
            if (empty($faculty['registrar_email'])) {
                continue;
            }

            $this->sendEmail(
                $faculty['registrar_email'],
                'Student Verification Request - ' . $sportName,
                $this->buildEmailContent($requesterName, $sportName, $faculty['faculty_name'])
            );
        }
    }

    /**
     * Build email content for verification request
     */
    private function buildEmailContent($requesterName, $sportName, $facultyName) {
        return "
        <html>
        <body style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto;'>
            <div style='background: linear-gradient(135deg, #111 0%, #4b0082 100%); padding: 20px; border-radius: 10px 10px 0 0;'>
                <h2 style='color: white; margin: 0;'>Student Verification Request</h2>
            </div>
            <div style='padding: 25px; background: #f9f9f9; border: 1px solid #ddd;'>
                <p>Dear Registrar,</p>
                <p><strong>$requesterName</strong> (Sport Manager for <strong>$sportName</strong>) has submitted a verification request for students from the <strong>$facultyName</strong> faculty.</p>
                <p>Please log in to the UOC Sports system to review and verify these students.</p>
                <a href='http://localhost/uoc-sports/public/registrar/verify-students' 
                   style='display: inline-block; background: #4b0082; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; margin-top: 15px;'>
                    View Pending Verifications
                </a>
            </div>
            <div style='padding: 15px; background: #eee; border-radius: 0 0 10px 10px; font-size: 12px; color: #666;'>
                This is an automated message from the UOC Sports E-Portal.
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Send email via Brevo API
     */
    private function sendEmail($to, $subject, $htmlContent) {
        global $smtpKey, $senderEmail;

        if (empty($smtpKey) || empty($senderEmail)) {
            return false;
        }

        $payload = [
            "sender" => [
                "name" => "UOC Sports System",
                "email" => $senderEmail
            ],
            "to" => [
                ["email" => $to]
            ],
            "subject" => $subject,
            "htmlContent" => $htmlContent
        ];

        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
            'api-key: ' . $smtpKey
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        curl_close($ch);

        return true;
    }
}
