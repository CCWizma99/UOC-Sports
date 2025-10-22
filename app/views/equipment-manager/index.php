<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Colombo Sports E-Portal</title>

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/equipment-manager/index.css");
    @import url("/uoc-sports/public/css/equipment-manager/calendar.css");
    @import url("/uoc-sports/public/css/sports-manager/sub-nav.css");
    @import url("/uoc-sports/public/css/general/footer.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/equipment-manager/weekly-reservations.css");

  </style>
</head>

<body class="body-index">
    
<?php
    require "../app/views/templates/general/header.php";
    require "../app/views/equipment-manager/header-subnav.php";
?>

  
  	<div class="container">
		<!-- Calender view-->
		<main class="calendar" role="application" aria-label="Monthly calendar">
			<?php
				require "../app/views/equipment-manager/weekly-reservations.php";
				require "../app/views/equipment-manager/calendar.php";
				?> 
		</main>
	</div>

<!-- footer-->
<?php
    require "../app/views/templates/general/footer.php";
?>

</body>
</html>