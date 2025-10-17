<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sport = htmlspecialchars($_POST['sport']);
    $equipment = htmlspecialchars($_POST['equipment']);
    $reserved_person_name = htmlspecialchars($_POST['reserved_person_name']);
    $reserved_person_id = htmlspecialchars($_POST['reserved_person_id']);
    $reserved_date = htmlspecialchars($_POST['reserved_date']);
    $reserved_time = htmlspecialchars($_POST['reserved-time']);
    $return_time = htmlspecialchars($_POST['return-time']);


    echo "<h2>Report Submitted Successfully</h2>";
    echo "<p><b>Sport:</b> $sport</p>";
    echo "<p><b>Equipment:</b> $equipment</p>";
    echo "<p><b>Reserved Person's Name:</b> $reserved_person_name</p>";
    echo "<p><b>Reserved Person's Student ID:</b> $reserved_person_id</p>";
    echo "<p><b>Reserved Date:</b> $reserved_date</p>";
    echo "<p><b>Reserve Time:</b> $reserved_time</p>";
    echo "<p><b>Return Time:</b> $return_time</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add a New Reservation</title>
  <style>
    @import url("/uoc-sports/public/css/equipment-manager/form.css");
    @import url("/uoc-sports/public/css/equipment-manager/footer.css");
    @import url("/uoc-sports/public/css/equipment-manager/header.css");
    </style>
</head>
<body>

  <?php
    require "../app/views/templates/sports-manager/header-nav.php";
  
?>

  <form action="" method="post" enctype="multipart/form-data" class="report-form">
    <h2>Add a New Reservation</h2>

    <label>Reserved Person's Name</label>
    <input type="text" name="reserved_person_name" placeholder="Add the reserved person's name" required>

    <label>Sport</label>
<select name="sport" id="sport" required>
  <option value=""> Select a Sport </option>
  <option value="cricket">Cricket</option>
  <option value="football">Football</option>
  <option value="badminton">Badminton</option>
</select>

<br><br>


<label>Equipment</label>
<select name="equipment" id="equipment" required>
  <option value=""> Select Equipment </option>
</select>

    <label>Reserved Person's Student ID Number</label>
    <input type="text" name="reserved_person_id" placeholder="Add the reserved person's student ID" required>

    <label>Reserved Date</label>
    <input type="date" name="reserved_date" placeholder="Add your reservation date" required>

    <label>Reserved Time</label>
    <input type="time" name="reserved_time" placeholder="Add your reservation time" required>

    <label>Return Time</label>
    <input type="time" name="return_time" placeholder="Add your return time" required>

    <div class="buttons">
      <button type="reset" class="reset-btn">Reset</button>
      <button type="submit" class="submit-btn">Submit</button>
    </div>
  </form>

  <script>
  const equipmentOptions = {
    cricket: ["Bat", "Ball", "Gloves", "Pads", "Helmet"],
    football: ["Football", "Jersey", "Boots", "Goalkeeper Gloves"],
    badminton: ["Racket", "Shuttlecock", "Net"]
  };

  document.getElementById("sport").addEventListener("change", function() {
    const sport = this.value;
    const equipmentSelect = document.getElementById("equipment");

    // Reset equipment options
    equipmentSelect.innerHTML = "<option value=''>-- Select Equipment --</option>";

    // Populate based on selected sport
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

<!-- footer-->
<?php
    require "../app/views/templates/equipment-manager/footer.php";
?>

</body>
</html>
