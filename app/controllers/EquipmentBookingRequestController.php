<?php

class EquipmentBookingRequestController {

    /**
     * Display all equipment booking requests
     */
    public function index() {
        $filters = [
            'status' => $_GET['status'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null,
            'category_id' => $_GET['category_id'] ?? null,
            'sport_id' => $_GET['sport_id'] ?? null
        ];
        
        try {
            $model = new EquipmentBookigRequest();
            $requests = $model->getAllRequests($filters);
            $categories = $model->getAllCategories();
            $sports = $model->getAllSports();
            $statistics = $model->getStatistics();
            
            // Debug: Log the data
            error_log("Requests count: " . count($requests));
            if (!empty($requests)) {
                error_log("First request: " . print_r($requests[0], true));
            }
            
        } catch (Exception $e) {
            error_log("Error in EquipmentBookingRequestController: " . $e->getMessage());
            $requests = [];
            $categories = [];
            $sports = [];
            $statistics = [
                'total_requests' => 0,
                'pending_count' => 0,
                'active_count' => 0,
                'completed_count' => 0,
                'rejected_count' => 0
            ];
        }
        
        view('equipment-manager/bookingrequests', [
            'requests' => $requests,
            'categories' => $categories,
            'sports' => $sports,
            'statistics' => $statistics,
            'filters' => $filters
        ]);
    }

    /**
     * Get request details (AJAX)
     */
    public function getDetails() {
        header('Content-Type: application/json');
        
        $requestId = $_GET['id'] ?? null;
        
        if (!$requestId) {
            echo json_encode(['success' => false, 'message' => 'Request ID is required']);
            exit();
        }
        
        $model = new EquipmentBookigRequest();
        $request = $model->getRequestById($requestId);
        
        if ($request) {
            echo json_encode(['success' => true, 'request' => $request]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Request not found']);
        }
    }

    /**
     * Create new request (AJAX)
     */
    public function create() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        if (empty($data['student_id']) || empty($data['category_id']) || 
            empty($data['request_date']) || empty($data['start_time']) || empty($data['end_time'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit();
        }
        
        $model = new EquipmentBookigRequest();
        
        // Check if user already has an active or accepted reservation
        $studentId = $data['student_id'] ?? null;
        $requesterName = $data['requester_name'] ?? null;
        
        if ($model->hasActiveReservation([$studentId])) {
            echo json_encode(['success' => false, 'message' => 'This user already has an active or accepted equipment reservation. Please complete or cancel the existing reservation before creating a new one.']);
            exit();
        }
        
        // Check for time conflicts
        $hasConflict = $model->checkTimeConflict(
            $data['category_id'],
            $data['request_date'],
            $data['start_time'],
            $data['end_time']
        );
        
        if ($hasConflict) {
            echo json_encode(['success' => false, 'message' => 'Time slot already booked for this equipment category']);
            exit();
        }
        
        $requestId = $model->createRequest($data);
        
        if ($requestId) {
            echo json_encode(['success' => true, 'message' => 'Request created successfully', 'request_id' => $requestId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create request']);
        }
    }

    /**
     * Check whether user already has an active/accepted reservation (AJAX)
     */
    public function checkActiveReservation() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }

        $studentId = trim($_GET['student_id'] ?? '');
        $requesterName = trim($_GET['requester_name'] ?? '');

        if ($studentId === '' && $requesterName === '') {
            echo json_encode(['success' => true, 'has_active_reservation' => false]);
            exit();
        }

        $model = new EquipmentBookigRequest();
        $hasActiveReservation = $model->hasActiveReservation([$studentId]);

        if ($hasActiveReservation) {
            echo json_encode([
                'success' => true,
                'has_active_reservation' => true,
                'message' => 'This user already has an active or accepted equipment reservation. Please complete or cancel the existing reservation before creating a new one.'
            ]);
        } else {
            echo json_encode(['success' => true, 'has_active_reservation' => false]);
        }
    }

    /**
     * Update request status (AJAX)
     */
    public function updateStatus() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Log the incoming data
        error_log("updateStatus called with data: " . print_r($data, true));
        
        if (empty($data['request_id']) || empty($data['status'])) {
            error_log("updateStatus failed: Missing request_id or status");
            echo json_encode(['success' => false, 'message' => 'Request ID and status are required']);
            exit();
        }
        
        try {
            $model = new EquipmentBookigRequest();
            $result = $model->updateStatus($data['request_id'], $data['status']);
            
            error_log("updateStatus result: " . ($result ? 'success' : 'failed'));
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update status']);
            }
        } catch (Exception $e) {
            error_log("updateStatus exception: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Update request details (AJAX)
     */
    public function update() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['request_id'])) {
            echo json_encode(['success' => false, 'message' => 'Request ID is required']);
            exit();
        }
        
        $model = new EquipmentBookigRequest();
        
        // Check for time conflicts (excluding current request)
        if (!empty($data['category_id']) && !empty($data['request_date']) && 
            !empty($data['start_time']) && !empty($data['end_time'])) {
            
            $hasConflict = $model->checkTimeConflict(
                $data['category_id'],
                $data['request_date'],
                $data['start_time'],
                $data['end_time'],
                $data['request_id']
            );
            
            if ($hasConflict) {
                echo json_encode(['success' => false, 'message' => 'Time slot already booked']);
                exit();
            }
        }
        
        $requestId = $data['request_id'];
        unset($data['request_id']);
        
        $result = $model->updateRequest($requestId, $data);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Request updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update request']);
        }
    }

