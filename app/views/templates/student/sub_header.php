<?php
    $current_uri = $_SERVER['REQUEST_URI'];
    
    // Helper function to check if link is active
    function isActive($uri, $path) {
        // Check if the current URI ends with the path or contains the path followed by a query string or slash
        if ($path === '/student/' && ($uri === '/uoc-sports/public/student/' || $uri === '/uoc-sports/public/student' || strpos($uri, '/student/index') !== false)) {
            // Also need to be careful not to match other routes starting with /student/
            // But since other routes are checked specifically below, this block is for the base URL.
            // A better check for 'Overview' (base path) is if it equals exactly or ends with index.
            // The original logic was:
            // if ($path === '/student/' && ($uri === '/uoc-sports/public/student/' || $uri === '/uoc-sports/public/student'))
            return 'active';
        }
        if ($path !== '/student/' && strpos($uri, $path) !== false) {
            return 'active';
        }
        return '';
    }
?>

<nav class="sub-nav">
  <ul>
    <li>
        <a href="/uoc-sports/public/student/" class="<?php echo isActive($current_uri, '/student/'); ?>">
            Overview
        </a>
    </li>
    <li>
        <a href="/uoc-sports/public/student/sports" class="<?php echo isActive($current_uri, '/student/sports'); ?>">
            Sports
        </a>
    </li>
    <li>
        <a href="/uoc-sports/public/student/equipment" class="<?php echo isActive($current_uri, '/student/equipment'); ?>">
            Equipments
        </a>
    </li>
    <li>
        <a href="/uoc-sports/public/student/facilities" class="<?php echo isActive($current_uri, '/student/facilities'); ?>">
            Facilities
        </a>
    </li>
    <li>
        <a href="/uoc-sports/public/student/bookings" class="<?php echo isActive($current_uri, '/student/bookings'); ?>">
            My Bookings
        </a>
    </li>
  </ul>
</nav>
