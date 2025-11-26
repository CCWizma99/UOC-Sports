<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Equipment Management | Colombo Sports E-Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/equipment-manager/index.css");
    @import url("/uoc-sports/public/css/sports-manager/sub-nav.css");
    @import url("/uoc-sports/public/css/general/footer.css");
    @import url("/uoc-sports/public/css/general/header.css");

  </style>
</head>

<body>
<?php
    require "../app/views/templates/general/header.php";
    require "../app/views/equipment-manager/header-subnav.php";
		require "../app/views/equipment-manager/calendar.php";
		require "../app/views/equipment-manager/weekly-reservations.php";
    require "../app/views/templates/general/footer.php";
?>

</body>
</html>