<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Dashboard | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
        @import url(/uoc-sports/public/css/admin/executive-dashboard.css);
    </style>
</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';
?>

<div class="main-content-wrapper">
    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <h1><i class="fas fa-chart-pie"></i> Executive Dashboard</h1>
                <p>University Sports Management Overview</p>
            </div>
            <div class="header-actions">
                <a href="./admin-index" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </div>
        </div>

        <!-- KPI Cards Row -->
        <div class="kpi-row" id="kpi-cards">
            <div class="kpi-card loading">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Loading...</span>
            </div>
        </div>

        <!-- Detailed Insights Grid -->
        <div class="insights-section">
            <h2><i class="fas fa-lightbulb"></i> Detailed Insights</h2>
            <div class="insights-grid" id="insights-grid">
                <div class="insight-card loading">
                    <i class="fas fa-spinner fa-spin"></i> Loading insights...
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let dashboardData = null;

document.addEventListener('DOMContentLoaded', loadDashboard);

async function loadDashboard() {
    try {
        const response = await fetch('admin-dashboard/analytics');
        const result = await response.json();
        
        if (result.status === 'success') {
            dashboardData = result.data;
            renderKPICards();
            renderInsights();
        } else {
            showError('Failed to load dashboard data');
        }
    } catch (error) {
        showError('Network error');
        console.error(error);
    }
}

function renderKPICards() {
    const container = document.getElementById('kpi-cards');
    const { users, reservations, equipment, events, budget } = dashboardData;
    
    container.innerHTML = `
        <a href="./admin-users" class="kpi-card users">
            <div class="kpi-icon"><i class="fas fa-users"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">${users.total.toLocaleString()}</div>
                <div class="kpi-label">Active Users</div>
                <div class="kpi-sub">+${users.new_this_month} this month</div>
            </div>
        </a>
        
        <a href="./admin-reservation-analytics" class="kpi-card reservations">
            <div class="kpi-icon"><i class="fas fa-calendar-check"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">${reservations.avg_utilization}%</div>
                <div class="kpi-label">Facility Utilization</div>
                <div class="kpi-sub">${reservations.this_month} bookings this month</div>
            </div>
        </a>
        
        <a href="./admin-equipment-analytics" class="kpi-card equipment">
            <div class="kpi-icon"><i class="fas fa-boxes-stacked"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">${equipment.total_quantity.toLocaleString()}</div>
                <div class="kpi-label">Equipment Items</div>
                <div class="kpi-sub">${equipment.needs_attention} need attention</div>
            </div>
        </a>
        
        <a href="./admin-events" class="kpi-card events">
            <div class="kpi-icon"><i class="fas fa-trophy"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">${events.active}</div>
                <div class="kpi-label">Active Events</div>
                <div class="kpi-sub">${events.upcoming} upcoming</div>
            </div>
        </a>
        
        <div class="kpi-card budget">
            <div class="kpi-icon"><i class="fas fa-wallet"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">${budget.percent_used}%</div>
                <div class="kpi-label">Budget Used</div>
                <div class="kpi-sub">Rs. ${formatCurrency(budget.remaining)} remaining</div>
            </div>
            <div class="budget-bar">
                <div class="budget-fill" style="width: ${Math.min(budget.percent_used, 100)}%"></div>
            </div>
        </div>
    `;
}

function renderInsights() {
    const container = document.getElementById('insights-grid');
    const { users, reservations, equipment, events, budget } = dashboardData;
    
    container.innerHTML = `
        <!-- User Distribution -->
        <div class="insight-card">
            <div class="card-header">
                <i class="fas fa-user-group"></i>
                <h3>User Distribution</h3>
            </div>
            <div class="card-content">
                ${renderBarChart(users.type_distribution, 'type', 'count')}
            </div>
        </div>
        
        <!-- Equipment Condition -->
        <div class="insight-card">
            <div class="card-header">
                <i class="fas fa-tools"></i>
                <h3>Equipment Condition</h3>
            </div>
            <div class="card-content">
                ${renderBarChart(equipment.condition_distribution, 'condition', 'count')}
            </div>
        </div>
        
        <!-- Budget Breakdown -->
        <div class="insight-card wide">
            <div class="card-header">
                <i class="fas fa-chart-pie"></i>
                <h3>Budget Overview (${budget.year})</h3>
            </div>
            <div class="card-content">
                <div class="budget-stats">
                    <div class="budget-stat">
                        <span class="stat-label">Allocated</span>
                        <span class="stat-value">Rs. ${formatCurrency(budget.allocated)}</span>
                    </div>
                    <div class="budget-stat">
                        <span class="stat-label">Spent</span>
                        <span class="stat-value spent">Rs. ${formatCurrency(budget.spent)}</span>
                    </div>
                    <div class="budget-stat">
                        <span class="stat-label">Remaining</span>
                        <span class="stat-value remaining">Rs. ${formatCurrency(budget.remaining)}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="insight-card">
            <div class="card-header">
                <i class="fas fa-bolt"></i>
                <h3>Quick Stats</h3>
            </div>
            <div class="card-content">
                <div class="quick-stats">
                    <div class="quick-stat">
                        <span class="stat-icon"><i class="fas fa-calendar"></i></span>
                        <span class="stat-info">
                            <strong>${reservations.total}</strong>
                            Total Reservations
                        </span>
                    </div>
                    <div class="quick-stat">
                        <span class="stat-icon"><i class="fas fa-hourglass-half"></i></span>
                        <span class="stat-info">
                            <strong>${reservations.pending}</strong>
                            Pending Approvals
                        </span>
                    </div>
                    <div class="quick-stat">
                        <span class="stat-icon"><i class="fas fa-medal"></i></span>
                        <span class="stat-info">
                            <strong>${events.completed_this_year}</strong>
                            Events Completed
                        </span>
                    </div>
                    <div class="quick-stat">
                        <span class="stat-icon"><i class="fas fa-box"></i></span>
                        <span class="stat-info">
                            <strong>${equipment.total_types}</strong>
                            Equipment Types
                        </span>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderBarChart(data, labelKey, valueKey) {
    if (!data || data.length === 0) return '<p class="no-data">No data available</p>';
    
    const maxValue = Math.max(...data.map(d => d[valueKey]));
    
    return `<div class="bar-chart">${data.map(item => `
        <div class="bar-row">
            <span class="bar-label">${item[labelKey]}</span>
            <div class="bar-track">
                <div class="bar-fill" style="width: ${(item[valueKey] / maxValue) * 100}%"></div>
            </div>
            <span class="bar-value">${item[valueKey]}</span>
        </div>
    `).join('')}</div>`;
}

function formatCurrency(amount) {
    return Number(amount).toLocaleString('en-IN');
}

function showError(message) {
    document.getElementById('kpi-cards').innerHTML = `
        <div class="error-card">
            <i class="fas fa-exclamation-circle"></i>
            <span>${message}</span>
        </div>
    `;
}
</script>

<?php require '../app/views/templates/admin/footer.php'; ?>

</body>
<script>
    var currentPage = document.getElementById("sidebar-dashboard");
    if (currentPage) currentPage.classList.add("active");
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</html>
