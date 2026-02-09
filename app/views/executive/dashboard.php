<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Dashboard | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        @import url(/uoc-sports/public/css/admin/executive-standalone.css);
    </style>
</head>
<body>

<!-- Standalone Header -->
<header class="exec-header">
    <div class="header-brand">
        <img src="/uoc-sports/public/images/uoc-logo.png" alt="UOC Sports" class="logo">
        <div class="brand-text">
            <h1>UOC Sports</h1>
            <span>Executive Dashboard</span>
        </div>
    </div>
    <div class="header-actions">
        <span class="current-date" id="current-date"></span>
        <a href="./admin-index" class="btn-admin">
            <i class="fas fa-cog"></i> Admin Portal
        </a>
    </div>
</header>

<!-- Main Content -->
<main class="exec-main">
    <div class="dashboard-container">
        
        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <div class="welcome-content">
                <h2>University Sports Management Overview</h2>
                <p>Real-time insights across all departments</p>
            </div>
            <div class="welcome-icon">
                <i class="fas fa-chart-pie"></i>
            </div>
        </div>

        <!-- KPI Cards Row -->
        <div class="kpi-row" id="kpi-cards">
            <div class="kpi-card loading">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Loading dashboard...</span>
            </div>
        </div>

        <!-- Detailed Insights Grid -->
        <div class="insights-section">
            <h2><i class="fas fa-lightbulb"></i> Detailed Breakdown</h2>
            <div class="insights-grid" id="insights-grid">
                <div class="insight-card loading">
                    <i class="fas fa-spinner fa-spin"></i> Loading insights...
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Footer -->
<footer class="exec-footer">
    <p>&copy; <?php echo date('Y'); ?> University of Colombo - Sports Management System</p>
</footer>

<script>
let dashboardData = null;

document.addEventListener('DOMContentLoaded', () => {
    loadDashboard();
    updateDate();
});

function updateDate() {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', options);
}

async function loadDashboard() {
    try {
        const response = await fetch('executive-dashboard/analytics');
        const result = await response.json();
        
        if (result.status === 'success') {
            dashboardData = result.data;
            renderKPICards();
            renderInsights();
        } else {
            showError('Failed to load dashboard data');
        }
    } catch (error) {
        showError('Network error - please try again');
        console.error(error);
    }
}

function renderKPICards() {
    const container = document.getElementById('kpi-cards');
    const { users, reservations, equipment, events, budget } = dashboardData;
    
    container.innerHTML = `
        <div class="kpi-card users">
            <div class="kpi-icon"><i class="fas fa-users"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">${users.total.toLocaleString()}</div>
                <div class="kpi-label">Active Users</div>
                <div class="kpi-trend up">
                    <i class="fas fa-arrow-up"></i> +${users.new_this_month} this month
                </div>
            </div>
        </div>
        
        <div class="kpi-card reservations">
            <div class="kpi-icon"><i class="fas fa-calendar-check"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">${reservations.avg_utilization}%</div>
                <div class="kpi-label">Facility Utilization</div>
                <div class="kpi-sub">${reservations.this_month} bookings this month</div>
            </div>
        </div>
        
        <div class="kpi-card equipment">
            <div class="kpi-icon"><i class="fas fa-boxes-stacked"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">${equipment.total_quantity.toLocaleString()}</div>
                <div class="kpi-label">Equipment Items</div>
                <div class="kpi-sub ${equipment.needs_attention > 0 ? 'warning' : ''}">
                    ${equipment.needs_attention > 0 ? '<i class="fas fa-exclamation-triangle"></i> ' : ''}
                    ${equipment.needs_attention} need attention
                </div>
            </div>
        </div>
        
        <div class="kpi-card events">
            <div class="kpi-icon"><i class="fas fa-trophy"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">${events.active}</div>
                <div class="kpi-label">Active Events</div>
                <div class="kpi-sub">${events.upcoming} upcoming</div>
            </div>
        </div>
        
        <div class="kpi-card budget">
            <div class="kpi-icon"><i class="fas fa-wallet"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">${budget.percent_used}%</div>
                <div class="kpi-label">Budget Utilized</div>
                <div class="kpi-sub">Rs. ${formatCurrency(budget.remaining)} remaining</div>
            </div>
            <div class="budget-bar">
                <div class="budget-fill ${budget.percent_used > 80 ? 'warning' : ''}" style="width: ${Math.min(budget.percent_used, 100)}%"></div>
            </div>
        </div>
    `;
}

