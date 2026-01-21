<?php
class SportExpense {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    /**
     * Add a new sport expense with receipt upload
     * @param array $data - Expense details
     * @param array $file - Uploaded receipt file
     * @return int - The inserted expense_id
     */
    public function addExpense($data, $file = null) {
        $receipt_image = '';
        
        // Handle receipt upload
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../internal/sport_exp_receipt/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = time() . "_" . basename($file['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $receipt_image = $fileName;
            }
        }
        
        $sql = "INSERT INTO sport_expenses 
                (sport, expense_title, amount, receipt, submitted_by, notes, expense_date)
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        $notes = $data['notes'] ?? '';
        $amount = $data['amount'] ?? 0;
        
        $stmt->execute([
            $data['sport'],
            $data['expense_title'],
            $amount,
            $receipt_image,
            $data['submitted_by'],
            $notes
        ]);
        
        $insertId = $this->conn->lastInsertId();
        
        return $insertId;
    }

    /**
     * Update an existing sport expense
     * @param int $expense_id
     * @param array $data - Updated expense details
     * @param array $file - New uploaded receipt file (optional)
     * @return bool
     */
    public function updateExpense($expense_id, $data, $file = null) {
        // Get existing expense to check for old receipt
        $existingExpense = $this->getById($expense_id);
        $receipt_image = $existingExpense['receipt'];
        
        // Handle new receipt upload
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../internal/sport_exp_receipt/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = time() . "_" . basename($file['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                // Delete old receipt if exists
                if ($existingExpense['receipt']) {
                    $oldReceiptPath = __DIR__ . '/../internal/sport_exp_receipt/' . $existingExpense['receipt'];
                    if (file_exists($oldReceiptPath)) {
                        unlink($oldReceiptPath);
                    }
                }
                $receipt_image = $fileName;
            }
        }
        
        $sql = "UPDATE sport_expenses 
                SET sport = ?,
                    expense_title = ?,
                    amount = ?,
                    receipt = ?,
                    submitted_by = ?,
                    notes = ?
                WHERE expense_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $notes = $data['notes'] ?? '';
        $amount = $data['amount'] ?? 0;
        
        $result = $stmt->execute([
            $data['sport'],
            $data['expense_title'],
            $amount,
            $receipt_image,
            $data['submitted_by'],
            $notes,
            $expense_id
        ]);
        
        return $result;
    }

    /**
     * Get all sport expenses with optional filtering
     * @param array $filters - Optional filters (sport, date range, etc.)
     * @return array
     */
    public function getAll($filters = []) {
        $sql = "SELECT expense_id, sport, expense_title, amount, receipt, submitted_by, 
                       notes, expense_date 
                FROM sport_expenses";
        
        $conditions = [];
        $params = [];
        
        if (isset($filters['sport'])) {
            $conditions[] = "sport = :sport";
            $params[':sport'] = $filters['sport'];
        }
        
        if (isset($filters['month']) && isset($filters['year'])) {
            $conditions[] = "MONTH(expense_date) = :month AND YEAR(expense_date) = :year";
            $params[':month'] = $filters['month'];
            $params[':year'] = $filters['year'];
        }
        
        if (isset($filters['search'])) {
            $conditions[] = "(expense_title LIKE :search OR sport LIKE :search OR submitted_by LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY expense_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get expenses from current month
     * @return array
     */
    public function getExpensesCurrentMonth() {
        $sql = "SELECT expense_id, sport, expense_title, amount, receipt, submitted_by, 
                       notes, expense_date 
                FROM sport_expenses
                WHERE YEAR(expense_date) = YEAR(CURDATE())
                AND MONTH(expense_date) = MONTH(CURDATE())
                ORDER BY expense_date DESC";
        
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single expense by ID
     * @param int $expense_id
     * @return array|false
     */
    public function getById($expense_id) {
        $sql = "SELECT * FROM sport_expenses WHERE expense_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$expense_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Delete an expense and its receipt
     * @param int $expense_id
     * @return bool
     */
    public function delete($expense_id) {
        // Get expense to delete receipt file
        $expense = $this->getById($expense_id);
        
        if ($expense && $expense['receipt']) {
            $receiptPath = __DIR__ . '/../internal/sport_exp_receipt/' . $expense['receipt'];
            if (file_exists($receiptPath)) {
                unlink($receiptPath);
            }
        }
        
        $sql = "DELETE FROM sport_expenses WHERE expense_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$expense_id]);
    }

    /**
     * Search expenses by keyword
     * @param string $query - Search keyword
     * @return array
     */
    public function search($query) {
        $sql = "SELECT expense_id, sport, expense_title, amount, receipt, submitted_by, 
                       notes, expense_date 
                FROM sport_expenses
                WHERE expense_title LIKE :query 
                   OR sport LIKE :query 
                   OR submitted_by LIKE :query
                   OR notes LIKE :query
                ORDER BY expense_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':query' => '%' . $query . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get statistics for expenses
     * @return array
     */
    public function getStatistics() {
        $sql = "SELECT 
                    COUNT(*) as total_expenses,
                    SUM(CASE WHEN MONTH(expense_date) = MONTH(CURDATE()) 
                        AND YEAR(expense_date) = YEAR(CURDATE()) THEN 1 ELSE 0 END) as this_month,
                    COUNT(DISTINCT sport) as total_sports
                FROM sport_expenses";
        
        $stmt = $this->conn->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get expenses by sport
     * @param string $sport
     * @return array
     */
    public function getBySport($sport) {
        $sql = "SELECT expense_id, sport, expense_title, amount, receipt, submitted_by, 
                       notes, expense_date 
                FROM sport_expenses
                WHERE sport = ?
                ORDER BY expense_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$sport]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get expenses by date range
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getByDateRange($startDate, $endDate) {
        $sql = "SELECT expense_id, sport, expense_title, amount, receipt, submitted_by, 
                       notes, expense_date 
                FROM sport_expenses
                WHERE expense_date BETWEEN ? AND ?
                ORDER BY expense_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total expense count
     * @return int
     */
    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as count FROM sport_expenses";
        $stmt = $this->conn->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    /**
     * Get recent expenses
     * @param int $limit
     * @return array
     */
    public function getRecent($limit = 5) {
        $sql = "SELECT expense_id, sport, expense_title, amount, receipt, submitted_by, 
                       notes, expense_date 
                FROM sport_expenses
                ORDER BY expense_date DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
