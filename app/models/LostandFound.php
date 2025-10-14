<?php

require_once '../app/core/Database.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['reporter_name']);
    $item = htmlspecialchars($_POST['item']);
    $location = htmlspecialchars($_POST['location']);
    $date = htmlspecialchars($_POST['date']);
    
    // Handle image upload
    $image = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $image = $targetDir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    }

    echo "<h2>Report Submitted Successfully! </h2>";
    echo "<p><b>Reporter:</b> $name</p>";
    echo "<p><b>Item:</b> $item</p>";
    echo "<p><b>Location:</b> $location</p>";
    echo "<p><b>Date:</b> $date</p>";
    if ($image) {
        echo "<p><b>Image:</b><br><img src='$image' width='200'></p>";
    }
}
?>


<?php
class Report {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    //Insert a new report
    public function insert($data) {
        $sql = "INSERT INTO reports 
                (reporter_name, item, location, date, image_path) 
                VALUES 
                (:reporter_name, :item, :location, :date, :image_path)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    //Fetch all reports (latest first)
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM reports ORDER BY report_id DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Add action buttons for each row
        foreach ($rows as &$row) {
            $id = $row['report_id'];
            $row['actions'] = '
                <a href="update_report.php?id='.$id.'" class="btn btn-sm btn-primary">Update</a>
                <a href="delete_report.php?id='.$id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</a>
            ';
        }

        return $rows;
    }

    //Fetch a single report by ID
    public function getById($id) {
        $sql = "SELECT * FROM reports WHERE report_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //Update an existing report
    public function update($id, $data) {
        $sql = "UPDATE reports 
                SET reporter_name = :reporter_name,
                    item = :item,
                    location = :location,
                    date = :date,
                    image_path = :image_path
                WHERE report_id = :id";

        $stmt = $this->pdo->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    //Delete a report
    public function delete($id) {
        $sql = "DELETE FROM reports WHERE report_id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
?>
