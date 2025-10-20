<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Colombo Sports E-Portal</title>

  <style>
    @import url(/uoc-sports/public/css/global.css);
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/general/footer.css");
    @import url("/uoc-sports/public/css/equipment-manager/calendar.css");

  </style>
</head>

<body class="body-index">
    
<?php
    require "../app/views/templates/general/header.php";
?>

  
  <div class="container">
    <!-- Calender view-->
  <main class="calendar" role="application" aria-label="Monthly calendar">
    <div class="cal-head">
      <div class="month-info" aria-hidden="true">
        <div class="month" id="monthLabel">Month</div>
        <div class="year" id="yearLabel">Year</div>
      </div>

      <div class="nav" role="toolbar" aria-label="Calendar navigation">
        <button id="prevBtn" class="prevBtn" title="Previous month" aria-label="Previous month"> Previous </button>
        <button id="nextBtn" class="nextBtn" title="Next month" aria-label="Next month"> Next</button>
        <p id="todayBtn" class="today-btn" title="Go to today"></p> 
      </div>
    </div>

    <div class="weekdays" aria-hidden="true">
      <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
    </div>

    <section class="days" id="daysGrid" aria-live="polite">
      <!-- JS will populate this -->
    </section>
  </main>

  <!-- reservations bar-->
<div class="today-reservation">
  <p class="reservations">Reservations </p>
  <div>
    <p>Reserved Person</p>
    <p>Reserved Time Slot</p>
    <p>Reserved Equipemnt Category</p>
    <p>Reserved Equipment Code</p><hr class="hrx">
  </div>
</div>

</div>



<!-- footer-->
<?php
    require "../app/views/templates/general/footer.php";
?>

</body>
</html>