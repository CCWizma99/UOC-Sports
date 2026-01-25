<?php

class EquipmentBookigRequest {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Get all equipment requests with filters
     */
    public function getAllRequests($filters = []) {
        try {
            $query = "SELECT 
                        er.request_id,
                        er.student_id,
                        er.category_name,
                        er.equipment_items,
                        er.sport_id,
                        COALESCE(s.sport_name, er.sport_id) as sport_name,
                        er.request_date,
                        er.start_time,
                        er.end_time,
                        COALESCE(er.reserved_location, '') as reserved_location,
                        COALESCE(er.requester_name, CONCAT(u.fname, ' ', u.lname), 'N/A') as student_name,
                        COALESCE(er.notes, '') as notes,
                        er.status,
                        u.email as student_email
                    FROM `equipment-requests` er
                    LEFT JOIN user u ON er.student_id = u.user_id
                    LEFT JOIN sport s ON er.sport_id = s.sport_id
                    WHERE 1=1";
            
            $params = [];
            
            if (!empty($filters['status'])) {
                $query .= " AND er.status = ?";
                $params[] = $filters['status'];
            }
            
            if (!empty($filters['student_id'])) {
                $query .= " AND er.student_id = ?";
                $params[] = $filters['student_id'];
            }
            
            if (!empty($filters['category_name'])) {
                $query .= " AND er.category_name = ?";
                $params[] = $filters['category_name'];
            }
            
            if (!empty($filters['sport_id'])) {
                $query .= " AND er.sport_id = ?";
                $params[] = $filters['sport_id'];
            }
            
            if (!empty($filters['date_from'])) {
                $query .= " AND er.request_date >= ?";
                $params[] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $query .= " AND er.request_date <= ?";
                $params[] = $filters['date_to'];
            }
            
            $query .= " ORDER BY er.request_date DESC, er.start_time DESC";
            
            error_log("EquipmentBookigRequest Query: " . $query);
            error_log("EquipmentBookigRequest Params: " . print_r($params, true));
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("EquipmentBookigRequest Results count: " . count($results));
            if (!empty($results)) {
                error_log("EquipmentBookigRequest First result: " . print_r($results[0], true));
            }
            
            return $results;
        } catch (PDOException $e) {
            error_log("DATABASE ERROR in getAllRequests: " . $e->getMessage());
            error_log("Query: " . $query);
            throw $e;
        }
    }

