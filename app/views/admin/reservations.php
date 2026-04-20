<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);

        @import url(/uoc-sports/public/css/admin/reservations-page.css);
        @import url(/uoc-sports/public/css/admin/search-user.css);
        @import url(/uoc-sports/public/css/admin/user-stat.css);
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
    <div class="reservations-grid-container">
        <div class="reservations-grid-left">
            <section id="search-user">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0;">Search Reservations</h2>
                    <a href="/uoc-sports/public/admin-facility-rates" class="action-link" style="background: var(--theme-color); color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-tags"></i> Manage Facility Rates
                    </a>
                </div>
                <div class="filter-bar">
                    <h3>Filter <i class="fa-solid fa-filter"></i></h3>

                    <!-- Date -->
                    <div class="btn" id="date-btn">
                        Date
                        <div class="dropdown" data-filter="date">
                            <input type="date" id="filter-date">
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="btn" id="location-btn">
                        Location
                        <div class="dropdown" data-filter="location" id="location-dropdown">
                            <!-- Locations will be loaded here -->
                            <div data-value="">All</div>
                        </div>
                    </div>

                    <!-- User Type -->
                    <div class="btn" id="user-type-btn">
                        User Type
                        <div class="dropdown" data-filter="user_type" id="user-type-dropdown">
                            <div data-value="">All</div>
                            <div data-value="PUBLIC">External Users</div>
                            <div data-value="STUDENT">Students</div>
                            <div data-value="INTERNAL">Internal Users</div>
                            <div data-value="ADMIN">Admin</div>
                        </div>
                    </div>
                </div>

                <input type="text" name="search-reservation-inp" id="search-reservation-inp" 
                    title="Enter Reservation ID or User Name" placeholder="Enter Reservation ID or Name">

                <div class="search-output"></div>
            </section>
        </div>

        <div class="reservations-grid-right">
            <section id="week-reservations">
                <h2>Upcoming Reservations</h2>
                
                <?php if (!empty($reservations)) : ?>
                    <div class="reservations-cards">
                        <?php foreach ($reservations as $r): ?>
                            <div class="reservation-card minimal">
                                <div class="card-header">
                                    <div class="facility-name">
                                        <i class="fa-solid fa-building"></i>
                                        <span><?= htmlspecialchars($r["facility_name"]) ?></span>
                                    </div>
                                    <span class="status-badge status-<?= strtolower($r['status']) ?>"><?= htmlspecialchars($r["status"]) ?></span>
                                </div>
                                
                                <div class="card-body minimal-body">
                                    <div class="date-time">
                                        <i class="fa-solid fa-calendar"></i>
                                        <span><?= htmlspecialchars($r["date"]) ?></span>
                                        <span class="separator">|</span>
                                        <i class="fa-solid fa-clock"></i>
                                        <span><?= htmlspecialchars($r["start_time"]) ?></span>
                                    </div>
                                </div>
                                
                                <div class="card-footer">
                                    <a href="admin-reservation?id=<?= urlencode($r['booking_id']) ?>" class="view-link">
                                        <i class="fa-solid fa-eye"></i> View Details
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-data">No upcoming reservations.</p>
                <?php endif; ?>
            </section>
        </div>
    </div>
</div>

<!-- Floating Analytics Button (opens modal with line chart) -->
<button class="floating-insights-btn" id="insights-btn">
    <i class="fa-solid fa-chart-line"></i>
    <span>Quick Stats</span>
</button>

