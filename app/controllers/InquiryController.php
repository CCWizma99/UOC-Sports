<?php
class InquiryController extends BaseController {

    // Load main search page
    public function index() {
        view('inquiry/search');
    }

    // API endpoint for live search
    public function search() {
        $query = trim($_GET['q'] ?? '');
        $inquiryModel = new Inquiry();

        if (empty($query)) {
            echo json_encode([]);
            return;
        }

        $results = $inquiryModel->search($query);
        echo json_encode($results);
    }

    /**
     * Handle contact form submission from homepage
     */
    public function submit() {
        header('Content-Type: application/json');

        try {
            // Validate input
            $email = trim($_POST['email'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $message = trim($_POST['message'] ?? '');

            if (empty($email) || empty($subject) || empty($message)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'All fields are required.'
                ]);
                return;
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid email address.'
                ]);
                return;
            }

            // Check if user is authenticated
            $userId = $_SESSION['user_id'] ?? 'GUEST';

            // Create inquiry
            $inquiryModel = new Inquiry();
            $data = [
                'user_id' => $userId,
                'email' => $email,
                'subject' => $subject,
                'message' => $message
            ];

            // Get the inquiry ID before creating (we need to modify the create method to return it)
            $inquiryId = $this->createInquiryAndGetId($inquiryModel, $data);

            if ($inquiryId) {
                // Send confirmation email
                $emailService = new EmailService();
                $emailResult = $emailService->sendInquiryConfirmation($email, $subject, $inquiryId);

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Your inquiry has been submitted successfully! A confirmation email has been sent to ' . $email,
                    'inquiry_id' => $inquiryId,
                    'email_sent' => $emailResult['status'] === 'success'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to submit inquiry. Please try again.'
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Helper method to create inquiry and return the ID
     */
    private function createInquiryAndGetId($inquiryModel, $data) {
        // Generate ID
        $inquiryId = 'INQ' . strtoupper(substr(uniqid(), -9));
        
        // Prepare data with ID
        $dataWithId = array_merge($data, ['inquiry_id' => $inquiryId]);
        
        // Create using modified approach
        $sql = "
            INSERT INTO inquiry (inquiry_id, user_id, email, subject, message, date, status)
            VALUES (:inquiry_id, :user_id, :email, :subject, :message, :date, :status)
        ";

        $db = Database::getConnection();
        $stmt = $db->prepare($sql);
        $success = $stmt->execute([
            'inquiry_id' => $inquiryId,
            'user_id' => $data['user_id'] ?? 'GUEST',
            'email' => $data['email'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'date' => date('Y-m-d'),
            'status' => 'NOT-RESOLVED'
        ]);

        return $success ? $inquiryId : false;
    }

    /**
     * Get all inquiries (admin only)
     */
    public function getAll() {
        header('Content-Type: application/json');

        try {
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
            $inquiryModel = new Inquiry();
            $inquiries = $inquiryModel->getAll($limit);

            echo json_encode([
                'status' => 'success',
                'data' => $inquiries
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to fetch inquiries: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get inquiry details by ID (admin only)
     */
    public function getDetails($id) {
        header('Content-Type: application/json');

        try {
            $inquiryModel = new Inquiry();
            $inquiry = $inquiryModel->getById($id);

            if ($inquiry) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $inquiry
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Inquiry not found.'
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to fetch inquiry: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update inquiry status (admin only)
     */
    public function updateStatus() {
        header('Content-Type: application/json');

        try {
            $inquiryId = trim($_POST['inquiry_id'] ?? '');
            $status = trim($_POST['status'] ?? '');

            if (empty($inquiryId) || empty($status)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Inquiry ID and status are required.'
                ]);
                return;
            }

            // Validate status
            if (!in_array($status, ['RESOLVED', 'NOT-RESOLVED'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid status value.'
                ]);
                return;
            }

            $inquiryModel = new Inquiry();
            $success = $inquiryModel->updateStatus($inquiryId, $status);

            if ($success) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Inquiry status updated successfully.'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to update inquiry status.'
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete an inquiry (admin only)
     */
    public function delete() {
        header('Content-Type: application/json');

        try {
            $inquiryId = trim($_POST['inquiry_id'] ?? '');

            if (empty($inquiryId)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Inquiry ID is required.'
                ]);
                return;
            }

            $inquiryModel = new Inquiry();
            $success = $inquiryModel->delete($inquiryId);

            if ($success) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Inquiry deleted successfully.'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to delete inquiry.'
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
}
