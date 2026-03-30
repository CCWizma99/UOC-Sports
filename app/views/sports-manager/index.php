<?php
$pageTitle = 'Equipment Manager Dashboard';
$userRole = $_SESSION['user_role'] ?? 'Equipment Manager';
$userName = $_SESSION['user_name'] ?? 'J Jayaweera';
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sport Manager | UOC Sports E-Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/sports-manager/index.css");
    @import url("/uoc-sports/public/css/general/footer.css");

    /* Practice Session Status Badges */
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.5rem;
    }

    .status-accepted {
        background-color: #10b981;
        color: white;
    }

    .status-pending {
        background-color: #f59e0b;
        color: white;
    }

    .status-active {
        background-color: #3b82f6;
        color: white;
    }

    .status-canceled,
    .status-cancelled {
        background-color: #ef4444;
        color: white;
    }

    .status-completed {
        background-color: #6b7280;
        color: white;
    }

    /* Calendar Tooltip Styles */
    .calendar-tooltip {
        position: fixed;
        background: white;
        border: 2px solid #6b1fa0;
        border-radius: 8px;
        padding: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s ease;
        max-width: 300px;
        font-size: 0.875rem;
    }

    .calendar-day {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .calendar-day:hover {
        transform: scale(1.05);
    }

    /* Mobile Menu Button */
    .mobile-menu-toggle {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 50%;
        width: 56px;
        height: 56px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        font-size: 1.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .mobile-menu-toggle:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
    }

    .mobile-menu-toggle:active {
        transform: scale(0.95);
    }

    /* Mobile Responsive Styles */
    @media (max-width: 1024px) {
        .main-wrapper {
            flex-direction: column;
        }

        .left-container,
        .right-sidebar {
            width: 100% !important;
            max-width: 100% !important;
        }

        .content-grid-row {
            flex-direction: column;
        }

        .left-column,
        .chart-section {
            width: 100% !important;
            max-width: 100% !important;
        }

        .calendar-section-compact {
            margin-bottom: 1.5rem;
            width: 100%;
        overflow: visible;
    }

    #calendar {
        width: 100%;
        overflow: visible;
    }

    .chart-wrapper {
        width: 100%;
        overflow: visible;
            width: 100%;
        }

        /* Show mobile menu button */
        .mobile-menu-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Hide right sidebar by default on mobile */
        .right-sidebar {
            position: fixed;
            top: 0;
            right: -100%;
            height: 100vh;
            width: 320px !important;
            max-width: 85vw !important;
            background: white;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
            transition: right 0.3s ease;
            overflow-y: auto;
            z-index: 999;
            padding: 1rem;
        }

        .right-sidebar.active {
            right: 0;
        }

        /* Overlay for mobile menu */
        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            transition: opacity 0.3s ease;
        }

        .mobile-overlay.active {
            display: block;
        }
    }

    @media (max-width: 768px) {
        .welcome-section h1 {
            font-size: 1.5rem;
        }

        .welcome-section p {
            font-size: 0.875rem;
        }

        .section-header {
            width: 100%;
        }

        .chart-controls {
            flex-direction: column;
            gap: 0.5rem;
            width: 100%;
        }

        .chart-controls select {
            width: 100%;
        }

        .balance-box {
            padding: 1rem;
            width: 100%;
        }

        .chart-wrapper {
            height: 200px;
            width: 100%;
        }

        #calendar,
        .calendar-section-compact {
            width: 100%;
        }

        .sidebar-section h3 {
            font-size: 1.125rem;
        }

        .session-item {
            padding: 0.75rem;
        }

        .session-details,
        .session-com {
            font-size: 0.875rem;
        }

        .status-badge {
            font-size: 0.625rem;
            padding: 0.2rem 0.5rem;
        }

        #competitionMonth {
            width: auto !important;
            min-width: 100px;
        }
    }

    @media (max-width: 480px) {
        .welcome-section h1 {
            font-size: 1.25rem;
        }

        .section-header h2 {
            font-size: 1rem;
        }

        .balance-box h3 {
            font-size: 1rem;
        }

        .balance-amount {
            font-size: 1.5rem;
        }

        .btn-select {
            font-size: 0.875rem;
            padding: 0.5rem;
        }

        .view-all-link {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }

        .sidebar-section h3 {
            font-size: 1rem;
        }
    }

</style>
</head> 

<body>
   <?php
    require "../app/views/templates/general/header.php";
