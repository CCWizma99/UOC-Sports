<?php
$currentPage = $_SERVER['REQUEST_URI'];
?>
<nav class="sub-nav">
  <ul>
    <li><a id="sub-expenses" href="/uoc-sports/public/equipment-manager/equipments/"<?php echo strpos($currentPage, '/equipment-manager/equipments') !== false ? ' class="active"' : ''; ?>>Equipments</a></li>
    <li><a id="sub-schedules" href="/uoc-sports/public/equipment-manager/bookingrequests/"<?php echo strpos($currentPage, '/equipment-manager/bookingrequests') !== false ? ' class="active"' : ''; ?>>Booking Requests</a></li>
    <li><a id="sub-history" href="/uoc-sports/public/equipment-manager/booking-history"<?php echo strpos($currentPage, '/equipment-manager/booking-history') !== false ? ' class="active"' : ''; ?>>Booking History</a></li>
    <li><a id="sub-reservations" href="/uoc-sports/public/equipment-manager/lostitem"<?php echo strpos($currentPage, '/equipment-manager/lostitem') !== false ? ' class="active"' : ''; ?>>Lost and Found</a></li>
  </ul>
</nav>
