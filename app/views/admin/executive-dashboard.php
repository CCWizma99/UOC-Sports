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

    <link rel="stylesheet" href="/uoc-sports/public/css/admin/executive-dashboard.css">
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

        <!-- Faculty Selector (for scoped dashboards) -->
        <div class="faculty-selector-container" style="margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 12px; background: white; padding: 14px 18px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); flex-wrap: wrap;">
                <label for="faculty-select" style="font-weight: 600; color: #1a1a2e; flex-shrink: 0;">
                    <i class="fas fa-building"></i> Faculty:
                </label>
                <select id="faculty-select" style="padding: 8px 12px; border: 1px solid #d0d0d0; border-radius: 6px; font-size: 0.95rem; cursor: pointer; flex: 0 1 300px;">
                    <option value="">-- All Faculties --</option>
                    <?php if (!empty($faculties)): ?>
                        <?php foreach ($faculties as $faculty): ?>
                            <option value="<?= htmlspecialchars($faculty['faculty_id']) ?>"
                                    <?= ($selectedFacultyId === $faculty['faculty_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($faculty['faculty_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <span id="faculty-status" style="font-size: 0.82rem; color: #666; font-style: italic; flex-shrink: 0;"></span>
                
                <!-- Export Buttons -->
                <div style="flex: 1; text-align: right; min-width: 300px;">
                    <button id="export-csv-btn" onclick="exportDashboard('csv')" 
                            style="padding: 8px 16px; background: #059669; color: white; border: none; border-radius: 6px; font-size: 0.9rem; cursor: pointer; margin-right: 8px; display: inline-flex; align-items: center; gap: 6px; transition: background 0.3s;"
                            onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                        <i class="fas fa-download"></i> <span>Export CSV</span>
                    </button>
                    <button id="export-pdf-btn" onclick="exportDashboard('pdf')" 
                            style="padding: 8px 16px; background: #dc2626; color: white; border: none; border-radius: 6px; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: background 0.3s;"
                            onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                        <i class="fas fa-file-pdf"></i> <span>Export PDF</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Date Range Filter (Phase 3) -->
        <div class="date-filter-container" style="margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 12px; background: white; padding: 14px 18px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); flex-wrap: wrap;">
                <label style="font-weight: 600; color: #1a1a2e; flex-shrink: 0;">
                    <i class="fas fa-calendar"></i> Date Range:
                </label>
                <input type="date" id="start-date" value="<?php echo date('Y-01-01'); ?>" 
                       style="padding: 8px 12px; border: 1px solid #d0d0d0; border-radius: 6px; font-size: 0.95rem; cursor: pointer;">
                <span style="color: #666; flex-shrink: 0;">to</span>
                <input type="date" id="end-date" value="<?php echo date('Y-m-d'); ?>" 
                       style="padding: 8px 12px; border: 1px solid #d0d0d0; border-radius: 6px; font-size: 0.95rem; cursor: pointer;">
                <button onclick="applyDateFilter()" 
                        style="padding: 8px 16px; background: #0891b2; color: white; border: none; border-radius: 6px; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: background 0.3s;"
                        onmouseover="this.style.background='#0e7490'" onmouseout="this.style.background='#0891b2'">
                    <i class="fas fa-filter"></i> Apply Filter
                </button>
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

<script src="/uoc-sports/public/js/admin/executive-dashboard.js"></script>
</body>
</html>