<!-- Analytics Modal -->
<div class="insights-modal" id="insights-modal">
    <div class="insights-modal-content">
        <button class="insights-modal-close" id="modal-close">&times;</button>
        
        <div class="user-stat-container">
            <div class="chart-header">
                <h2>Facility Reservation Analytics</h2>
                <p>Track facility reservation patterns</p>
            </div>

            <div class="controls">
                <div class="filter-group">
                    <button onclick="updateAnalytics('monthly')" 
                        class="filter-btn ajax-filter active" 
                        data-period="monthly">
                        Monthly
                    </button>
                    <button onclick="updateAnalytics('weekly')" 
                        class="filter-btn ajax-filter" 
                        data-period="weekly">
                        Weekly
                    </button>
                    <button onclick="updateAnalytics('annually')" 
                        class="filter-btn ajax-filter" 
                        data-period="annually">
                        Annually
                    </button>
                    
                    <select id="year-selector" class="year-selector" onchange="updateAnalytics(null, this.value)">
                        <?php for ($year = 2020; $year <= date('Y'); $year++): ?>
                            <option value="<?php echo $year; ?>" <?php echo $year == date('Y') ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="chart-container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <strong id="total-reservations-val">-</strong>
                        <p>Total Reservations</p>
                    </div>
                    <div class="stat-card">
                        <strong id="periods-val">-</strong>
                        <p>Periods</p>
                    </div>
                    <div class="stat-card">
                        <strong id="avg-reservations-val">-</strong>
                        <p>Average per Period</p>
                    </div>
                </div>
                
                <div class="chart-wrapper">
                    <div class="chart-title">Facility Reservations - Loading...</div>

                    <!-- Line Chart -->
                    <div id="lineChart" class="chart-display">
                        <div class="line-chart">
                            <svg class="line-svg" viewBox="0 0 800 300">
                                <defs>
                                    <linearGradient id="gradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" style="stop-color:#4b0082;stop-opacity:0.3" />
                                        <stop offset="100%" style="stop-color:#4b0082;stop-opacity:0.05" />
                                    </linearGradient>
                                </defs>
                                
                                <!-- Grid lines -->
                                <line x1="50" y1="50" x2="750" y2="50" stroke="#e9ecef" stroke-width="1" />
                                <line x1="50" y1="100" x2="750" y2="100" stroke="#e9ecef" stroke-width="1" />
                                <line x1="50" y1="150" x2="750" y2="150" stroke="#e9ecef" stroke-width="1" />
                                <line x1="50" y1="200" x2="750" y2="200" stroke="#e9ecef" stroke-width="1" />
                                <line x1="50" y1="250" x2="750" y2="250" stroke="#e9ecef" stroke-width="1" />
                                
                                <!-- Placeholder paths (will be updated by JS) -->
                                <path d="" class="area-path" />
                                <path d="" class="line-path" />
                                
                                <!-- Y-axis labels (will be updated by JS) -->
                                <text x="40" y="55" text-anchor="end" fill="#666" font-size="12" class="y-axis-label">100</text>
                                <text x="40" y="105" text-anchor="end" fill="#666" font-size="12" class="y-axis-label">80</text>
                                <text x="40" y="155" text-anchor="end" fill="#666" font-size="12" class="y-axis-label">60</text>
                                <text x="40" y="205" text-anchor="end" fill="#666" font-size="12" class="y-axis-label">40</text>
                                <text x="40" y="255" text-anchor="end" fill="#666" font-size="12" class="y-axis-label">20</text>
                            </svg>
                        </div>
                    </div>

                    <!-- Mobile Data Table (Alternative to Chart) -->
                    <div class="mobile-only">
                        <div class="data-table-wrapper">
                            <table class="stat-table" id="mobile-stat-table">
                                <thead>
                                    <tr>
                                        <th>Period</th>
                                        <th>Reservations</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Populated by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Search Reservations Script
    const filters = { date: '', location: '', user_type: '' };

    // Store original labels
    document.querySelectorAll('.btn').forEach(btn => {
        btn.setAttribute('data-original', btn.childNodes[0].textContent.trim());
    });

    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('click', e => {
            // If clicked inside a date input, skip toggle
            if (e.target.tagName === 'INPUT' && e.target.type === 'date') {
                return;
            }

            e.stopPropagation();
            document.querySelectorAll('.dropdown').forEach(dd => {
                if (dd.parentElement !== btn) dd.classList.remove('show');
            });
            btn.querySelector('.dropdown').classList.toggle('show');
        });
    });

    // Keep dropdown open if clicking inside
    document.querySelectorAll('.dropdown').forEach(dd => {
        dd.addEventListener('click', e => e.stopPropagation());
    });

    // Debounce function to limit API calls
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Handle dropdown selections
    function handleOptionClick(e) {
        const value = e.target.getAttribute('data-value');
        const filterType = e.target.parentElement.getAttribute('data-filter');
        const btn = e.target.closest('.btn');

        filters[filterType] = value;
        const originalLabel = btn.getAttribute('data-original');
        btn.childNodes[0].textContent = value === '' ? originalLabel : e.target.textContent;

        e.target.closest('.dropdown').classList.remove('show');
        debouncedSearch();
    }

    // Fetch and populate locations
    function loadLocations() {
        fetch('/uoc-sports/public/api/reservation/locations')
            .then(res => res.json())
            .then(data => {
                const dropdown = document.getElementById('location-dropdown');
                data.forEach(loc => {
                    const div = document.createElement('div');
                    div.setAttribute('data-value', loc.facility_id);
                    div.textContent = loc.facility_name;
                    div.addEventListener('click', e => handleOptionClick(e));
                    dropdown.appendChild(div);
                });
            })
            .catch(err => console.error('Error loading locations:', err));
    }

    // Initial binding for non-dynamic options
    document.querySelectorAll('.dropdown div[data-value]').forEach(option => {
        option.addEventListener('click', e => handleOptionClick(e));
    });

    // Handle date selection
    const dateInput = document.getElementById('filter-date');
    if (dateInput) {
        ['click', 'mousedown', 'focus'].forEach(evt =>
            dateInput.addEventListener(evt, e => e.stopPropagation())
        );

        dateInput.addEventListener('change', e => {
            filters.date = e.target.value;
            const btn = e.target.closest('.btn');
            const originalLabel = btn.getAttribute('data-original');
            btn.childNodes[0].textContent = e.target.value || originalLabel;
            debouncedSearch();
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown').forEach(dd => dd.classList.remove('show'));
    });

    // Search when typing in the input (with debounce)
    const debouncedSearch = debounce(performSearch, 300);
    document.getElementById('search-reservation-inp').addEventListener('input', debouncedSearch);

    function performSearch() {
        const query = document.getElementById('search-reservation-inp').value.trim();

        if (query.length === 0 && Object.values(filters).every(f => f === '')) {
            document.querySelector('.search-output').innerHTML = '';
            return;
        }

        const outputDiv = document.querySelector('.search-output');

        outputDiv.innerHTML = '<div class="searching-indicator"><i class="fa-solid fa-spinner fa-spin"></i> Searching...</div>';

        const params = new URLSearchParams({ q: query, ...filters });

        fetch(`/uoc-sports/public/api/reservation/search?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    let html = `
                        <table class="user-table">
                            <colgroup>
                                <col class="col-id">
                                <col class="col-user">
                                <col class="col-date">
                                <col class="col-facility">
                                <col class="col-location">
                                <col class="col-status">
                                <col class="col-action">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Date</th>
                                    <th>Facility</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    data.forEach(r => {
                        const statusClass = `status-${r.status.toLowerCase()}`;
                        const payClass = r.payment_status === "COMPLETE" ? "pay-complete" : "pay-incomplete";
                        const facilityName = r.facility_name;
                        
                        html += `
                            <tr class="${payClass}">
                                <td><strong>${r.booking_id}</strong></td>
                                <td>
                                    <div class="user-info-cell">
                                        <span>${r.user_name}</span>
                                        <small>${r.user_type}</small>
                                    </div>
                                </td>
                                <td>${r.date}</td>
                                <td>
                                    <div class="text-truncate" title="${facilityName}" style="max-width: 100%;">
                                        ${facilityName}
                                    </div>
                                </td>
                                <td><span class="location-tag">${r.physical_location || 'N/A'}</span></td>
                                <td>
                                    <span class="status-badge ${statusClass}">${r.flag_status === 'FLAGGED' ? 'FLAGGED' : r.status.replace('_', ' ')}</span>
                                    ${r.status === 'BOOKED' && r.payment_status === 'INCOMPLETE' ? (function(){
                                        const created = new Date(r.created_at).getTime();
                                        const now = new Date().getTime();
                                        const diff = Math.floor((created + 30 * 60 * 1000 - now) / 1000);
                                        if (diff > 0) {
                                            const mins = Math.floor(diff / 60);
                                            const secs = diff % 60;
                                            return `<br><small style="color:#d32f2f; font-weight:600;"><i class="fas fa-clock"></i> ${mins}:${secs.toString().padStart(2, '0')}</small>`;
                                        }
                                        return '';
                                    })() : ''}
                                </td>
                                <td>
                                    <a href="/uoc-sports/public/admin-reservation?id=${r.booking_id}" class="action-link" title="View Reservation">
                                        <i class="fa-solid fa-circle-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                    html += `</tbody></table>`;
                    outputDiv.innerHTML = html;
                } else {
                    outputDiv.innerHTML = `
                        <div class="no-results">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <p>No reservations matching your criteria were found.</p>
                        </div>
                    `;
                }
            })

            .catch(err => {
                console.error('Search error:', err);
                outputDiv.innerHTML = '<p style="color: #d9534f; padding: 20px; text-align: center;">An error occurred while searching. Please try again.</p>';
            });
    }

</script>

<script>
    // Analytics Modal Script
    let currentPeriod = 'monthly';
    let currentYear = '<?php echo date('Y'); ?>';

    function updateAnalytics(period = null, year = null) {
        if (period) currentPeriod = period;
        if (year) currentYear = year;

        // Update active state of buttons
        document.querySelectorAll('.ajax-filter').forEach(btn => {
            if (btn.getAttribute('data-period') === currentPeriod) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // Toggle year selector
        const yearSelector = document.getElementById('year-selector');
        if (currentPeriod === 'annually') {
            yearSelector.style.display = 'none';
        } else {
            yearSelector.style.display = 'block';
        }

        const params = new URLSearchParams({ period: currentPeriod, year: currentYear });

        fetch(`/uoc-sports/public/api/reservation/stats?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) throw new Error(data.message);

                // Update stats cards
                document.getElementById('total-reservations-val').textContent = data.total_reservations;
                document.getElementById('periods-val').textContent = data.chart_data.length;
                document.getElementById('avg-reservations-val').textContent = data.avg_reservations;

                // Update chart title
                document.querySelector('.chart-title').textContent = `Facility Reservations - ${currentPeriod.charAt(0).toUpperCase() + currentPeriod.slice(1)} View`;

                // Update SVG Chart
                updateSvgChart(data.chart_data, data.max_value);
            })
            .catch(err => {
                console.error('Analytics update error:', err);
            });
    }

    function updateSvgChart(chartData, maxValue) {
        const width = 800;
        const height = 300;
        const padding = 50;
        const chartWidth = width - (padding * 2);
        const chartHeight = height - (padding * 2);
        const dataCount = chartData.length;

        const points = [];
        const areaPoints = [];

        if (dataCount > 1) {
            chartData.forEach((data, index) => {
                const x = padding + (index * (chartWidth / (dataCount - 1)));
                const y = padding + (chartHeight - ((data.res_count / maxValue) * chartHeight));
                points.push(`${x},${y}`);
                
                if (index === 0) areaPoints.push(`${x},${height - padding}`);
                areaPoints.push(`${x},${y}`);
                if (index === dataCount - 1) areaPoints.push(`${x},${height - padding}`);
            });
        } else if (dataCount === 1) {
            const x = width / 2;
            const y = padding + (chartHeight - ((chartData[0].res_count / maxValue) * chartHeight));
            points.push(`${x},${y}`);
            areaPoints.push(`${x},${height - padding}`, `${x},${y}`, `${x},${height - padding}`);
        }

        const pathData = points.length ? `M ${points.join(' L ')}` : "";
        const areaData = areaPoints.length ? `M ${areaPoints.join(' L ')} Z` : "";

        // Update Paths
        const linePath = document.querySelector('.line-path');
        const areaPath = document.querySelector('.area-path');
        
        linePath.setAttribute('d', pathData);
        areaPath.setAttribute('d', areaData);

        // Re-trigger animation
        linePath.style.animation = 'none';
        linePath.offsetHeight; // trigger reflow
        linePath.style.animation = null;

        // Update Data circles
        const svg = document.querySelector('.line-svg');
        // Remove old circles and labels (keep grid and definitions)
        svg.querySelectorAll('.data-point, text').forEach(el => {
            if (!el.classList.contains('y-axis-label')) el.remove(); 
        });

        // Add new Data Points
        chartData.forEach((data, index) => {
            const x = dataCount > 1 ? padding + (index * (chartWidth / (dataCount - 1))) : width / 2;
            const y = padding + (chartHeight - ((data.res_count / maxValue) * chartHeight));

            const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            circle.setAttribute('cx', x);
            circle.setAttribute('cy', y);
            circle.setAttribute('r', '6');
            circle.setAttribute('class', 'data-point');
            const title = document.createElementNS('http://www.w3.org/2000/svg', 'title');
            title.textContent = `${data.period_label}: ${data.res_count} reservations`;
            circle.appendChild(title);
            svg.appendChild(circle);

            // X-axis label
            const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            text.setAttribute('x', x);
            text.setAttribute('y', height - 20);
            text.setAttribute('text-anchor', 'middle');
            text.setAttribute('fill', '#666');
            text.setAttribute('font-size', '12');
            text.textContent = data.period_label.substring(0, 8);
            svg.appendChild(text);
        });

        // Update Y-axis labels
        const yAxisTexts = svg.querySelectorAll('.y-axis-label');
        yAxisTexts.forEach((text, i) => {
            const value = Math.round((maxValue / 5) * (5 - i));
            text.textContent = value;
        });

        // Update Mobile Data Table
        const tableBody = document.querySelector('#mobile-stat-table tbody');
        if (tableBody) {
            tableBody.innerHTML = '';
            chartData.forEach(data => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${data.period_label}</td>
                    <td><strong>${data.res_count}</strong></td>
                `;
                tableBody.appendChild(tr);
            });

            if (chartData.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="2" style="text-align:center;">No data available</td></tr>';
            }
        }
    }

    // Modal toggle for reservation insights
    const insightsBtn = document.getElementById('insights-btn');
    const insightsModal = document.getElementById('insights-modal');
    const modalClose = document.getElementById('modal-close');

    insightsBtn.addEventListener('click', () => {
        insightsModal.classList.add('active');
    });

    modalClose.addEventListener('click', () => {
        insightsModal.classList.remove('active');
    });

    insightsModal.addEventListener('click', (e) => {
        if (e.target === insightsModal) {
            insightsModal.classList.remove('active');
        }
    });

    // Load chart data on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadLocations();
        updateAnalytics();
    });

</script>

<?php require '../app/views/templates/admin/footer.php'; ?>

</body>
<script>
    var currentPage = document.getElementById("sidebar-reservations");
    currentPage.classList.add("active") 
</script>
<script src="/uoc-sports/public/js/search-keyboard-nav.js"></script>
<script>
    SearchKeyboardNav.init({
        inputSelector: '#search-reservation-inp',
        resultsSelector: '.search-output',
        itemSelector: 'tbody tr',
        actionSelector: '.action-link'
    });
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</html>
