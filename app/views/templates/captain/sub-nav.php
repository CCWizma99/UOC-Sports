<?php
$currentPage = $_SERVER['REQUEST_URI'];
?>
<nav class="sub-nav">
    <ul>
        <li><a href="/uoc-sports/public/captain/add-members"<?php echo strpos($currentPage, '/captain/add-members') !== false ? ' class="active"' : ''; ?>>Team</a></li>
        <li><a href="/uoc-sports/public/captain/schedule-practice"<?php echo strpos($currentPage, '/captain/schedule-practice') !== false ? ' class="active"' : ''; ?>>Schedule</a></li>
        <li><a href="/uoc-sports/public/captain/mark-attendance"<?php echo strpos($currentPage, '/captain/mark-attendance') !== false ? ' class="active"' : ''; ?>>Attendance</a></li>
        <li><a href="/uoc-sports/public/captain/communication"<?php echo strpos($currentPage, '/captain/communication') !== false ? ' class="active"' : ''; ?>>Communication</a></li>
    </ul>
</nav>