    /**
     * Delete request (AJAX)
     */
    public function delete() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['request_id'])) {
            echo json_encode(['success' => false, 'message' => 'Request ID is required']);
            exit();
        }
        
        $model = new EquipmentBookigRequest();
        $result = $model->deleteRequest($data['request_id']);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Request deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete request']);
        }
    }

    /**
     * Approve request
     */
    public function approve() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['request_id'])) {
            echo json_encode(['success' => false, 'message' => 'Request ID is required']);
            exit();
        }
        
        $model = new EquipmentBookigRequest();
        $result = $model->updateStatus($data['request_id'], 'ACTIVE');
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Request approved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to approve request']);
        }
    }

    /**
     * Reject request
     */
    public function reject() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['request_id'])) {
            echo json_encode(['success' => false, 'message' => 'Request ID is required']);
            exit();
        }
        
        $model = new EquipmentBookigRequest();
        $result = $model->updateStatus($data['request_id'], 'REJECTED');
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Request rejected']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to reject request']);
        }
    }

    /**
     * Complete request
     */
    public function complete() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['request_id'])) {
            echo json_encode(['success' => false, 'message' => 'Request ID is required']);
            exit();
        }
        
        $model = new EquipmentBookigRequest();
        $result = $model->updateStatus($data['request_id'], 'COMPLETED');
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Request marked as completed']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to complete request']);
        }
    }

    /**
     * Send special notification for a booking request (AJAX)
     */
    public function sendRequestNotification() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['request_id']) || empty($data['student_id']) || empty($data['requester_name']) || empty($data['message'])) {
            echo json_encode(['success' => false, 'message' => 'Request ID, student ID, requester name and message are required']);
            exit();
        }

        try {
            $model = new EquipmentBookigRequest();
            $saved = $model->createRequestNotification([
                'request_id' => trim($data['request_id']),
                'student_id' => trim($data['student_id']),
                'requester_name' => trim($data['requester_name']),
                'message' => trim($data['message'])
            ]);

            if ($saved) {
                echo json_encode(['success' => true, 'message' => 'Notification sent successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send notification']);
            }
        } catch (Exception $e) {
            error_log('Error in sendRequestNotification: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error while sending notification: ' . $e->getMessage()]);
        }
    }

    /**
     * Fetch sent notifications for a booking request (AJAX)
     */
    public function getRequestNotifications() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }

        $requestId = trim($_GET['request_id'] ?? '');
        $studentId = trim($_GET['student_id'] ?? '');
        $requesterName = trim($_GET['requester_name'] ?? '');

        if ($requestId === '' || $studentId === '' || $requesterName === '') {
            echo json_encode(['success' => false, 'message' => 'request_id, student_id and requester_name are required']);
            exit();
        }

        try {
            $model = new EquipmentBookigRequest();
            $notifications = $model->getRequestNotifications($requestId, $studentId, $requesterName);

            echo json_encode([
                'success' => true,
                'notifications' => $notifications
            ]);
        } catch (Exception $e) {
            error_log('Error in getRequestNotifications: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error while fetching notifications: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete one sent notification for a booking request (AJAX)
     */
    public function deleteRequestNotification() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $notificationId = trim($data['notification_id'] ?? '');
        $requestId = trim($data['request_id'] ?? '');
        $studentId = trim($data['student_id'] ?? '');
        $requesterName = trim($data['requester_name'] ?? '');

        if ($notificationId === '' || $requestId === '' || $studentId === '' || $requesterName === '') {
            echo json_encode(['success' => false, 'message' => 'notification_id, request_id, student_id and requester_name are required']);
            exit();
        }

        try {
            $model = new EquipmentBookigRequest();
            $deleted = $model->deleteRequestNotification($notificationId, $requestId, $studentId, $requesterName);

            if ($deleted) {
                echo json_encode(['success' => true, 'message' => 'Notification deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Notification not found or already deleted']);
            }
        } catch (Exception $e) {
            error_log('Error in deleteRequestNotification: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error while deleting notification: ' . $e->getMessage()]);
        }
    }

    /**
     * Get student's requests (for student view)
     */
    public function myRequests() {
        $studentId = $_SESSION['student_id'] ?? null;
        
        if (!$studentId) {
            $_SESSION['error_message'] = 'Student ID not found in session';
            header('Location: /uoc-sports/public/');
            exit();
        }
        
        $model = new EquipmentBookigRequest();
        $requests = $model->getRequestsByStudent($studentId);
        $categories = $model->getAllCategories();
        
        view('student/equipment-requests', [
            'requests' => $requests,
            'categories' => $categories
        ]);
    }
}
