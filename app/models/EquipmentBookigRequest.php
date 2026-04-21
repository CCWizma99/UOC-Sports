<?php

class EquipmentBookigRequest {
    private $db;

    private const NEED_EQUIPMENT_TRUE_VALUES = ['YES', 'TRUE', '1'];

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
                        COALESCE(er.requester_name, CONCAT(u.fname, ' ', u.lname), 'N/A') as requester_name,
                        COALESCE(er.requester_name, CONCAT(u.fname, ' ', u.lname), 'N/A') as student_name,
                        COALESCE(er.notes, '') as notes,
                        er.status,
                        u.email as student_email
                    FROM `equipment-requests` er
                    LEFT JOIN user u ON er.student_id = u.user_id
                    LEFT JOIN sport s ON er.sport_id = s.sport_id
                    WHERE 1=1 ";
            
            $params = [];
            
            if (!empty($filters['status'])) {
                $query .= " AND er.status = ?";
                $params[] = $filters['status'];
            }
            
            if (!empty($filters['student_id'])) {
                $userId = $filters['user_id'] ?? $filters['student_id'];
                $query .= " AND (er.student_id = ? OR er.student_id = ?)";
                $params[] = $filters['student_id'];
                $params[] = $userId;
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
            
            $stmt = $this->db->prepare($query);
            
            // Bind parameters
            $paramIndex = 1;
            if (!empty($filters['status'])) {
                $stmt->bindValue($paramIndex++, $filters['status'], PDO::PARAM_STR);
            }
            if (!empty($filters['student_id'])) {
                $userId = $filters['user_id'] ?? $filters['student_id'];
                $stmt->bindValue($paramIndex++, $filters['student_id'], PDO::PARAM_STR);
                $stmt->bindValue($paramIndex++, $userId, PDO::PARAM_STR);
            }
            if (!empty($filters['category_name'])) {
                $stmt->bindValue($paramIndex++, $filters['category_name'], PDO::PARAM_STR);
            }
            if (!empty($filters['sport_id'])) {
                $stmt->bindValue($paramIndex++, $filters['sport_id'], PDO::PARAM_STR);
            }
            if (!empty($filters['date_from'])) {
                $stmt->bindValue($paramIndex++, $filters['date_from'], PDO::PARAM_STR);
            }
            if (!empty($filters['date_to'])) {
                $stmt->bindValue($paramIndex++, $filters['date_to'], PDO::PARAM_STR);
            }

            if (isset($filters['limit']) && isset($filters['offset'])) {
                $queryWithLimit = $query . " LIMIT ? OFFSET ?";
                $stmt = $this->db->prepare($queryWithLimit);
                
                // RE-BIND ALL because we re-prepared
                $paramIndex = 1;
                if (!empty($filters['status'])) $stmt->bindValue($paramIndex++, $filters['status'], PDO::PARAM_STR);
                if (!empty($filters['student_id'])) {
                    $stmt->bindValue($paramIndex++, $filters['student_id'], PDO::PARAM_STR);
                    $stmt->bindValue($paramIndex++, $userId, PDO::PARAM_STR);
                }
                if (!empty($filters['category_name'])) $stmt->bindValue($paramIndex++, $filters['category_name'], PDO::PARAM_STR);
                if (!empty($filters['sport_id'])) $stmt->bindValue($paramIndex++, $filters['sport_id'], PDO::PARAM_STR);
                if (!empty($filters['date_from'])) $stmt->bindValue($paramIndex++, $filters['date_from'], PDO::PARAM_STR);
                if (!empty($filters['date_to'])) $stmt->bindValue($paramIndex++, $filters['date_to'], PDO::PARAM_STR);
                
                $stmt->bindValue($paramIndex++, (int)$filters['limit'], PDO::PARAM_INT);
                $stmt->bindValue($paramIndex++, (int)$filters['offset'], PDO::PARAM_INT);
            }
            
            error_log("EquipmentBookigRequest Final Query: " . $stmt->queryString);
            
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("EquipmentBookigRequest Results count: " . count($results));
            
            return $results;
        } catch (PDOException $e) {
            error_log("DATABASE ERROR in getAllRequests: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get request by ID
     */
    public function getRequestById($requestId) {
        $query = "SELECT 
                    er.*
                    
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
        // Prepare equipment_items as JSON if provided
        $equipmentItemsJson = null;
        if (isset($data['equipment_items']) && is_array($data['equipment_items'])) {
            $equipmentItemsJson = json_encode($data['equipment_items']);
        }
        
        $query = "UPDATE `equipment-requests` 
                  SET student_id = ?,
                      category_name = ?,
                      equipment_items = ?,
                      sport_id = ?,
                      request_date = ?,
                      start_time = ?,
                      end_time = ?,
                      reserved_location = ?,
                      requester_name = ?,
                      notes = ?,
                      status = ?
                  WHERE request_id = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['student_id'] ?? null,
            $data['category_name'],
            $equipmentItemsJson,
            $data['sport_id'] ?? null,
            $data['request_date'],
            $data['start_time'],
            $data['end_time'],
            $data['reserved_location'] ?? '',
            $data['requester_name'] ?? '',
            $data['notes'] ?? '',
            $data['status'] ?? 'PENDING',
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
     * Check if user has any active or accepted reservations
     */
    public function hasActiveReservation($ids = []) {
        if (empty($ids)) {
            return false;
        }

        // Filter out empty IDs
        $ids = array_filter($ids);
        if (empty($ids)) return false;

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $query = "SELECT COUNT(*) as active_count
                  FROM `equipment-requests`
                  WHERE status IN ('ACTIVE', 'ACCEPTED')
                  AND student_id IN ($placeholders)";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute(array_values($ids));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['active_count'] > 0;
    }

    /**
     * Check for time conflicts
     */
    public function checkTimeConflict($categoryName, $date, $startTime, $endTime, $excludeRequestId = null) {
        $query = "SELECT COUNT(*) as conflict_count
                  FROM `equipment-requests`
                  WHERE category_name = ?
                  AND request_date = ?
                  AND status IN ('ACTIVE', 'ACCEPTED', 'PENDING')
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
     * Detect conflicts for selected equipment items in a given time slot.
     * Returns an array of conflict records; empty array means no conflicts.
     */
    public function getItemConflicts($sportId, $date, $startTime, $endTime, array $equipmentItems, $excludeRequestId = null, $includePractice = true) {
        if (empty($sportId) || empty($date) || empty($startTime) || empty($endTime) || empty($equipmentItems)) {
            return [];
        }

        $sportId = strtoupper(trim($sportId));
        $selectedNames = [];
        foreach ($equipmentItems as $item) {
            if (!empty($item['equipment_name'])) {
                $selectedNames[] = strtolower(trim($item['equipment_name']));
            }
        }
        $selectedNames = array_values(array_unique($selectedNames));
        if (empty($selectedNames)) {
            return [];
        }

        $extractRequestedQty = static function (array $row, string $equipmentName): int {
            $target = strtolower(trim($equipmentName));

            if (!empty($row['equipment_items'])) {
                $items = json_decode($row['equipment_items'], true);
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $itemName = strtolower(trim((string)($item['equipment_name'] ?? '')));
                        if ($itemName === $target) {
                            $qty = (int)($item['quantity'] ?? 1);
                            return $qty > 0 ? $qty : 1;
                        }
                    }
                }
            }

            $category = strtolower(trim((string)($row['category_name'] ?? '')));
            if ($category !== '' && strpos($category, $target) !== false) {
                if (preg_match('/\(\s*x\s*(\d+)\s*\)/i', (string)$row['category_name'], $m)) {
                    $qty = (int)$m[1];
                    return $qty > 0 ? $qty : 1;
                }
                return 1;
            }

            return 0;
        };

        $query = "SELECT request_id, requester_name, start_time, end_time, status, equipment_items, category_name
                  FROM `equipment-requests`
                  WHERE UPPER(sport_id) = ?
                    AND request_date = ?
                    AND status IN ('PENDING', 'ACCEPTED', 'ACTIVE')
                    AND (
                        (start_time < ? AND end_time > ?)
                        OR (start_time >= ? AND start_time < ?)
                        OR (end_time > ? AND end_time <= ?)
                    )";

        $params = [$sportId, $date, $endTime, $startTime, $startTime, $endTime, $startTime, $endTime];
        if (!empty($excludeRequestId)) {
            $query .= " AND request_id != ?";
            $params[] = $excludeRequestId;
        }
        $query .= " ORDER BY start_time";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $conflicts = [];
        foreach ($rows as $row) {
            foreach ($selectedNames as $name) {
                $qty = $extractRequestedQty($row, $name);
                if ($qty <= 0) {
                    continue;
                }
                $conflicts[] = [
                    'source' => 'booking',
                    'equipment_name' => $name,
                    'request_id' => $row['request_id'] ?? '',
                    'requester_name' => $row['requester_name'] ?? 'Booking',
                    'status' => $row['status'] ?? '',
                    'start_time' => $row['start_time'] ?? '',
                    'end_time' => $row['end_time'] ?? '',
                    'requested_quantity' => $qty,
                ];
            }
        }

        if ($includePractice) {
            $needEquipmentPlaceholders = implode(',', array_fill(0, count(self::NEED_EQUIPMENT_TRUE_VALUES), '?'));
            $practiceQuery = "SELECT id, start_time, end_time, status
                              FROM practice_sessions
                              WHERE UPPER(sport_id) = ?
                                AND session_date = ?
                                AND UPPER(COALESCE(need_equipment, '')) IN ($needEquipmentPlaceholders)
                                AND status IN ('PENDING', 'ACCEPTED', 'ACTIVE')
                                AND (
                                    (start_time < ? AND end_time > ?)
                                    OR (start_time >= ? AND start_time < ?)
                                    OR (end_time > ? AND end_time <= ?)
                                )
                              ORDER BY start_time";
            $practiceParams = array_merge(
                [$sportId, $date],
                self::NEED_EQUIPMENT_TRUE_VALUES,
                [$endTime, $startTime, $startTime, $endTime, $startTime, $endTime]
            );
            $practiceStmt = $this->db->prepare($practiceQuery);
            $practiceStmt->execute($practiceParams);
            $practiceRows = $practiceStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($practiceRows as $practice) {
                foreach ($selectedNames as $name) {
                    $conflicts[] = [
                        'source' => 'practice',
                        'equipment_name' => $name,
                        'request_id' => 'PS-' . ($practice['id'] ?? ''),
                        'requester_name' => 'Practice Session',
                        'status' => $practice['status'] ?? '',
                        'start_time' => $practice['start_time'] ?? '',
                        'end_time' => $practice['end_time'] ?? '',
                        'requested_quantity' => null,
                    ];
                }
            }
        }

        return $conflicts;
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
                    SUM(CASE WHEN status = 'ACCEPTED' THEN 1 ELSE 0 END) as accepted_count,
                    SUM(CASE WHEN status = 'ACTIVE' THEN 1 ELSE 0 END) as active_count,
                    SUM(CASE WHEN status = 'COMPLETED' THEN 1 ELSE 0 END) as completed_count,
                    SUM(CASE WHEN status = 'REJECTED' THEN 1 ELSE 0 END) as rejected_count
                  FROM `equipment-requests`";
        
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get total count of requests with filters
     */
    public function getTotalCount($filters = []) {
        try {
            $query = "SELECT COUNT(*) as total FROM `equipment-requests` er WHERE 1=1 ";
            $params = [];
            
            if (!empty($filters['status'])) {
                $query .= " AND er.status = ?";
                $params[] = $filters['status'];
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
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("DATABASE ERROR in getTotalCount: " . $e->getMessage());
            return 0;
        }
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

    /**
     * Save a special notification for a booking request
     */
    public function createRequestNotification($data) {
        $notificationId = 'ern_' . substr(uniqid('', true), -10);

        $query = "INSERT INTO equipment_request_notifications
                (notification_id, request_id, student_id, requester_name, message)
                  SELECT ?, er.request_id, er.student_id,
                         COALESCE(er.requester_name, CONCAT(u.fname, ' ', u.lname), ''),
                    ?
                  FROM `equipment-requests` er
                  LEFT JOIN user u ON er.student_id = u.user_id
                  WHERE er.request_id = ?
                    AND er.student_id = ?
                    AND COALESCE(er.requester_name, CONCAT(u.fname, ' ', u.lname), '') = ?
                  LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            $notificationId,
            $data['message'],
            $data['request_id'],
            $data['student_id'],
            $data['requester_name']
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Get sent notifications for a specific booking request
     */
    public function getRequestNotifications($requestId, $studentId, $requesterName) {
        $query = "SELECT notification_id, request_id, student_id, requester_name, message
                  FROM equipment_request_notifications
                  WHERE request_id = ?
                    AND student_id = ?
                    AND requester_name = ?
                  ORDER BY notification_id DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$requestId, $studentId, $requesterName]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        /**
         * Delete one notification for a specific booking request context
         */
        public function deleteRequestNotification($notificationId, $requestId, $studentId, $requesterName) {
                $query = "DELETE FROM equipment_request_notifications
                                    WHERE notification_id = ?
                                        AND request_id = ?
                                        AND student_id = ?
                                        AND requester_name = ?
                                    LIMIT 1";

                $stmt = $this->db->prepare($query);
                $stmt->execute([$notificationId, $requestId, $studentId, $requesterName]);
                return $stmt->rowCount() > 0;
        }
}
