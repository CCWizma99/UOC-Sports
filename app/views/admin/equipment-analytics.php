<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Analytics | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
        @import url(/uoc-sports/public/css/admin/equipment-analytics.css);
    </style>
</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';
?>

<div class="main-content-wrapper">
    <div class="analytics-container">
        <!-- Header -->
        <div class="analytics-header">
            <div class="header-content">
                <h1><i class="fas fa-chart-line"></i> Equipment Analytics</h1>
                <p>Actionable insights for equipment management decisions</p>
            </div>
            <div class="header-actions">
                <a href="./admin-equipments/report/pdf" class="btn-download" target="_blank">
                    <i class="fas fa-file-pdf"></i> Download Report
                </a>
                <button class="btn-customize" onclick="openCustomizeModal()">
                    <i class="fas fa-sliders"></i> Customize Top Insights
                </button>
                <a href="./admin-equipments" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <!-- Priority Insights (Customizable Top 2) -->
        <div class="priority-section">
            <div class="section-label">
                <i class="fas fa-star"></i> Priority Insights
                <span class="hint">(Click "Customize" to change)</span>
            </div>
            <div class="priority-cards" id="priority-insights">
                <div class="loading-card"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
            </div>
        </div>

        <!-- All Insights Grid -->
        <div class="insights-grid" id="all-insights">
            <div class="loading-card"><i class="fas fa-spinner fa-spin"></i> Loading insights...</div>
        </div>
    </div>
</div>

<!-- Customize Modal -->
<div class="modal-overlay" id="customize-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-sliders"></i> Select Top 2 Priority Insights</h3>
            <button class="modal-close" onclick="closeCustomizeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p class="modal-hint">Choose 2 insights to display prominently at the top of your dashboard.</p>
            <div class="insight-options" id="insight-options">
                <!-- Populated by JS -->
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeCustomizeModal()">Cancel</button>
            <button class="btn-save" onclick="savePreferences()">Save Preferences</button>
        </div>
    </div>
</div>

<script>
// Available insights configuration - all use theme color
const INSIGHTS = {
    utilization: { name: 'Equipment Utilization Rate', icon: 'fa-chart-pie' },
    high_demand: { name: 'High Demand Items', icon: 'fa-fire' },
    underutilized: { name: 'Underutilized Equipment', icon: 'fa-box-open' },
    peak_hours: { name: 'Peak Booking Hours', icon: 'fa-clock' },
    peak_days: { name: 'Peak Booking Days', icon: 'fa-calendar-week' },
    condition_alerts: { name: 'Equipment Condition Alerts', icon: 'fa-wrench' },
    sport_demand: { name: 'Sport-wise Demand', icon: 'fa-running' },
    booking_duration: { name: 'Avg Booking Duration', icon: 'fa-hourglass-half' },
    active_students: { name: 'Most Active Students', icon: 'fa-users' }
};

// Default priorities (1=utilization, 2=high_demand)
let selectedPriorities = JSON.parse(localStorage.getItem('equipmentAnalyticsPriorities')) || ['utilization', 'high_demand'];
let analyticsData = null;

document.addEventListener('DOMContentLoaded', loadAnalytics);

async function loadAnalytics() {
    try {
        const response = await fetch('admin-equipments/analytics');
        const result = await response.json();
        
        if (result.status === 'success') {
            analyticsData = result.data;
            renderDashboard();
        } else {
            showError('Failed to load analytics');
        }
    } catch (error) {
        showError('Network error');
        console.error(error);
    }
}

function renderDashboard() {
    renderPriorityInsights();
    renderAllInsights();
}

function renderPriorityInsights() {
    const container = document.getElementById('priority-insights');
    let html = '';
    
    selectedPriorities.forEach(key => {
        html += renderInsightCard(key, true);
    });
    
    container.innerHTML = html || '<p class="no-data">Select priority insights</p>';
}

function renderAllInsights() {
    const container = document.getElementById('all-insights');
    let html = '';
    
    // Render all insights except the priority ones
    Object.keys(INSIGHTS).forEach(key => {
        if (!selectedPriorities.includes(key)) {
            html += renderInsightCard(key, false);
        }
    });
    
    container.innerHTML = html;
}

