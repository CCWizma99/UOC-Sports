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
?>
        <div class="main-content-wrapper">
        
        <aside class="left-sidebar">
            <!-- Today's Reservations -->
            <div class="sidebar-section">
                <h3>Today's Reservations</h3>
                <div class="reservations-list" id="todayReservations">

                    <div class="reservation-item">                      
                        <div class="reservation-header">
                            <span class="reservation-time">09:00 AM - 11:00 AM</span>
                            <span class="status-badge pending">Pending</span>
                        </div>
                        <p class="reservation-equipment">Basketball Court A</p>
                        <p class="reservation-user">User: Sarah Johnson</p>
                    </div>

                    <div class="reservation-item">
                        <div class="reservation-header">
                            <span class="reservation-time">11:30 AM - 01:00 PM</span>
                            <span class="status-badge approved">Approved</span>
                        </div>
                        <p class="reservation-equipment">Tennis Rackets (x4)</p>
                        <p class="reservation-user">User: Mike Chen</p>
                    </div>

                    <div class="reservation-item">
                        <div class="reservation-header">
                            <span class="reservation-time">02:00 PM - 04:00 PM</span>
                            <span class="status-badge approved">Approved</span>
                        </div>
                        <p class="reservation-equipment">Swimming Pool Lane 3</p>
                        <p class="reservation-user">User: Emma Davis</p>
                    </div>

                    <div class="reservation-item">
                        <div class="reservation-header">
                            <span class="reservation-time">04:30 PM - 06:00 PM</span>
                            <span class="status-badge pending">Pending</span>
                        </div>
                        <p class="reservation-equipment">Volleyball Net & Balls</p>
                        <p class="reservation-user">User: James Wilson</p>
                    </div>

                     <a href="/uoc-sports/public/equipment-manager/practiceschedule">
                <div class="view-all-link">
                    
                        View All Found Items 
                   
                </div>  </a>

                </div>
            </div>
        </aside>
    
        <!-- Main Content Area with Vertical Layout -->
        <div class="content-wrapper">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <h1>Welcome, <?php echo htmlspecialchars($userName); ?>!</h1>
                <p>Manage your equipment bookings and reservations</p>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">                  
                    <div class="stat-info">
                        <h4>Pending Requests</h4>
                        <p class="stat-number">12</p>
                    </div>
                </div>
                <div class="stat-card">
                  
                    <div class="stat-info">
                        <h4>Approved Today</h4>
                        <p class="stat-number">8</p>
                    </div>
                </div>
                
            </div>

            <!-- Calendar Section (Compact) -->
            <div class="calendar-section-compact">
                <h3>Equipment Reservations Calendar</h3>
                <div id="calendar"></div>
            </div>
        </div>

        <!-- Right Sidebar with Unclaimed Items -->
        <aside class="right-sidebar">
            <div class="sidebar-section">
                <h3>Unclaimed Items (This Month)</h3>
                <div class="found-items-list" id="foundItems">
                    <?php if (count($lostitem) > 0): ?>
                        <?php foreach ($lostitem as $lst): ?>
                            <?php if ($lst['itemStatus'] == 'unclaimed'): ?>
                                <div class="found-item">
                                    <div class="found-item-header">
                                        <span class="found-item-name"><?php echo htmlspecialchars($lst['itemName']); ?></span>
                                        <span class="days-ago"><?php echo htmlspecialchars($lst['foundDate']); ?></span>
                                    </div>
                                    <p >
                                        <?php echo htmlspecialchars($lst['description']); ?>
                            </p>
                                    <?php if (!empty($lst['image'])): ?>
                                        <img src="/project/uoc-sports/public/<?php echo htmlspecialchars($lst['image']); ?>" 
                                             alt="Item Image" 
                                             style="width: 100%; max-width: 150px; border-radius: 6px; margin-top: 0.5rem;">
                                    <?php else: ?>
                                        <span>No image</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>
                            No unclaimed items this month
                        </p>
                    <?php endif; ?>
                </div>
                <a href="/uoc-sports/public/equipment-manager/lostitem">
                <div class="view-all-link">
                    
                        View All Found Items 
                   
                </div>  </a>
            </div>
        </aside>
        
    </div>
            
    <script src="/project/uoc-sports/public/js/equipment-manager/calendar.js"></script>

<?php
    require "../app/views/templates/general/footer.php";
?>

</body>
</html>

