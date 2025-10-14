<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lost and Found-Report</title>

  <style>
    @import url("/uoc-sports/public/css/equipment-manager/footer.css");
    @import url("/uoc-sports/public/css/equipment-manager/header.css");
    @import url("/uoc-sports/public/css/equipment-manager/form.css");
  </style>
</head>

<!-- header-->
<?php
   require "../app/views/templates/equipment-manager/header.php";
?>

<body>

<form action="" method="post" enctype="multipart/form-data" class="report-form">
    <h2>Report Lost Item</h2>

    <label>Receiver’s Name</label>
    <input type="text" name="receiver_name" placeholder="Name" required>

    <label>Item</label>
    <textarea name="item" placeholder="Item Description" required></textarea>

    <label>Found Location</label>
    <input type="text" name="location" placeholder="Click on map" readonly required>

    <label>Date</label>
    <input type="date" name="date" required>

    <label>Upload an Image</label>
    <input type="file" name="image" accept="image/*">


    <div class="buttons">
      <button type="reset" class="reset-btn">Reset</button>
      <button type="submit" class="submit-btn">Submit</button>
    </div>
  </form>
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['receiver_name']);
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

    echo "<h2>Report Submitted Successfully!</h2>";
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
    require "../app/views/templates/equipment-manager/footer.php";
?>

    </body>
</html>