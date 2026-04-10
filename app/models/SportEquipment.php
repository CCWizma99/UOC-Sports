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
        // First get all equipment for the sport with inventory data
        $query = "SELECT 
                    e.equipment_id,
                    e.equipment_name,
                    e.sport_id,
                    e.max_allow,
                    ec.category_id,
                    COALESCE(ec.category_name, 'Uncategorized') as category_name,
                    COALESCE(SUM(ei.quantity), 0) as quantity,
                    COALESCE(SUM(ei.usable), 0) as usable_count
                FROM equipment e
                LEFT JOIN equipment_categories ec ON e.category_id = ec.category_id
                LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id
                WHERE e.sport_id = ?
                GROUP BY e.equipment_id, e.equipment_name, e.sport_id, e.max_allow, ec.category_id, ec.category_name
                ORDER BY ec.category_name, e.equipment_name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sportId]);
        $equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Now get active booking requests for this sport
        // Match by sport_id (for new requests) OR by equipment name (for legacy requests)
        $requestQuery = "SELECT DISTINCT
                            er.request_id,
                            er.equipment_items,
                            er.category_name,
                            er.request_date,
                            er.start_time,
                            er.end_time,
                            er.reserved_location
                        FROM `equipment-requests` er
                        WHERE er.status = 'ACTIVE' 
                        AND (er.sport_id = ? OR er.category_name IN (
                            SELECT e.equipment_name 
                            FROM equipment e 
                            WHERE e.sport_id = ?
                        ))";
        
        $requestStmt = $this->db->prepare($requestQuery);
        $requestStmt->execute([$sportId, $sportId]);
        $activeRequests = $requestStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate reserved quantities and locations for each equipment
        foreach ($equipment as &$item) {
            $reservedQuantity = 0;
            $reservedCount = 0;
            $reservedTimes = [];
            
            foreach ($activeRequests as $request) {
                $matchFound = false;
                $qty = 1; // Default quantity
                
                // Try to parse equipment_items JSON first (new format)
                if (!empty($request['equipment_items'])) {
                    $items = json_decode($request['equipment_items'], true);
                    if (is_array($items)) {
                        foreach ($items as $equipmentItem) {
                            if (isset($equipmentItem['equipment_name']) && 
                                $equipmentItem['equipment_name'] === $item['equipment_name']) {
                                $qty = isset($equipmentItem['quantity']) ? (int)$equipmentItem['quantity'] : 1;
                                $matchFound = true;
                                break;
                            }
                        }
                    }
                }
                
                // Fallback to category_name for old/legacy requests
                if (!$matchFound && !empty($request['category_name'])) {
                    if ($request['category_name'] === $item['equipment_name']) {
                        $qty = 1; // Legacy requests don't have quantity info
                        $matchFound = true;
                    }
                }
                
                // If this request matches the equipment, add to reservations
                if ($matchFound) {
                    $reservedQuantity += $qty;
                    $reservedCount++;
                    
                    $location = !empty($request['reserved_location']) ? $request['reserved_location'] : 'N/A';
                    $reservedTimes[] = $request['request_date'] . ' ' . 
                                       substr($request['start_time'], 0, 5) . '-' . 
                                       substr($request['end_time'], 0, 5) . ' @ ' . 
                                       $location . ' (Qty: ' . $qty . ')';
                }
            }
            
            $item['reserved_count'] = $reservedCount;
            $item['reserved_quantity'] = $reservedQuantity;
            $item['available_count'] = max(0, $item['usable_count'] - $reservedQuantity);
            $item['reserved_times'] = !empty($reservedTimes) ? implode('; ', $reservedTimes) : '';
        }
        
        return $equipment;
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
        // Get equipment name for this equipment
        $nameQuery = "SELECT equipment_name FROM equipment WHERE equipment_id = ?";
        $nameStmt = $this->db->prepare($nameQuery);
        $nameStmt->execute([$equipmentId]);
        $equipmentName = $nameStmt->fetchColumn();
        
        // Check if equipment has active requests
        $checkQuery = "SELECT COUNT(*) FROM `equipment-requests` 
                      WHERE category_name = ? AND status = 'ACTIVE'";
        $stmt = $this->db->prepare($checkQuery);
        $stmt->execute([$equipmentName]);
        
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
        // Get basic equipment stats
        $query = "SELECT 
                    COUNT(DISTINCT e.equipment_id) as total_equipment,
                    COALESCE(SUM(ei.quantity), 0) as total_items,
                    COALESCE(SUM(ei.usable), 0) as usable_items
                FROM equipment e
                LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id
                WHERE e.sport_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sportId]);
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get active reservations count for this sport  
        $reservationQuery = "SELECT COUNT(*) as active_reservations 
                            FROM `equipment-requests` 
                            WHERE status = 'ACTIVE' AND sport_id = ?";
        
        $reservationStmt = $this->db->prepare($reservationQuery);
        $reservationStmt->execute([$sportId]);
        $reservationData = $reservationStmt->fetch(PDO::FETCH_ASSOC);
        
        $summary['active_reservations'] = $reservationData['active_reservations'] ?? 0;
        
        return $summary;
    }
}
