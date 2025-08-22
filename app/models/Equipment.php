<?php
class Equipment {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection(); // Your PDO connection
    }

    /**
     * Search equipment by ID or name or category
     * @param string $query
     * @return array
     */
    public function search($query) {
        $sql = "SELECT equipment_id, equipment_name, category, equipment_condition, quantity, image_name
                FROM equipment
                WHERE equipment_id LIKE :query
                   OR equipment_name LIKE :query
                   OR category LIKE :query";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['query' => "%$query%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all equipment
     * @return array
     */
    public function getAll() {
        $sql = "SELECT equipment_id, equipment_name, category, equipment_condition, quantity, image_name
                FROM equipment";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
