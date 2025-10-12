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
}
