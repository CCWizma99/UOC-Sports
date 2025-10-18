<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sport Manager | UOC Sports E-Portal</title>

 <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/sports-manager/index.css");
    @import url("/uoc-sports/public/css/sports-manager/sub-nav.css");
    @import url("/uoc-sports/public/css/sports-manager/barchart.css");
    @import url("/uoc-sports/public/css/general/footer.css");
</style>
</head> 

<body>
   <?php
    require "../app/views/templates/general/header.php";
    require "../app/views/sports-manager/header-subnav.php";  
  ?> 


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
  </div>
    <?php 
      // require "../app/views/sports-manager/calendar.php";
      require "../app/views/templates/general/footer.php";      
    ?>
</body>
</html>