<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);

        @import url(/uoc-sports/public/css/admin/users-page.css);
        @import url(/uoc-sports/public/css/admin/search-user.css);
        @import url(/uoc-sports/public/css/admin/add-user.css);
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
<?php
// Chart data will be loaded via AJAX on page load
$current_period = $_GET['period'] ?? 'monthly';
$selected_year = $_GET['year'] ?? date('Y');
?>

<div class="main-content-wrapper">
    <div class="users-grid-container">
        <div class="users-grid-left">
            <section id="search-user">
                <h2>Search Users</h2>
                <div class="filter-bar">
                    <h3>Filter <i class="fa-solid fa-filter"></i></h3>

                    <div class="btn" id="faculty-btn">
                        Faculty
                        <div class="dropdown" data-filter="faculty">
                            <div data-value="">All</div>
                            <?php foreach ($faculty_data as $faculty): ?>
                                <div data-value="<?= htmlspecialchars($faculty['faculty_name']) ?>">
                                    <?= htmlspecialchars($faculty['faculty_name']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="btn" id="sport-btn">
                        Sport
                        <div class="dropdown" data-filter="sport">
                            <div data-value="">All</div>
                            <?php foreach ($sport_data as $sport): ?>
                                <div data-value="<?= htmlspecialchars($sport['sport_name']) ?>">
                                    <?= htmlspecialchars($sport['sport_name']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="btn" id="public-btn">
                        Type
                        <div class="dropdown" data-filter="type">
                            <div data-value="">All</div>
                            <div data-value="Student">Student</div>
                            <div data-value="Staff">Staff</div>
                        </div>
                    </div>
                </div>

                <input type="text" name="search-user-inp" id="search-user-inp" 
                    title="Enter user ID No. or Name" placeholder="Enter User ID or Name">

                <div class="search-output">
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h3>Search for Users</h3>
                        <p>Enter a user ID or name above, or use the filters to find users</p>
                    </div>
                </div>
            </section>
        </div>

        <div class="users-grid-right">
            <section id="add-user">
                <h2>Add a User</h2>
                <form id="add-user-form" class="add-user-form-content" novalidate>
                    <p class="required-note"><span>*</span> Required fields</p>
                    <div class="name-div">
                        <div class="input-field">
                            <label for="user-fname">First Name <span class="required">*</span></label>
                            <input type="text" name="fname" id="user-fname" 
                                   autocomplete="given-name" 
                                   aria-required="true" 
                                   required>
                        </div>
                        <div class="input-field">
                            <label for="user-lname">Last Name</label>
                            <input type="text" name="lname" id="user-lname" 
                                   autocomplete="family-name">
                        </div>
                    </div>

                    <div class="input-field">
                        <label for="user-email">Email <span class="required">*</span></label>
                        <input type="email" name="email" id="user-email" 
                               autocomplete="email"
                               aria-required="true"
                               required>
                    </div>

                    <div class="input-field">
                        <label for="user-phone">Phone Number <span class="required">*</span></label>
                        <input type="tel" name="phone" id="user-phone" 
                               placeholder="+94XXXXXXXXX"
                               pattern="^\+94[0-9]{9}$"
                               inputmode="tel"
                               autocomplete="tel"
                               aria-required="true"
                               required>
                    </div>

                    <div class="input-field">
                        <label for="user-type">User Type <span class="required">*</span></label>
                        <select name="type" id="user-type" 
                                aria-required="true"
                                required>
                            <option value="">Select User Type</option>
                            <option value="COACH">Coach</option>
                            <option value="SPT">Sport Manager</option>
                            <option value="EQP">Equipment Manager</option>
                            <option value="REG">Registrar</option>
                        </select>
                    </div>

                    <!-- Dynamic fields container -->
                    <div id="extra-fields"></div>

                    <button type="button" class="add-user-btn" id="add-user-submit-btn">Add User</button>
                </form>
            </section>
        </div>
    </div>
</div>

<!-- Floating Insights Button -->
<button class="floating-insights-btn" id="insights-btn">
    <i class="fa-solid fa-chart-line"></i>
    <span>User Insights</span>
</button>

<!-- Insights Modal -->
<div class="insights-modal" id="insights-modal">
    <div class="insights-modal-content">
        <button class="insights-modal-close" id="modal-close">&times;</button>
        
        <div class="user-stat-container">
            <div class="chart-header">
                <h2>User Registration Analytics</h2>
                <p>Track user growth and registration patterns</p>
            </div>

            <div class="controls">
                <div class="filter-group">
                    <button onclick="updateAnalytics('monthly')" 
                        class="filter-btn ajax-filter <?php echo $current_period === 'monthly' ? 'active' : ''; ?>" 
                        data-period="monthly">
                        Monthly
                    </button>
                    <button onclick="updateAnalytics('weekly')" 
                        class="filter-btn ajax-filter <?php echo $current_period === 'weekly' ? 'active' : ''; ?>" 
                        data-period="weekly">
                        Weekly
                    </button>
                    <button onclick="updateAnalytics('annually')" 
                        class="filter-btn ajax-filter <?php echo $current_period === 'annually' ? 'active' : ''; ?>" 
                        data-period="annually">
                        Annually
                    </button>
                    
                    <select id="year-selector" class="year-selector" onchange="updateAnalytics(null, this.value)" 
                        style="<?php echo $current_period === 'annually' ? 'display:none;' : ''; ?>">
                        <?php for ($year = 2020; $year <= date('Y'); $year++): ?>
                            <option value="<?php echo $year; ?>" <?php echo $year == $selected_year ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="chart-container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <strong id="total-users-val">-</strong>
                        <p>Total Users</p>
                    </div>
                    <div class="stat-card">
                        <strong id="periods-val">-</strong>
                        <p>Periods</p>
                    </div>
                    <div class="stat-card">
                        <strong id="avg-users-val">-</strong>
                        <p>Average per Period</p>
                    </div>
                </div>
                
                <div class="chart-wrapper">
                    <div class="chart-title">User Registrations - Loading...</div>

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

                    <!-- Data Table (Mobile Alternative) -->
                    <div class="data-table-wrapper mobile-only">
                        <table class="stat-table">
                            <thead>
                                <tr>
                                    <th>Period</th>
                                    <th>Registered Users</th>
                                </tr>
                            </thead>
                            <tbody id="stat-table-body">
                                <tr>
                                    <td colspan="2" style="text-align:center;">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
  const filters = { faculty: '', year: '', sport: '', type: '' };

// Store original button labels for reset
document.querySelectorAll('.btn').forEach(btn => {
    btn.setAttribute('data-original', btn.childNodes[0].textContent.trim());
});

// Toggle dropdown visibility
document.querySelectorAll('.btn').forEach(btn => {
    btn.addEventListener('click', e => {
        e.stopPropagation();
        document.querySelectorAll('.dropdown').forEach(dd => {
            if (dd.parentElement !== btn) dd.classList.remove('show');
        });
        btn.querySelector('.dropdown').classList.toggle('show');
    });
});

// Select filter value
document.querySelectorAll('.dropdown div').forEach(option => {
    option.addEventListener('click', e => {
        const value = e.target.getAttribute('data-value');
        const filterType = e.target.parentElement.getAttribute('data-filter');
        const btn = e.target.closest('.btn');

        filters[filterType] = value;

        const labelNode = btn.childNodes[0]; 
        const originalLabel = btn.getAttribute('data-original');

        if (value === '') {
            labelNode.textContent = originalLabel;
        } else {
            labelNode.textContent = value;
        }

        e.target.closest('.dropdown').classList.remove('show');
        performSearch();
    });
});

// Close dropdowns when clicking outside
document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown').forEach(dd => dd.classList.remove('show'));
});

