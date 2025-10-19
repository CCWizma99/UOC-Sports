<?php
session_start();
require_once "db.php"; // your DB connection file

$equipment_id = "";
$category = "";
$code = "";
$location = "";
$date = "";
$reserve = "";
$return = "";
$update = false;


if (isset($_GET['id'])) {
    $schedule_code = $_GET['id'];
    $update = true;

    $stmt = $conn->prepare("SELECT * FROM practice_schedule WHERE schedule_code = ?");
    $stmt->bind_param("i", $schedule_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $category = $row['sport'];
        $code = $row['equipment'];
        $location = $row['location'];
        $date = $row['reserved_date'];
        $reserve = $row['reserved_time'];
        $return = $row['return_time'];
    }
}

// --- If submitting form ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $category = trim($_POST['sport']);
    $code = trim($_POST['equipment']);
    $location = trim($_POST['location']);
    $date = trim($_POST['reserved_date']);
    $reserve = trim($_POST['reserved_time']);
    $return = trim($_POST['return_time']);

    if (isset($_POST['schedule_code']) && !empty($_POST['schedule_code'])) {
        // UPDATE existing record
        $schedule_code = intval($_POST['schedule_code']);
        $sql = "UPDATE practice_schedule 
                SET sport = ?, equipment = ?, `location` = ?, reserved_date = ?, reserved_time = ?, return_time = ?
                WHERE schedule_code = ?"; 
        $stmt = $conn->prepare($sql);
        if (!$stmt) die("Update prepare failed: " . $conn->error);
        $stmt->bind_param("ssssssi", $category, $code, $location, $date, $reserve, $return, $schedule_code);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Equipment updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update equipment: " . $stmt->error;
        }
    } else {
        // INSERT new record
        $sql = "INSERT INTO practice_schedule (sport, equipment, `location`, reserved_date, reserved_time, return_time) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) die("Insert prepare failed: " . $conn->error);
        $stmt->bind_param("ssssss", $category, $code, $location, $date, $reserve, $return);

        if ($stmt->execute()) {
            $_SESSION['success'] = "New equipment added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add equipment: " . $stmt->error;
        }
    }

    header("Location: schedule-record.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $update ? 'Edit Schedule' : 'Add a New Schedule' ?></title>
  <link rel="stylesheet" href="form.css">
</head>
<body>
  <form action="" method="post" enctype="multipart/form-data" class="form">
    <?php if ($update): ?>
        <input type="hidden" name="schedule_code" value="<?= htmlspecialchars($schedule_code) ?>">
    <?php endif; ?>  

    <h2><?= $update ? 'Edit Schedule' : 'Add a New Schedule' ?></h2>

    <label>Sport</label>
    <select name="sport" id="sport" required>
      <option value=""> Select a Sport </option>
      <option value="cricket" <?= $category == "cricket" ? "selected" : "" ?>>Cricket</option>
      <option value="football" <?= $category == "football" ? "selected" : "" ?>>Football</option>
      <option value="badminton" <?= $category == "badminton" ? "selected" : "" ?>>Badminton</option>
    </select>

    <br><br>

    <label>Equipment</label>
    <select name="equipment" id="equipment" required>
      <option value=""> Select Equipment </option>
    </select>


    <label>Location</label>
    <input type="text" name="location" value="<?= htmlspecialchars($location) ?>">

    <label>Reserved Date</label>
    <input type="date" name="reserved_date" value="<?= htmlspecialchars($date) ?>" required>
 
    <label>Reserved Time</label>
    <input type="time" name="reserved_time" value="<?= htmlspecialchars($reserve) ?>" required>

    <label>Return Time</label>
    <input type="time" name="return_time" value="<?= htmlspecialchars($return) ?>" required>

    <div class="buttons">
      <button type="reset" class="reset-btn">Reset</button>
      <button type="submit" class="submit-btn"><?= $update ? 'Update Equipment' : 'Add Equipment' ?></button>
    </div>
  </form>

<script>
const equipmentOptions = {
  cricket: ["Bat", "Ball", "Gloves", "Pads", "Helmet"],
  football: ["Football", "Jersey", "Boots", "Goalkeeper Gloves"],
  badminton: ["Racket", "Shuttlecock", "Net"]
};

// populate on load if already selected (for edit form)
window.addEventListener("load", function() {
  const sport = document.getElementById("sport").value;
  const currentEquipment = "<?= htmlspecialchars($code) ?>";
  const equipmentSelect = document.getElementById("equipment");

  if (equipmentOptions[sport]) {
    equipmentOptions[sport].forEach(function(item) {
      const option = document.createElement("option");
      option.value = item;
      option.textContent = item;
      if (item === currentEquipment) option.selected = true;
      equipmentSelect.appendChild(option);
    });
  }
});

document.getElementById("sport").addEventListener("change", function() {
  const sport = this.value;
  const equipmentSelect = document.getElementById("equipment");
  equipmentSelect.innerHTML = "<option value=''>Select Equipment</option>";

  if (equipmentOptions[sport]) {
    equipmentOptions[sport].forEach(function(item) {
      const option = document.createElement("option");
      option.value = item;
      option.textContent = item;
      equipmentSelect.appendChild(option);
    });
  }
});
</script>

</body>
</html>
