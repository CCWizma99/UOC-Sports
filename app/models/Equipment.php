<?php
class Equipment {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection(); // Your PDO connection
    }

    public function addEquipment($name, $quantity, $date, $remarks, $sport_id, $condition, $files) {
        $equipmentId = uniqid("eq_", true);
    
        // Insert main equipment record matching actual schema
        $sql = "
            INSERT INTO equipment 
            (equipment_id, equipment_name, sport_id, max_allow, image_name)
            VALUES (:equipment_id, :equipment_name, :sport_id, :max_allow, '')
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'equipment_id' => $equipmentId,
            'equipment_name' => $name,
            'sport_id' => $sport_id,
            'max_allow' => $quantity
        ]);

        // Add initial stock to equipment_inventory if quantity provided
        if ($quantity > 0 && $date) {
            $this->addStock($equipmentId, $quantity, $date, $remarks ?: '-');
        }
    
        // Handle image uploads
        if ($files && count($files['tmp_name']) > 0) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uoc-sports/public/images/equipment/';
            if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
    
            foreach ($files['tmp_name'] as $key => $tmpName) {
                if ($files['error'][$key] !== UPLOAD_ERR_OK) continue;
    
                $ext = pathinfo($files['name'][$key], PATHINFO_EXTENSION);
                $fileName = uniqid("img_", true) . "." . $ext;
                $targetPath = $uploadDir . $fileName;
    
                if (move_uploaded_file($tmpName, $targetPath)) {
                    // Insert each uploaded image record
                    $imgStmt = $this->db->prepare("
                        INSERT INTO equipment_image (equipment_id, image_name)
                        VALUES (:equipment_id, :image_name)
                    ");
                    $imgStmt->execute([
                        'equipment_id' => $equipmentId,
                        'image_name' => $fileName
                    ]);
                }
            }
        }
    
        return $equipmentId;
    }
    

    /**
     * Search equipment by ID or name or category
     * @param string $query
     * @return array
     */
    public function searchEquipment($query) {

        $sql = "SELECT 
                    e.equipment_id,
                    e.equipment_name,
                    e.image_name,
                    s.sport_name AS category,
                    COALESCE(SUM(ei.quantity), 0) AS total_quantity,
                    COALESCE(SUM(ei.usable), 0) AS quantity
                FROM equipment e
                INNER JOIN sport s ON e.sport_id = s.sport_id
                LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id AND ei.status = 'ACTIVE'
                WHERE (e.status = 'ACTIVE') AND (
                    e.equipment_name LIKE :q 
                    OR e.equipment_id LIKE :q
                    OR s.sport_name LIKE :q
                )
                GROUP BY e.equipment_id, e.equipment_name, e.image_name, s.sport_name
                ORDER BY e.equipment_name
                LIMIT 4";
    
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':q' => '%' . $query . '%'
        ]);
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function minimalSearch($query) {
        $sql = "
            SELECT 
                e.equipment_id,
                e.equipment_name,
                e.max_allow,
                e.image_name,
                s.sport_name,
                COALESCE(
                    (
                        SELECT SUM(ei.usable)
                        FROM equipment_inventory ei
                        WHERE ei.equipment_id = e.equipment_id
                    ), 0
                ) AS total_stock,
                COALESCE(
                    (
                        SELECT COUNT(*)
                        FROM `equipment-requests` er
                        WHERE er.equipment_id = e.equipment_id
                        AND er.status = 'ACTIVE'
                    ), 0
                ) AS active_bookings,
                GREATEST(
                    0,
                    COALESCE(
                        (
                            SELECT SUM(ei.usable)
                            FROM equipment_inventory ei
                            WHERE ei.equipment_id = e.equipment_id
                        ), 0
                    ) - (
                        COALESCE(
                            (
                                SELECT COUNT(*)
                                FROM `equipment-requests` er
                                WHERE er.equipment_id = e.equipment_id
                                AND er.status = 'ACTIVE'
                            ), 0
                        ) * e.max_allow
                    )
                ) AS available_quantity
            FROM equipment e
            INNER JOIN sport s ON e.sport_id = s.sport_id
            WHERE e.status = 'ACTIVE' AND e.equipment_name LIKE :query
            HAVING available_quantity > 0
            ORDER BY e.equipment_name
            LIMIT 10
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['query' => "%$query%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSports() {
        $sql = "SELECT sport_id, sport_name FROM sport ORDER BY sport_name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addEquipmentType($sport_id, $equipment_name, $image_name, $max_allow = 0) {
        // Prevent duplicates for same sport
        $checkSql = "SELECT equipment_id FROM equipment 
                     WHERE sport_id = :sport_id AND equipment_name = :equipment_name";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute([
            ':sport_id' => $sport_id,
            ':equipment_name' => $equipment_name
        ]);
    
        if ($checkStmt->rowCount() > 0) {
            return "DUPLICATE";
        }
    
        // Generate equipment ID
        $equipment_id = uniqid("EQ");
    
        $sql = "INSERT INTO equipment 
                (equipment_id, sport_id, equipment_name, max_allow, image_name)
                VALUES (:equipment_id, :sport_id, :equipment_name, :max_allow, :image_name)";
    
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':equipment_id' => $equipment_id,
            ':sport_id' => $sport_id,
            ':equipment_name' => $equipment_name,
            ':max_allow' => $max_allow,
            ':image_name' => $image_name
        ]);
    
        return true;
    }
    

    public function getEquipments($sport_id){
        $sql = "SELECT equipment_id, equipment_name, image_name
                FROM equipment
                WHERE sport_id = :sport_id AND status = 'ACTIVE'
                ORDER BY equipment_name";
        $stmt = $this -> db -> prepare($sql);
        $stmt -> execute([':sport_id' => $sport_id]);
        return $stmt -> fetchAll(PDO::FETCH_ASSOC);
    }

    public function addStock($equipment_id, $quantity, $date, $remarks) {
        // Generate stock ID (8 chars)
        $stock_id = substr(uniqid("STK"), 0, 8);
    
        // Get sport_id from equipment_id
        $sportSql = "SELECT sport_id FROM equipment WHERE equipment_id = :equipment_id";
        $sportStmt = $this->db->prepare($sportSql);
        $sportStmt->execute([':equipment_id' => $equipment_id]);
        $sport = $sportStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$sport) {
            throw new Exception("Equipment not found");
        }
        
        $sport_id = $sport['sport_id'];
    
        // Insert into equipment_inventory with usable = quantity (all items are usable initially)
        $sql = "INSERT INTO equipment_inventory
                (stock_id, equipment_id, sport_id, quantity, usable, added_date, remarks)
                VALUES (:stock_id, :equipment_id, :sport_id, :quantity, :usable, :added_date, :remarks)";
    
        $stmt = $this->db->prepare($sql);
    
        return $stmt->execute([
            ':stock_id' => $stock_id,
            ':equipment_id' => $equipment_id,
            ':sport_id' => $sport_id,
            ':quantity' => $quantity,
            ':usable' => $quantity, // All items are usable initially
            ':added_date' => $date,
            ':remarks' => $remarks
        ]);
    }
    

    // Fetch all equipment
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM equipment WHERE status = 'ACTIVE' ORDER BY equipment_id DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Add action buttons (Update/Delete) for each row
        foreach ($rows as &$row) {
            $id = $row['equipment_id'];
            $row['actions'] = '
                <a href="update_equipment.php?id='.$id.'" class="btn btn-sm btn-primary">Update</a>
                <a href="delete_equipment.php?id='.$id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</a>
            ';
        }
        return $rows;
    }

    // Delete equipment (Soft Delete)
    public function delete($id) {
        $sql = "UPDATE equipment SET status = 'DELETED' WHERE equipment_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Get single equipment (for update form)
    public function getById($id) {
        $sql = "SELECT * FROM equipment WHERE equipment_id = :id AND status = 'ACTIVE'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update equipment
    public function update($id, $data) {
        $sql = "UPDATE equipment 
                SET equipment_category = :equipment_category,
                    availability_status = :availability_status,
                    reserved_person_name = :reserved_person_name,
                    reserved_person_id = :reserved_person_id,
                    reserved_date = :reserved_date,
                    reserved_time = :reserved_time,
                    return_time = :return_time
                WHERE equipment_id = :id";

        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function getReservedTimes($equipment_id) {
        $sql = "SELECT student_id, request_date, start_time, end_time 
                FROM `equipment-requests` 
                WHERE equipment_id = :equipment_id 
                AND status = 'ACTIVE'
                ORDER BY request_date DESC, start_time ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['equipment_id' => $equipment_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isTimeOverlapping($equipment_id, $date, $start, $end) {
        $sql = "SELECT COUNT(*) 
                FROM `equipment-requests`
                WHERE equipment_id = :equipment_id
                AND request_date = :date
                AND status = 'ACTIVE'
                AND (
                    (start_time < :end AND end_time > :start)
                )";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'equipment_id' => $equipment_id,
            'date' => $date,
            'start' => $start,
            'end' => $end
        ]);
        return $stmt->fetchColumn() > 0;
    }

    public function addReservation($equipment_id, $student_id, $date, $start, $end, $purpose, $notes) {
        $id = uniqid('req_', true);

        // Look up sport_id and equipment_name from equipment table
        $eqSql = "SELECT sport_id, equipment_name FROM equipment WHERE equipment_id = :equipment_id";
        $eqStmt = $this->db->prepare($eqSql);
        $eqStmt->execute(['equipment_id' => $equipment_id]);
        $eq = $eqStmt->fetch(PDO::FETCH_ASSOC);

        $sport_id = $eq ? $eq['sport_id'] : '';
        $category_name = $eq ? $eq['equipment_name'] : '';

        $sql = "INSERT INTO `equipment-requests`
                (request_id, student_id, equipment_id, category_name, sport_id, request_date, start_time, end_time, purpose, notes)
                VALUES (:id, :student_id, :equipment_id, :category_name, :sport_id, :date, :start, :end, :purpose, :notes)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'student_id' => $student_id,
            'equipment_id' => $equipment_id,
            'category_name' => $category_name,
            'sport_id' => $sport_id,
            'date' => $date,
            'start' => $start,
            'end' => $end,
            'purpose' => $purpose,
            'notes' => $notes
        ]);
        return $id;
    }

    public function getReservedItems($studentId) {
        $sql = "
            SELECT *
            FROM `equipment-requests` r
            LEFT JOIN equipment e ON r.equipment_id = e.equipment_id
            WHERE r.student_id = :student_id
            GROUP BY r.request_id
            ORDER BY r.request_date DESC;
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    
    

    public function cancelReservation($reservationId, $studentId) {
        $sql = "UPDATE `equipment-requests` SET status = 'CANCELLED' WHERE request_id = :reservation_id AND student_id = :student_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'reservation_id' => $reservationId,
            'student_id' => $studentId['student_id']
        ]);
    }

    /**
     * Get actionable equipment inventory analytics
     * @return array Analytics data with meaningful insights
     */
    public function getAnalytics() {
        $analytics = [];

        // 1. EQUIPMENT UTILIZATION RATE - How much each equipment is being used
        // Calculates: (Active Reservations / Total Usable Stock) * 100 per equipment
        $utilizationSQL = "
            SELECT 
                e.equipment_id,
                e.equipment_name,
                s.sport_name,
                COALESCE(SUM(ei.usable), 0) as total_stock,
                COALESCE(active_res.active_count, 0) as active_reservations,
                CASE 
                    WHEN COALESCE(SUM(ei.usable), 0) > 0 
                    THEN ROUND((COALESCE(active_res.active_count, 0) / SUM(ei.usable)) * 100, 1)
                    ELSE 0 
                END as utilization_rate
            FROM equipment e
            LEFT JOIN sport s ON e.sport_id = s.sport_id
            LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id
            LEFT JOIN (
                SELECT equipment_id, COUNT(*) as active_count
                FROM `equipment-requests`
                WHERE status = 'ACTIVE'
                GROUP BY equipment_id
            ) active_res ON e.equipment_id = active_res.equipment_id
            GROUP BY e.equipment_id, e.equipment_name, s.sport_name
            HAVING total_stock > 0
            ORDER BY utilization_rate DESC
            LIMIT 10
        ";
        $stmt = $this->db->query($utilizationSQL);
        $analytics['utilization'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. HIGH DEMAND EQUIPMENT - Items that are frequently fully booked or nearly full
        // These need more stock
        $highDemandSQL = "
            SELECT 
                e.equipment_id,
                e.equipment_name,
                s.sport_name,
                COALESCE(SUM(ei.usable), 0) as available_stock,
                COUNT(DISTINCT er.request_id) as total_bookings_last_30_days,
                COALESCE(active_res.active_count, 0) as current_active,
                CASE 
                    WHEN COALESCE(SUM(ei.usable), 0) > 0 
                    THEN ROUND((COALESCE(active_res.active_count, 0) / SUM(ei.usable)) * 100, 1)
                    ELSE 0 
                END as demand_pressure
            FROM equipment e
            LEFT JOIN sport s ON e.sport_id = s.sport_id
            LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id
            LEFT JOIN `equipment-requests` er ON e.equipment_id = er.equipment_id 
                AND er.request_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            LEFT JOIN (
                SELECT equipment_id, COUNT(*) as active_count
                FROM `equipment-requests`
                WHERE status = 'ACTIVE'
                GROUP BY equipment_id
            ) active_res ON e.equipment_id = active_res.equipment_id
            GROUP BY e.equipment_id, e.equipment_name, s.sport_name
            HAVING demand_pressure >= 50 OR total_bookings_last_30_days >= 5
            ORDER BY demand_pressure DESC, total_bookings_last_30_days DESC
            LIMIT 8
        ";
        $stmt = $this->db->query($highDemandSQL);
        $analytics['high_demand'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. UNDERUTILIZED EQUIPMENT - Items rarely borrowed (waste of budget)
        $underutilizedSQL = "
            SELECT 
                e.equipment_id,
                e.equipment_name,
                s.sport_name,
                COALESCE(SUM(ei.usable), 0) as available_stock,
                COUNT(DISTINCT er.request_id) as total_bookings_last_90_days,
                DATEDIFF(CURDATE(), MAX(er.request_date)) as days_since_last_booking
            FROM equipment e
            LEFT JOIN sport s ON e.sport_id = s.sport_id
            LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id
            LEFT JOIN `equipment-requests` er ON e.equipment_id = er.equipment_id
            GROUP BY e.equipment_id, e.equipment_name, s.sport_name
            HAVING available_stock > 0 AND (total_bookings_last_90_days <= 2 OR days_since_last_booking > 30 OR days_since_last_booking IS NULL)
            ORDER BY total_bookings_last_90_days ASC, days_since_last_booking DESC
            LIMIT 8
        ";
        $stmt = $this->db->query($underutilizedSQL);
        $analytics['underutilized'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 4. PEAK BOOKING HOURS - When do students book most?
        $peakHoursSQL = "
            SELECT 
                HOUR(start_time) as hour,
                COUNT(*) as booking_count,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM `equipment-requests`), 1) as percentage
            FROM `equipment-requests`
            GROUP BY HOUR(start_time)
            ORDER BY booking_count DESC
            LIMIT 6
        ";
        $stmt = $this->db->query($peakHoursSQL);
        $analytics['peak_hours'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 5. PEAK BOOKING DAYS - Which days are busiest?
        $peakDaysSQL = "
            SELECT 
                DAYNAME(request_date) as day_name,
                DAYOFWEEK(request_date) as day_num,
                COUNT(*) as booking_count
            FROM `equipment-requests`
            GROUP BY DAYNAME(request_date), DAYOFWEEK(request_date)
            ORDER BY booking_count DESC
        ";
        $stmt = $this->db->query($peakDaysSQL);
        $analytics['peak_days'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 6. EQUIPMENT CONDITION ALERTS - Items with high damage rate
        $conditionSQL = "
            SELECT 
                e.equipment_id,
                e.equipment_name,
                s.sport_name,
                SUM(ei.quantity) as total_stock,
                SUM(ei.usable) as usable_stock,
                (SUM(ei.quantity) - SUM(ei.usable)) as damaged_count,
                ROUND(((SUM(ei.quantity) - SUM(ei.usable)) / NULLIF(SUM(ei.quantity), 0)) * 100, 1) as damage_rate
            FROM equipment e
            LEFT JOIN sport s ON e.sport_id = s.sport_id
            LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id
            GROUP BY e.equipment_id, e.equipment_name, s.sport_name
            HAVING total_stock > 0 AND damage_rate > 10
            ORDER BY damage_rate DESC
            LIMIT 8
        ";
        $stmt = $this->db->query($conditionSQL);
        $analytics['condition_alerts'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 7. SPORT-WISE DEMAND - Which sports have most equipment requests?
        $sportDemandSQL = "
            SELECT 
                s.sport_id,
                s.sport_name,
                COUNT(DISTINCT er.request_id) as total_requests,
                COUNT(DISTINCT er.student_id) as unique_students,
                COUNT(DISTINCT e.equipment_id) as equipment_types
            FROM sport s
            LEFT JOIN equipment e ON s.sport_id = e.sport_id
            LEFT JOIN `equipment-requests` er ON e.equipment_id = er.equipment_id
            GROUP BY s.sport_id, s.sport_name
            HAVING total_requests > 0
            ORDER BY total_requests DESC
            LIMIT 8
        ";
        $stmt = $this->db->query($sportDemandSQL);
        $analytics['sport_demand'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 8. AVERAGE BOOKING DURATION per equipment type
        $durationSQL = "
            SELECT 
                e.equipment_name,
                s.sport_name,
                ROUND(AVG(TIMESTAMPDIFF(MINUTE, er.start_time, er.end_time)), 0) as avg_duration_mins,
                COUNT(*) as sample_size
            FROM `equipment-requests` er
            LEFT JOIN equipment e ON er.equipment_id = e.equipment_id
            LEFT JOIN sport s ON e.sport_id = s.sport_id
            GROUP BY e.equipment_id, e.equipment_name, s.sport_name
            HAVING sample_size >= 2
            ORDER BY avg_duration_mins DESC
            LIMIT 8
        ";
        $stmt = $this->db->query($durationSQL);
        $analytics['booking_duration'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 9. MOST ACTIVE STUDENTS - Frequent borrowers
        $activeStudentsSQL = "
            SELECT 
                er.student_id,
                COUNT(*) as total_bookings,
                COUNT(DISTINCT er.equipment_id) as unique_equipment_borrowed
            FROM `equipment-requests` er
            GROUP BY er.student_id
            ORDER BY total_bookings DESC
            LIMIT 5
        ";
        $stmt = $this->db->query($activeStudentsSQL);
        $analytics['active_students'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 10. QUICK SUMMARY STATS
        $summarySQL = "
            SELECT 
                (SELECT COUNT(*) FROM `equipment-requests` WHERE status = 'ACTIVE') as active_reservations,
                (SELECT COUNT(DISTINCT student_id) FROM `equipment-requests` WHERE status = 'ACTIVE') as students_with_active,
                (SELECT COUNT(DISTINCT equipment_id) FROM equipment) as total_equipment_types,
                (SELECT COUNT(DISTINCT sport_id) FROM equipment) as sports_covered
        ";
        $stmt = $this->db->query($summarySQL);
        $analytics['summary'] = $stmt->fetch(PDO::FETCH_ASSOC);

        return $analytics;
    }

    public function getAllBookingRequests() {
        $sql = "
            SELECT 
                r.request_id,
                r.student_id,
                r.equipment_id,
                e.equipment_name,
                r.request_date,
                r.start_time,
                r.end_time,
                r.purpose,
                r.status,
                r.notes,
                u.first_name,
                u.last_name,
                s.sport_name
            FROM `equipment-requests` r
            LEFT JOIN equipment e ON r.equipment_id = e.equipment_id
            LEFT JOIN student st ON r.student_id = st.student_id
            LEFT JOIN user u ON st.user_id = u.user_id
            LEFT JOIN sport s ON e.sport_id = s.sport_id
            ORDER BY r.request_date DESC, r.start_time DESC
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get equipment inventory data for PDF report
     * @return array Report data with summary and detailed equipment list
     */
    public function getInventoryReport() {
        $report = [];

        // Summary stats
        $summarySQL = "
            SELECT 
                COUNT(DISTINCT e.equipment_id) as total_equipment_types,
                COALESCE(SUM(ei.quantity), 0) as total_stock,
                COALESCE(SUM(ei.usable), 0) as total_usable,
                (COALESCE(SUM(ei.quantity), 0) - COALESCE(SUM(ei.usable), 0)) as total_damaged,
                COUNT(DISTINCT e.sport_id) as sports_covered,
                CASE 
                    WHEN COALESCE(SUM(ei.quantity), 0) > 0 
                    THEN ROUND((COALESCE(SUM(ei.usable), 0) / SUM(ei.quantity)) * 100, 1)
                    ELSE 100 
                END as overall_condition
            FROM equipment e
            LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id
        ";
        $stmt = $this->db->query($summarySQL);
        $report['summary'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Detailed equipment list
        $detailSQL = "
            SELECT 
                s.sport_name,
                e.equipment_name,
                COALESCE(SUM(ei.quantity), 0) as total_stock,
                COALESCE(SUM(ei.usable), 0) as usable,
                (COALESCE(SUM(ei.quantity), 0) - COALESCE(SUM(ei.usable), 0)) as damaged,
                CASE 
                    WHEN COALESCE(SUM(ei.quantity), 0) > 0 
                    THEN ROUND((COALESCE(SUM(ei.usable), 0) / SUM(ei.quantity)) * 100, 0)
                    ELSE 100 
                END as condition_percent
            FROM equipment e
            LEFT JOIN sport s ON e.sport_id = s.sport_id
            LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id
            GROUP BY e.equipment_id, e.equipment_name, s.sport_name
            HAVING total_stock > 0
            ORDER BY s.sport_name ASC, e.equipment_name ASC
        ";
        $stmt = $this->db->query($detailSQL);
        $report['equipment'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $report;
    }
}

