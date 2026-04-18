<?php
class Lostitem {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    /**
     * Add a new lost item with image upload
     * @param array $data - Item details
     * @param array $file - Uploaded file details
     * @return int - The inserted lostItem_id
     */
    public function addLostItem($data, $file = null) {
        $Image = '';
        
        // Handle image upload
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../internal/lostitem/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = time() . "_" . basename($file['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $Image = $fileName;
            }
        }
        
        $sql = "INSERT INTO lost_item 
                (item_name, lost_date, `description`, lost_location, reported_by, contact_number, item_status, `image`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $itemStatus = $data['item_status'] ?? 'Not Found';
        $description = $data['description'] ?? '';
        
        $stmt->execute([
            $data['item_name'],
            $data['lost_date'],
            $description,
            $data['lost_location'],
            $data['reported_by'],
            $data['contact_number'],
            $itemStatus,
            $Image
        ]);
        
        $insertId = $this->conn->lastInsertId();
        
        return $insertId;
    }

    /**
     * Update an existing lost item
     * @param int $lostItem_id
     * @param array $data - Updated item details
     * @param array $file - New uploaded file (optional)
     * @return bool
     */
    public function updateLostItem($lostItem_id, $data, $file = null) {
        // Get existing item to check for old image
        $existingItem = $this->getById($lostItem_id);
        $image = $existingItem['image'];
        
        // Handle new image upload
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../internal/lostitem/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = time() . "_" . basename($file['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                // Delete old image if exists
                if ($existingItem['image']) {
                    $oldImagePath = __DIR__ . '/../internal/lostitem/' . $existingItem['image'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                $image = $fileName;
            }
        }
        
        $sql = "UPDATE lost_item 
                SET item_name = ?,
                    lost_date = ?,
                    `description` = ?,
                    lost_location = ?,
                    reported_by = ?,
                    contact_number = ?,
                    item_status = ?,
                    `image` = ?
                WHERE lostItem_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $itemStatus = $data['item_status'] ?? 'Not Found';
        $description = $data['description'] ?? '';
        
        $result = $stmt->execute([
            $data['item_name'],
            $data['lost_date'],
            $description,
            $data['lost_location'],
            $data['reported_by'],
            $data['contact_number'],
            $itemStatus,
            $image,
            $lostItem_id
        ]);
        
        return $result;
    }

    /**
     * Get all lost items with optional filtering
     * @param array $filters - Optional filters (status, date range, etc.)
     * @return array
     */
    public function getAll($filters = []) {
        $sql = "SELECT lostItem_id, item_name, lost_date, `description`, lost_location, 
                       reported_by, contact_number, `image`, item_status 
                FROM lost_item";
        
        $conditions = [];
        $params = [];
        
        if (isset($filters['status'])) {
            $conditions[] = "item_status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (isset($filters['month']) && isset($filters['year'])) {
            $conditions[] = "MONTH(lost_date) = :month AND YEAR(lost_date) = :year";
            $params[':month'] = $filters['month'];
            $params[':year'] = $filters['year'];
        }
        
        if (isset($filters['search'])) {
            $conditions[] = "(item_name LIKE :search OR lost_location LIKE :search OR reported_by LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY lost_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get not found items from current month
     * @return array
     */
    public function getUnclaimedItemsCurrentMonth() {
        try {
            // New schema support (snake_case columns).
            $sql = "SELECT lostItem_id, item_name, lost_date, `description`, lost_location,
                           reported_by, contact_number, `image`, item_status
                    FROM lost_item
                    WHERE REPLACE(LOWER(TRIM(item_status)), ' ', '') IN ('notfound', 'unclaimed')
                    AND YEAR(lost_date) = YEAR(CURDATE())
                    AND MONTH(lost_date) = MONTH(CURDATE())
                    ORDER BY lost_date DESC";

            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Legacy schema fallback (camelCase columns from older SQL files).
            $legacySql = "SELECT lostItem_id,
                                 item_name,
                                 lost_date,
                                 `description`,
                                 lost_location,
                                 reported_by,
                                 contact_number,
                                 `image`,
                                 itemStatus AS item_status
                          FROM lost_item
                          WHERE REPLACE(LOWER(TRIM(itemStatus)), ' ', '') IN ('notfound')
                          AND YEAR(lost_date) = YEAR(CURDATE())
                          AND MONTH(lost_date) = MONTH(CURDATE())
                          ORDER BY lost_date DESC";

            $stmt = $this->conn->query($legacySql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * Get a single lost item by ID
     * @param int $lostItem_id
     * @return array|false
     */
    public function getById($lostItem_id) {
        $sql = "SELECT * FROM lost_item WHERE lostItem_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$lostItem_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Delete a lost item and its image
     * @param int $lostItem_id
     * @return bool
     */
    public function delete($lostItem_id) {
        // Get item to delete image file
        $item = $this->getById($lostItem_id);
        
        if ($item && $item['image']) {
            $imagePath = __DIR__ . '/../internal/lostitem/' . $item['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        $sql = "DELETE FROM lost_item WHERE lostItem_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$lostItem_id]);
    }

    /**
     * Update item status (claimed/unclaimed)
     * @param int $lostItem_id
     * @param string $status
     * @return bool
     */
    public function updateStatus($lostItem_id, $status) {
        $sql = "UPDATE lost_item SET item_status = :status WHERE lostItem_id = :lostItem_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':lostItem_id' => $lostItem_id
        ]);
    }

    /**
     * Search lost items by keyword
     * @param string $query - Search keyword
     * @return array
     */
    public function search($query) {
          $sql = "SELECT lostItem_id, item_name, lost_date, `description`, lost_location, 
                              reported_by, contact_number, `image`, item_status 
                FROM lost_item
                     WHERE item_name LIKE :query 
                         OR lost_location LIKE :query 
                         OR reported_by LIKE :query
                   OR `description` LIKE :query
                     ORDER BY lost_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':query' => '%' . $query . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get statistics for lost items
     * @return array
     */
    public function getStatistics() {
        $sql = "SELECT 
                    COUNT(*) as total_items,
                  SUM(CASE WHEN item_status = 'Not Found' THEN 1 ELSE 0 END) as not_found_items,
                  SUM(CASE WHEN item_status = 'Found' THEN 1 ELSE 0 END) as found_items,
                  COUNT(CASE WHEN MONTH(lost_date) = MONTH(CURDATE()) 
                      AND YEAR(lost_date) = YEAR(CURDATE()) THEN 1 END) as this_month_items
                FROM lost_item";
        
        $stmt = $this->conn->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get items found by a specific person
     * @param string $foundBy - Name of the person who found items
     * @return array
     */
    public function getByFoundBy($foundBy) {
        $sql = "SELECT * FROM lost_item WHERE reported_by = :foundBy ORDER BY lost_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':foundBy' => $foundBy]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get items by location
     * @param string $location
     * @return array
     */
    public function getByLocation($location) {
        $sql = "SELECT * FROM lost_item 
                WHERE lost_location LIKE :location 
                ORDER BY lost_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':location' => '%' . $location . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get items by date range
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getByDateRange($startDate, $endDate) {
        $sql = "SELECT * FROM lost_item 
                WHERE lost_date BETWEEN :startDate AND :endDate 
                ORDER BY lost_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':startDate' => $startDate,
            ':endDate' => $endDate
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recent items (last N items)
     * @param int $limit
     * @return array
     */
    public function getRecent($limit = 1) {
        $sql = "SELECT * FROM lost_item 
            ORDER BY lost_date DESC, lostItem_id DESC 
                LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count items by status
     * @param string $status
     * @return int
     */
    public function countByStatus($status) {
        $sql = "SELECT COUNT(*) FROM lost_item WHERE item_status = :status";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':status' => $status]);
        return $stmt->fetchColumn();
    }
}