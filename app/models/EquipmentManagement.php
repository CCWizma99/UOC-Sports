<?php

class EquipmentManagement {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // ─── Lookup Methods ───

    public function getAllSports() {
        $sql = "SELECT sport_id, sport_name FROM sport ORDER BY sport_name ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getArticlesBySport($sportId) {
        $sql = "SELECT equipment_id, equipment_name FROM equipment WHERE sport_id = :sid ORDER BY equipment_name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':sid' => $sportId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSuppliers() {
        $sql = "SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStockEntries($sportId = null, $equipmentId = null) {
        $sql = "SELECT ei.stock_id, e.equipment_name, ei.quantity, ei.usable
                FROM equipment_inventory ei
                JOIN equipment e ON ei.equipment_id = e.equipment_id
                WHERE 1=1";
        $params = [];

        if ($sportId) {
            $sql .= " AND ei.sport_id = :sid";
            $params[':sid'] = $sportId;
        }
        if ($equipmentId) {
            $sql .= " AND ei.equipment_id = :eid";
            $params[':eid'] = $equipmentId;
        }

        $sql .= " ORDER BY ei.stock_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUsersByType($type) {
        $sql = "SELECT user_id, CONCAT(fname, ' ', lname) AS full_name
                FROM user WHERE type = :type AND status = 'ACTIVE' ORDER BY fname";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':type' => $type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─── Insert Methods ───

    public function addGoodReceivedNote($data) {
        $sql = "INSERT INTO good_received_notes 
                (sport_id, equipment_id, description, date, po_number, supplier_id, invoice_no, quantity, unit, unit_price, reference_info, stock_id)
                VALUES (:sport_id, :equipment_id, :description, :date, :po_number, :supplier_id, :invoice_no, :quantity, :unit, :unit_price, :reference_info, :stock_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':sport_id'      => $data['sport_id'],
            ':equipment_id'  => $data['equipment_id'],
            ':description'   => $data['description'] ?? '',
            ':date'          => $data['date'],
            ':po_number'     => $data['po_number'] ?? '',
            ':supplier_id'   => $data['supplier_id'],
            ':invoice_no'    => $data['invoice_no'] ?? '',
            ':quantity'      => $data['quantity'],
            ':unit'          => $data['unit'],
            ':unit_price'    => $data['unit_price'],
            ':reference_info' => $data['reference_info'] ?? '',
            ':stock_id'      => $data['stock_id']
        ]);
    }

