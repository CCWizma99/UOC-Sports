<?php

require_once '../app/core/Database.php';

class Equipment {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    // Insert new equipment
    public function insert($data) {
        // Automatically add EQ prefix to code
        $data['equipment_code'] = "EQ" . $data['equipment_code'];

        $sql = "INSERT INTO equipment 
                (equipment_category, equipment_code, availability_status, reserved_person_name, reserved_person_id, reserved_date, reserved_time, return_time) 
                VALUES 
                (:equipment_category, :equipment_code, :availability_status, :reserved_person_name, :reserved_person_id, :reserved_date, :reserved_time, :return_time)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    // Fetch all equipment
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM equipment ORDER BY equipment_id DESC");
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
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Get single equipment (for update form)
    public function getById($id) {
        $sql = "SELECT * FROM equipment WHERE equipment_id = :id";
        $stmt = $this->pdo->prepare($sql);
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

        $stmt = $this->pdo->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }
}
?>