?>
<div class="main-wrapper"> 
<!-- Left Sidebar -->

        <aside class="left-container">
            <!-- Content Grid: Welcome/Calendar and Chart side by side -->
            <div class="content-grid-row">
                <!-- Left Column: Welcome and Calendar -->
                <div class="left-column">
                    <div class="welcome-section" style="background:#e9ddfa">
                        <h1>Welcome, <?php echo htmlspecialchars($userName); ?>!</h1>
                        <p>Manage your sports activities and track progress</p>
                    </div>

                    <!-- Calendar Section -->
                    <div class="calendar-section-compact">
                        <div class="section-header" style="height: 1rem; color: black;">
                            <h2>Practice Sessions Calendar</h2>
                        </div>
                        <div id="calendar"></div>
                    </div>
                </div>

                <!-- Chart Section -->
                <div class="chart-section">
                    <div class="section-header">
                        <h2>Expense Overview</h2>
                
                    <div class="chart-controls">
                       
                        <select id="year" class="btn-select ">
                            <option value="2026">2026</option>
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>
                    </div>

                    <script>
                        // Store the selected sport ID for JavaScript to use
                        window.selectedSportId = '<?= htmlspecialchars($managedSportId ?? '') ?>';
                        console.log('PHP set window.selectedSportId to:', window.selectedSportId);
                        console.log('Current URL sport parameter:', new URLSearchParams(window.location.search).get('sport'));
                    </script>

                    <div class="balance-box">
                        <h3>Remaining Budget </h3>
                        <div class="balance-amount" id="balance">Rs 0.00</div>
                        <div class="progress-bar">
                            <div class="progress" id="progress"></div>
                        </div> 
                        <div class="percentage" id="percent">0% Expenses</div>
                    </div>

                    <div class="chart-wrapper" style="background: white; border-radius: 8px; ">
                       
                        <div style="position: relative; height: 350px;">
                            <canvas id="expenseLineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Right Sidebar -->
        <aside class="right-sidebar">
            <!-- Today's Sessions -->
            <div class="sidebar-section">
                <h3>Today's Sessions</h3>
                <div class="session-list" id="todaySessionsContainer">
                    <?php if (!empty($todaySessions)): ?>
                        <?php foreach ($todaySessions as $session): ?>
                            <div class="session-item">
                                <div class="session-details"><?= htmlspecialchars(date('h:i A', strtotime($session['start_time']))) ?> - <?= htmlspecialchars(date('h:i A', strtotime($session['end_time']))) ?></div>
                                <div class="session-com"><?= htmlspecialchars($session['sport_name'] ?? 'Unknown Sport') ?></div>
                                <div class="session-com"><?= htmlspecialchars($session['location']) ?></div>
                                <?php if (!empty($session['status'])): ?>
                                    <span class="status-badge status-<?= strtolower(htmlspecialchars($session['status'])) ?>">
                                        <?= htmlspecialchars($session['status']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="session-item" style="text-align: center; color: #9ca3af; padding: 1rem;">
                            No practice sessions scheduled for today
                        </div>
                    <?php endif; ?>

                     <a href="/uoc-sports/public/sport-manager/practicesessions" class="view-all-link">View All Practice Sessions</a>
                </div>
            </div>

            <!-- Upcoming Competitions -->
            <div class="sidebar-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="margin: 0;">Upcoming Competitions</h3>
                    <select id="competitionMonth" class="btn-select" style="width: auto; padding: 0.25rem; font-size: 0.8rem; margin-bottom: 0.65rem; border:2px solid #6b21a8; background: #f3e8ff;
    color: #6b21a8; box-shadow:none;">
                        <option value="01">January</option>
                        <option value="02">February</option>
                        <option value="03">March</option>
                        <option value="04">April</option>
                        <option value="05">May</option>
                        <option value="06">June</option>
                        <option value="07">July</option>
                        <option value="08">August</option>
                        <option value="09">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>
                <div class="session-list" id="upcomingCompetitionsContainer">
                    <?php 
                    // Debug: Show what sport is being used
                    if (!empty($upcomingCompetitions)) {
                        error_log("Rendering " . count($upcomingCompetitions) . " competitions for sport: " . ($managedSportId ?? 'ALL'));
                    }
                    ?>
                    <?php if (!empty($upcomingCompetitions)): ?>
                        <?php foreach ($upcomingCompetitions as $competition): ?>
                            <div class="session-item">
                                <div class="session-details"><?= htmlspecialchars($competition['competition_name']) ?></div>
                                <div class="session-com"><?= htmlspecialchars($competition['sport_name'] ?? 'Unknown Sport') ?></div>
                                <div class="session-com"><?= !empty($competition['date']) ? htmlspecialchars(date('M d, Y', strtotime($competition['date']))) : 'Date TBD' ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="session-item" style="text-align: center; color: #9ca3af; padding: 1rem;">
                           <p>No competitions scheduled</p>
                        </div>
                    <?php endif; ?>

                    <a href="/uoc-sports/public/sport-manager/competitions" class="view-all-link">View All Competitions</a>
                </div>
            </div>
        </aside>

</div>

<!-- Mobile Overlay -->
<div class="mobile-overlay" id="mobileOverlay"></div>

<!-- Mobile Menu Toggle Button -->
<button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle Sessions Menu">
    <i class="fas fa-calendar-alt"></i>
</button>

<script>
// Mobile menu toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('mobileMenuToggle');
    const rightSidebar = document.querySelector('.right-sidebar');
    const mobileOverlay = document.getElementById('mobileOverlay');

    function toggleMenu() {
        rightSidebar.classList.toggle('active');
        mobileOverlay.classList.toggle('active');
        
        // Change icon
        const icon = menuToggle.querySelector('i');
        if (rightSidebar.classList.contains('active')) {
            icon.className = 'fas fa-times';
        } else {
            icon.className = 'fas fa-calendar-alt';
        }
    }

    menuToggle.addEventListener('click', toggleMenu);
    mobileOverlay.addEventListener('click', toggleMenu);
});
</script>

<script src="/uoc-sports/public/js/sports-manager/calendar.js"></script>
<script src="/uoc-sports/public/js/sports-manager/expense-chart.js"></script>

<?php
    require "../app/views/templates/general/footer.php";      
    ?>
</body>
</html>