    public function addGoodIssueNote($data) {
        $sql = "INSERT INTO good_issue_notes 
                (sport_id, equipment_id, date, quantity, unit, stock_id, sport_manager_id, captain_id, equipment_manager_id)
                VALUES (:sport_id, :equipment_id, :date, :quantity, :unit, :stock_id, :sport_manager_id, :captain_id, :equipment_manager_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':sport_id'             => $data['sport_id'],
            ':equipment_id'         => $data['equipment_id'],
            ':date'                 => $data['date'],
            ':quantity'             => $data['quantity'],
            ':unit'                 => $data['unit'],
            ':stock_id'             => $data['stock_id'],
            ':sport_manager_id'     => $data['sport_manager_id'] ?? null,
            ':captain_id'           => $data['captain_id'] ?? null,
            ':equipment_manager_id' => $data['equipment_manager_id'] ?? null
        ]);
    }

    public function addGoodCondemnNote($data) {
        $sql = "INSERT INTO good_condemn_notes 
                (sport_id, equipment_id, stock_id, quantity)
                VALUES (:sport_id, :equipment_id, :stock_id, :quantity)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':sport_id'     => $data['sport_id'],
            ':equipment_id' => $data['equipment_id'],
            ':stock_id'     => $data['stock_id'],
            ':quantity'     => $data['quantity']
        ]);
    }

    public function addSport($sportName) {
        // Generate a short sport_id from name (first 3 chars uppercase)
        $id = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $sportName), 0, 4));

        // Check for duplicate id
        $check = $this->db->prepare("SELECT sport_id FROM sport WHERE sport_id = :id");
        $check->execute([':id' => $id]);
        if ($check->fetch()) {
            // Append a number to make it unique
            $id = strtoupper(substr($id, 0, 3)) . rand(1, 9);
        }

        // Check for duplicate name
        $checkName = $this->db->prepare("SELECT sport_id FROM sport WHERE sport_name = :name");
        $checkName->execute([':name' => $sportName]);
        if ($checkName->fetch()) {
            return 'DUPLICATE';
        }

        $sql = "INSERT INTO sport (sport_id, sport_name, sport_category, coach_id, captain_id, manager_id)
                VALUES (:id, :name, 'TEAM_GOAL', '', '', '')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id, ':name' => $sportName]);
        return true;
    }

    public function addArticle($sportId, $articleName) {
        // Check for duplicates
        $check = $this->db->prepare("SELECT equipment_id FROM equipment WHERE sport_id = :sid AND equipment_name = :name");
        $check->execute([':sid' => $sportId, ':name' => $articleName]);
        if ($check->fetch()) {
            return 'DUPLICATE';
        }

        $equipmentId = 'EQ' . substr(uniqid(), -10);
        $sql = "INSERT INTO equipment (equipment_id, sport_id, equipment_name, max_allow, image_name)
                VALUES (:id, :sid, :name, 0, '')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $equipmentId, ':sid' => $sportId, ':name' => $articleName]);
        return true;
    }

    public function addSupplier($data) {
        // Check for duplicates by name
        $check = $this->db->prepare("SELECT supplier_id FROM suppliers WHERE supplier_name = :name");
        $check->execute([':name' => $data['supplier_name']]);
        if ($check->fetch()) {
            return 'DUPLICATE';
        }

        $sql = "INSERT INTO suppliers (supplier_name, address, telephone_1, telephone_2, email)
                VALUES (:name, :address, :tel1, :tel2, :email)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name'    => $data['supplier_name'],
            ':address' => $data['address'],
            ':tel1'    => $data['telephone_1'],
            ':tel2'    => $data['telephone_2'] ?? '',
            ':email'   => $data['email'] ?? ''
        ]);
        return true;
    }

    // ─── Report Methods (all support All-Time / Annual / Monthly via $year, $month) ───

    /**
     * Build date filter clause + params for a given date column
     */
    private function buildDateFilter($dateCol, $year = null, $month = null) {
        $where = '';
        $params = [];
        if ($year) {
            $where .= " AND YEAR($dateCol) = :filter_year";
            $params[':filter_year'] = $year;
        }
        if ($month) {
            $where .= " AND MONTH($dateCol) = :filter_month";
            $params[':filter_month'] = $month;
        }
        return [$where, $params];
    }

    /**
     * 1. Equipment-wise Inventory Report
     * Groups by equipment, shows stock/usable/damaged/condition for each
     */
    public function getEquipmentInventoryReport($year = null, $month = null) {
        [$dateWhere, $dateParams] = $this->buildDateFilter('ei.added_date', $year, $month);

        $summarySQL = "
            SELECT 
                COUNT(DISTINCT e.equipment_id) as total_equipment_types,
                COALESCE(SUM(ei.quantity), 0) as total_stock,
                COALESCE(SUM(ei.usable), 0) as total_usable,
                (COALESCE(SUM(ei.quantity), 0) - COALESCE(SUM(ei.usable), 0)) as total_damaged,
                COUNT(DISTINCT e.sport_id) as sports_covered,
                CASE WHEN COALESCE(SUM(ei.quantity), 0) > 0 
                     THEN ROUND((COALESCE(SUM(ei.usable), 0) / SUM(ei.quantity)) * 100, 1) ELSE 100 END as overall_condition
            FROM equipment e
            LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id
            WHERE 1=1 $dateWhere
        ";
        $stmt = $this->db->prepare($summarySQL);
        $stmt->execute($dateParams);
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);

        $detailSQL = "
            SELECT s.sport_name, e.equipment_name,
                COALESCE(SUM(ei.quantity), 0) as total_stock,
                COALESCE(SUM(ei.usable), 0) as usable,
                (COALESCE(SUM(ei.quantity), 0) - COALESCE(SUM(ei.usable), 0)) as damaged,
                CASE WHEN COALESCE(SUM(ei.quantity), 0) > 0 
                     THEN ROUND((COALESCE(SUM(ei.usable), 0) / SUM(ei.quantity)) * 100, 0) ELSE 100 END as condition_percent
            FROM equipment e
            LEFT JOIN sport s ON e.sport_id = s.sport_id
            LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id
            WHERE 1=1 $dateWhere
            GROUP BY e.equipment_id, e.equipment_name, s.sport_name
            HAVING total_stock > 0
            ORDER BY s.sport_name, e.equipment_name
        ";
        $stmt = $this->db->prepare($detailSQL);
        $stmt->execute($dateParams);
        $equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['summary' => $summary, 'equipment' => $equipment];
    }

    /**
     * 2. Supplier-wise Details & Analysis
     */
    public function getSupplierReport($year = null, $month = null) {
        [$dateWhere, $dateParams] = $this->buildDateFilter('grn.date', $year, $month);

        $sql = "
            SELECT s.supplier_id, s.supplier_name, s.address, s.telephone_1, s.email,
                COUNT(grn.grn_id) as total_grns,
                COALESCE(SUM(grn.quantity), 0) as total_items_supplied,
                COALESCE(SUM(grn.quantity * grn.unit_price), 0) as total_value
            FROM suppliers s
            LEFT JOIN good_received_notes grn ON s.supplier_id = grn.supplier_id $dateWhere
            GROUP BY s.supplier_id, s.supplier_name, s.address, s.telephone_1, s.email
            ORDER BY total_value DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($dateParams);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 3. All Equipment Snapshot
     */
    public function getAllEquipmentSnapshot($year = null, $month = null) {
        [$dateWhere, $dateParams] = $this->buildDateFilter('ei.added_date', $year, $month);

        $sql = "
            SELECT s.sport_name, e.equipment_id, e.equipment_name,
                COALESCE(SUM(ei.quantity), 0) as total_stock,
                COALESCE(SUM(ei.usable), 0) as usable
            FROM equipment e
            LEFT JOIN sport s ON e.sport_id = s.sport_id
            LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id AND 1=1 $dateWhere
            GROUP BY s.sport_name, e.equipment_id, e.equipment_name
            ORDER BY s.sport_name, e.equipment_name
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($dateParams);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 4. Stock-wise Snapshot
     */
    public function getStockSnapshot($year = null, $month = null) {
        [$dateWhere, $dateParams] = $this->buildDateFilter('ei.added_date', $year, $month);

        $sql = "
            SELECT ei.stock_id, s.sport_name, e.equipment_name,
                ei.quantity, ei.usable,
                (ei.quantity - ei.usable) as damaged,
                ei.added_date, ei.remarks
            FROM equipment_inventory ei
            JOIN equipment e ON ei.equipment_id = e.equipment_id
            JOIN sport s ON ei.sport_id = s.sport_id
            WHERE 1=1 $dateWhere
            ORDER BY ei.added_date DESC, s.sport_name
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($dateParams);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 5. Period Activity Snapshot (GRN / GIN / GCN counts + quantities)
     */
    public function getPeriodSnapshot($year = null, $month = null) {
        $result = [];

        // GRN summary
        [$dw, $dp] = $this->buildDateFilter('grn.date', $year, $month);
        $sql = "SELECT COUNT(*) as total_grns, COALESCE(SUM(grn.quantity), 0) as total_received,
                       COALESCE(SUM(grn.quantity * grn.unit_price), 0) as total_cost
                FROM good_received_notes grn WHERE 1=1 $dw";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($dp);
        $result['grn'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // GIN summary
        [$dw, $dp] = $this->buildDateFilter('gin.date', $year, $month);
        $sql = "SELECT COUNT(*) as total_gins, COALESCE(SUM(gin.quantity), 0) as total_issued
                FROM good_issue_notes gin WHERE 1=1 $dw";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($dp);
        $result['gin'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // GCN summary
        [$dw, $dp] = $this->buildDateFilter('gcn.created_at', $year, $month);
        $sql = "SELECT COUNT(*) as total_gcns, COALESCE(SUM(gcn.quantity), 0) as total_condemned
                FROM good_condemn_notes gcn WHERE 1=1 $dw";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($dp);
        $result['gcn'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // GRN details
        [$dw, $dp] = $this->buildDateFilter('grn.date', $year, $month);
        $sql = "SELECT grn.date, e.equipment_name, s.sport_name, sup.supplier_name,
                       grn.quantity, grn.unit, grn.unit_price, grn.po_number, grn.invoice_no
                FROM good_received_notes grn
                JOIN equipment e ON grn.equipment_id = e.equipment_id
                JOIN sport s ON grn.sport_id = s.sport_id
                JOIN suppliers sup ON grn.supplier_id = sup.supplier_id
                WHERE 1=1 $dw ORDER BY grn.date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($dp);
        $result['grn_details'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // GIN details
        [$dw, $dp] = $this->buildDateFilter('gin.date', $year, $month);
        $sql = "SELECT gin.date, e.equipment_name, s.sport_name,
                       gin.quantity, gin.unit, gin.stock_id
                FROM good_issue_notes gin
                JOIN equipment e ON gin.equipment_id = e.equipment_id
                JOIN sport s ON gin.sport_id = s.sport_id
                WHERE 1=1 $dw ORDER BY gin.date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($dp);
        $result['gin_details'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // GCN details
        [$dw, $dp] = $this->buildDateFilter('gcn.created_at', $year, $month);
        $sql = "SELECT gcn.created_at as date, e.equipment_name, s.sport_name,
                       gcn.quantity, gcn.stock_id
                FROM good_condemn_notes gcn
                JOIN equipment e ON gcn.equipment_id = e.equipment_id
                JOIN sport s ON gcn.sport_id = s.sport_id
                WHERE 1=1 $dw ORDER BY gcn.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($dp);
        $result['gcn_details'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     * 6. Supplier Detail Report – all GRNs from a specific supplier
     */
    public function getSupplierDetailReport($supplierId, $year = null, $month = null) {
        // Supplier info
        $stmt = $this->db->prepare("SELECT * FROM suppliers WHERE supplier_id = :id");
        $stmt->execute([':id' => $supplierId]);
        $supplier = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$supplier) return null;

        // GRN details
        [$dateWhere, $dateParams] = $this->buildDateFilter('grn.date', $year, $month);
        $dateParams[':sup_id'] = $supplierId;

        $sql = "SELECT grn.grn_id, grn.date, grn.po_number, grn.invoice_no,
                       grn.description, grn.quantity, grn.unit, grn.unit_price,
                       grn.reference_info, grn.stock_id,
                       e.equipment_name, s.sport_name
                FROM good_received_notes grn
                JOIN equipment e ON grn.equipment_id = e.equipment_id
                JOIN sport s ON grn.sport_id = s.sport_id
                WHERE grn.supplier_id = :sup_id $dateWhere
                ORDER BY grn.date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($dateParams);
        $grns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Summary
        $totalItems = 0; $totalValue = 0;
        foreach ($grns as $g) {
            $totalItems += $g['quantity'];
            $totalValue += $g['quantity'] * $g['unit_price'];
        }

        return [
            'supplier' => $supplier,
            'grns' => $grns,
            'summary' => [
                'total_grns' => count($grns),
                'total_items' => $totalItems,
                'total_value' => $totalValue
            ]
        ];
    }
}
