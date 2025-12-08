<?php

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
            $sport_id = $_POST['sport_id'] ?? null;
            $quantity = $_POST['quantity'] ?? null;
            $date = $_POST['date'] ?? null;
            $status = $_POST['equipment_condition'] ?? null;
            $remarks = $_POST['remarks'] ?? "";
    
            if (!$sport_id || !$quantity || !$date || !$status) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
                return;
            }
    
            $model = new Equipment();
            $result = $model->addStock($sport_id, $quantity, $date, $status, $remarks);
    
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

        try {
            $equipment_id = $_POST['equipment_id'] ?? '';
            $student_id = $_POST['student_id'] ?? '';
            $date = $_POST['date'] ?? '';
            $start_time = $_POST['start_time'] ?? '';
            $end_time = $_POST['end_time'] ?? '';
            $purpose = $_POST['purpose'] ?? '';
            $notes = $_POST['notes'] ?? '';

            if (empty($equipment_id) || empty($student_id) || empty($date) || empty($start_time) || empty($end_time)) {
                echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
                return;
            }

            if (strtotime($end_time) <= strtotime($start_time)) {
                echo json_encode(['status' => 'error', 'message' => 'End time must be after start time.']);
                return;
            }

            $model = new Equipment();
            if ($model->isTimeOverlapping($equipment_id, $date, $start_time, $end_time)) {
                echo json_encode(['status' => 'error', 'message' => 'Time slot overlaps with an existing reservation.']);
                return;
            }

            $request_id = $model->addReservation($equipment_id, $student_id, $date, $start_time, $end_time, $purpose, $notes);

            echo json_encode(['status' => 'success', 'message' => 'Reservation successful!', 'request_id' => $request_id]);
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
            echo json_encode([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
            return;
        }
    
        $userModel = new User();
        $studentData = $userModel->getStudentId($_SESSION['user_id']);
    
        if (!$studentData || !isset($studentData['student_id'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Student ID not found'
            ]);
            return;
        }
    
        $studentId = $studentData['student_id'];
    
        $reservationModel = new Equipment();
        $equipmentResults = $reservationModel->getReservedItems($studentId);
    
        echo json_encode([
            'status' => 'success',
            'data' => $equipmentResults
        ]);
    }    

    public function cancelReservation() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
            return;
        }

        $userModel = new User();
        $studentId = $userModel->getStudentId($_SESSION['user_id']);
        $reservationId = $_POST['reservation_id'] ?? null;

        if (!$reservationId) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid reservation ID'
            ]);
            return;
        }

        $reservationModel = new Equipment();
        $success = $reservationModel->cancelReservation($reservationId, $studentId);

        echo json_encode([
            'status' => $success ? 'success' : 'error',
            'message' => $success ? 'Reservation cancelled successfully.' : 'Failed to cancel reservation.'
        ]);
    }
}
