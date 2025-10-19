<?php
session_start();
require_once "db.php";

if (isset($_GET['id'])) {
    $schedule_code = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM practice_schedule WHERE schedule_code = ?");
    $stmt->bind_param("i", $schedule_code);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Equipment deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete equipment.";
    }
} else {
    $_SESSION['error'] = "Invalid equipment ID.";
}

header("Location: schedule-record.php");
exit();
?>
