<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Reports | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
        @import url(/uoc-sports/public/css/admin/equipments-page.css);
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
    <div class="equipments-grid-container">
        <!-- LEFT PANEL: Back link + Report Buttons + Filters -->
        <div class="equipments-grid-left">
            <div class="analytics-link-box">
                <a href="./admin-equipments" class="btn-analytics">
                    <i class="fas fa-arrow-left"></i>
                    Back to Equipment Management
                </a>
            </div>

            <div class="form-nav-panel">
                <h2><i class="fas fa-file-lines"></i> Reports</h2>

                <div class="form-nav-buttons">
                    <button class="form-nav-btn active" data-report="inventory" onclick="switchReport('inventory', this)">
                        <i class="fas fa-boxes-stacked"></i>
                        <span>Equipment Inventory</span>
                    </button>
                    <button class="form-nav-btn" data-report="suppliers" onclick="switchReport('suppliers', this)">
                        <i class="fas fa-truck-field"></i>
                        <span>Supplier Details</span>
                    </button>
                    <button class="form-nav-btn" data-report="snapshot" onclick="switchReport('snapshot', this)">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Equipment Snapshot</span>
                    </button>
                    <button class="form-nav-btn" data-report="stock" onclick="switchReport('stock', this)">
                        <i class="fas fa-warehouse"></i>
                        <span>Stock Snapshot</span>
                    </button>
                    <button class="form-nav-btn" data-report="period" onclick="switchReport('period', this)">
                        <i class="fas fa-chart-bar"></i>
                        <span>Activity Snapshot</span>
                    </button>
                    <button class="form-nav-btn" data-report="supplier-detail" onclick="switchReport('supplier-detail', this)">
                        <i class="fas fa-magnifying-glass-dollar"></i>
                        <span>Supplier Detail</span>
                    </button>
                </div>

                <!-- Dynamic Filters (Loaded based on report selection) -->
                <div class="dynamic-filters">
                    <!-- Supplier Picker (only visible for supplier-detail report) -->
                    <div class="filter-section dynamic-filter-container" id="supplier-picker" style="display:none;">
                        <label class="filter-label">Select Supplier</label>
                        <select id="filter-supplier" class="filter-select" onchange="loadCurrentReport()">
                            <option value="">-- Choose a supplier --</option>
                        </select>
                    </div>
                </div>

                <!-- Divider -->
                <div class="panel-divider"></div>

                <!-- Time Period Filter -->
                <div class="filter-section">
                    <label class="filter-label">Time Period</label>
                    <div class="filter-tabs">
                        <button class="filter-tab active" data-period="all" onclick="setPeriod('all', this)">All Time</button>
                        <button class="filter-tab" data-period="annual" onclick="setPeriod('annual', this)">Annual</button>
                        <button class="filter-tab" data-period="monthly" onclick="setPeriod('monthly', this)">Monthly</button>
                    </div>
                </div>

                <!-- Year Filter -->
                <div class="filter-section" id="year-filter" style="display:none;">
                    <label class="filter-label">Year</label>
                    <select id="filter-year" class="filter-select" onchange="loadCurrentReport()"></select>
                </div>

                <!-- Month Filter -->
                <div class="filter-section" id="month-filter" style="display:none;">
                    <label class="filter-label">Month</label>
                    <select id="filter-month" class="filter-select" onchange="loadCurrentReport()">
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>

                <div class="filter-summary" id="period-label">
                    <i class="fas fa-clock"></i> Showing: <strong>All Time</strong>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Report Data + PDF Export -->
        <div class="equipments-grid-right">
            <div class="equipments-forms-container">

                <!-- Report header bar -->
                <div class="report-header-bar">
                    <h2 id="report-title"><i class="fas fa-boxes-stacked"></i> Equipment-wise Inventory</h2>
                    <button class="report-pdf-btn" onclick="exportPDF()">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>

                <!-- Loading -->
                <div id="report-loading" class="report-loading" style="display:none;">
                    <i class="fas fa-spinner fa-spin"></i> Loading report data...
                </div>

                <!-- Empty state -->
                <div id="report-empty" class="report-empty" style="display:none;">
                    <i class="fas fa-folder-open"></i>
                    <p>No data found for the selected period.</p>
                </div>

                <!-- Report content area -->
                <div id="report-content"></div>

            </div>
        </div>
    </div>
</div>

<script>
const DATA_BASE = 'admin-equipments/report-data/';
const PDF_BASE = 'admin-equipments/report/';
let currentReport = 'inventory';
let currentPeriod = 'all';

