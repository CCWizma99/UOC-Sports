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
}
