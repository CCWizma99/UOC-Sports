<?php
require_once __DIR__ . '/../../services/EmailService.php';

class EquipmentApiController {
    public function searchEquipment() {
        header('Content-Type: application/json');
    
        try {
            $q = trim($_GET['q'] ?? "");
    
            if (!$q) {
                echo json_encode(['status' => 'error', 'message' => 'Empty search']);
                return;
            }
    
            $model = new Equipment();
            $results = $model->searchEquipment($q);
    
            echo json_encode([
                'status' => 'success',
                'data' => $results
            ]);
    
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Search failed'
            ]);
        }
    }
    

    public function minimalSearch() {
        header('Content-Type: application/json');
        $query = $_GET['q'] ?? '';

        $equipmentModel = new Equipment();
        $results = $equipmentModel->minimalSearch($query);

        echo json_encode([
            'status' => 'success',
            'data' => $results
        ]);
    }

    public function add() {
        header('Content-Type: application/json');
    
        try {
            $name = $_POST['equipment_name'] ?? '';
            $quantity = $_POST['quantity'] ?? '';
            $date = $_POST['date'] ?? '';
            $remarks = $_POST['remarks'] ?? '';
            $sport_id = $_POST['sport_id'] ?? '';
            $condition = $_POST['equipment_condition'] ?? '';
            $files = $_FILES['images'] ?? null;
    
            if (empty($name) || empty($quantity) || empty($sport_id)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Please fill all required fields.'
                ]);
                return;
            }
    
            $equipmentModel = new Equipment();
            $equipmentId = $equipmentModel->addEquipment($name, $quantity, $date, $remarks, $sport_id, $condition, $files);
    
            echo json_encode([
                'status' => 'success',
                'message' => 'Equipment added successfully!',
                'equipment_id' => $equipmentId
            ]);
    
        } catch (PDOException $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Unexpected error: ' . $e->getMessage()
            ]);
        }
    }
    

    public function getSports() {
        header('Content-Type: application/json');

        try {
            $model = new Equipment();
            $sports = $model->getSports();

            echo json_encode(['status' => 'success', 'data' => $sports]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error loading sports.']);
        }
    }

    public function addEquipmentType() {
        header('Content-Type: application/json');
    
        try {
            $sport_id = $_POST['sport_id'] ?? null;
            $equipment_name = trim($_POST['equipment_name'] ?? "");
    
            if (!$sport_id || !$equipment_name) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
                return;
            }
    
            $image_name = "";
    
            if (!empty($_FILES['image']['name'])) {
    
                $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];
                $maxSize = 2 * 1024 * 1024; // 2MB
    
                $originalName = $_FILES['image']['name'];
                $fileSize = $_FILES['image']['size'];
                $tmpPath = $_FILES['image']['tmp_name'];
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    
                // ❌ Invalid file type
                if (!in_array($extension, $allowedTypes)) {
                    echo json_encode(['status' => 'error', 'message' => 'Only JPG, PNG, JPEG, WEBP allowed']);
                    return;
                }
    
                // ❌ File too large
                if ($fileSize > $maxSize) {
                    echo json_encode(['status' => 'error', 'message' => 'Image must be less than 2MB']);
                    return;
                }
    
                // ✅ Clean equipment name
                $safeName = preg_replace('/[^A-Za-z0-9\-]/', '_', strtolower($equipment_name));
                $random = rand(1000, 9999);
    
                $image_name = $safeName . "_" . $random . "." . $extension;
                $uploadDir = __DIR__ . '/../../../public/images/equipment-types/';
                $finalPath = $uploadDir . $image_name;
    
                if (!move_uploaded_file($tmpPath, $finalPath)) {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to upload image']);
                    return;
                }
            }
    
            $model = new Equipment();
            $result = $model->addEquipmentType($sport_id, $equipment_name, $image_name);
    
            if ($result !== true) {
                if ($image_name && file_exists($finalPath)) {
                    unlink($finalPath);
                }
    
                if ($result === "DUPLICATE") {
                    echo json_encode(['status' => 'error', 'message' => 'Equipment already exists for this sport']);
                    return;
                }
    
                echo json_encode(['status' => 'error', 'message' => 'Database insert failed']);
                return;
            }
    
            echo json_encode(['status' => 'success', 'message' => 'Equipment type added successfully']);
    
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Server error']);
        }
    }
    
    public function getEquipments() {
        header('Content-Type: application/json');
        if (isset($_GET['sport_id'])){
            try {
                $model = new Equipment();
                $equipments = $model->getEquipments($_GET['sport_id']);
                echo json_encode(['status' => 'success', 'data' => $equipments]);
            }  catch (Exception $e) {
                echo json_encode(['status' => 'error', 'message' => 'Error loading equipments.']);
            }
        }
        else{
            echo json_encode(['status' => 'error', 'message' => 'Please select a sport.']);
        }
    }

    public function addStock() {
        header('Content-Type: application/json');
    
        try {
            $equipment_id = $_POST['equipment_id'] ?? null;
            $quantity = $_POST['quantity'] ?? null;
            $date = $_POST['date'] ?? null;
            $remarks = $_POST['remarks'] ?? "";
    
            if (!$equipment_id || !$quantity || !$date) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
                return;
            }
    
            $model = new Equipment();
            $result = $model->addStock($equipment_id, $quantity, $date, $remarks);
    
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Stock added successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add stock']);
            }
    
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Server error']);
        }
    }
    

    public function addReservation() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please sign in.']);
            return;
        }

        try {
            require_once '../app/models/EquipmentBookigRequest.php';
            $bookingModel = new EquipmentBookigRequest();
            $userModel = new User();

            // Fetch student full name for requester_name
            $userProfile = $userModel->getUserProfile($_SESSION['user_id']);
            $requesterName = $userProfile['full_name'] ?? 'N/A';
            $studentId = $userProfile['student_id'] ?? '';

            // Get form data
            $sportId = $_POST['sport'] ?? '';
            $requestDate = $_POST['date'] ?? '';
            $startTime = $_POST['start_time'] ?? '';
            $endTime = $_POST['end_time'] ?? '';
            $reservedLocation = $_POST['reserved_location'] ?? '';
            $notes = $_POST['notes'] ?? '';
            
            // Get selected equipment and quantities
            $selectedEquipment = $_POST['equipment'] ?? [];
            $quantities = $_POST['quantity'] ?? [];

            if (empty($sportId) || empty($requestDate) || empty($startTime) || empty($endTime) || empty($selectedEquipment)) {
                echo json_encode(['status' => 'error', 'message' => 'Please fill all required fields and select at least one equipment item.']);
                return;
            }

            if (strtotime($endTime) <= strtotime($startTime)) {
                echo json_encode(['status' => 'error', 'message' => 'End time must be after start time.']);
                return;
            }

            // Prepare equipment items array with quantities
            $equipmentItems = [];
            foreach ($selectedEquipment as $equipmentName) {
                // In student portal, equipmentName might be the ID if I change the form, 
                // but let's stick to the name for now as the manager does.
                $quantity = isset($quantities[$equipmentName]) ? intval($quantities[$equipmentName]) : 1;
                $equipmentItems[] = [
                    'equipment_name' => $equipmentName,
                    'quantity' => $quantity
                ];
            }

            // Create summary for category_name field (for backward compatibility)
            $categoryNameSummary = implode(', ', array_map(function($item) {
                return $item['equipment_name'] . ' (x' . $item['quantity'] . ')';
            }, $equipmentItems));

            // Prepare request data
            $data = [
                'student_id' => $_SESSION['user_id'],
                'sport_id' => $sportId,
                'request_date' => $requestDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'reserved_location' => $reservedLocation,
                'requester_name' => $requesterName,
                'status' => 'PENDING',
                'category_name' => $categoryNameSummary,
                'equipment_items' => $equipmentItems,
                'notes' => $notes
            ];

            $requestId = $bookingModel->createRequest($data);

            if ($requestId) {
                // Send confirmation email
                try {
                    $emailService = new EmailService();
                    $emailService->sendEquipmentRequestStatusEmail($userProfile['email'], $userProfile['fname'], 'PENDING', [
                        'request_id' => $requestId,
                        'equipment_name' => $categoryNameSummary,
                        'request_date' => $requestDate,
                        'start_time' => $startTime,
                        'end_time' => $endTime
                    ]);
                } catch (Exception $e) {
                    error_log("Failed to send equipment request email: " . $e->getMessage());
                }

                echo json_encode(['status' => 'success', 'message' => 'Booking request created successfully!', 'request_id' => $requestId]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to create booking request.']);
            }

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function getTimes() {
        header('Content-Type: application/json');
        $equipment_id = $_GET['equipment_id'] ?? '';
        $model = new Equipment();
        $times = $model->getReservedTimes($equipment_id);

        echo json_encode(['status' => 'success', 'data' => $times]);
    }

    public function getReservedItems() {
        header('Content-Type: application/json');
    
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }
    
        try {
            require_once '../app/models/EquipmentBookigRequest.php';
            $bookingModel = new EquipmentBookigRequest();
            $userModel = new User();
            $studentData = $userModel->getStudentId($_SESSION['user_id']);
            
            if (!$studentData || !isset($studentData['student_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Student ID not found']);
                return;
            }
            
            $requests = $bookingModel->getRequestsByStudent($_SESSION['user_id']);
            
            // Format for frontend
            $formattedData = array_map(function($req) {
                return [
                    'request_id' => $req['request_id'],
                    'equipment_name' => $req['category_name'] ?: 'Equipment Set',
                    'request_date' => $req['request_date'],
                    'start_time' => $req['start_time'],
                    'end_time' => $req['end_time'],
                    'status' => $req['status'],
                    'image_name' => 'default_equipment.png' // Generic icon as multiple items might be present
                ];
            }, $requests);
            echo json_encode(['status' => 'success', 'data' => $formattedData]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }    

    public function cancelReservation() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        try {
            $requestId = $_POST['request_id'] ?? '';
            $userModel = new User();
            $userProfile = $userModel->getUserProfile($_SESSION['user_id']);
            $studentIdAlphanumeric = $userProfile['student_id'] ?? '';
            $currentUserId = $_SESSION['user_id'];
            
            require_once '../app/models/EquipmentBookigRequest.php';
            $bookingModel = new EquipmentBookigRequest();
            
            // Verify ownership before deletion
            $request = $bookingModel->getRequestById($requestId);
            
            if ($request && ($request['student_id'] === $currentUserId || $request['student_id'] === $studentIdAlphanumeric)) {
                if ($bookingModel->deleteRequest($requestId)) {
                    echo json_encode(['status' => 'success', 'message' => 'Reservation request deleted successfully.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to delete reservation request.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Request not found or access denied.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Get equipment analytics data for admin dashboard
     */
    public function getAnalytics() {
        header('Content-Type: application/json');
        
        try {
            $model = new Equipment();
            $analytics = $model->getAnalytics();
            
            echo json_encode([
                'status' => 'success',
                'data' => $analytics
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to load analytics data'
            ]);
        }
    }

    // ─── Equipment Management Form Endpoints ───

    public function getSuppliers() {
        header('Content-Type: application/json');
        try {
            $model = new EquipmentManagement();
            $suppliers = $model->getSuppliers();
            echo json_encode(['status' => 'success', 'data' => $suppliers]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error loading suppliers']);
        }
    }

    public function getStockEntries() {
        header('Content-Type: application/json');
        try {
            $sportId = $_GET['sport_id'] ?? null;
            $equipmentId = $_GET['equipment_id'] ?? null;
            $model = new EquipmentManagement();
            $entries = $model->getStockEntries($sportId, $equipmentId);
            echo json_encode(['status' => 'success', 'data' => $entries]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error loading stock entries']);
        }
    }

    public function getUsersByType() {
        header('Content-Type: application/json');
        try {
            $type = $_GET['type'] ?? '';
            if (!$type) {
                echo json_encode(['status' => 'error', 'message' => 'Type is required']);
                return;
            }
            $model = new EquipmentManagement();
            $users = $model->getUsersByType($type);
            echo json_encode(['status' => 'success', 'data' => $users]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error loading users']);
        }
    }

    public function addGoodReceivedNote() {
        header('Content-Type: application/json');
        try {
            $required = ['sport_id', 'equipment_id', 'date', 'supplier_id', 'quantity', 'unit', 'unit_price', 'stock_id'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    echo json_encode(['status' => 'error', 'message' => "Missing required field: $field"]);
                    return;
                }
            }
            $model = new EquipmentManagement();
            $result = $model->addGoodReceivedNote($_POST);
            echo json_encode([
                'status' => $result ? 'success' : 'error',
                'message' => $result ? 'Good Received Note added successfully' : 'Failed to add GRN'
            ]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    public function addGoodIssueNote() {
        header('Content-Type: application/json');
        try {
            $required = ['sport_id', 'equipment_id', 'date', 'quantity', 'unit', 'stock_id'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    echo json_encode(['status' => 'error', 'message' => "Missing required field: $field"]);
                    return;
                }
            }
            $model = new EquipmentManagement();
            $result = $model->addGoodIssueNote($_POST);
            echo json_encode([
                'status' => $result ? 'success' : 'error',
                'message' => $result ? 'Good Issue Note added successfully' : 'Failed to add GIN'
            ]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    public function addGoodCondemnNote() {
        header('Content-Type: application/json');
        try {
            $required = ['sport_id', 'equipment_id', 'stock_id', 'quantity'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    echo json_encode(['status' => 'error', 'message' => "Missing required field: $field"]);
                    return;
                }
            }
            $model = new EquipmentManagement();
            $result = $model->addGoodCondemnNote($_POST);
            echo json_encode([
                'status' => $result ? 'success' : 'error',
                'message' => $result ? 'Good Condemn Note added successfully' : 'Failed to add GCN'
            ]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    public function addSport() {
        header('Content-Type: application/json');
        try {
            $name = trim($_POST['sport_name'] ?? '');
            if (!$name) {
                echo json_encode(['status' => 'error', 'message' => 'Sport name is required']);
                return;
            }
            $model = new EquipmentManagement();
            $result = $model->addSport($name);
            if ($result === 'DUPLICATE') {
                echo json_encode(['status' => 'error', 'message' => 'Sport already exists']);
                return;
            }
            echo json_encode(['status' => 'success', 'message' => 'Sport added successfully']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Server error']);
        }
    }

    public function addArticle() {
        header('Content-Type: application/json');
        try {
            $sportId = $_POST['sport_id'] ?? '';
            $name = trim($_POST['article_name'] ?? '');
            if (!$sportId || !$name) {
                echo json_encode(['status' => 'error', 'message' => 'Sport and Article name are required']);
                return;
            }
            $model = new EquipmentManagement();
            $result = $model->addArticle($sportId, $name);
            if ($result === 'DUPLICATE') {
                echo json_encode(['status' => 'error', 'message' => 'Article already exists for this sport']);
                return;
            }
            echo json_encode(['status' => 'success', 'message' => 'Sport Item / Article added successfully']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Server error']);
        }
    }

    public function addSupplier() {
        header('Content-Type: application/json');
        try {
            $required = ['supplier_name', 'address', 'telephone_1'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    echo json_encode(['status' => 'error', 'message' => "Missing required field: $field"]);
                    return;
                }
            }
            $model = new EquipmentManagement();
            $result = $model->addSupplier($_POST);
            if ($result === 'DUPLICATE') {
                echo json_encode(['status' => 'error', 'message' => 'Supplier already exists']);
                return;
            }
            echo json_encode(['status' => 'success', 'message' => 'Supplier added successfully']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Server error']);
        }
    }

    // ─── Report Data (JSON) Endpoints ───

    private function getReportFilters() {
        return [ $_GET['year'] ?? null, $_GET['month'] ?? null ];
    }

    public function reportInventory() {
        header('Content-Type: application/json');
        try {
            [$year, $month] = $this->getReportFilters();
            $model = new EquipmentManagement();
            echo json_encode(['status' => 'success', 'data' => $model->getEquipmentInventoryReport($year, $month)]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to load report']);
        }
    }

    public function reportSuppliers() {
        header('Content-Type: application/json');
        try {
            [$year, $month] = $this->getReportFilters();
            $model = new EquipmentManagement();
            echo json_encode(['status' => 'success', 'data' => $model->getSupplierReport($year, $month)]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to load report']);
        }
    }

    public function reportSnapshot() {
        header('Content-Type: application/json');
        try {
            [$year, $month] = $this->getReportFilters();
            $model = new EquipmentManagement();
            echo json_encode(['status' => 'success', 'data' => $model->getAllEquipmentSnapshot($year, $month)]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to load report']);
        }
    }

    public function reportStock() {
        header('Content-Type: application/json');
        try {
            [$year, $month] = $this->getReportFilters();
            $model = new EquipmentManagement();
            echo json_encode(['status' => 'success', 'data' => $model->getStockSnapshot($year, $month)]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to load report']);
        }
    }

    public function reportPeriod() {
        header('Content-Type: application/json');
        try {
            [$year, $month] = $this->getReportFilters();
            $model = new EquipmentManagement();
            echo json_encode(['status' => 'success', 'data' => $model->getPeriodSnapshot($year, $month)]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to load report']);
        }
    }

    public function reportSupplierDetail() {
        header('Content-Type: application/json');
        try {
            [$year, $month] = $this->getReportFilters();
            $supplierId = $_GET['supplier_id'] ?? null;
            if (!$supplierId) {
                echo json_encode(['status' => 'error', 'message' => 'Supplier ID required']);
                return;
            }
            $model = new EquipmentManagement();
            $data = $model->getSupplierDetailReport($supplierId, $year, $month);
            if (!$data) {
                echo json_encode(['status' => 'error', 'message' => 'Supplier not found']);
                return;
            }
            echo json_encode(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to load report']);
        }
    }
}
