<?php

class EquipmentApiController {
    public function search() {
        header('Content-Type: application/json');
        $query = $_GET['q'] ?? '';

        $equipmentModel = new Equipment();
        $results = $equipmentModel->search($query);

        echo json_encode([
            'status' => 'success',
            'data' => $results
        ]);
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
        $results = $reservationModel->getReservedItems($studentId);
    
        echo json_encode([
            'status' => 'success',
            'data' => $results
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
