<?php

class SportEquipmentController {

    /**
     * Display equipment list by sport
     */
    public function index() {
        $sportId = $_GET['sport_id'] ?? null;
        
        if (!$sportId) {
            $_SESSION['error_message'] = 'Sport ID is required';
            header('Location: /uoc-sports/public/equipment-manager/equipments');
            exit();
        }

        $equipmentModel = new SportEquipment();
        
        // Get equipment data
        $equipment = $equipmentModel->getEquipmentBySport($sportId);
        
        // Get sport name
        $sportQuery = "SELECT sport_name FROM sport WHERE sport_id = ?";
        $stmt = Database::getConnection()->prepare($sportQuery);
        $stmt->execute([$sportId]);
        $sportData = $stmt->fetch(PDO::FETCH_ASSOC);
        $sportName = $sportData['sport_name'] ?? 'Unknown Sport';
        
        // Get summary
        $summary = $equipmentModel->getSportSummary($sportId);
        
        view('equipment-manager/manage-equipment', [
            'sport' => $sportName,
            'sportId' => $sportId,
            'equipment' => $equipment,
            'summary' => $summary
        ]);
    }

    /**
     * Get equipment details (AJAX)
     */
    public function getDetails() {
        header('Content-Type: application/json');
        
        $equipmentId = $_GET['id'] ?? null;
        
        if (!$equipmentId) {
            echo json_encode(['success' => false, 'message' => 'Missing equipment ID']);
            exit();
        }
        
        $equipmentModel = new SportEquipment();
        $equipment = $equipmentModel->getEquipmentById($equipmentId);
        $reservations = $equipmentModel->getActiveReservations($equipmentId);
        
        if ($equipment) {
            echo json_encode([
                'success' => true,
                'equipment' => $equipment,
                'reservations' => $reservations
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Equipment not found']);
        }
        exit();
    }

    /**
     * Update equipment
     */
    public function update() {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['equipmentId'])) {
            echo json_encode(['success' => false, 'message' => 'Missing equipment ID']);
            exit();
        }
        
        $equipmentModel = new SportEquipment();
        
        try {
            $result = $equipmentModel->updateEquipment($input['equipmentId'], $input);
            
            // Update inventory if usable count provided
            if (isset($input['usable_count'])) {
                $equipmentModel->updateInventory($input['equipmentId'], $input['usable_count']);
            }
            
            echo json_encode(['success' => true, 'message' => 'Equipment updated successfully']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    /**
     * Delete equipment
     */
    public function delete() {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['equipmentId'])) {
            echo json_encode(['success' => false, 'message' => 'Missing equipment ID']);
            exit();
        }
        
        $equipmentModel = new SportEquipment();
        
        try {
            $result = $equipmentModel->deleteEquipment($input['equipmentId']);
            echo json_encode(['success' => true, 'message' => 'Equipment deleted successfully']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }
}
