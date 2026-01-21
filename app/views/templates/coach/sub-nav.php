<?php
$currentPage = $_SERVER['REQUEST_URI'];
?>
<nav class="sub-nav">
  <ul>
    <li><a href="/uoc-sports/public/coach/report-injury"<?php echo strpos($currentPage, '/coach/report-injury') !== false ? ' class="active"' : ''; ?>>Injuries</a></li>
    <li><a href="/uoc-sports/public/coach/coach-communicate"<?php echo strpos($currentPage, '/coach/coach-communicate') !== false ? ' class="active"' : ''; ?>>Communication</a></li>
  </ul>
</nav>