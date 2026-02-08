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
        
        if ($model->hasActiveReservation($studentId, $requesterName)) {
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
