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
  <title>Equipment Management | Colombo Sports E-Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/equipment-manager/index.css");
    @import url("/uoc-sports/public/css/general/footer.css");
    @import url("/uoc-sports/public/css/general/header.css");

    /* Status-based reservation item colors */
    .reservation-item {
        border-left: 4px solid #d1d5db;
        transition: all 0.3s ease;
    }

    .reservation-item:has(.status-badge.pending) {
        border-left-color: #f59e0b;
        
    }

    .reservation-item:has(.status-badge.accepted) {
        border-left-color: #10b981;
        
    }

    .reservation-item:has(.status-badge.approved) {
        border-left-color: #3b82f6;
        
    }

    .reservation-item:has(.status-badge.completed) {
        border-left-color: #6b7280;
        
    }

    .reservation-item:has(.status-badge.rejected) {
        border-left-color: #ef4444;
        
    }

    /* Equipment Reservation Calendar Tooltip Styles */
    .calendar-tooltip {
        position: fixed;
        background: white;
        border: 3px solid #a855f7;
        border-radius: 10px;
        padding: 14px;
        box-shadow: 0 8px 24px rgba(168, 85, 247, 0.3);
        z-index: 10000;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s ease;
        max-width: 350px;
        min-width: 280px;
        font-size: 0.875rem;
        display: block;
    }

    .calendar-day {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    /* Equipment Reservation Status Badges */
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

    .status-badge.accepted {
        background-color: #10b981;
        color: white;
    }

    .status-badge.pending {
        background-color: #f59e0b;
        color: white;
    }

    .status-badge.completed {
        background-color: #6b7280;
        color: white;
    }

    .status-badge.rejected {
        background-color: #ef4444;
        color: white;
    }

  </style>
</head>

<body>
<?php
    require "../app/views/templates/general/header.php";
?>
        <div class="main-content-wrapper">
        
        <aside class="left-sidebar">
            <!-- Today's Pending Requests -->
            <div class="sidebar-section">
                <h3>Today's Pending Requests (<?= $todayDate ?? date('Y-m-d') ?>)</h3>
                
                <!-- Debug Info -->
                <?php if (isset($_GET['debug'])): ?>
                    <div style="background: #fff3cd; padding: 0.5rem; margin: 0.5rem 0; border-radius: 4px; font-size: 0.75rem;">
                        <strong>Debug:</strong><br>
                        Today's Date: <?= $todayDate ?? 'Not set' ?><br>
                        Reservations Count: <?= count($todayReservations ?? []) ?><br>
                        <?php if (!empty($todayReservations)): ?>
                            <pre style="font-size: 0.7rem;"><?= print_r($todayReservations[0], true) ?></pre>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="reservations-list" id="todayReservations">
                    <?php if (!empty($todayReservations)): ?>
                        <?php foreach ($todayReservations as $reservation): 
                            $statusClass = match($reservation['status']) {
                                'PENDING' => 'pending',
                                'ACCEPTED' => 'accepted',
                                'ACTIVE' => 'approved',
                                'COMPLETED' => 'completed',
                                'REJECTED' => 'rejected',
                                default => 'pending'
                            };
                        ?>
                            <div class="reservation-item">                      
                                <div class="reservation-header">
                                    <span class="reservation-time">
                                        <?= date('h:i A', strtotime($reservation['start_time'])) ?> - 
                                        <?= date('h:i A', strtotime($reservation['end_time'])) ?>
                                    </span>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <?= htmlspecialchars($reservation['status']) ?>
                                    </span>
                                </div>
                                <p class="reservation-equipment">
                                    <?= htmlspecialchars($reservation['category_name'] ?? 'Equipment') ?>
                                    <?php if (!empty($reservation['sport_name'])): ?>
                                        (<?= htmlspecialchars($reservation['sport_name']) ?>)
                                    <?php endif; ?>
                                </p>
                                
                                <?php if (!empty($reservation['reserved_location'])): ?>
                                    <p class="reservation-location" style="font-size: 0.85rem;">
                                        <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($reservation['reserved_location']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-reservations">
                            <p>No pending requests for today</p>
                        </div>
                    <?php endif; ?>

                     <a href="/uoc-sports/public/equipment-manager/bookingrequests" class="view-all-link">
                        View All Booking Requests
                    </a>

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

            <!-- Two Column Layout: Stats and Calendar -->
            <div class="stats-calendar-wrapper">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">                  
                        <div class="stat-info">
                            <h4>Pending Requests</h4>
                            <p class="stat-number"><?= $statistics['pending_count'] ?? 0 ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                      
                        <div class="stat-info">
                            <h4>Active Bookings</h4>
                            <p class="stat-number"><?= $statistics['active_count'] ?? 0 ?></p>
                        </div>
                    </div>
                </div>

                <!-- Calendar Section (Compact) -->
                <div class="calendar-section-compact">
                    <h3>Equipment Reservations Calendar</h3>
                    <div id="equipmentCalendar"></div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar with Unclaimed Items -->
        <aside class="right-sidebar">
            <div class="sidebar-section">
                <h3>Unclaimed Items (This Month)</h3>
                <div class="found-items-list" id="foundItems">
                    <?php if (count($lostitem) > 0): ?>
                        <?php foreach ($lostitem as $lst): ?>
                            <?php
                                $itemStatus = $lst['item_status'] ?? ($lst['itemStatus'] ?? '');
                                $itemName = $lst['item_name'] ?? ($lst['itemName'] ?? '');
                                $lostDate = $lst['lost_date'] ?? ($lst['foundDate'] ?? '');
                            ?>
                            <?php if ($itemStatus === 'unclaimed'): ?>
                                <div class="found-item">
                                    <div class="found-item-header">
                                        <span class="found-item-name"><?php echo htmlspecialchars($itemName); ?></span>
                                        <span class="days-ago"><?php echo htmlspecialchars($lostDate); ?></span>
                                    </div>
                                    <p >
                                        <?php echo htmlspecialchars($lst['description']); ?>
                            </p>
                                    <?php if (!empty($lst['image'])): ?>
                                        <img src="/uoc-sports/app/internal/lostitem/<?php echo htmlspecialchars($lst['image']); ?>" 
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
                <a href="/uoc-sports/public/equipment-manager/lostitem" class="view-all-link">
                    View All Found Items
                </a>
            </div>
        </aside>
        
    </div>
            
    <script src="/uoc-sports/public/js/equipment-manager/calendar.js"></script>

<?php
    require "../app/views/templates/general/footer.php";
?>

</body>
</html>