function renderInsightCard(key, isPriority) {
    const info = INSIGHTS[key];
    const data = analyticsData[key];
    
    let contentHtml = '';
    
    switch(key) {
        case 'utilization':
            contentHtml = renderUtilizationContent(data);
            break;
        case 'high_demand':
            contentHtml = renderHighDemandContent(data);
            break;
        case 'underutilized':
            contentHtml = renderUnderutilizedContent(data);
            break;
        case 'peak_hours':
            contentHtml = renderPeakHoursContent(data);
            break;
        case 'peak_days':
            contentHtml = renderPeakDaysContent(data);
            break;
        case 'condition_alerts':
            contentHtml = renderConditionContent(data);
            break;
        case 'sport_demand':
            contentHtml = renderSportDemandContent(data);
            break;
        case 'booking_duration':
            contentHtml = renderDurationContent(data);
            break;
        case 'active_students':
            contentHtml = renderActiveStudentsContent(data);
            break;
    }
    
    return `
        <div class="insight-card ${isPriority ? 'priority' : ''}">
            <div class="card-header">
                <i class="fas ${info.icon}"></i>
                <h3>${info.name}</h3>
            </div>
            <div class="card-content">
                ${contentHtml}
            </div>
        </div>
    `;
}

// Content renderers for each insight type
function renderUtilizationContent(data) {
    if (!data || data.length === 0) return '<p class="no-data">No utilization data</p>';
    
    return `<div class="data-list">${data.slice(0, 5).map(item => `
        <div class="data-row">
            <div class="data-info">
                <span class="data-name">${item.equipment_name}</span>
                <span class="data-sub">${item.sport_name}</span>
            </div>
            <div class="data-metric">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: ${Math.min(item.utilization_rate, 100)}%"></div>
                </div>
                <span class="metric-value">${item.utilization_rate}%</span>
            </div>
        </div>
    `).join('')}</div>`;
}

function renderHighDemandContent(data) {
    if (!data || data.length === 0) return '<p class="no-data-good"><i class="fas fa-check-circle"></i> No high demand alerts</p>';
    
    return `<div class="alert-list">${data.slice(0, 5).map(item => `
        <div class="alert-row demand">
            <i class="fas fa-fire"></i>
            <div class="alert-info">
                <span class="alert-name">${item.equipment_name}</span>
                <span class="alert-sub">${item.sport_name} • ${item.total_bookings_last_30_days} bookings/month</span>
            </div>
            <span class="alert-badge">${item.demand_pressure}% pressure</span>
        </div>
    `).join('')}</div>`;
}

function renderUnderutilizedContent(data) {
    if (!data || data.length === 0) return '<p class="no-data-good"><i class="fas fa-check-circle"></i> All equipment is being used</p>';
    
    return `<div class="alert-list">${data.slice(0, 5).map(item => `
        <div class="alert-row warning">
            <i class="fas fa-box-open"></i>
            <div class="alert-info">
                <span class="alert-name">${item.equipment_name}</span>
                <span class="alert-sub">${item.sport_name} • ${item.available_stock} in stock</span>
            </div>
            <span class="alert-badge idle">${item.days_since_last_booking ? item.days_since_last_booking + ' days idle' : 'Never booked'}</span>
        </div>
    `).join('')}</div>`;
}

function renderPeakHoursContent(data) {
    if (!data || data.length === 0) return '<p class="no-data">No booking data</p>';
    
    const maxCount = Math.max(...data.map(d => d.booking_count));
    return `<div class="bar-chart">${data.map(item => `
        <div class="bar-row">
            <span class="bar-label">${formatHour(item.hour)}</span>
            <div class="bar-track">
                <div class="bar-fill" style="width: ${(item.booking_count / maxCount) * 100}%"></div>
            </div>
            <span class="bar-value">${item.booking_count}</span>
        </div>
    `).join('')}</div>`;
}

function renderPeakDaysContent(data) {
    if (!data || data.length === 0) return '<p class="no-data">No booking data</p>';
    
    const maxCount = Math.max(...data.map(d => d.booking_count));
    return `<div class="bar-chart">${data.slice(0, 5).map(item => `
        <div class="bar-row">
            <span class="bar-label">${item.day_name}</span>
            <div class="bar-track">
                <div class="bar-fill" style="width: ${(item.booking_count / maxCount) * 100}%"></div>
            </div>
            <span class="bar-value">${item.booking_count}</span>
        </div>
    `).join('')}</div>`;
}

function renderConditionContent(data) {
    if (!data || data.length === 0) return '<p class="no-data-good"><i class="fas fa-check-circle"></i> All equipment in good condition</p>';
    
    return `<div class="alert-list">${data.slice(0, 5).map(item => `
        <div class="alert-row danger">
            <i class="fas fa-wrench"></i>
            <div class="alert-info">
                <span class="alert-name">${item.equipment_name}</span>
                <span class="alert-sub">${item.sport_name} • ${item.damaged_count} damaged of ${item.total_stock}</span>
            </div>
            <span class="alert-badge danger">${item.damage_rate}% damaged</span>
        </div>
    `).join('')}</div>`;
}

