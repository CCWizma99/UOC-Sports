
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Yearly Expenses Chart</title>

 <style>
    @import url("/uoc-sports/public/css/sports-manager/index.css");
</style>
</head> 

<body>
<div class ="header">
   <?php
    require "../app/views/sports-manager/header-nav.php";
    require "../app/views/sports-manager/header-subnav.php";
  
?> 

  
</div>

  <div class="main-content">

      <div class="side-nav">
        <h3>Menu</h3>
  <ul>
    <li><a href="home.php" >Home</a></li>
    <li><a href="profile.php">Profile</a></li>
    <li><a href="events.php">Upcoming Events</a></li>
    <li><a href="messages.php">Messages</a></li>
  </ul>
      </div>

    <div class="barchart">   
    <?php require "../app/views/sports-manager/barchart.php"; ?>
    </div>

   <!-- <div class="calendar">
    <?php require "../app/views/sports-manager/calendar.php"; ?>

</div>
-->
</body>
</html>