function renderInsights() {
    const container = document.getElementById('insights-grid');
    const { users, insights, budget } = dashboardData;
    const { budget_efficiency, facility_demand, athlete_engagement, action_required } = insights;
    
    container.innerHTML = `
        <!-- USER DISTRIBUTION -->
        <div class="insight-card">
            <div class="card-header">
                <i class="fas fa-user-group"></i>
                <h3>User Distribution</h3>
            </div>
            <div class="card-content">
                ${renderUserDistribution(users.type_distribution)}
            </div>
        </div>
        
        <!-- ACTION REQUIRED - Most Important for Executives -->
        <div class="insight-card action-card wide">
            <div class="card-header">
                <i class="fas fa-bell"></i>
                <h3>Action Required</h3>
                <span class="action-badge ${action_required.total_actions > 0 ? 'active' : ''}">${action_required.total_actions}</span>
            </div>
            <div class="card-content">
                <div class="action-grid">
                    <div class="action-item ${action_required.pending_reservations > 0 ? 'needs-action' : 'ok'}">
                        <i class="fas fa-calendar-plus"></i>
                        <span class="action-count">${action_required.pending_reservations}</span>
                        <span class="action-label">Pending Reservations</span>
                    </div>
                    <div class="action-item ${action_required.unresolved_inquiries > 0 ? 'needs-action' : 'ok'}">
                        <i class="fas fa-question-circle"></i>
                        <span class="action-count">${action_required.unresolved_inquiries}</span>
                        <span class="action-label">Unresolved Inquiries</span>
                    </div>
                    <div class="action-item ${action_required.pending_equipment_requests > 0 ? 'needs-action' : 'ok'}">
                        <i class="fas fa-hand-holding"></i>
                        <span class="action-count">${action_required.pending_equipment_requests}</span>
                        <span class="action-label">Equipment Requests</span>
                    </div>
                    <div class="action-item ${action_required.low_stock_items > 0 ? 'warning' : 'ok'}">
                        <i class="fas fa-box-open"></i>
                        <span class="action-count">${action_required.low_stock_items}</span>
                        <span class="action-label">Low Stock Items</span>
                    </div>
                    <div class="action-item ${action_required.upcoming_events > 0 ? 'info' : 'ok'}">
                        <i class="fas fa-trophy"></i>
                        <span class="action-count">${action_required.upcoming_events}</span>
                        <span class="action-label">Events This Week</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- BUDGET EFFICIENCY -->
        <div class="insight-card">
            <div class="card-header">
                <i class="fas fa-chart-line"></i>
                <h3>Budget Efficiency (${budget.year})</h3>
            </div>
            <div class="card-content">
                <div class="efficiency-summary">
                    <div class="efficiency-metric risk">
                        <span class="metric-value">${budget_efficiency.summary.overspend_risk}</span>
                        <span class="metric-label">Overspend Risk (>80%)</span>
                    </div>
                    <div class="efficiency-metric good">
                        <span class="metric-value">${budget_efficiency.summary.on_track}</span>
                        <span class="metric-label">On Track (30-80%)</span>
                    </div>
                    <div class="efficiency-metric warning">
                        <span class="metric-value">${budget_efficiency.summary.underspend}</span>
                        <span class="metric-label">Underspend (<30%)</span>
                    </div>
                </div>
                ${renderUtilizationBars(budget_efficiency.sports)}
            </div>
        </div>
        
        <!-- FACILITY DEMAND -->
        <div class="insight-card">
            <div class="card-header">
                <i class="fas fa-building"></i>
                <h3>Facility Demand</h3>
            </div>
            <div class="card-content">
                <div class="demand-overview">
                    <div class="demand-stat">
                        <span class="demand-value ${facility_demand.overall.approval_rate >= 70 ? 'good' : facility_demand.overall.approval_rate >= 40 ? 'medium' : 'poor'}">${facility_demand.overall.approval_rate}%</span>
                        <span class="demand-label">Approval Rate</span>
                    </div>
                    <div class="demand-stat">
                        <span class="demand-value">${facility_demand.overall.total}</span>
                        <span class="demand-label">Total Bookings</span>
                    </div>
                </div>
                ${renderFacilityList(facility_demand.top_facilities)}
            </div>
        </div>
        
        <!-- ATHLETE ENGAGEMENT -->
        <div class="insight-card">
            <div class="card-header">
                <i class="fas fa-running"></i>
                <h3>Athlete Engagement</h3>
            </div>
            <div class="card-content">
                <div class="engagement-hero">
                    <div class="participation-circle ${athlete_engagement.participation_rate >= 30 ? 'good' : athlete_engagement.participation_rate >= 15 ? 'medium' : 'needs-work'}">
                        <span class="part-value">${athlete_engagement.participation_rate}%</span>
                        <span class="part-label">Participation</span>
                    </div>
                </div>
                <div class="engagement-stats">
                    <div class="eng-stat">
                        <i class="fas fa-users"></i>
                        <div>
                            <strong>${athlete_engagement.total_athletes}</strong>
                            <span>Active Athletes</span>
                        </div>
                    </div>
                    <div class="eng-stat">
                        <i class="fas fa-medal"></i>
                        <div>
                            <strong>${athlete_engagement.multi_sport_athletes}</strong>
                            <span>Multi-Sport</span>
                        </div>
                    </div>
                    <div class="eng-stat">
                        <i class="fas fa-futbol"></i>
                        <div>
                            <strong>${athlete_engagement.active_sports}</strong>
                            <span>Active Sports</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- BUDGET OVERVIEW -->
        <div class="insight-card wide">
            <div class="card-header">
                <i class="fas fa-wallet"></i>
                <h3>Budget Overview (${budget.year})</h3>
            </div>
            <div class="card-content">
                <div class="budget-stats">
                    <div class="budget-stat allocated">
                        <div class="stat-icon"><i class="fas fa-coins"></i></div>
                        <div class="stat-info">
                            <span class="stat-label">Allocated</span>
                            <span class="stat-value">Rs. ${formatCurrency(budget.allocated)}</span>
                        </div>
                    </div>
                    <div class="budget-stat spent">
                        <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
                        <div class="stat-info">
                            <span class="stat-label">Spent</span>
                            <span class="stat-value">Rs. ${formatCurrency(budget.spent)}</span>
                        </div>
                    </div>
                    <div class="budget-stat remaining">
                        <div class="stat-icon"><i class="fas fa-piggy-bank"></i></div>
                        <div class="stat-info">
                            <span class="stat-label">Remaining</span>
                            <span class="stat-value">Rs. ${formatCurrency(budget.remaining)}</span>
                        </div>
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

function renderBudgetChart(data) {
    if (!data || data.length === 0) return '<p class="no-data">No budget data for current year</p>';
    
    const maxValue = Math.max(...data.map(d => d.value));
    
    return `<div class="bar-chart">${data.map(item => `
        <div class="bar-row">
            <span class="bar-label">${item.label}</span>
            <div class="bar-track">
                <div class="bar-fill budget" style="width: ${(item.value / maxValue) * 100}%"></div>
            </div>
            <span class="bar-value">Rs. ${formatCurrency(item.value)}</span>
        </div>
    `).join('')}</div>`;
}

function renderStatusChart(data) {
    if (!data || data.length === 0) return '<p class="no-data">No reservation data</p>';
    
    const statusColors = {
        'BOOKED': '#f59e0b',
        'ACCEPTED': '#10b981',
        'REJECTED': '#ef4444'
    };
    const statusIcons = {
        'BOOKED': 'fa-clock',
        'ACCEPTED': 'fa-check-circle',
        'REJECTED': 'fa-times-circle'
    };
    
    return `<div class="status-grid">${data.map(item => `
        <div class="status-item" style="--status-color: ${statusColors[item.label] || '#6b7280'}">
            <i class="fas ${statusIcons[item.label] || 'fa-circle'}"></i>
            <span class="status-value">${item.value}</span>
            <span class="status-label">${item.label}</span>
        </div>
    `).join('')}</div>`;
}

function formatCurrency(amount) {
    return Number(amount).toLocaleString('en-IN');
}

function renderUtilizationBars(sports) {
    if (!sports || sports.length === 0) return '<p class="no-data">No budget data for current year</p>';
    
    return `<div class="utilization-list">
        ${sports.map(s => {
            const util = parseFloat(s.utilization);
            const statusClass = util > 80 ? 'risk' : util < 30 ? 'underspend' : 'good';
            return `
                <div class="util-row">
                    <span class="util-sport">${s.sport_name}</span>
                    <div class="util-bar-track">
                        <div class="util-bar-fill ${statusClass}" style="width: ${Math.min(util, 100)}%"></div>
                    </div>
                    <span class="util-percent ${statusClass}">${util}%</span>
                </div>
            `;
        }).join('')}
    </div>`;
}

function renderUserDistribution(data) {
    if (!data || data.length === 0) return '<p class="no-data">No user data</p>';
    
    const maxValue = Math.max(...data.map(d => d.count));
    const colors = {
        'STUDENT': '#6366f1',
        'ADMIN': '#ef4444',
        'COACH': '#10b981',
        'CAPTAIN': '#f59e0b',
        'SPT': '#8b5cf6',
        'EQP': '#ec4899',
        'REG': '#3b82f6',
        'PUBLIC': '#6b7280'
    };
    
    return `<div class="user-dist-chart">
        ${data.map(item => `
            <div class="user-row">
                <span class="user-type">${item.type}</span>
                <div class="user-bar-track">
                    <div class="user-bar-fill" style="width: ${(item.count / maxValue) * 100}%; background: ${colors[item.type] || '#4b0082'}"></div>
                </div>
                <span class="user-count">${item.count}</span>
            </div>
        `).join('')}
    </div>`;
}

function renderFacilityList(facilities) {
    if (!facilities || facilities.length === 0) return '<p class="no-data">No facility booking data</p>';
    
    return `<div class="facility-list">
        ${facilities.map((f, i) => `
            <div class="facility-row">
                <span class="facility-rank">${i + 1}</span>
                <span class="facility-name">${f.facility_name}</span>
                <span class="facility-bookings">${f.total_bookings} bookings</span>
            </div>
        `).join('')}
    </div>`;
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

</body>
</html>