function renderSportDemandContent(data) {
    if (!data || data.length === 0) return '<p class="no-data">No sport demand data</p>';
    
    const maxReq = Math.max(...data.map(d => d.total_requests));
    return `<div class="bar-chart">${data.slice(0, 6).map(item => `
        <div class="bar-row">
            <span class="bar-label">${item.sport_name}</span>
            <div class="bar-track">
                <div class="bar-fill sport" style="width: ${(item.total_requests / maxReq) * 100}%"></div>
            </div>
            <span class="bar-value">${item.total_requests} <small>(${item.unique_students} students)</small></span>
        </div>
    `).join('')}</div>`;
}

function renderDurationContent(data) {
    if (!data || data.length === 0) return '<p class="no-data">No duration data</p>';
    
    return `<div class="data-list">${data.slice(0, 5).map(item => `
        <div class="data-row">
            <div class="data-info">
                <span class="data-name">${item.equipment_name}</span>
                <span class="data-sub">${item.sport_name}</span>
            </div>
            <span class="duration-badge">${formatDuration(item.avg_duration_mins)}</span>
        </div>
    `).join('')}</div>`;
}

function renderActiveStudentsContent(data) {
    if (!data || data.length === 0) return '<p class="no-data">No student data</p>';
    
    return `<div class="data-list">${data.map((item, i) => `
        <div class="data-row">
            <span class="rank-badge">#${i + 1}</span>
            <div class="data-info">
                <span class="data-name">${item.student_id}</span>
                <span class="data-sub">${item.unique_equipment_borrowed} different items</span>
            </div>
            <span class="count-badge">${item.total_bookings} bookings</span>
        </div>
    `).join('')}</div>`;
}

// Utility functions
function formatHour(hour) {
    const h = parseInt(hour);
    if (h === 0) return '12 AM';
    if (h === 12) return '12 PM';
    return h > 12 ? `${h - 12} PM` : `${h} AM`;
}

function formatDuration(mins) {
    if (mins < 60) return `${mins} min`;
    const hours = Math.floor(mins / 60);
    const remainMins = mins % 60;
    return remainMins > 0 ? `${hours}h ${remainMins}m` : `${hours} hour${hours > 1 ? 's' : ''}`;
}

// Modal functions
function openCustomizeModal() {
    const container = document.getElementById('insight-options');
    let html = '';
    
    Object.entries(INSIGHTS).forEach(([key, info]) => {
        const isSelected = selectedPriorities.includes(key);
        html += `
            <label class="insight-option ${isSelected ? 'selected' : ''}" data-key="${key}">
                <input type="checkbox" ${isSelected ? 'checked' : ''} onchange="toggleInsight('${key}', this)">
                <i class="fas ${info.icon}"></i>
                <span>${info.name}</span>
            </label>
        `;
    });
    
    container.innerHTML = html;
    document.getElementById('customize-modal').classList.add('active');
}

function closeCustomizeModal() {
    document.getElementById('customize-modal').classList.remove('active');
}

function toggleInsight(key, checkbox) {
    const option = checkbox.closest('.insight-option');
    
    if (checkbox.checked) {
        if (selectedPriorities.length >= 2) {
            // Uncheck the first one
            const firstKey = selectedPriorities.shift();
            document.querySelector(`.insight-option[data-key="${firstKey}"] input`).checked = false;
            document.querySelector(`.insight-option[data-key="${firstKey}"]`).classList.remove('selected');
        }
        selectedPriorities.push(key);
        option.classList.add('selected');
    } else {
        selectedPriorities = selectedPriorities.filter(k => k !== key);
        option.classList.remove('selected');
    }
}

function savePreferences() {
    if (selectedPriorities.length !== 2) {
        UI.showToast('Please select exactly 2 insights', 'warning');
        return;
    }
    
    localStorage.setItem('equipmentAnalyticsPriorities', JSON.stringify(selectedPriorities));
    closeCustomizeModal();
    renderDashboard();
    
    UI.showToast('Preferences saved!', 'success');
}

function showToast(message) { /* Deprecated */ }

function showError(message) {
    UI.showToast(message, 'error');
    document.getElementById('priority-insights').innerHTML = `
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
    var currentPage = document.getElementById("sidebar-equipments");
    currentPage.classList.add("active");
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</html>
