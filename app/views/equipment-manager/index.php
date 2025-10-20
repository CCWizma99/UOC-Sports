<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Colombo Sports E-Portal</title>

  <style>
    @import url("/uoc-sports/public/css/equipment-manager/index.css");
    @import url("/uoc-sports/public/css/equipment-manager/footer.css");
    @import url("/uoc-sports/public/css/equipment-manager/header.css");


  </style>
</head>

<body class="body-index">
    
<?php
    require "../app/views/templates/equipment-manager/header.php";
  
?>

  
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <p class="Menu">Menu </p>
      <a href="#">Home</a><hr class="hr1">
      <a href="#">Profile</a><hr class="hr2">
      <a href="#">News</a><hr class="hr3">
      <a href="#">Settings</a><hr class="hr4">
      <a href="http://localhost/uoc-sports/public/equipment-manager/addequipment.php">Equipment</a><hr class="hr5">
      <a href="#">Lost and Found</a><hr class="hr6">
    </aside>
    

    <!-- Calender view-->
  <main class="calendar" role="application" aria-label="Monthly calendar">
  <?php
    require "../app/views/equipment-manager/calendar.php";

?> 

  </main>



</div>



<!-- footer-->
<?php
    require "../app/views/templates/equipment-manager/footer.php";
?>

</body>
</html>