<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authorization: Only ADMIN and EXECUTIVE users
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_type'] ?? '', ['ADMIN', 'EXECUTIVE'])) {
    header('Location: /uoc-sports/public/sign-in');
    exit;
}

$userType = $_SESSION['user_type'];
$userName = $_SESSION['user_name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Dashboard | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

    <style>
        @import url(/uoc-sports/public/css/admin/executive-dashboard.css);
    </style>

    <style>
        /* ── Standalone Reset & Layout ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f0ff 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Standalone Header ── */
        .exec-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #4b0082 100%);
            color: white;
            padding: 14px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .header-brand .logo {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            object-fit: cover;
        }

        .brand-text h1 {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .brand-text span {
            font-size: 0.82rem;
            opacity: 0.8;
            font-weight: 500;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .current-date {
            font-size: 0.85rem;
            opacity: 0.85;
        }

        .header-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
        }

        .header-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        .header-btn.admin-back {
            background: rgba(99, 102, 241, 0.3);
            border-color: rgba(99, 102, 241, 0.5);
        }

        .header-btn.admin-back:hover {
            background: rgba(99, 102, 241, 0.5);
        }

        .header-btn.logout:hover {
            background: rgba(220, 38, 38, 0.4);
            border-color: rgba(220, 38, 38, 0.6);
        }

        /* ── Chart Containers ── */
        .chart-wrapper {
            position: relative;
            width: 100%;
            max-width: 280px;
            margin: 0 auto;
        }
        .chart-wrapper.line-chart {
            max-width: 100%;
            height: 250px;
        }
        .chart-legend {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            margin-top: 16px;
            font-size: 0.8rem;
        }
        .chart-legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #555;
            font-weight: 500;
        }
        .chart-legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* ── Main Content Area ── */
        .exec-main {
            flex: 1;
            padding: 28px 32px 40px;
            max-width: 1440px;
            margin: 0 auto;
            width: 100%;
        }

        /* Override dashboard-container from imported CSS */
        .exec-main .dashboard-container {
            max-width: none;
            padding: 0;
        }

        /* ── Footer ── */
        .exec-footer {
            text-align: center;
            padding: 16px 32px;
            color: #888;
            font-size: 0.82rem;
            border-top: 1px solid #e5e7eb;
            background: white;
        }

        @media (max-width: 768px) {
            .exec-header {
                padding: 12px 16px;
                flex-wrap: wrap;
                gap: 10px;
            }
            .exec-main {
                padding: 16px 12px 32px;
            }
            .header-actions {
                gap: 8px;
            }
            .current-date { display: none; }
            .header-btn span { display: none; }
        }

        /* ── Facilities Tab Specific Layout ── */
        .facilities-grid-layout {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        .facilities-grid-layout .insight-card.full-width {
            grid-column: 1 / -1;
        }
        .scrollable-chart-container {
            width: 100%;
            overflow-x: auto;
            padding-bottom: 8px;
        }
        .scrollable-chart-inner {
            min-width: 800px;
            height: 300px;
        }
    </style>
</head>
<body>

<!-- Standalone Executive Header -->
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
        <?php if ($userType === 'ADMIN'): ?>
            <a href="/uoc-sports/public/admin-index" class="header-btn admin-back">
                <i class="fas fa-arrow-left"></i> <span>Admin Panel</span>
            </a>
        <?php endif; ?>
        <a href="/uoc-sports/public/logout" class="header-btn logout">
            <i class="fas fa-sign-out-alt"></i> <span>Log Out</span>
        </a>
    </div>
</header>

<!-- Main Content -->
<main class="exec-main">
    <div class="dashboard-container">

        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <h1><i class="fas fa-chart-pie"></i> Executive Dashboard</h1>
                <p>University Sports Management Overview</p>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="date-filter-container" style="margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 10px; background: white; padding: 6px 12px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #e5e7eb;">
                <label for="year-select" style="font-size: 0.85rem; font-weight: 600; color: #4b5563;">Select Year:</label>
                <select id="year-select" onchange="applyYearFilter()" 
                        style="padding: 6px 10px; border: 1px solid #d0d0d0; border-radius: 6px; font-size: 0.9rem; cursor: pointer; min-width: 130px; background: #f9fafb;">
                    <option value="<?php echo date('Y') ?>" selected><?php echo date('Y') ?></option>
                    <option value="<?php echo date('Y') - 1 ?>"><?php echo date('Y') - 1 ?></option>
                    <option value="<?php echo date('Y') - 2 ?>"><?php echo date('Y') - 2 ?></option>
                    <option value="all">All Time</option>
                </select>

                <!-- Export Buttons -->
                <div style="flex: 1; text-align: right; display: flex; justify-content: flex-end; gap: 8px;">
                    <button id="export-csv-btn" onclick="exportDashboard('csv')" 
                            style="padding: 8px 16px; background: #059669; color: white; border: none; border-radius: 6px; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-download"></i> <span>Export</span>
                    </button>
                    <button id="export-pdf-btn" onclick="exportDashboard('pdf')" 
                            style="padding: 8px 16px; background: #dc2626; color: white; border: none; border-radius: 6px; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-file-pdf"></i> <span>PDF</span>
                    </button>
                </div>
            </div>
        </div>



        <!-- KPI Cards Row -->
        <div class="kpi-row" id="kpi-cards">
            <div class="kpi-card loading">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Loading...</span>
            </div>
        </div>

        <!-- Category Tabs -->
        <div class="category-tabs" id="category-tabs">
            <button class="tab-btn active" data-tab="users" onclick="switchTab('users', this)">
                <i class="fas fa-users"></i> <span>Users</span>
            </button>
            <button class="tab-btn" data-tab="facilities" onclick="switchTab('facilities', this)">
                <i class="fas fa-building"></i> <span>Facilities</span>
            </button>
            <button class="tab-btn" data-tab="equipment" onclick="switchTab('equipment', this)">
                <i class="fas fa-boxes-stacked"></i> <span>Equipment</span>
            </button>
            <button class="tab-btn" data-tab="budget" onclick="switchTab('budget', this)">
                <i class="fas fa-wallet"></i> <span>Budget</span>
            </button>
            <button class="tab-btn" data-tab="achievements" onclick="switchTab('achievements', this)">
                <i class="fas fa-medal"></i> <span>Achievements</span>
            </button>
            <button class="tab-btn" data-tab="community" onclick="switchTab('community', this)">
                <i class="fas fa-comments"></i> <span>Community</span>
            </button>
        </div>

        <!-- Tab Content Panels -->
        <div id="tab-users" class="tab-content active">
            <div class="insights-grid" id="users-grid">
                <div class="insight-card loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
            </div>
        </div>

        <div id="tab-facilities" class="tab-content">
            <div class="insights-grid" id="facilities-grid">
                <div class="insight-card loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
            </div>
        </div>

        <div id="tab-equipment" class="tab-content">
            <div class="insights-grid" id="equipment-grid">
                <div class="insight-card loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
            </div>
        </div>

        <div id="tab-budget" class="tab-content">
            <div class="insights-grid" id="budget-grid">
                <div class="insight-card loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
            </div>
        </div>



        <div id="tab-achievements" class="tab-content">
            <div class="insights-grid" id="achievements-grid">
                <div class="insight-card loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
            </div>
        </div>

        <div id="tab-community" class="tab-content">
            <div class="insights-grid" id="community-grid">
                <div class="insight-card loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
            </div>
        </div>
    </div>
</main>

<footer class="exec-footer">
    <p>&copy; <?php echo date('Y'); ?> University of Colombo - Sports Management System</p>
</footer>

<script>
let dashboardData = null;

// Determine correct API base from current URL
const isExecRoute = window.location.pathname.includes('executive-');
const API_BASE = isExecRoute 
    ? '/uoc-sports/public/executive-dashboard/analytics'
    : '/uoc-sports/public/admin-dashboard/analytics';

document.addEventListener('DOMContentLoaded', () => {
    updateDate();
    loadDashboard();
});

function updateDate() {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', options);
}

// ── Tab Switching ──
function switchTab(tabId, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tabId).classList.add('active');
    btn.classList.add('active');
}

// ── Load Dashboard Data ──
let equipmentDeepData = null;
let facilityDeepData = null;

async function loadDashboard() {
    try {
        const year = document.getElementById('year-select').value;
        
        // Build API URL with correct prefix and date filters
        const API_PREFIX = isExecRoute ? '/uoc-sports/public/executive-' : '/uoc-sports/public/admin-';
        const dateParams = `?year=${year}`;
        const apiUrl = API_BASE + dateParams;
        
        // Fetch all 3 APIs in parallel
        const [mainRes, equipRes, facRes] = await Promise.all([
            fetch(apiUrl),
            fetch(API_PREFIX + 'equipments/analytics' + dateParams).catch(() => null),
            fetch(API_PREFIX + 'reservations/analytics' + dateParams).catch(() => null)
        ]);
        
        const result = await mainRes.json();
        
        // Try parse equipment deep analytics
        try {
            if (equipRes && equipRes.ok) {
                const eqResult = await equipRes.json();
                if (eqResult.status === 'success') equipmentDeepData = eqResult.data;
            }
        } catch(e) {}
        
        // Try parse facility deep analytics
        try {
            if (facRes && facRes.ok) {
                const facResult = await facRes.json();
                if (facResult.status === 'success') facilityDeepData = facResult.data;
            }
        } catch(e) {}
        
        if (result.status === 'success') {
            dashboardData = getSafeData(result.data);
            renderKPICards();
            renderUsersTab();
            renderFacilitiesTab();
            renderEquipmentTab();
            renderBudgetTab();
            renderAchievementsTab();
            renderCommunityTab();
        } else {
            showError('Failed to load dashboard data');
        }
    } catch (error) {
        showError('Network error');
        console.error(error);
    }
}

/**
 * Returns a default object structure to prevent TypeErrors
 */
function getSafeData(data) {
    const defaults = {
        users: { total: 0, new_this_month: 0, type_distribution: [] },
        reservations: { total: 0, range_total: 0, avg_utilization: 0 },
        equipment: { total_types: 0, total_quantity: 0, needs_attention: 0 },
        events: { total: 0, active: 0, completed_this_year: 0, upcoming: 0 },
        budget: { allocated: 0, spent: 0, remaining: 0, percent_used: 0, year: new Date().getFullYear() },
        insights: {
            budget_efficiency: { summary: { on_track: 0, overspend_risk: 0, underspend: 0, total: 0 }, sports: [] },
            facility_demand: { top_facilities: [], overall: { total: 0 } },
            athlete_engagement: { total_athletes: 0, total_students: 0, participation_rate: 0, active_sports: 0 },
            action_required: { unresolved_inquiries: 0, low_stock_items: 0, total_actions: 0 }
        },
        community: { post_stats: { total_posts: 0, active_posts: 0, commenting_enabled: 0 }, inquiry_stats: { total: 0, unresolved: 0, resolved: 0 }, recent_comments: [], recent_inquiries: [] },
        achievements: { total: 0, recent: [], by_sport: [], top_performers: [] },
        facility_analytics: { utilization: [], by_status: [], monthly_trend: [] },
        equipment_analytics: { by_sport: [], low_stock: [], recent_activity: [] }
    };

    function deepMerge(target, source) {
        if (typeof target !== 'object' || target === null) return source !== undefined ? source : target;
        if (typeof source !== 'object' || source === null) return target;
        if (Array.isArray(target)) return Array.isArray(source) ? source : target;
        
        const output = { ...target };
        Object.keys(source).forEach(key => {
            if (source[key] !== undefined) {
                output[key] = deepMerge(target[key], source[key]);
            }
        });
        return output;
    }

    return deepMerge(defaults, data);
}

// ══════════════════════════════════════════
// KPI CARDS
// ══════════════════════════════════════════
function renderKPICards() {
    const container = document.getElementById('kpi-cards');
    const { users, reservations, equipment, events, budget } = dashboardData;
    
    container.innerHTML = `
        <div class="kpi-card users">
            <div class="kpi-icon"><i class="fas fa-users"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">${users.total.toLocaleString()}</div>
                <div class="kpi-label">Active Users</div>
                <div class="kpi-sub">+${users.new_this_month} in selected year</div>
            </div>
        </div>
        
        <div class="kpi-card reservations">
            <div class="kpi-icon"><i class="fas fa-calendar-check"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">${reservations.avg_utilization}%</div>
                <div class="kpi-label">Facility Utilization</div>
                <div class="kpi-sub">${reservations.range_total} bookings in selected year</div>
            </div>
        </div>
        
        <div class="kpi-card equipment">
            <div class="kpi-icon"><i class="fas fa-boxes-stacked"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">${equipment.total_quantity.toLocaleString()}</div>
                <div class="kpi-label">Equipment Items</div>
                <div class="kpi-sub">${equipment.needs_attention} need attention</div>
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
                <div class="kpi-label">Budget Used</div>
                <div class="kpi-sub">Rs. ${formatCurrency(budget.remaining)} remaining</div>
            </div>
        </div>
    `;
}

// ══════════════════════════════════════════
// USERS TAB
// ══════════════════════════════════════════
function renderUsersTab() {
    const container = document.getElementById('users-grid');
    const { users, insights } = dashboardData;
    const { athlete_engagement } = insights;
    
    container.innerHTML = `
        <div class="insight-card wide horizontal-overview">
            <div class="overview-title">
                <h3><i class="fas fa-chart-bar"></i> User Overview</h3>
                <p>Key user metrics and summary</p>
            </div>
            <div class="overview-stats">
                <div class="stat-item">
                    <div class="stat-num" style="color: #4b0082">${users.total.toLocaleString()}</div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">${users.new_this_month}</div>
                    <div class="stat-label">New This Year</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">${athlete_engagement.total_athletes}</div>
                    <div class="stat-label">Total Athletes</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">${athlete_engagement.participation_rate}%</div>
                    <div class="stat-label">Participation Rate</div>
                </div>
            </div>
        </div>

        <div class="insight-card">
            ${cardHeader('fa-user-group', 'User Distribution', 'Breakdown of registered accounts by role type')}
            <div class="card-content">
                <div class="chart-wrapper"><canvas id="chart-user-distribution"></canvas></div>
            </div>
        </div>
        
        <div class="insight-card">
            ${cardHeader('fa-running', 'Athlete Engagement', 'Student participation across sports programs')}
            <div class="card-content">
                <div class="stat-grid">
                    <div class="stat-item">
                        <div class="stat-num">${athlete_engagement.active_sports}</div>
                        <div class="stat-label">Active Sports</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num">${athlete_engagement.multi_sport_athletes}</div>
                        <div class="stat-label">Multi-Sport Athletes</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num">${athlete_engagement.total_students}</div>
                        <div class="stat-label">Total Students</div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Render user distribution donut chart
    const typeLabels = { STUDENT:'Student', ADMIN:'Admin', COACH:'Coach', CAPTAIN:'Captain', SPT:'Sport Manager', EQP:'Equipment Manager', REG:'Registrar' };
    if (users.type_distribution && users.type_distribution.length > 0) {
        createPieChart('chart-user-distribution',
            users.type_distribution.map(d => typeLabels[d.type] || d.type),
            users.type_distribution.map(d => d.count));
    }
}

// ══════════════════════════════════════════
// FACILITIES TAB
// ══════════════════════════════════════════
function renderFacilitiesTab() {
    const container = document.getElementById('tab-facilities');
    const { reservations, facility_analytics } = dashboardData;
    const year = document.getElementById('year-select').value;
    const isAllTime = year === 'all';
    
    container.innerHTML = `
        <div class="facilities-grid-layout" id="facilities-grid">
            <!-- Row 1: 3 Column Grid -->
            <!-- Card 1: Facility Utilization -->
            <div class="insight-card">
                ${cardHeader('fa-chart-pie', 'Facility Utilization', 'Usage frequency and rate per facility')}
                <div class="card-content">
                    ${facility_analytics.utilization.length > 0 ? `
                        <div class="data-list">
                            ${facility_analytics.utilization
                                .map(f => `
                                <div class="data-row">
                                    <div class="data-info">
                                        <span class="name">${truncName(f.facility_name)}</span>
                                        <span class="sub">${f.total_bookings} bookings in selected period</span>
                                    </div>
                                    <span class="data-value">${f.utilization_rate}%</span>
                                </div>`).join('')}
                        </div>
                    ` : '<p class="no-data">No facility data available</p>'}
                </div>
            </div>

            <!-- Card 2: Peak Booking Days -->
            <div class="insight-card">
                ${cardHeader('fa-calendar-day', 'Peak Booking Days', 'Weekly distribution of reservation activity')}
                <div class="card-content">
                    ${facility_analytics.peak_days.length > 0 ? `
                        <div class="data-list">
                            ${facility_analytics.peak_days.map(d => `
                                <div class="data-row">
                                    <div class="data-info">
                                        <span class="name">${d.day_name}</span>
                                        <span class="sub">Total bookings on this day</span>
                                    </div>
                                    <span class="data-value">${d.count}</span>
                                </div>
                            `).join('')}
                        </div>
                    ` : '<p class="no-data">No data available</p>'}
                </div>
            </div>

            <!-- Card 3: Booking by User Type -->
            <div class="insight-card">
                ${cardHeader('fa-users-gear', 'Booking by User Type', 'Facility usage distribution by student roles')}
                <div class="card-content">
                    ${facility_analytics.by_user_type.length > 0 ? `
                        <div class="data-list">
                            ${(() => {
                                const typeLabels = { STUDENT:'Student', ADMIN:'Admin', COACH:'Coach', CAPTAIN:'Captain', SPT:'Sport Manager', EQP:'Equipment Manager', REG:'Registrar' };
                                return facility_analytics.by_user_type.map(d => `
                                    <div class="data-row">
                                        <div class="data-info">
                                            <span class="name">${typeLabels[d.user_type] || d.user_type}</span>
                                            <span class="sub">Account role type</span>
                                        </div>
                                        <span class="data-value">${d.count}</span>
                                    </div>
                                `).join('');
                            })()}
                        </div>
                    ` : '<p class="no-data">No data available</p>'}
                </div>
            </div>

            <!-- Row 2: Full Width Grid -->
            <!-- Card 4: Monthly Booking Trend -->
            <div class="insight-card full-width">
                ${cardHeader('fa-chart-line', 'Monthly Booking Trend', 'Reservation volume progression over time')}
                <div class="card-content">
                    ${facility_analytics.monthly_trend.length > 0 ? `
                        <div class="scrollable-chart-container">
                            <div class="${isAllTime ? 'scrollable-chart-inner' : 'chart-wrapper line-chart'}">
                                <canvas id="chart-booking-trend"></canvas>
                            </div>
                        </div>
                    ` : '<p class="no-data">No trend data available</p>'}
                </div>
            </div>
        </div>
    `;



    // ── Monthly Trend Chart (Card 4) ──
    if (facility_analytics.monthly_trend && facility_analytics.monthly_trend.length > 0) {
        createLineChart('chart-booking-trend',
            facility_analytics.monthly_trend.map(d => d.month_label),
            facility_analytics.monthly_trend.map(d => d.count),
            'Reservations');
    }
}


// ══════════════════════════════════════════
// EQUIPMENT TAB
// ══════════════════════════════════════════
function renderEquipmentTab() {
    const container = document.getElementById('equipment-grid');
    const { equipment, equipment_analytics } = dashboardData;
    
    container.innerHTML = `
        <div class="insight-card wide horizontal-overview">
            <div class="overview-title">
                <h3><i class="fas fa-boxes-stacked"></i> Equipment Overview</h3>
                <p>Total equipment and inventory status</p>
            </div>
            <div class="overview-stats">
                <div class="stat-item">
                    <div class="stat-num" style="color: #4b0082">${equipment.total_types}</div>
                    <div class="stat-label">Equipment Types</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">${equipment.total_quantity.toLocaleString()}</div>
                    <div class="stat-label">Total Items</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">${equipment.needs_attention}</div>
                    <div class="stat-label">Need Attention</div>
                </div>
            </div>
        </div>
        
        <div class="insight-card">
            ${cardHeader('fa-trophy', 'Equipment by Sport', 'Distribution of equipment items across sports')}
            <div class="card-content">
                ${equipment_analytics.by_sport.length > 0 ?
                    '<div class="chart-wrapper"><canvas id="chart-equipment-sport"></canvas></div>'
                    : '<p class="no-data">No equipment data</p>'}
            </div>
        </div>
        
        <div class="insight-card">
            ${cardHeader('fa-exclamation-triangle', 'Low Stock Items', 'Equipment running low and needing restocking')}
            <div class="card-content">
                ${equipment_analytics.low_stock.length > 0 ? `
                    <div class="data-list">${equipment_analytics.low_stock.map(item => `
                        <div class="data-row">
                            <div class="data-info">
                                <span class="name">${truncName(item.equipment_name)}</span>
                                <span class="sub">${item.sport_name}</span>
                            </div>
                            <span class="data-value" style="color: ${item.quantity < 5 ? '#ef4444' : '#d97706'}">${item.quantity} left</span>
                        </div>
                    `).join('')}</div>
                ` : '<p class="no-data" style="color: #059669;"><i class="fas fa-check-circle"></i> All items sufficiently stocked</p>'}
            </div>
        </div>
        
        <div class="insight-card">
            ${cardHeader('fa-clock-rotate-left', 'Recent GRN Activity', 'Latest goods received notes and purchases')}
            <div class="card-content">
                ${equipment_analytics.recent_activity.length > 0 ? `
                    <div class="activity-feed">${equipment_analytics.recent_activity.map(a => `
                        <div class="activity-item">
                            <div class="activity-icon"><i class="fas fa-box-open"></i></div>
                            <div class="activity-details">
                                <div class="activity-title">${truncName(a.equipment_name)}</div>
                                <div class="activity-sub">${a.sport_name} &bull; Qty: ${a.quantity} &bull; Rs. ${Number(a.unit_price).toLocaleString()}</div>
                                <div class="activity-meta">${a.date}</div>
                            </div>
                        </div>
                    `).join('')}</div>
                ` : '<p class="no-data">No recent activity</p>'}
            </div>
        </div>
    `;

    // Append deep equipment analytics if available
    if (equipmentDeepData) {
        const deepCards = renderEquipmentDeepAnalytics();
        container.innerHTML += deepCards;
    }

    // Render pie chart for equipment by sport (top 6 + Others)
    if (equipment_analytics.by_sport.length > 0) {
        const top6 = equipment_analytics.by_sport.slice(0, 6);
        const rest = equipment_analytics.by_sport.slice(6);
        const labels = top6.map(s => s.sport_name);
        const values = top6.map(s => Number(s.total_quantity));
        if (rest.length > 0) {
            labels.push('Others');
            values.push(rest.reduce((sum, s) => sum + Number(s.total_quantity), 0));
        }
        createPieChart('chart-equipment-sport', labels, values);
    }
}

// ══════════════════════════════════════════
// DEEP EQUIPMENT ANALYTICS (from old API)
// ══════════════════════════════════════════
function renderEquipmentDeepAnalytics() {
    let html = '';
    const d = equipmentDeepData;

    // Utilization Rate
    if (d.utilization && d.utilization.length) {
        html += `<div class="insight-card">${cardHeader('fa-chart-pie', 'Utilization Rate', 'How frequently each equipment item is booked')}<div class="card-content"><div class="data-list">${d.utilization.slice(0,8).map(item => `
            <div class="data-row"><div class="data-info"><span class="name">${truncName(item.equipment_name)}</span><span class="sub">${item.sport_name}</span></div><span class="data-value">${item.utilization_rate}%</span></div>
        `).join('')}</div></div></div>`;
    }

    // High Demand
    if (d.high_demand && d.high_demand.length) {
        html += `<div class="insight-card">${cardHeader('fa-fire', 'High Demand Items', 'Equipment with highest demand pressure ratio')}<div class="card-content"><div class="data-list">${d.high_demand.slice(0,6).map(item => `
            <div class="data-row"><div class="data-info"><span class="name">${truncName(item.equipment_name)}</span><span class="sub">${item.sport_name} &bull; ${item.total_bookings_last_30_days} bookings/mo</span></div><span class="data-value" style="color:#ef4444">${item.demand_pressure}%</span></div>
        `).join('')}</div></div></div>`;
    }

    // Peak Hours
    if (d.peak_hours && d.peak_hours.length) {
        const maxH = Math.max(...d.peak_hours.map(h => h.booking_count));
        html += `<div class="insight-card">${cardHeader('fa-clock', 'Peak Booking Hours', 'Busiest hours for equipment checkouts')}<div class="card-content"><div class="bar-chart">${d.peak_hours.map(item => `
            <div class="bar-row"><span class="bar-label">${formatHour(item.hour)}</span><div class="bar-track"><div class="bar-fill" style="width:${(item.booking_count/maxH)*100}%"></div></div><span class="bar-value">${item.booking_count}</span></div>
        `).join('')}</div></div></div>`;
    }

    // Peak Days
    if (d.peak_days && d.peak_days.length) {
        html += `<div class="insight-card">${cardHeader('fa-calendar-week', 'Peak Booking Days', 'Days of the week with most equipment bookings')}<div class="card-content">${renderBarChart(d.peak_days.slice(0,5), 'day_name', 'booking_count')}</div></div>`;
    }

    // Condition Alerts
    if (d.condition_alerts && d.condition_alerts.length) {
        html += `<div class="insight-card">${cardHeader('fa-wrench', 'Condition Alerts', 'Equipment with damage or wear issues')}<div class="card-content"><div class="data-list">${d.condition_alerts.slice(0,6).map(item => `
            <div class="data-row"><div class="data-info"><span class="name">${truncName(item.equipment_name)}</span><span class="sub">${item.sport_name} &bull; ${item.damaged_count}/${item.total_stock} damaged</span></div><span class="data-value" style="color:#ef4444">${item.damage_rate}%</span></div>
        `).join('')}</div></div></div>`;
    }

    // Sport Demand
    if (d.sport_demand && d.sport_demand.length) {
        html += `<div class="insight-card">${cardHeader('fa-running', 'Sport-wise Demand', 'Equipment requests grouped by sport')}<div class="card-content">${renderBarChart(d.sport_demand.slice(0,6), 'sport_name', 'total_requests')}</div></div>`;
    }

    // Underutilized
    if (d.underutilized && d.underutilized.length) {
        html += `<div class="insight-card">${cardHeader('fa-box-open', 'Underutilized Equipment', 'Items with low or no recent booking activity')}<div class="card-content"><div class="data-list">${d.underutilized.slice(0,6).map(item => `
            <div class="data-row"><div class="data-info"><span class="name">${truncName(item.equipment_name)}</span><span class="sub">${item.sport_name} &bull; ${item.available_stock} in stock</span></div><span class="data-value" style="color:#d97706">${item.days_since_last_booking ? item.days_since_last_booking + 'd idle' : 'Never'}</span></div>
        `).join('')}</div></div></div>`;
    }

    return html;
}

// ══════════════════════════════════════════
// DEEP FACILITY ANALYTICS (from old API)
// ══════════════════════════════════════════
function renderFacilityDeepAnalytics() {
    let html = '';
    const d = facilityDeepData;

    // Utilization Rate — merged into main Facilities tab, skip here

    // High Demand Facilities
    if (d.high_demand && d.high_demand.length) {
        html += `<div class="insight-card">${cardHeader('fa-fire', 'High Demand Facilities', 'Facilities with highest daily demand rate')}<div class="card-content"><div class="data-list">${d.high_demand.slice(0,6).map(item => `
            <div class="data-row"><div class="data-info"><span class="name">${truncName(item.facility_name)}</span><span class="sub">${item.facility_type} &bull; ${item.bookings_last_30_days} bookings/mo</span></div><span class="data-value" style="color:#ef4444">${item.daily_demand_rate}% daily</span></div>
        `).join('')}</div></div></div>`;
    }

    // Peak Booking Days
    if (d.peak_days && d.peak_days.length) {
        html += `<div class="insight-card">${cardHeader('fa-calendar-week', 'Peak Booking Days', 'Days with highest facility reservation volume')}<div class="card-content">${renderBarChart(d.peak_days.slice(0,5), 'day_name', 'booking_count')}</div></div>`;
    }

    // Booking by User Type
    if (d.user_type && d.user_type.length) {
        html += `<div class="insight-card">${cardHeader('fa-users', 'Booking by User Type', 'Reservation distribution across user roles')}<div class="card-content">${renderBarChart(d.user_type, 'user_type', 'booking_count')}</div></div>`;
    }

    // Underutilized
    if (d.underutilized && d.underutilized.length) {
        html += `<div class="insight-card">${cardHeader('fa-box-open', 'Underutilized Facilities', 'Facilities with low or no recent bookings')}<div class="card-content"><div class="data-list">${d.underutilized.slice(0,6).map(item => `
            <div class="data-row"><div class="data-info"><span class="name">${truncName(item.facility_name)}</span><span class="sub">${item.facility_type} &bull; ${item.total_bookings} bookings</span></div><span class="data-value" style="color:#d97706">${item.days_since_last_booking ? item.days_since_last_booking + 'd idle' : 'Never'}</span></div>
        `).join('')}</div></div></div>`;
    }

    return html;
}

// ══════════════════════════════════════════
// BUDGET TAB
// ══════════════════════════════════════════
function renderBudgetTab() {
    const container = document.getElementById('budget-grid');
    const { budget, insights } = dashboardData;
    const { budget_efficiency } = insights;
    
    container.innerHTML = `
        <div class="insight-card wide horizontal-overview">
            <div class="overview-title">
                <h3><i class="fas fa-heartbeat"></i> Budget Health</h3>
                <p>Sports on track, at risk, or underspending</p>
            </div>
            <div class="overview-stats">
                <div class="stat-item">
                    <div class="stat-num" style="color: #059669">${budget_efficiency.summary.on_track}</div>
                    <div class="stat-label">On Track</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num" style="color: #ef4444">${budget_efficiency.summary.overspend_risk}</div>
                    <div class="stat-label">Overspend Risk</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num" style="color: #d97706">${budget_efficiency.summary.underspend}</div>
                    <div class="stat-label">Underspend</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">${budget_efficiency.summary.total}</div>
                    <div class="stat-label">Total Sports</div>
                </div>
            </div>
        </div>

        <div class="insight-card">
            ${cardHeader('fa-chart-pie', 'Budget Overview (' + budget.year + ')', 'Allocation vs spending for the current fiscal year')}
            <div class="card-content">
                <div class="chart-wrapper" style="max-width:220px;margin:0 auto"><canvas id="chart-budget-overview"></canvas></div>
                <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:12px 20px;margin-top:16px;font-size:0.82rem;font-weight:600">
                    <span style="color:#6b21a8">Allocated: Rs. ${formatCurrency(budget.allocated)}</span>
                    <span style="color:#ef4444">Spent: Rs. ${formatCurrency(budget.spent)}</span>
                    <span style="color:#059669">Left: Rs. ${formatCurrency(budget.remaining)}</span>
                </div>
            </div>
        </div>
        
        <div class="insight-card">
            ${cardHeader('fa-gauge-high', 'Budget Utilization by Sport', 'Spending vs allocation percentage per sport')}
            <div class="card-content">
                ${budget_efficiency.sports.length > 0 ? `
                    <div class="data-list">${budget_efficiency.sports.map(s => `
                        <div class="data-row">
                            <div class="data-info">
                                <span class="name">${truncName(s.sport_name)}</span>
                                <span class="sub">Rs. ${formatCurrency(s.spent_amount)} / ${formatCurrency(s.allocated_amount)}</span>
                            </div>
                            <span class="data-value" style="color: ${s.utilization > 80 ? '#ef4444' : s.utilization > 50 ? '#d97706' : '#059669'}">${s.utilization}%</span>
                        </div>
                    `).join('')}</div>
                ` : '<p class="no-data">No budget data available</p>'}
            </div>
        </div>
        

    `;

    // Render budget donut chart
    createPieChart('chart-budget-overview',
        ['Spent', 'Remaining'],
        [Number(budget.spent), Number(budget.remaining)],
        ['#ef4444', '#059669']);
}

// ══════════════════════════════════════════
// ACHIEVEMENTS TAB (merged with Events)
// ══════════════════════════════════════════
function renderAchievementsTab() {
    const container = document.getElementById('achievements-grid');
    const { events, achievements } = dashboardData;
    
    container.innerHTML = `
        <div class="insight-card wide horizontal-overview">
            <div class="overview-title">
                <h3><i class="fas fa-trophy"></i> Events & Achievements</h3>
                <p>Summary of events and active programs</p>
            </div>
            <div class="overview-stats">
                <div class="stat-item">
                    <div class="stat-num" style="color: #4b0082">${events.total}</div>
                    <div class="stat-label">Total Events</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num" style="color: #059669">${events.active}</div>
                    <div class="stat-label">Active Events</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">${achievements.total}</div>
                    <div class="stat-label">Total Achievements</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">${achievements.top_performers.length}</div>
                    <div class="stat-label">Active Performers</div>
                </div>
            </div>
        </div>
        
        <div class="insight-card">
            ${cardHeader('fa-ranking-star', 'Top Performers', 'Students with the most achievements and points')}
            <div class="card-content">
                ${achievements.top_performers.length > 0 ? `
                    <div class="data-list">${achievements.top_performers.map((p, i) => `
                        <div class="data-row">
                            <div class="data-info">
                                <span class="name">${i === 0 ? '🥇' : i === 1 ? '🥈' : i === 2 ? '🥉' : '🏅'} ${p.student_name}</span>
                                <span class="sub">${p.achievement_count} achievements</span>
                            </div>
                            <span class="data-value">${Number(p.total_points).toLocaleString()} pts</span>
                        </div>
                    `).join('')}</div>
                ` : '<p class="no-data">No performers recorded yet</p>'}
            </div>
        </div>
        
        <div class="insight-card">
            ${cardHeader('fa-chart-bar', 'Achievements by Sport', 'Award distribution across different sports')}
            <div class="card-content">
                ${achievements.by_sport.length > 0 ?
                    renderBarChart(achievements.by_sport, 'sport_name', 'count')
                    : '<p class="no-data">No sport achievements data</p>'}
            </div>
        </div>
        
        <div class="insight-card">
            ${cardHeader('fa-clock', 'Recent Achievements', 'Latest awards granted to students')}
            <div class="card-content">
                ${achievements.recent.length > 0 ? `
                    <div class="activity-feed">${achievements.recent.map(a => `
                        <div class="activity-item">
                            <div class="activity-icon"><i class="fas fa-star"></i></div>
                            <div class="activity-details">
                                <div class="activity-title">${a.student_name}</div>
                                <div class="activity-sub">${a.achievement_type} ${a.sport_name ? '&bull; ' + a.sport_name : ''} &bull; ${a.points} pts</div>
                                <div class="activity-meta">${a.date_achieved}</div>
                            </div>
                        </div>
                    `).join('')}</div>
                ` : '<p class="no-data">No recent achievements</p>'}
            </div>
        </div>
    `;
}

// ══════════════════════════════════════════
// COMMUNITY TAB
// ══════════════════════════════════════════
function renderCommunityTab() {
    const container = document.getElementById('community-grid');
    const { community } = dashboardData;
    
    container.innerHTML = `
        <div class="insight-card wide horizontal-overview">
            <div class="overview-title">
                <h3><i class="fas fa-newspaper"></i> Community Overview</h3>
                <p>Engagement metrics and post stats</p>
            </div>
            <div class="overview-stats">
                <div class="stat-item">
                    <div class="stat-num" style="color: #4b0082">${community.post_stats.total_posts || 0}</div>
                    <div class="stat-label">Total Posts</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">${community.post_stats.active_posts || 0}</div>
                    <div class="stat-label">Active Posts</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">${community.total_comments}</div>
                    <div class="stat-label">Total Comments</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">${community.post_stats.commenting_enabled || 0}</div>
                    <div class="stat-label">Comments Enabled</div>
                </div>
            </div>
        </div>
        
        <div class="insight-card">
            ${cardHeader('fa-envelope', 'Inquiries', 'User inquiry status breakdown')}
            <div class="card-content">
                <div class="stat-grid">
                    <div class="stat-item">
                        <div class="stat-num">${community.inquiry_stats.total || 0}</div>
                        <div class="stat-label">Total Inquiries</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num" style="color: #d97706">${community.inquiry_stats.unresolved || 0}</div>
                        <div class="stat-label">Unresolved</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num" style="color: #059669">${community.inquiry_stats.resolved || 0}</div>
                        <div class="stat-label">Resolved</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="insight-card">
            ${cardHeader('fa-comments', 'Recent Comments', 'Latest user comments on community posts')}
            <div class="card-content">
                ${community.recent_comments.length > 0 ? `
                    <div class="activity-feed">${community.recent_comments.map(c => `
                        <div class="activity-item">
                            <div class="activity-icon"><i class="fas fa-comment"></i></div>
                            <div class="activity-details">
                                <div class="activity-title">${escapeHtml(c.content)}</div>
                                <div class="activity-sub">${c.user_name} on "${c.post_title}"</div>
                            </div>
                        </div>
                    `).join('')}</div>
                ` : '<p class="no-data">No comments yet</p>'}
            </div>
        </div>
        
        <div class="insight-card">
            ${cardHeader('fa-inbox', 'Recent Inquiries', 'Latest user inquiries and their resolution status')}
            <div class="card-content">
                ${community.recent_inquiries.length > 0 ? `
                    <div class="activity-feed">${community.recent_inquiries.map(inq => `
                        <div class="activity-item">
                            <div class="activity-icon"><i class="fas fa-envelope-open"></i></div>
                            <div class="activity-details">
                                <div class="activity-title">${escapeHtml(inq.subject)}</div>
                                <div class="activity-sub">${inq.email}</div>
                                <div class="activity-meta">${inq.date} &bull; <span class="status-badge ${inq.status.toLowerCase()}">${inq.status}</span></div>
                            </div>
                        </div>
                    `).join('')}</div>
                ` : '<p class="no-data">No inquiries yet</p>'}
            </div>
        </div>
    `;
}

// ══════════════════════════════════════════
// HELPER FUNCTIONS
// ══════════════════════════════════════════
function renderBarChart(data, labelKey, valueKey) {
    if (!data || data.length === 0) return '<p class="no-data">No data available</p>';
    const maxValue = Math.max(...data.map(d => d[valueKey]));
    return `<div class="bar-chart">${data.map(item => `
        <div class="bar-row">
            <span class="bar-label">${truncName(item[labelKey])}</span>
            <div class="bar-track">
                <div class="bar-fill" style="width: ${maxValue > 0 ? (item[valueKey] / maxValue) * 100 : 0}%"></div>
            </div>
            <span class="bar-value">${Number(item[valueKey]).toLocaleString()}</span>
        </div>
    `).join('')}</div>`;
}

function cardHeader(icon, title, desc) {
    return `<div class="card-header"><i class="fas ${icon}"></i><div class="card-header-text"><h3>${title}</h3><p class="card-desc">${desc}</p></div></div>`;
}

function truncName(name, max = 32) {
    if (!name) return '';
    return name.length > max ? name.substring(0, max) + '...' : name;
}

function formatCurrency(amount) {
    return Number(amount).toLocaleString('en-IN');
}

function formatHour(hour) {
    const h = parseInt(hour);
    if (h === 0) return '12 AM';
    if (h === 12) return '12 PM';
    return h > 12 ? `${h - 12} PM` : `${h} AM`;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showError(message) {
    document.getElementById('kpi-cards').innerHTML = `
        <div class="error-card">
            <i class="fas fa-exclamation-circle"></i>
            <span>${message}</span>
        </div>
    `;
}

// ══════════════════════════════════════════
// CHART.JS FACTORY FUNCTIONS
// ══════════════════════════════════════════
const CHART_COLORS = [
    '#6366f1', '#8b5cf6', '#a855f7', '#d946ef',
    '#ec4899', '#f43f5e', '#f97316', '#eab308',
    '#22c55e', '#14b8a6', '#06b6d4', '#3b82f6'
];

let activeCharts = {}; // Track charts for cleanup

function createPieChart(canvasId, labels, data, colors = null) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    // Destroy existing chart on same canvas
    if (activeCharts[canvasId]) activeCharts[canvasId].destroy();

    const palette = colors || CHART_COLORS.slice(0, labels.length);

    activeCharts[canvasId] = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: palette,
                borderColor: '#fff',
                borderWidth: 2,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '55%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 12,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { family: 'Inter', size: 11, weight: 500 }
                    }
                },
                tooltip: {
                    backgroundColor: '#1a1a2e',
                    titleFont: { family: 'Inter', weight: 600 },
                    bodyFont: { family: 'Inter' },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(ctx) {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = ((ctx.parsed / total) * 100).toFixed(1);
                            return ` ${ctx.label}: ${ctx.parsed.toLocaleString()} (${pct}%)`;
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                duration: 800
            }
        }
    });
}

function createBarChart(canvasId, labels, data, label) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    if (activeCharts[canvasId]) activeCharts[canvasId].destroy();

    activeCharts[canvasId] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                backgroundColor: 'rgba(99, 102, 241, 0.8)',
                borderColor: '#6366f1',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1a1a2e',
                    titleFont: { family: 'Inter', weight: 600 },
                    bodyFont: { family: 'Inter' },
                    padding: 12,
                    cornerRadius: 8
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Inter', size: 11 },
                        color: '#888'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: {
                        font: { family: 'Inter', size: 11 },
                        color: '#888'
                    }
                }
            },
            animation: {
                duration: 800
            }
        }
    });
}

function createLineChart(canvasId, labels, data, label) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    if (activeCharts[canvasId]) activeCharts[canvasId].destroy();

    activeCharts[canvasId] = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderWidth: 2.5,
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#6366f1',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1a1a2e',
                    titleFont: { family: 'Inter', weight: 600 },
                    bodyFont: { family: 'Inter' },
                    padding: 12,
                    cornerRadius: 8
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Inter', size: 11 },
                        color: '#888'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: {
                        font: { family: 'Inter', size: 11 },
                        color: '#888'
                    }
                }
            },
            animation: {
                duration: 800
            }
        }
    });
}

// ══════════════════════════════════════════
// EXPORT FUNCTIONS
// ══════════════════════════════════════════
function exportDashboard(format) {
    const facultyId = document.getElementById('faculty-select').value;
    let url = `/uoc-sports/public/executive-dashboard/export/${format}`;
    
    if (facultyId) {
        url += '?faculty_id=' + encodeURIComponent(facultyId);
    }
    
    // Show loading state
    const btn = format === 'csv' ? document.getElementById('export-csv-btn') : document.getElementById('export-pdf-btn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
    btn.disabled = true;
    
    // Trigger download
    setTimeout(() => {
        window.location.href = url;
        
        // Restore button state
        btn.innerHTML = originalText;
        btn.disabled = false;
    }, 500);
}

// ══════════════════════════════════════════
// PHASE 3: YEAR FILTERING & DRILL-DOWN
// ══════════════════════════════════════════

/**
 * Apply year filter to dashboard
 */
function applyYearFilter() {
    const year = document.getElementById('year-select').value;
    
    // Store year in session storage for drill-down use
    sessionStorage.setItem('dashboardYear', year);
    
    // Reload dashboard with updated data
    loadDashboard();
}

/**
 * Navigate to sport performance drill-down view
 */
function drillDownSportPerformance(sportId) {
    const year = document.getElementById('year-select').value;
    
    let url = `/uoc-sports/public/executive-dashboard/drill-down/sport-performance?sport_id=${sportId}&year=${year}`;
    window.location.href = url;
}

/**
 * Navigate to budget trends drill-down view
 */
function drillDownBudgetTrends() {
    const year = document.getElementById('year-select').value;
    
    let url = `/uoc-sports/public/executive-dashboard/drill-down/budget-trends?year=${year}`;
    window.location.href = url;
}

/**
 * Navigate to utilization trends drill-down view
 */
function drillDownUtilizationTrends() {
    const year = document.getElementById('year-select').value;
    
    let url = `/uoc-sports/public/executive-dashboard/drill-down/utilization?year=${year}`;
    window.location.href = url;
}
</script>

</body>
</html>
