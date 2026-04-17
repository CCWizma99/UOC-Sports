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
                (sport, expense_title, sport_event, amount, receipt, submitted_by, expense_date)
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($sql);
  
        $amount = $data['amount'] ?? 0;
        $sport_event = !empty($data['sport_event']) ? $data['sport_event'] : NULL;
        
        $stmt->execute([
            $data['sport'],
            $data['expense_title'],
            $sport_event,
            $amount,
            $receipt_image,
            $data['submitted_by'],
       
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
                    sport_event = ?,
                    amount = ?,
                    receipt = ?,
                    submitted_by = ?,
                    
                WHERE expense_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        
        $amount = $data['amount'] ?? 0;
        $sport_event = !empty($data['sport_event']) ? $data['sport_event'] : NULL;
        
        $result = $stmt->execute([
            $data['sport'],
            $data['expense_title'],
            $sport_event,
            $amount,
            $receipt_image,
            $data['submitted_by'],
            
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
        $sql = "SELECT expense_id, sport, expense_title, sport_event, amount, receipt, submitted_by, 
                       expense_date 
                FROM sport_expenses";
        
        $conditions = [];
        $params = [];
        
        if (isset($filters['sport'])) {
            $conditions[] = "LOWER(sport) = LOWER(:sport)";
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
            $sql .= " WHERE status = 'ACTIVE' AND " . implode(" AND ", $conditions);
        } else {
            $sql .= " WHERE status = 'ACTIVE'";
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
        $sql = "SELECT expense_id, sport, expense_title, amount, receipt, submitted_by, expense_date 
                FROM sport_expenses
                WHERE YEAR(expense_date) = YEAR(CURDATE())
                AND MONTH(expense_date) = MONTH(CURDATE())
                AND status = 'ACTIVE'
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
        $sql = "SELECT * FROM sport_expenses WHERE expense_id = ? AND status = 'ACTIVE'";
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
        // No longer deleting receipt file to preserve audit trail
        
        $sql = "UPDATE sport_expenses SET status = 'DELETED' WHERE expense_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$expense_id]);
    }

    /**
     * Search expenses by keyword
     * @param string $query - Search keyword
     * @return array
     */
    public function search($query) {
        $sql = "SELECT expense_id, sport, expense_title, sport_event, amount, receipt, submitted_by, 
                       notes, expense_date 
                FROM sport_expenses
                WHERE (expense_title LIKE :query 
                   OR sport LIKE :query 
                   OR submitted_by LIKE :query)
                AND status = 'ACTIVE'
                
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
        $sql = "SELECT expense_id, sport, expense_title, sport_event, amount, receipt, submitted_by, 
                     expense_date 
                FROM sport_expenses
                WHERE sport = ? AND status = 'ACTIVE'
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
        $sql = "SELECT expense_id, sport, expense_title, sport_event, amount, receipt, submitted_by, 
                     expense_date 
                FROM sport_expenses
                WHERE (expense_date BETWEEN ? AND ?) AND status = 'ACTIVE'
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
        $sql = "SELECT COUNT(*) as count FROM sport_expenses WHERE status = 'ACTIVE'";
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
        $sql = "SELECT expense_id, sport, expense_title, sport_event, amount, receipt, submitted_by, 
                      expense_date 
                FROM sport_expenses
                WHERE status = 'ACTIVE'
                ORDER BY expense_date DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get monthly expenses grouped by month for a specific sport and year
     * @param string $sportId - Sport ID (optional, null for all sports)
     * @param int $year - Year (default: current year)
     * @return array - Array with month names as keys and total amounts as values
     */
    public function getMonthlyExpenses($sportId = null, $year = null) {
        if ($year === null) {
            $year = date('Y');
        }
        
        $sql = "SELECT 
                    MONTH(se.expense_date) as month_num,
                    MONTHNAME(se.expense_date) as month_name,
                    SUM(se.amount) as total_amount
                FROM sport_expenses se";
        
        $params = [$year];
        
        if ($sportId !== null && $sportId !== '') {
            $sql .= " INNER JOIN sport s ON se.sport COLLATE utf8mb4_unicode_ci = s.sport_name COLLATE utf8mb4_unicode_ci
                      WHERE YEAR(se.expense_date) = ? AND s.sport_id = ? AND se.status = 'ACTIVE'";
            $params[] = $sportId;
        } else {
            $sql .= " WHERE YEAR(se.expense_date) = ? AND se.status = 'ACTIVE'";
        }
        
        $sql .= " GROUP BY MONTH(se.expense_date), MONTHNAME(se.expense_date)
                  ORDER BY MONTH(se.expense_date)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Create array with all 12 months initialized to 0
        $months = [
            "January" => 0, "February" => 0, "March" => 0, "April" => 0,
            "May" => 0, "June" => 0, "July" => 0, "August" => 0,
            "September" => 0, "October" => 0, "November" => 0, "December" => 0
        ];
        
        // Fill in actual expense data
        foreach ($results as $row) {
            $months[$row['month_name']] = (float)$row['total_amount'];
        }
        
        return $months;
    }
    
    /**
     * Get cumulative expenses over time for line chart
     * @param string $sportId - Sport ID (optional, null for all sports)
     * @param int $year - Year (default: current year)
     * @return array - Array of expense entries with cumulative totals
     */
    public function getCumulativeExpenses($sportId = null, $year = null) {
        if ($year === null) {
            $year = date('Y');
        }
        
        $sql = "SELECT 
                    se.expense_id,
                    se.expense_title,
                    se.amount,
                    se.expense_date,
                    DATE_FORMAT(se.expense_date, '%Y-%m-%d') as date_only,
                    DATE_FORMAT(se.expense_date, '%b %d') as label
                FROM sport_expenses se";
        
        $params = [$year];
        
        if ($sportId !== null && $sportId !== '') {
            $sql .= " INNER JOIN sport s ON se.sport COLLATE utf8mb4_unicode_ci = s.sport_name COLLATE utf8mb4_unicode_ci
                      WHERE YEAR(se.expense_date) = ? AND s.sport_id = ? AND se.status = 'ACTIVE'";
            $params[] = $sportId;
        } else {
            $sql .= " WHERE YEAR(se.expense_date) = ? AND se.status = 'ACTIVE'";
        }
        
        $sql .= " ORDER BY se.expense_date ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate cumulative totals
        $cumulative = 0;
        $result = [];
        
        foreach ($expenses as $expense) {
            $cumulative += (float)$expense['amount'];
            $result[] = [
                'date' => $expense['date_only'],
                'label' => $expense['label'],
                'expense_title' => $expense['expense_title'],
                'amount' => (float)$expense['amount'],
                'cumulative' => $cumulative
            ];
        }
        
        return $result;
    }
}