    /**
     * Get request by ID
     */
    public function getRequestById($requestId) {
        $query = "SELECT 
                    er.*,
                    COALESCE(s.sport_name, er.sport_id) as sport_name,
                    COALESCE(er.requester_name, CONCAT(u.fname, ' ', u.lname), 'N/A') as student_name,
                    u.email as student_email
                FROM `equipment-requests` er
                LEFT JOIN user u ON er.student_id = u.user_id
                LEFT JOIN sport s ON er.sport_id = s.sport_id
                WHERE er.request_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$requestId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create new equipment request
     */
    public function createRequest($data) {
        // Generate unique request ID
        $requestId = 'req_' . substr(uniqid(), -8);
        
        // Prepare equipment_items as JSON if provided
        $equipmentItemsJson = null;
        if (isset($data['equipment_items']) && is_array($data['equipment_items'])) {
            $equipmentItemsJson = json_encode($data['equipment_items']);
        }
        
        $query = "INSERT INTO `equipment-requests` 
                  (request_id, student_id, category_name, equipment_items, sport_id, request_date, start_time, end_time, reserved_location, requester_name, notes, status)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        $result = $stmt->execute([
            $requestId,
            $data['student_id'],
            $data['category_name'] ?? null,
            $equipmentItemsJson,
            $data['sport_id'] ?? null,
            $data['request_date'],
            $data['start_time'],
            $data['end_time'],
            $data['reserved_location'] ?? '',
            $data['requester_name'] ?? '',
            $data['notes'] ?? '',
            $data['status'] ?? 'PENDING'
        ]);
        
        return $result ? $requestId : false;
    }

    /**
     * Update request status
     */
    public function updateStatus($requestId, $status) {
        try {
            $query = "UPDATE `equipment-requests` 
                      SET status = ?
                      WHERE request_id = ?";
            
            error_log("Updating status for request_id: $requestId to status: $status");
            
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([$status, $requestId]);
            
            error_log("Update affected rows: " . $stmt->rowCount());
            
            return $result;
        } catch (PDOException $e) {
            error_log("Database error in updateStatus: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update request details
     */
    public function updateRequest($requestId, $data) {
        $query = "UPDATE `equipment-requests` 
                  SET category_name = ?,
                      sport_id = ?,
                      request_date = ?,
                      start_time = ?,
                      end_time = ?,
                      reserved_location = ?,
                      requester_name = ?,
                      notes = ?
                  WHERE request_id = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['category_name'],
            $data['sport_id'] ?? null,
            $data['request_date'],
            $data['start_time'],
            $data['end_time'],
            $data['reserved_location'] ?? '',
            $data['requester_name'] ?? '',
            $data['notes'] ?? '',
            $requestId
        ]);
    }

    /**
     * Delete request
     */
    public function deleteRequest($requestId) {
        $query = "DELETE FROM `equipment-requests` WHERE request_id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$requestId]);
    }

    /**
     * Check for time conflicts
     */
    public function checkTimeConflict($categoryName, $date, $startTime, $endTime, $excludeRequestId = null) {
        $query = "SELECT COUNT(*) as conflict_count
                  FROM `equipment-requests`
                  WHERE category_name = ?
                  AND request_date = ?
                  AND status IN ('ACTIVE', 'PENDING')
                  AND (
                      (start_time < ? AND end_time > ?) OR
                      (start_time >= ? AND start_time < ?) OR
                      (end_time > ? AND end_time <= ?)
                  )";
        
        $params = [$categoryName, $date, $endTime, $startTime, $startTime, $endTime, $startTime, $endTime];
        
        if ($excludeRequestId) {
            $query .= " AND request_id != ?";
            $params[] = $excludeRequestId;
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['conflict_count'] > 0;
    }

    /**
     * Get requests by student
     */
    public function getRequestsByStudent($studentId) {
        return $this->getAllRequests(['student_id' => $studentId]);
    }

    /**
     * Get statistics
     */
    public function getStatistics() {
        $query = "SELECT 
                    COUNT(*) as total_requests,
                    SUM(CASE WHEN status = 'PENDING' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN status = 'ACTIVE' THEN 1 ELSE 0 END) as active_count,
                    SUM(CASE WHEN status = 'COMPLETED' THEN 1 ELSE 0 END) as completed_count,
                    SUM(CASE WHEN status = 'REJECTED' THEN 1 ELSE 0 END) as rejected_count
                  FROM `equipment-requests`";
        
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all categories
     */
    public function getAllCategories() {
        // First try to get from equipment_categories table
        try {
            $query = "SELECT * FROM equipment_categories ORDER BY category_name";
            $stmt = $this->db->query($query);
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($categories)) {
                return $categories;
            }
        } catch (PDOException $e) {
            // Table doesn't exist, get from equipment-requests table
        }
        
        // Fallback: Get distinct category names from equipment-requests table
        $query = "SELECT DISTINCT category_name 
                  FROM `equipment-requests` 
                  WHERE category_name IS NOT NULL AND category_name != ''
                  ORDER BY category_name";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all sports
     */
    public function getAllSports() {
        $query = "SELECT sport_id, sport_name, sport_category FROM sport ORDER BY sport_name";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get categories by sport
     */
    public function getCategoriesBySport($sportId) {
        $query = "SELECT DISTINCT e.equipment_name as category_name
                  FROM equipment e
                  WHERE e.sport_id = ?
                  ORDER BY e.equipment_name";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sportId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
