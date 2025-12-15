<?php
class Equipment {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection(); // Your PDO connection
    }

    public function addEquipment($name, $quantity, $date, $remarks, $sport_id, $condition, $files) {
        $equipmentId = uniqid("eq_", true);
    
        // Insert main equipment record (no image_name here)
        $sql = "
            INSERT INTO equipment 
            (equipment_id, equipment_name, sport_id, equipment_condition, remarks, quantity, image_name)
            VALUES (:equipment_id, :equipment_name, :sport_id, :condition, :remarks, :quantity, '')
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'equipment_id' => $equipmentId,
            'equipment_name' => $name,
            'sport_id' => $sport_id,
            'condition' => $condition,
            'remarks' => $remarks,
            'quantity' => $quantity
        ]);
    
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
                LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id
                WHERE 
                    e.equipment_name LIKE :q 
                    OR e.equipment_id LIKE :q
                    OR s.sport_name LIKE :q
                GROUP BY e.equipment_id, e.equipment_name, e.image_name, s.sport_name
                ORDER BY e.equipment_name";
    
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
            WHERE e.equipment_name LIKE :query
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

    public function addEquipmentType($sport_id, $equipment_name, $image_name) {
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
                (equipment_id, sport_id, equipment_name, image_name)
                VALUES (:equipment_id, :sport_id, :equipment_name, :image_name)";
    
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':equipment_id' => $equipment_id,
            ':sport_id' => $sport_id,
            ':equipment_name' => $equipment_name,
            ':image_name' => $image_name
        ]);
    
        return true;
    }
    

    public function getEquipments($sport_id){
        $sql = "SELECT equipment_id, equipment_name, image_name
                FROM equipment
                WHERE sport_id = :sport_id
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
        $stmt = $this->db->query("SELECT * FROM equipment ORDER BY equipment_id DESC");
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

    // Delete equipment
    public function delete($id) {
        $sql = "DELETE FROM equipment WHERE equipment_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Get single equipment (for update form)
    public function getById($id) {
        $sql = "SELECT * FROM equipment WHERE equipment_id = :id";
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
        $sql = "INSERT INTO `equipment-requests`
                (request_id, student_id, equipment_id, request_date, start_time, end_time, purpose, notes)
                VALUES (:id, :student_id, :equipment_id, :date, :start, :end, :purpose, :notes)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'student_id' => $student_id,
            'equipment_id' => $equipment_id,
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
        $sql = "DELETE FROM `equipment-requests` WHERE request_id = :reservation_id AND student_id = :student_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'reservation_id' => $reservationId,
            'student_id' => $studentId['student_id']
        ]);
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
}

