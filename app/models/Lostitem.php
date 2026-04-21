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
                (itemName, foundDate, `description`, foundLocation, foundBy, contactNumber, itemStatus, `image`)
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
                SET itemName = ?,
                    foundDate = ?,
                    `description` = ?,
                    foundLocation = ?,
                    foundBy = ?,
                    contactNumber = ?,
                    itemStatus = ?,
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
        $sql = "SELECT lostItem_id, itemName AS item_name, foundDate AS lost_date, `description`, foundLocation AS lost_location, 
                       foundBy AS reported_by, contactNumber AS contact_number, `image`, itemStatus AS item_status 
                FROM lost_item";
        
        $conditions = [];
        $params = [];
        
        if (isset($filters['status'])) {
            $conditions[] = "itemStatus = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (isset($filters['month']) && isset($filters['year'])) {
            $conditions[] = "MONTH(foundDate) = :month AND YEAR(foundDate) = :year";
            $params[':month'] = $filters['month'];
            $params[':year'] = $filters['year'];
        }
        
        if (isset($filters['search'])) {
            $conditions[] = "(itemName LIKE :search OR foundLocation LIKE :search OR foundBy LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY foundDate DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get not found items from current month
     * @return array
     */
    public function getUnclaimedItemsCurrentMonth() {
        $sql = "SELECT lostItem_id, itemName AS item_name, foundDate AS lost_date, `description`, foundLocation AS lost_location,
                       foundBy AS reported_by, contactNumber AS contact_number, `image`, itemStatus AS item_status
                FROM lost_item
                WHERE REPLACE(LOWER(TRIM(itemStatus)), ' ', '') IN ('notfound', 'unclaimed')
                AND YEAR(foundDate) = YEAR(CURDATE())
                AND MONTH(foundDate) = MONTH(CURDATE())
                ORDER BY foundDate DESC";

        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single lost item by ID
     * @param int $lostItem_id
     * @return array|false
     */
    public function getById($lostItem_id) {
        $sql = "SELECT lostItem_id, itemName AS item_name, foundDate AS lost_date, `description`, foundLocation AS lost_location,
                       foundBy AS reported_by, contactNumber AS contact_number, `image`, itemStatus AS item_status FROM lost_item WHERE lostItem_id = ?";
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
        $sql = "UPDATE lost_item SET itemStatus = :status WHERE lostItem_id = :lostItem_id";
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
          $sql = "SELECT lostItem_id, itemName AS item_name, foundDate AS lost_date, `description`, foundLocation AS lost_location, 
                              foundBy AS reported_by, contactNumber AS contact_number, `image`, itemStatus AS item_status 
                FROM lost_item
                     WHERE itemName LIKE :query 
                         OR foundLocation LIKE :query 
                         OR foundBy LIKE :query
                   OR `description` LIKE :query
                     ORDER BY foundDate DESC";
        
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
                  SUM(CASE WHEN itemStatus = 'Not Found' THEN 1 ELSE 0 END) as not_found_items,
                  SUM(CASE WHEN itemStatus = 'Found' THEN 1 ELSE 0 END) as found_items,
                  COUNT(CASE WHEN MONTH(foundDate) = MONTH(CURDATE()) 
                      AND YEAR(foundDate) = YEAR(CURDATE()) THEN 1 END) as this_month_items
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
        $sql = "SELECT lostItem_id, itemName AS item_name, foundDate AS lost_date, `description`, foundLocation AS lost_location, foundBy AS reported_by, contactNumber AS contact_number, `image`, itemStatus AS item_status FROM lost_item WHERE foundBy = :foundBy ORDER BY foundDate DESC";
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
        $sql = "SELECT lostItem_id, itemName AS item_name, foundDate AS lost_date, `description`, foundLocation AS lost_location, foundBy AS reported_by, contactNumber AS contact_number, `image`, itemStatus AS item_status FROM lost_item 
                WHERE foundLocation LIKE :location 
                ORDER BY foundDate DESC";
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
        $sql = "SELECT lostItem_id, itemName AS item_name, foundDate AS lost_date, `description`, foundLocation AS lost_location, foundBy AS reported_by, contactNumber AS contact_number, `image`, itemStatus AS item_status FROM lost_item 
                WHERE foundDate BETWEEN :startDate AND :endDate 
                ORDER BY foundDate DESC";
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
        $sql = "SELECT lostItem_id, itemName AS item_name, foundDate AS lost_date, `description`, foundLocation AS lost_location, foundBy AS reported_by, contactNumber AS contact_number, `image`, itemStatus AS item_status FROM lost_item 
            ORDER BY foundDate DESC, lostItem_id DESC 
                LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recent items for the news feed with mapped names
     * @param int $limit
     * @return array
     */
    public function getRecentForNewsFeed($limit = 6) {
        $sql = "SELECT lostItem_id AS case_id, itemName AS case_title, `description`, `image` AS image_name, foundDate AS reported_time
                FROM lost_item
                WHERE itemStatus IN ('unclaimed', 'Not Found')
                ORDER BY foundDate DESC, lostItem_id DESC
                LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count items by status
     * @param string $status
     * @return int
     */
    public function countByStatus($status) {
        $sql = "SELECT COUNT(*) FROM lost_item WHERE itemStatus = :status";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':status' => $status]);
        return $stmt->fetchColumn();
    }
}