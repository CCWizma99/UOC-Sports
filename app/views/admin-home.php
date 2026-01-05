<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
        @import url(/uoc-sports/public/css/admin/admin-home-page.css);
        @import url(/uoc-sports/public/css/admin/admin-calendar.css);
        @import url(/uoc-sports/public/css/admin/ui-improvements.css);
    </style>

</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';
?>

<div class="main-content-wrapper">
    <div class="home-grid-container">
        <!-- Left Column: Calendar -->
        <div class="home-grid-left">
            <section id="calendar-section">
                <h2><i class="fas fa-calendar-alt"></i> Facility Reservations</h2>
                <div class="calendars-wrapper">
                    <div id="calendar-current-month"></div>
                    <div id="calendar-next-month"></div>
                </div>
                <div id="booking-info">
                    <h3>Booking Info</h3>
                    <div id="booking-details" class="text-center">
                        <i class="fas fa-calendar-day"></i>
                        <p>Select a day to view booking details</p>
                    </div>
                </div>
            </section>
        </div>

        <!-- Right Column: Budget Overview -->
        <div class="home-grid-right">
            <section id="budget-section">
                <h2><i class="fas fa-chart-pie"></i> Budget Overview</h2>
                <div class="budget-chart-container">
                    <canvas id="pieChart" width="250" height="250"></canvas>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <span class="legend-color spent"></span>
                            <span>Spent Amount</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color remaining"></span>
                            <span>Remaining Amount</span>
                        </div>
                    </div>
                </div>
                <div class="budget-stats">
                    <div class="stat-row">
                        <span class="stat-label">Total Allocated Budget</span>
                        <span class="stat-value">Rs. <?= number_format($budget_summary['total_allocated'] ?? 0) ?></span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Total Expenditure</span>
                        <span class="stat-value spent">Rs. <?= number_format($budget_summary['total_spent'] ?? 0) ?></span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Total Remaining</span>
                        <span class="stat-value remaining">Rs. <?= number_format($budget_summary['total_remaining'] ?? 0) ?></span>
                    </div>
                </div>
                <a href="./admin-budget" class="view-more-btn">
                    <i class="fas fa-external-link-alt"></i> View Budget Details
                </a>
            </section>

            <!-- Quick Stats Section -->
            <section id="quick-stats">
                <h2><i class="fas fa-chart-bar"></i> Quick Stats</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-users"></i>
                        <strong id="total-users"><?= $dashboard_stats['total_users'] ?? 0 ?></strong>
                        <p>Total Users</p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-calendar-check"></i>
                        <strong id="pending-reservations"><?= $dashboard_stats['pending_reservations'] ?? 0 ?></strong>
                        <p>Pending Reservations</p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-trophy"></i>
                        <strong id="active-events"><?= $dashboard_stats['active_events'] ?? 0 ?></strong>
                        <p>Active Events</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script src="/uoc-sports/public/js/admin-calendar.js"></script>
<script>
// Draw pie chart with PHP data
function drawPieChart(spent, remaining) {
    const canvas = document.getElementById('pieChart');
    const ctx = canvas.getContext('2d');
    
    const data = [
        { label: "Spent", value: spent, color: "#111" },
        { label: "Remaining", value: remaining, color: "#4b0082" }
    ];
    
    const total = data.reduce((sum, d) => sum + d.value, 0);
    if (total === 0) return;
    
    let startAngle = -Math.PI / 2; // Start from top
    
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    data.forEach((d) => {
        const sliceAngle = (d.value / total) * 2 * Math.PI;
        
        ctx.beginPath();
        ctx.moveTo(125, 125);
        ctx.arc(125, 125, 100, startAngle, startAngle + sliceAngle);
        ctx.closePath();
        ctx.fillStyle = d.color;
        ctx.fill();
        
        startAngle += sliceAngle;
    });
    
    // Draw center circle for donut effect
    ctx.beginPath();
    ctx.arc(125, 125, 50, 0, 2 * Math.PI);
    ctx.fillStyle = '#fff';
    ctx.fill();
}

// Draw chart on page load with PHP data
document.addEventListener('DOMContentLoaded', function() {
    const spent = <?= $budget_summary['total_spent'] ?? 0 ?>;
    const remaining = <?= $budget_summary['total_remaining'] ?? 0 ?>;
    drawPieChart(spent, remaining);
});
</script>

<?php require '../app/views/templates/admin/footer.php'; ?>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</body>
</html>
