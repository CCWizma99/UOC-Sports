<?php

class SportPracticeSessionController {
    
    /**
     * Display list of practice sessions
     */
    public function index() {
        $model = new SportPracticeSession();
        $userId = $_SESSION['user_id'] ?? null;
        
        // Get selected sport from URL parameter
        $selectedSportId = $_GET['sport'] ?? null;
        
        // If no sport selected, get the first managed sport as default
        if (!$selectedSportId && $userId) {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT s.sport_id FROM manager_sport ms
                                  JOIN sport s ON ms.sport_id = s.sport_id
                                  WHERE ms.user_id = ?
                                  ORDER BY s.sport_name LIMIT 1");
            $stmt->execute([$userId]);
            $selectedSportId = $stmt->fetchColumn();
        }
        
        // Filter by sport if selected
        $filters = [];
        if ($selectedSportId) {
            $filters['sport_id'] = $selectedSportId;
        }
        
        $sessions = $model->getAll($filters);
        view('sports-manager/practicesessions', [
            'sessions' => $sessions,
            'selectedSportId' => $selectedSportId
        ]);
    }

    /**
     * Show add practice session form
     */
    public function create() {
        $model = new SportPracticeSession();
        $sports = $model->getAllSports();
        $selectedSport = $_GET['sport'] ?? null;
        view('sports-manager/add-practice', ['sports' => $sports, 'selectedSport' => $selectedSport]);
    }

    /**
     * Save new practice session
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = 'Invalid request method';
            $sportParam = isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '';
            header('Location: /uoc-sports/public/sport-manager/add-practice' . $sportParam);
            exit();
        }

        // Get sport parameter to preserve in redirects
        $sportParam = isset($_POST['sport_param']) ? '?sport=' . urlencode($_POST['sport_param']) : (isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '');

        try {
            $model = new SportPracticeSession();

            // Prepare data
            $data = [
                'sport_name' => $_POST['sport'] ?? '',
                'facility' => '',  // Can be empty
                'location' => $_POST['location'] ?? '',
                'session_date' => $_POST['date'] ?? '',
                'start_time' => $_POST['stime'] ?? '',
                'end_time' => $_POST['etime'] ?? '',
                'notes' => $_POST['notes'] ?? '',
                'need_equipment' => $_POST['need_equipment'] ?? 'No',
                'added_by' => $_SESSION['user_type'] ?? 'MANAGER',
                'status' => 'ACTIVE'
            ];

            // Validate required fields
            if (empty($data['sport_name']) || empty($data['session_date']) || 
                empty($data['start_time']) || empty($data['end_time'])) {
                $_SESSION['error_message'] = 'Please fill in all required fields';
                header('Location: /uoc-sports/public/sport-manager/add-practice' . $sportParam);
                exit();
            }

            // Check for time conflicts
            $hasConflict = $model->checkTimeConflict(
                $data['location'],
                $data['session_date'],
                $data['start_time'],
                $data['end_time']
            );

            if ($hasConflict) {
                $_SESSION['error_message'] = 'This facility is already booked for the selected date and time';
                header('Location: /uoc-sports/public/sport-manager/add-practice' . $sportParam);
                exit();
            }

            // Create the session
            $sessionId = $model->create($data);

            if ($sessionId) {
                $_SESSION['success_message'] = 'Practice session created successfully!';
                header('Location: /uoc-sports/public/sport-manager/practicesessions' . $sportParam);
            } else {
                $_SESSION['error_message'] = 'Failed to create practice session';
                header('Location: /uoc-sports/public/sport-manager/add-practice' . $sportParam);
            }

        } catch (Exception $e) {
            error_log("Error creating practice session: " . $e->getMessage());
            $_SESSION['error_message'] = 'An error occurred: ' . $e->getMessage();
            header('Location: /uoc-sports/public/sport-manager/add-practice');
        }
        exit();
    }

    /**
     * Show edit form
     */
    public function edit() {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $_SESSION['error_message'] = 'Invalid practice session';
            $sportParam = isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '';
            header('Location: /uoc-sports/public/sport-manager/practicesessions' . $sportParam);
            exit();
        }

        $model = new SportPracticeSession();
        $session = $model->getById($id);
        $sports = $model->getAllSports();

        if (!$session) {
            $_SESSION['error_message'] = 'Practice session not found';
            $sportParam = isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '';
            header('Location: /uoc-sports/public/sport-manager/practicesessions' . $sportParam);
            exit();
        }

        view('sports-manager/edit-practice', ['session' => $session, 'sports' => $sports, 'selectedSport' => $_GET['sport'] ?? null]);
    }

    /**
     * Update practice session
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = 'Invalid request method';
            header('Location: /uoc-sports/public/sport-manager/practicesessions');
            exit();
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            $_SESSION['error_message'] = 'Invalid practice session';
            header('Location: /uoc-sports/public/sport-manager/practicesessions');
            exit();
        }

        try {
            $model = new SportPracticeSession();

            $data = [
                'sport_name' => $_POST['sport'] ?? '',
                'facility' => '',
                'location' => $_POST['location'] ?? '',
                'session_date' => $_POST['date'] ?? '',
                'start_time' => $_POST['stime'] ?? '',
                'end_time' => $_POST['etime'] ?? '',
                'notes' => $_POST['notes'] ?? '',
                'need_equipment' => $_POST['need_equipment'] ?? 'No',
                'status' => $_POST['status'] ?? 'ACTIVE'
            ];

            $result = $model->update($id, $data);

            if ($result) {
                $_SESSION['success_message'] = 'Practice session updated successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to update practice session';
            }

        } catch (Exception $e) {
            error_log("Error updating practice session: " . $e->getMessage());
            $_SESSION['error_message'] = 'An error occurred: ' . $e->getMessage();
        }

        // Preserve sport filter if it exists
        $sportParam = isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '';
        header('Location: /uoc-sports/public/sport-manager/practicesessions' . $sportParam);
        exit();
    }

    /**
     * Delete practice session
     */
    public function delete() {
        $id = $_POST['id'] ?? $_GET['id'] ?? null;

        if (!$id) {
            $_SESSION['error_message'] = 'Invalid practice session';
            header('Location: /uoc-sports/public/sport-manager/practicesessions');
            exit();
        }

        try {
            $model = new SportPracticeSession();
            $result = $model->delete($id);

            if ($result) {
                $_SESSION['success_message'] = 'Practice session deleted successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to delete practice session';
            }

        } catch (Exception $e) {
            error_log("Error deleting practice session: " . $e->getMessage());
            $_SESSION['error_message'] = 'An error occurred while deleting the practice session';
        }

        // Preserve sport filter if it exists
        $sportParam = isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '';
        header('Location: /uoc-sports/public/sport-manager/practicesessions' . $sportParam);
        exit();
    }

    /**
     * Update practice session status via AJAX
     */
    public function updateStatus() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }

        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$id || !$status) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit();
        }

        // Validate status value
        $validStatuses = ['ACTIVE', 'ACCEPTED', 'CANCELED', 'PENDING'];
        if (!in_array($status, $validStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Invalid status value']);
            exit();
        }

        try {
            $model = new SportPracticeSession();
            $result = $model->updateStatus($id, $status);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update status']);
            }

        } catch (Exception $e) {
            error_log("Error updating practice session status: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
        exit();
    }
}
