<?php

class Equipment {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Create new equipment
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO equipments (
                equipment_id, category, code, availability_status, 
                reserved_person_name, reserved_person_id, reserved_date, reserved_time_slot, claimed_return
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $this->generateUniqueEquipmentId(),
            $data['category'],
            $data['code'],
            $data['availability_status'] ?? 'Available',
            $data['reserved_person_name'] ?? null,
            $data['reserved_person_id'] ?? null,
            $data['reserved_date'] ?? null,
            $data['reserved_time_slot'] ?? null,
            $data['claimed_return'] ?? null
        ]);
    }

    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM equipments");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 
    
    // Generate unique equipment ID like EQ-1, EQ-2
    private function generateUniqueEquipmentId() {
        $stmt = $this->db->query("SELECT MAX(CAST(SUBSTRING(equipment_id, 4) AS UNSIGNED)) AS max_id FROM equipments");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $nextId = $row['max_id'] ? $row['max_id'] + 1 : 1;
        return "EQ-" . $nextId;
    }


}
