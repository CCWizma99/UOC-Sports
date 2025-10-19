<?php
session_start();
require_once "db.php";

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "No equipment ID provided.";
    header("Location: schedule-record.php");
    exit();
}

$schedule_code = $_GET['id'];

// Fetch existing data
$stmt = $conn->prepare("SELECT * FROM practice_schedule WHERE schedule_code = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $schedule_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Equipment not found.";
    header("Location: schedule-record.php");
    exit();
}

$equipment = $result->fetch_assoc();
$stmt->close();

// Handle form submission (update)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $category = trim($_POST['sport']);
    $code = trim($_POST['equipment']);
    $location = trim($_POST['location']);
    
    $date = trim($_POST['reserved_date']);
    $reserve = trim($_POST['reserved_time']);
    $return = trim($_POST['return_time']);

    $stmt = $conn->prepare("UPDATE practice_schedule SET sport = ?, equipment = ?, `location` = ?, reserved_date = ?, reserved_time = ?, return_time = ? WHERE schedule_code = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssssssi", $category, $code, $location, $date, $reserve, $return, $schedule_code);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Equipment updated successfully!";
        header("Location: schedule-record.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to update equipment: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Equipment</title>
    <link rel="stylesheet" href="form.css">
</head>
<body>
    
    
        <div class="form">
            <h2>Edit Equipment</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Sport</label>
                    <input type="text" name="sport" value="<?= htmlspecialchars($equipment['sport']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Equipment</label>
                    <input type="text" name="equipment" value="<?= htmlspecialchars($equipment['equipment']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" value="<?= htmlspecialchars($equipment['location']) ?>" >
                </div>
                
                <div class="form-group">
                    <label>Reserved Date</label>
                    <input type="date" name="reserved_date" value="<?= htmlspecialchars($equipment['reserved_date']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Reserved Time</label>
                    <input type="time" name="reserved_time" value="<?= htmlspecialchars($equipment['reserved_time']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Return Time:</label>
                    <input type="time" name="return_time" value="<?= htmlspecialchars($equipment['return_time']) ?>" required>
                </div>

                <button type="submit" class="submit-btn">Update</button>
            </form>
        </div>
  

    
</body>
</html>
