<?php
$pageTitle = 'Equipment Manager Dashboard';
$userRole = $_SESSION['user_role'] ?? 'Equipment Manager';
$userName = $_SESSION['user_name'] ?? 'John Doe';
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sport Manager | UOC Sports E-Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/sports-manager/sub-nav.css");
    @import url("/uoc-sports/public/css/sports-manager/index.css");
    @import url("/uoc-sports/public/css/general/footer.css");

</style>
</head> 

<body>
   <?php
    require "../app/views/templates/general/header.php";
    require "../app/views/sports-manager/header-subnav.php";  
?>
<div class="main-wrapper"> 
<!-- Left Sidebar -->

        <aside class="left-container">
           <div class="welcome-section">
                <h1>Welcome, <?php echo htmlspecialchars($userRole); ?>!</h1>
                <p>Manage your sports activities and track progress</p>
            </div>

            <!-- Content Grid: Calendar and Chart in one row -->
            <div class="content-grid-row">
                <!-- Calendar Section -->
                <div class="calendar-section-compact">
                    <div class="section-header">
                        <h2>Practice Sessions Calendar</h2>
                    </div>
                    <div id="calendar"></div>
                </div>

                <!-- Chart Section -->
                <div class="chart-section">
                    <div class="section-header">
                        <h2>Expense Overview</h2>
                
                    <div class="chart-controls">
                        <select id="sport" class="btn-primary ">
                            <option value="">All Sports</option>
                            <option value="basketball">Basketball</option>
                            <option value="football">Football</option>
                            <option value="volleyball">Volleyball</option>
                        </select>
                        <select id="year" class="btn-primary ">
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>
                    </div>

                    <div class="balance-box">
                        <h3>Remaining Balance</h3>
                        <div class="balance-amount" id="balance">Rs 0.00</div>
                        <div class="progress-bar">
                            <div class="progress" id="progress"></div>
                        </div> 
                        <div class="percentage" id="percent">0% Expenses</div>
                    </div>

                    <div class="chart-wrapper" id="chartBox">
                        <div class="y-axis" id="yAxis"></div>
                        <div class="y-axis-title">Expenses (Rs)</div>
                        <div class="x-axis-title">Months</div> 
                    </div>
                </div>
            </div>
        </aside>

        <!-- Right Sidebar -->
        <aside class="right-sidebar">
            <!-- Today's Sessions -->
            <div class="sidebar-section">
                <h3> Today's Sessions</h3>
                <div class="sessions-list" id="todaySessions">
                    <div class="session-item">
                        <div class="session-details">09:00 AM - 11:00 AM</div>
                        <div class="session-com">Basketball</div>
                        <div class="session-com">Court 1</div>
                    </div>
                    <div class="session-item">
                        <div class="session-details">02:00 PM - 04:00 PM</div>
                        <div class="session-com">Football</div>
                        <div class="session-com">Main Field</div>
                    </div>

                     <a href="/uoc-sports/sports-manager/practiceschedule" class="view-all-link">View All Practice Sessions</a>
                </div>
            </div>

            <!-- Upcoming Competitions -->
            <div class="sidebar-section">
                <h3> Upcoming Competitions</h3>
                <div class="session-list">
                    <div class="session-item">
                        <div class="session-details">Inter-University Basketball</div>
                        <div class="session-com">Nov 5, 2025</div>
                        
                    </div>
                    <div class="session-item">
                        <div class="session-details">Football Championship</div>
                        <div class="session-com">Nov 12, 2025</div>
                       
                    </div>

                    <a href="/uoc-sports/sports-manager/practiceschedule" class="view-all-link">View All Competitions</a>
                </div>
            </div>
        </aside>

</div>

<script src="/uoc-sports/public/js/equipment-manager/calendar.js"></script>
<script src="/uoc-sports/public/js/sports-manager/expense-chart.js"></script>

<?php
    require "../app/views/templates/general/footer.php";      
    ?>
</body>
</html>