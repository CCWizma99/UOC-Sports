<?php

class SportEquipment {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Get all equipment for a sport with availability details
     */
    public function getEquipmentBySport($sportId) {
        $query = "SELECT 
                    e.equipment_id,
                    e.equipment_name,
                    e.sport_id,
                    e.max_allow,
                    e.category_id,
                    COALESCE(ec.category_name, 'Uncategorized') as category_name,
                    COALESCE(SUM(ei.usable), 0) as usable_count,
                    COALESCE(COUNT(DISTINCT CASE WHEN er.status = 'ACTIVE' THEN er.request_id END), 0) as reserved_count,
                    (COALESCE(SUM(ei.usable), 0) - (COALESCE(COUNT(DISTINCT CASE WHEN er.status = 'ACTIVE' THEN er.request_id END), 0) * e.max_allow)) as available_count,
                    GROUP_CONCAT(DISTINCT CONCAT(er.request_date, ' ', er.start_time, '-', er.end_time, ' @ ', COALESCE(er.reserved_location, 'N/A')) SEPARATOR ', ') as reserved_times
                FROM equipment e
                LEFT JOIN equipment_categories ec ON e.category_id = ec.category_id
                LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id
                LEFT JOIN `equipment-requests` er ON e.equipment_name = er.category_name AND er.status = 'ACTIVE'
                WHERE e.sport_id = ?
                GROUP BY e.equipment_id, e.equipment_name, e.sport_id, e.max_allow, e.category_id, ec.category_name
                ORDER BY ec.category_name, e.equipment_name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sportId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get equipment details by ID
     */
    public function getEquipmentById($equipmentId) {
        $query = "SELECT e.*, 
                    COALESCE(SUM(ei.quantity), 0) as total_quantity,
                    COALESCE(SUM(ei.usable), 0) as usable_count
                FROM equipment e
                LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id
                WHERE e.equipment_id = ?
                GROUP BY e.equipment_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$equipmentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get active reservations for an equipment
     */
    public function getActiveReservations($equipmentId) {
        // Get equipment name for this equipment first
        $nameQuery = "SELECT equipment_name FROM equipment WHERE equipment_id = ?";
        $nameStmt = $this->db->prepare($nameQuery);
        $nameStmt->execute([$equipmentId]);
        $equipmentName = $nameStmt->fetchColumn();
        
        if (!$equipmentName) {
            return [];
        }
        
        $query = "SELECT 
                    er.request_id,
                    er.category_name,
                    er.request_date,
                    er.start_time,
                    er.end_time,
                    er.reserved_location,
                    er.notes,
                    CONCAT(u.fname, ' ', u.lname) as student_name,
                    er.student_id
                FROM `equipment-requests` er
                JOIN user u ON er.student_id = u.user_id AND u.type = 'STUDENT'
                WHERE er.category_name = ? AND er.status = 'ACTIVE'
                ORDER BY er.request_date, er.start_time";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$equipmentName]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update equipment details
     */
    public function updateEquipment($equipmentId, $data) {
        $query = "UPDATE equipment 
                 SET equipment_name = ?, max_allow = ?
                 WHERE equipment_id = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['equipment_name'],
            $data['max_allow'] ?? 1,
            $equipmentId
        ]);
    }

    /**
     * Update inventory usable quantity
     */
    public function updateInventory($equipmentId, $usableCount) {
        $query = "UPDATE equipment_inventory 
                 SET usable = ?
                 WHERE equipment_id = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$usableCount, $equipmentId]);
    }

    /**
     * Delete equipment (with validation)
     */
    public function deleteEquipment($equipmentId) {
        // Get category_id for this equipment
        $catQuery = "SELECT category_id FROM equipment WHERE equipment_id = ?";
        $catStmt = $this->db->prepare($catQuery);
        $catStmt->execute([$equipmentId]);
        $categoryId = $catStmt->fetchColumn();
        
        // Check if equipment has active requests for this category
        $checkQuery = "SELECT COUNT(*) FROM `equipment-requests` 
                      WHERE category_id = ? AND status = 'ACTIVE'";
        $stmt = $this->db->prepare($checkQuery);
        $stmt->execute([$categoryId]);
        
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Cannot delete equipment with active reservations");
        }
        
        try {
            $this->db->beginTransaction();
            
            // Delete inventory first
            $delInvQuery = "DELETE FROM equipment_inventory WHERE equipment_id = ?";
            $this->db->prepare($delInvQuery)->execute([$equipmentId]);
            
            // Delete equipment
            $delEqQuery = "DELETE FROM equipment WHERE equipment_id = ?";
            $this->db->prepare($delEqQuery)->execute([$equipmentId]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Get all sports
     */
    public function getAllSports() {
        $query = "SELECT sport_id, sport_name FROM sport ORDER BY sport_name";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get sport summary statistics
     */
    public function getSportSummary($sportId) {
        $query = "SELECT 
                    COUNT(DISTINCT e.equipment_id) as total_equipment,
                    COALESCE(SUM(ei.quantity), 0) as total_items,
                    COALESCE(SUM(ei.usable), 0) as usable_items,
                    COUNT(DISTINCT CASE WHEN er.status = 'ACTIVE' THEN er.request_id END) as active_reservations
                FROM equipment e
                LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id
                LEFT JOIN `equipment-requests` er ON e.equipment_name = er.category_name
                WHERE e.sport_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sportId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
