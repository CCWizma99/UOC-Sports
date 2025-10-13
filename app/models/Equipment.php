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
    public function search($query) {
        $sql = "
            SELECT 
                e.equipment_id,
                e.equipment_name,
                e.sport_id,
                s.sport_name,
                e.equipment_condition,
                e.quantity,
                MIN(i.image_name) AS image_name
            FROM equipment e
            JOIN sport s ON e.sport_id = s.sport_id
            JOIN equipment_image i ON e.equipment_id = i.equipment_id
            WHERE e.equipment_id LIKE :query
            OR e.equipment_name LIKE :query
            OR e.sport_id LIKE :query
            OR s.sport_name LIKE :query
            GROUP BY e.equipment_id
            LIMIT 5;
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
}