// Search on typing
document.getElementById('search-user-inp').addEventListener('input', performSearch);

function performSearch() {
    const query = document.getElementById('search-user-inp').value.trim();
    if (query.length === 0 && Object.values(filters).every(f => f === '')) {
        document.querySelector('.search-output').innerHTML = '';
        return;
    }

    const params = new URLSearchParams({ q: query, ...filters });

    fetch(`/uoc-sports/public/api/search-user.php?${params.toString()}`)
        .then(res => res.json())
        .then(data => {
            const outputDiv = document.querySelector('.search-output');
            if (data.length > 0) {
                let html = `
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                data.forEach(user => {
                    html += `
                        <tr>
                            <td>${user.user_id}</td>
                            <td>${user.fname} ${user.lname}</td>
                            <td>${user.type}</td>
                            <td>
                                <a href="./admin-user-profile?id=${user.user_id}" class="action-link" title="View User">
                                    <i class="fa-solid fa-circle-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                });
                html += `</tbody></table>`;
                outputDiv.innerHTML = html;
            } else {
                outputDiv.innerHTML = '<p>No users found.</p>';
            }
        })
        .catch(err => {
            console.error('Search error:', err);
            document.querySelector('.search-output').innerHTML = '<p>Error occurred.</p>';
        });
}
</script>

<script>
let currentPeriod = '<?php echo $current_period; ?>';
let currentYear = '<?php echo $selected_year; ?>';

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

    fetch(`/uoc-sports/public/api/user/registration-stats?${params.toString()}`)
        .then(res => res.json())
        .then(data => {
            if (data.error) throw new Error(data.message);

            // Update stats cards
            document.getElementById('total-users-val').textContent = data.total_users;
            document.getElementById('periods-val').textContent = data.chart_data.length;
            document.getElementById('avg-users-val').textContent = data.avg_users;

            // Update chart title
            document.querySelector('.chart-title').textContent = `User Registrations - ${currentPeriod.charAt(0).toUpperCase() + currentPeriod.slice(1)} View`;

            // Update SVG Chart
            updateSvgChart(data.chart_data, data.max_value);

            // Update Data Table
            const tbody = document.getElementById('stat-table-body');
            tbody.innerHTML = data.chart_data.map(d => `
                <tr>
                    <td>${d.period_label}</td>
                    <td><strong>${d.user_count}</strong></td>
                </tr>
            `).join('');
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
            const y = padding + (chartHeight - ((data.user_count / maxValue) * chartHeight));
            points.push(`${x},${y}`);
            
            if (index === 0) areaPoints.push(`${x},${height - padding}`);
            areaPoints.push(`${x},${y}`);
            if (index === dataCount - 1) areaPoints.push(`${x},${height - padding}`);
        });
    } else if (dataCount === 1) {
        const x = width / 2;
        const y = padding + (chartHeight - ((chartData[0].user_count / maxValue) * chartHeight));
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
        const y = padding + (chartHeight - ((data.user_count / maxValue) * chartHeight));

        const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        circle.setAttribute('cx', x);
        circle.setAttribute('cy', y);
        circle.setAttribute('r', '6');
        circle.setAttribute('class', 'data-point');
        const title = document.createElementNS('http://www.w3.org/2000/svg', 'title');
        title.textContent = `${data.period_label}: ${data.user_count} users`;
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
}