const REPORT_TITLES = {
    inventory: '<i class="fas fa-boxes-stacked"></i> Equipment-wise Inventory',
    suppliers: '<i class="fas fa-truck-field"></i> Supplier Details & Analysis',
    snapshot:  '<i class="fas fa-clipboard-list"></i> All Equipment Snapshot',
    stock:     '<i class="fas fa-warehouse"></i> Stock-wise Snapshot',
    period:    '<i class="fas fa-chart-bar"></i> Activity Snapshot',
    'supplier-detail': '<i class="fas fa-magnifying-glass-dollar"></i> Supplier Detail'
};
let suppliersLoaded = false;

// ── Populate year dropdown ──
(function() {
    const yearSel = document.getElementById('filter-year');
    const currentYear = new Date().getFullYear();
    for (let y = currentYear; y >= currentYear - 10; y--) {
        yearSel.innerHTML += `<option value="${y}">${y}</option>`;
    }
    document.getElementById('filter-month').value = new Date().getMonth() + 1;
})();

// ── Switching reports ──
function switchReport(report, btn) {
    currentReport = report;
    document.querySelectorAll('.form-nav-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('report-title').innerHTML = REPORT_TITLES[report];

    // Show/hide supplier picker
    document.getElementById('supplier-picker').style.display = report === 'supplier-detail' ? 'block' : 'none';
    if (report === 'supplier-detail' && !suppliersLoaded) {
        loadSupplierDropdown();
    }
    loadCurrentReport();
}

// ── Load supplier dropdown ──
async function loadSupplierDropdown() {
    try {
        const res = await fetch('admin-equipments/get-suppliers');
        const json = await res.json();
        if (json.status === 'success') {
            const sel = document.getElementById('filter-supplier');
            sel.innerHTML = '<option value="">-- Choose a supplier --</option>';
            json.data.forEach(s => {
                sel.innerHTML += `<option value="${s.supplier_id}">${s.supplier_name}</option>`;
            });
            suppliersLoaded = true;
        }
    } catch (e) { console.error('Failed to load suppliers', e); }
}

// ── Period selection ──
function setPeriod(period, btn) {
    currentPeriod = period;
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');

    document.getElementById('year-filter').style.display = (period === 'annual' || period === 'monthly') ? 'block' : 'none';
    document.getElementById('month-filter').style.display = period === 'monthly' ? 'block' : 'none';

    updatePeriodLabel();
    loadCurrentReport();
}

function updatePeriodLabel() {
    const year = document.getElementById('filter-year').value;
    const monthSel = document.getElementById('filter-month');
    let label = 'All Time';
    if (currentPeriod === 'annual') label = `Year ${year}`;
    else if (currentPeriod === 'monthly') label = `${monthSel.options[monthSel.selectedIndex].text} ${year}`;
    document.getElementById('period-label').innerHTML = `<i class="fas fa-clock"></i> Showing: <strong>${label}</strong>`;
}

function getQueryParams() {
    const year = document.getElementById('filter-year').value;
    const month = document.getElementById('filter-month').value;
    let params = '';
    if (currentPeriod === 'annual') params = `?year=${year}`;
    else if (currentPeriod === 'monthly') params = `?year=${year}&month=${month}`;
    // Add supplier_id for supplier-detail report
    if (currentReport === 'supplier-detail') {
        const sid = document.getElementById('filter-supplier').value;
        if (sid) params += (params ? '&' : '?') + `supplier_id=${sid}`;
    }
    return params;
}

// ── Export PDF ──
function exportPDF() {
    window.open(PDF_BASE + currentReport + getQueryParams(), '_blank');
}

// ── Load report data ──
async function loadCurrentReport() {
    updatePeriodLabel();
    const content = document.getElementById('report-content');
    const loading = document.getElementById('report-loading');
    const empty = document.getElementById('report-empty');

    content.innerHTML = '';
    empty.style.display = 'none';

    // For supplier-detail, require a supplier selection
    if (currentReport === 'supplier-detail' && !document.getElementById('filter-supplier').value) {
        content.innerHTML = '<div class="report-empty" style="display:flex;"><i class="fas fa-hand-pointer"></i><p>Please select a supplier from the dropdown.</p></div>';
        return;
    }

    loading.style.display = 'flex';

    try {
        const res = await fetch(DATA_BASE + currentReport + getQueryParams());
        const json = await res.json();
        loading.style.display = 'none';

        if (json.status !== 'success') { empty.style.display = 'flex'; return; }

        const renderers = {
            inventory: renderInventory,
            suppliers: renderSuppliers,
            snapshot:  renderSnapshot,
            stock:     renderStock,
            period:    renderPeriod,
            'supplier-detail': renderSupplierDetail
        };
        renderers[currentReport](json.data, content);
    } catch (e) {
        loading.style.display = 'none';
        empty.style.display = 'flex';
    }
}

// ── Helper: Build HTML table ──
function buildTable(headers, rows) {
    let html = '<div class="report-table-wrap"><table class="report-table"><thead><tr>';
    headers.forEach(h => html += `<th>${h}</th>`);
    html += '</tr></thead><tbody>';
    rows.forEach(row => {
        html += '<tr>';
        row.forEach(cell => html += `<td>${cell}</td>`);
        html += '</tr>';
    });
    if (rows.length === 0) {
        html += `<tr><td colspan="${headers.length}" style="text-align:center;color:#999;padding:24px;">No data available</td></tr>`;
    }
    html += '</tbody></table></div>';
    return html;
}

// ── Helper: Summary card ──
function summaryCard(icon, label, value, color = '#4b0082') {
    return `<div class="summary-card">
        <div class="summary-card-icon" style="color:${color}"><i class="${icon}"></i></div>
        <div class="summary-card-info">
            <span class="summary-card-value">${value}</span>
            <span class="summary-card-label">${label}</span>
        </div>
    </div>`;
}

// ── Condition badge ──
function condBadge(pct) {
    let cls = 'cond-good';
    if (pct < 50) cls = 'cond-bad';
    else if (pct < 80) cls = 'cond-warn';
    return `<span class="cond-badge ${cls}">${pct}%</span>`;
}

// ══════════════════════════════════════
// Renderer: Equipment Inventory
// ══════════════════════════════════════
function renderInventory(data, el) {
    const s = data.summary;
    let html = '<div class="summary-row">';
    html += summaryCard('fas fa-cubes', 'Equip. Types', s.total_equipment_types);
    html += summaryCard('fas fa-boxes-stacked', 'Total Stock', s.total_stock);
    html += summaryCard('fas fa-check-circle', 'Usable', s.total_usable, '#2e7d32');
    html += summaryCard('fas fa-exclamation-triangle', 'Damaged', s.total_damaged, '#c62828');
    html += summaryCard('fas fa-heartbeat', 'Condition', s.overall_condition + '%', s.overall_condition >= 80 ? '#2e7d32' : '#e65100');
    html += '</div>';

    const rows = data.equipment.map(r => [
        r.sport_name, r.equipment_name, r.total_stock, r.usable, r.damaged, condBadge(r.condition_percent)
    ]);
    html += buildTable(['Sport', 'Equipment', 'Stock', 'Usable', 'Damaged', 'Condition'], rows);
    el.innerHTML = html;
}

// ══════════════════════════════════════
// Renderer: Supplier Report
// ══════════════════════════════════════
function renderSuppliers(data, el) {
    const totalVal = data.reduce((sum, r) => sum + parseFloat(r.total_value), 0);
    let html = '<div class="summary-row">';
    html += summaryCard('fas fa-truck-field', 'Suppliers', data.length);
    html += summaryCard('fas fa-money-bill-wave', 'Total Spent', 'Rs. ' + totalVal.toLocaleString(undefined, {minimumFractionDigits:2}), '#1565c0');
    html += '</div>';

    const rows = data.map(r => [
        r.supplier_name,
        r.telephone_1,
        r.email || '-',
        r.total_grns,
        r.total_items_supplied,
        'Rs. ' + parseFloat(r.total_value).toLocaleString(undefined, {minimumFractionDigits:2})
    ]);
    html += buildTable(['Supplier', 'Telephone', 'Email', 'GRNs', 'Items', 'Total Value'], rows);
    el.innerHTML = html;
}

// ══════════════════════════════════════
// Renderer: All Equipment Snapshot
// ══════════════════════════════════════
function renderSnapshot(data, el) {
    let i = 0;
    const rows = data.map(r => [++i, r.sport_name ?? '-', r.equipment_name, r.total_stock, r.usable]);
    el.innerHTML = buildTable(['#', 'Sport', 'Equipment', 'Total Stock', 'Usable'], rows);
}

// ══════════════════════════════════════
// Renderer: Stock-wise Snapshot
// ══════════════════════════════════════
function renderStock(data, el) {
    const rows = data.map(r => [
        r.stock_id, r.sport_name, r.equipment_name, r.quantity, r.usable, r.damaged, r.added_date, r.remarks ?? '-'
    ]);
    el.innerHTML = buildTable(['Stock ID', 'Sport', 'Equipment', 'Qty', 'Usable', 'Damaged', 'Date Added', 'Remarks'], rows);
}

// ══════════════════════════════════════
// Renderer: Activity Snapshot (Period)
// ══════════════════════════════════════
function renderPeriod(data, el) {
    const grn = data.grn, gin = data.gin, gcn = data.gcn;
    let html = '<div class="summary-row">';
    html += summaryCard('fas fa-arrow-down', 'GRNs', `${grn.total_grns} notes / ${grn.total_received} items`, '#2e7d32');
    html += summaryCard('fas fa-arrow-up', 'GINs', `${gin.total_gins} notes / ${gin.total_issued} items`, '#1565c0');
    html += summaryCard('fas fa-ban', 'GCNs', `${gcn.total_gcns} notes / ${gcn.total_condemned} items`, '#c62828');
    html += summaryCard('fas fa-money-bill-wave', 'GRN Cost', 'Rs. ' + parseFloat(grn.total_cost).toLocaleString(undefined, {minimumFractionDigits:2}), '#e65100');
    html += '</div>';

    // GRN details
    if (data.grn_details && data.grn_details.length) {
        html += '<h3 class="section-subtitle"><i class="fas fa-arrow-down"></i> Goods Received</h3>';
        const grnRows = data.grn_details.map(r => [
            r.date, r.equipment_name, r.sport_name, r.supplier_name, r.quantity, r.unit,
            'Rs. ' + parseFloat(r.unit_price).toLocaleString(undefined, {minimumFractionDigits:2}),
            r.po_number || '-'
        ]);
        html += buildTable(['Date', 'Equipment', 'Sport', 'Supplier', 'Qty', 'Unit', 'Price', 'PO No.'], grnRows);
    }

    // GIN details
    if (data.gin_details && data.gin_details.length) {
        html += '<h3 class="section-subtitle"><i class="fas fa-arrow-up"></i> Goods Issued</h3>';
        const ginRows = data.gin_details.map(r => [r.date, r.equipment_name, r.sport_name, r.quantity, r.unit, r.stock_id]);
        html += buildTable(['Date', 'Equipment', 'Sport', 'Qty', 'Unit', 'Stock ID'], ginRows);
    }

    // GCN details
    if (data.gcn_details && data.gcn_details.length) {
        html += '<h3 class="section-subtitle"><i class="fas fa-ban"></i> Goods Condemned</h3>';
        const gcnRows = data.gcn_details.map(r => [r.date ? r.date.substring(0, 10) : '-', r.equipment_name, r.sport_name, r.quantity, r.stock_id]);
        html += buildTable(['Date', 'Equipment', 'Sport', 'Qty', 'Stock ID'], gcnRows);
    }

    el.innerHTML = html;
}

// ══════════════════════════════════════
// Renderer: Supplier Detail
// ══════════════════════════════════════
function renderSupplierDetail(data, el) {
    const sup = data.supplier;
    const s = data.summary;

    let html = '<div class="supplier-info-header">';
    html += `<h3><i class="fas fa-building"></i> ${sup.supplier_name}</h3>`;
    html += `<p><i class="fas fa-location-dot"></i> ${sup.address}</p>`;
    html += `<p><i class="fas fa-phone"></i> ${sup.telephone_1}${sup.telephone_2 ? ' / ' + sup.telephone_2 : ''}`;
    html += `&nbsp;&nbsp;<i class="fas fa-envelope"></i> ${sup.email || '-'}</p>`;
    html += '</div>';

    html += '<div class="summary-row">';
    html += summaryCard('fas fa-file-lines', 'Total GRNs', s.total_grns);
    html += summaryCard('fas fa-boxes-stacked', 'Items Supplied', s.total_items, '#2e7d32');
    html += summaryCard('fas fa-money-bill-wave', 'Total Value', 'Rs. ' + parseFloat(s.total_value).toLocaleString(undefined, {minimumFractionDigits:2}), '#1565c0');
    html += '</div>';

    if (data.grns && data.grns.length) {
        html += '<h3 class="section-subtitle"><i class="fas fa-truck-ramp-box"></i> Delivery Records</h3>';
        const rows = data.grns.map(r => [
            r.date,
            r.equipment_name,
            r.sport_name,
            r.description || '-',
            r.quantity,
            r.unit,
            'Rs. ' + parseFloat(r.unit_price).toLocaleString(undefined, {minimumFractionDigits:2}),
            'Rs. ' + (r.quantity * r.unit_price).toLocaleString(undefined, {minimumFractionDigits:2}),
            r.po_number || '-',
            r.invoice_no || '-'
        ]);
        html += buildTable(['Date', 'Equipment', 'Sport', 'Description', 'Qty', 'Unit', 'Price', 'Total', 'PO No.', 'Invoice'], rows);
    } else {
        html += '<div class="report-empty" style="display:flex;"><i class="fas fa-inbox"></i><p>No deliveries from this supplier in the selected period.</p></div>';
    }

    el.innerHTML = html;
}

// ── Auto-load first report ──
loadCurrentReport();
</script>

<?php require '../app/views/templates/admin/footer.php'; ?>

</body>
<script>
    var currentPage = document.getElementById("sidebar-equipments");
    currentPage.classList.add("active");
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</html>
