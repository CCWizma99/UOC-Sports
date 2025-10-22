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
    @import url("/uoc-sports/public/css/sports-manager/calendar.css");
    @import url("/uoc-sports/public/css/general/footer.css");
</style>
</head> 

<body>
   <?php
    require "../app/views/templates/general/header.php";
    require "../app/views/sports-manager/header-subnav.php";  
  ?> 

    <div class="barchart">   
    <?php require "../app/views/sports-manager/barchart.php"; ?>
    <?php require "../app/views/sports-manager/calendar.php"; ?>
    </div>
  </div>
    <?php 
      require "../app/views/templates/general/footer.php";      
    ?>
</body>
</html>