// Modal toggle for user insights
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
    updateAnalytics();
});
</script>


<?php
// Note: $sport_data is already provided by the controller
$sports = $sport_data ?? []; 
?>

<script>
document.getElementById('user-type').addEventListener('change', function () {
    const extraFields = document.getElementById('extra-fields');
    extraFields.innerHTML = '';

    if (this.value === 'SPT' || this.value === 'COACH') {
        // Encode PHP array into JS
        const sports = <?php echo json_encode($sports ?? []); ?>;
        const label = this.value === 'COACH' ? 'Select Sport to Coach' : 'Select Sport';

        let options = `<option value="">Select Sport</option>`;
        sports.forEach(sport => {
            options += `<option value="${sport.sport_id}">${sport.sport_name}</option>`;
        });

        extraFields.innerHTML = `
            <div class="input-field">
                <label for="user-sport">${label}</label>
                <select id="user-sport" name="sport_id" required>
                    ${options}
                </select>
            </div>
        `;
    } 
    else if (this.value === 'REG') {
        extraFields.innerHTML = `
            <div class="input-field">
                <label for="user-faculty">Select Faculty</label>
                <select id="user-faculty" name="faculty">
                    <option value="">Select Faculty</option>
                    <option value="Science">Science</option>
                    <option value="Arts">Arts</option>
                    <option value="Management">Management</option>
                    <option value="Computing">Computing</option>
                </select>
            </div>
        `;
    }
});

// Handle Add User Form Submission via Fetch API (using button click, not form submit)
document.getElementById('add-user-submit-btn').addEventListener('click', async function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const form = document.getElementById('add-user-form');
    const submitBtn = this;
    const originalBtnText = submitBtn.textContent;
    
    // Disable button and show loading state
    submitBtn.disabled = true;
    submitBtn.textContent = 'Adding User...';
    
    // Gather form data
    const formData = {
        fname: document.getElementById('user-fname').value.trim(),
        lname: document.getElementById('user-lname').value.trim(),
        email: document.getElementById('user-email').value.trim(),
        phone: document.getElementById('user-phone').value.trim(),
        type: document.getElementById('user-type').value
    };
    
    // Add optional fields if present
    const sportField = document.getElementById('user-sport');
    const facultyField = document.getElementById('user-faculty');
    
    if (sportField) {
        formData.sport = sportField.value;
    }
    if (facultyField) {
        formData.faculty = facultyField.value;
    }
    
    // Validate required fields
    if (!formData.fname || !formData.email || !formData.type) {
        showNotification('Please fill in all required fields.', 'error');
        submitBtn.disabled = false;
        submitBtn.textContent = originalBtnText;
        return;
    }
    
    try {
        const response = await fetch('/uoc-sports/public/admin-users/add-internal-user', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            showNotification(result.message || 'User added successfully! Temporary password sent via email.', 'success');
            form.reset();
            document.getElementById('extra-fields').innerHTML = '';
        } else {
            showNotification(result.message || 'Failed to add user.', 'error');
        }
    } catch (error) {
        console.error('Add user error:', error);
        showNotification('An error occurred while adding the user.', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalBtnText;
    }
});

// Notification helper (uses admin notification system if available, falls back to alert)
function showNotification(message, type = 'info') {
    if (typeof window.showAdminNotification === 'function') {
        window.showAdminNotification(message, type);
    } else {
        // Fallback: create a simple toast notification
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <span class="toast-icon">${type === 'success' ? '✓' : type === 'error' ? '✕' : 'ℹ'}</span>
            <span class="toast-message">${message}</span>
        `;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease;
            background: ${type === 'success' ? 'linear-gradient(135deg, #28a745, #20c997)' : 
                        type === 'error' ? 'linear-gradient(135deg, #dc3545, #ff6b6b)' : 
                        'linear-gradient(135deg, #6c5ce7, #a29bfe)'};
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }
}
</script>


<?php require '../app/views/templates/admin/footer.php'; ?>

</body>
<script>
    var currentPage = document.getElementById("sidebar-users");
    currentPage.classList.add("active") 
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</html>

