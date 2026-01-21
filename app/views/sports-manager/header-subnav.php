<?php
$currentPage = $_SERVER['REQUEST_URI'];
?>
<nav class="sub-nav">
  <ul>
    <li><a id="sub-expenses" href="/uoc-sports/public/sport-manager/expenses/"<?php echo strpos($currentPage, '/sport-manager/expenses') !== false ? ' class="active"' : ''; ?>>Expenses</a></li>
    <li><a id="sub-schedules" href="/uoc-sports/public/sport-manager/practicesessions/"<?php echo strpos($currentPage, '/sport-manager/practicesessions') !== false ? ' class="active"' : ''; ?>>Practice Sessions</a></li>
    <li><a id="sub-competitions" href="/uoc-sports/public/sport-manager/competitions/"<?php echo strpos($currentPage, '/sport-manager/competitions') !== false ? ' class="active"' : ''; ?>>Competitions</a></li>
    <li><a id="sub-team" href="/uoc-sports/public/sport-manager/team/"<?php echo strpos($currentPage, '/sport-manager/team') !== false ? ' class="active"' : ''; ?>>Student Achievements</a></li>
    <li><a id="sub-messages" href="/uoc-sports/public/sport-manager/messages/"<?php echo strpos($currentPage, '/sport-manager/messages') !== false ? ' class="active"' : ''; ?>>Messages</a></li>
  </ul>
</nav>
