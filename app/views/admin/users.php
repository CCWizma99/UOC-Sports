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
    </style>
</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';
?>

<?php

$pdo = Database::getConnection();

function getUserRegistrationData($pdo, $period = 'monthly', $year = null) {
    if (!$year) {
        $year = date('Y');
    }
    
    try {
        switch ($period) {
            case 'weekly':
                $sql = "SELECT 
                    WEEK(joined_date) as period_num,
                    CONCAT('Week ', WEEK(joined_date)) as period_label,
                    COUNT(*) as user_count 
                    FROM user 
                    WHERE YEAR(joined_date) = :year 
                    GROUP BY WEEK(joined_date) 
                    ORDER BY period_num";
                break;
            case 'annually':
                $sql = "SELECT 
                    YEAR(joined_date) as period_num,
                    YEAR(joined_date) as period_label,
                    COUNT(*) as user_count 
                    FROM user 
                    GROUP BY YEAR(joined_date) 
                    ORDER BY period_num";
                break;
            default:
                $sql = "SELECT 
                    MONTH(joined_date) as period_num,
                    MONTHNAME(joined_date) as period_label,
                    COUNT(*) as user_count 
                    FROM user 
                    WHERE YEAR(joined_date) = :year 
                    GROUP BY MONTH(joined_date), MONTHNAME(joined_date) 
                    ORDER BY period_num";
        }
        $stmt = $pdo->prepare($sql);
        if ($period !== 'annually') {
            $stmt->bindParam(':year', $year);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return null;
    }
}

$current_year = date('Y');
$current_period = $_GET['period'] ?? 'monthly';
$selected_year = $_GET['year'] ?? $current_year;

// Fetch data from backend
$chart_data = getUserRegistrationData($pdo, $current_period, $selected_year) ?? [];

// Calculate max value for chart scaling
$max_value = !empty($chart_data) ? max(array_column($chart_data, 'user_count')) : 100;
$total_users = !empty($chart_data) ? array_sum(array_column($chart_data, 'user_count')) : 0;
$avg_users = !empty($chart_data) ? round($total_users / count($chart_data), 1) : 0;
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
                            <div data-value="Science">Science</div>
                            <div data-value="Arts">Arts</div>
                            <div data-value="Medicine">Medicine</div>
                        </div>
                    </div>

                    <div class="btn" id="year-btn">
                        Year
                        <div class="dropdown" data-filter="year">
                            <div data-value="">All</div>
                            <div data-value="1">1</div>
                            <div data-value="2">2</div>
                            <div data-value="3">3</div>
                            <div data-value="4">4</div>
                        </div>
                    </div>

                    <div class="btn" id="sport-btn">
                        Sport
                        <div class="dropdown" data-filter="sport">
                            <div data-value="">All</div>
                            <div data-value="Cricket">Cricket</div>
                            <div data-value="Football">Football</div>
                            <div data-value="Rowing">Rowing</div>
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

                <div class="search-output"></div>
            </section>
        </div>

        <div class="users-grid-right">
            <section id="add-user">
                <h2>Add a User</h2>
                <div class="add-user-form-content">
                    <div class="name-div">
                        <div class="input-field">
                            <label for="user-fname">First Name</label>
                            <input type="text" name="fname" id="user-fname">
                        </div>
                        <div class="input-field">
                            <label for="user-lname">Last Name</label>
                            <input type="text" name="lname" id="user-lname">
                        </div>
                    </div>

                    <div class="input-field">
                        <label for="user-email">Email</label>
                        <input type="email" name="email" id="user-email">
                    </div>

                    <div class="input-field">
                        <label for="user-phone">Phone Number</label>
                        <input type="tel" name="phone" id="user-phone" placeholder="+94XXXXXXXXX">
                    </div>

                    <div class="input-field">
                        <label for="user-type">User Type</label>
                        <select name="type" id="user-type">
                            <option value="">Select User Type</option>
                            <option value="SPT">Sport Manager</option>
                            <option value="EQP">Equipment Manager</option>
                            <option value="REG">Registrar</option>
                        </select>
                    </div>

                    <!-- Dynamic fields container -->
                    <div id="extra-fields"></div>

                    <a href="#" class="add-user-btn" id="submit-user">Add User</a>
                </div>
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
                    <a href="?period=monthly&year=<?php echo $selected_year; ?>" 
                       class="filter-btn <?php echo $current_period === 'monthly' ? 'active' : ''; ?>">
                        Monthly
                    </a>
                    <a href="?period=weekly&year=<?php echo $selected_year; ?>" 
                       class="filter-btn <?php echo $current_period === 'weekly' ? 'active' : ''; ?>">
                        Weekly
                    </a>
                    <a href="?period=annually" 
                       class="filter-btn <?php echo $current_period === 'annually' ? 'active' : ''; ?>">
                        Annually
                    </a>
                    
                    <?php if ($current_period !== 'annually'): ?>
                    <select class="year-selector" onchange="changeYear(this.value)">
                        <?php for ($year = 2020; $year <= date('Y'); $year++): ?>
                            <option value="<?php echo $year; ?>" <?php echo $year == $selected_year ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <?php endif; ?>
                </div>
            </div>

            <div class="chart-container">
                <?php if (!empty($chart_data)): ?>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <strong><?php echo $total_users; ?></strong>
                            <p>Total Users</p>
                        </div>
                        <div class="stat-card">
                            <strong><?php echo count($chart_data); ?></strong>
                            <p>Periods</p>
                        </div>
                        <div class="stat-card">
                            <strong><?php echo $avg_users; ?></strong>
                            <p>Average per Period</p>
                        </div>
                    </div>
                    
                    <div class="chart-wrapper">
                        <div class="chart-title">User Registrations - <?php echo ucfirst($current_period); ?> View</div>

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
                                    
                                    <?php
                                    $points = [];
                                    $areaPoints = [];
                                    $width = 800;
                                    $height = 300;
                                    $padding = 50;
                                    $chartWidth = $width - ($padding * 2);
                                    $chartHeight = $height - ($padding * 2);
                                    $dataCount = count($chart_data);
                                    
                                    if ($dataCount > 1) {
                                        foreach ($chart_data as $index => $data) {
                                            $x = $padding + ($index * ($chartWidth / ($dataCount - 1)));
                                            $y = $padding + ($chartHeight - (($data['user_count'] / $max_value) * $chartHeight));
                                            $points[] = "$x,$y";
                                            
                                            if ($index === 0) {
                                                $areaPoints[] = "$x," . ($height - $padding);
                                            }
                                            $areaPoints[] = "$x,$y";
                                            if ($index === $dataCount - 1) {
                                                $areaPoints[] = "$x," . ($height - $padding);
                                            }
                                        }
                                    } elseif ($dataCount === 1) {
                                        // Single data point - center it
                                        $x = $width / 2;
                                        $y = $padding + ($chartHeight - (($chart_data[0]['user_count'] / $max_value) * $chartHeight));
                                        $points[] = "$x,$y";
                                        $areaPoints[] = "$x," . ($height - $padding);
                                        $areaPoints[] = "$x,$y";
                                        $areaPoints[] = "$x," . ($height - $padding);
                                    }
                                    
                                    $pathData = !empty($points) ? "M " . implode(" L ", $points) : "";
                                    $areaData = !empty($areaPoints) ? "M " . implode(" L ", $areaPoints) . " Z" : "";
                                    ?>
                                    
                                    <!-- Grid lines -->
                                    <?php for ($i = 0; $i <= 5; $i++): ?>
                                        <line x1="<?php echo $padding; ?>" 
                                              y1="<?php echo $padding + ($i * $chartHeight / 5); ?>" 
                                              x2="<?php echo $width - $padding; ?>" 
                                              y2="<?php echo $padding + ($i * $chartHeight / 5); ?>" 
                                              stroke="#e9ecef" stroke-width="1" />
                                    <?php endfor; ?>
                                    
                                    <!-- Area -->
                                    <path d="<?php echo $areaData; ?>" class="area-path" />
                                    
                                    <!-- Line -->
                                    <path d="<?php echo $pathData; ?>" class="line-path" />
                                    
                                    <!-- Data points -->
                                    <?php 
                                    if ($dataCount > 1) {
                                        foreach ($chart_data as $index => $data): 
                                            $x = $padding + ($index * ($chartWidth / ($dataCount - 1)));
                                            $y = $padding + ($chartHeight - (($data['user_count'] / $max_value) * $chartHeight));
                                    ?>
                                        <circle cx="<?php echo $x; ?>" cy="<?php echo $y; ?>" r="6" class="data-point">
                                            <title><?php echo $data['period_label'] . ': ' . $data['user_count']; ?> users</title>
                                        </circle>
                                    <?php 
                                        endforeach;
                                    } elseif ($dataCount === 1) {
                                        $x = $width / 2;
                                        $y = $padding + ($chartHeight - (($chart_data[0]['user_count'] / $max_value) * $chartHeight));
                                    ?>
                                        <circle cx="<?php echo $x; ?>" cy="<?php echo $y; ?>" r="6" class="data-point">
                                            <title><?php echo $chart_data[0]['period_label'] . ': ' . $chart_data[0]['user_count']; ?> users</title>
                                        </circle>
                                    <?php } ?>
                                    
                                    <!-- X-axis labels -->
                                    <?php 
                                    if ($dataCount > 1) {
                                        foreach ($chart_data as $index => $data): 
                                            $x = $padding + ($index * ($chartWidth / ($dataCount - 1)));
                                    ?>
                                        <text x="<?php echo $x; ?>" y="<?php echo $height - 20; ?>" 
                                              text-anchor="middle" fill="#666" font-size="12">
                                            <?php echo substr($data['period_label'], 0, 8); ?>
                                        </text>
                                    <?php 
                                        endforeach;
                                    } elseif ($dataCount === 1) {
                                        $x = $width / 2;
                                    ?>
                                        <text x="<?php echo $x; ?>" y="<?php echo $height - 20; ?>" 
                                              text-anchor="middle" fill="#666" font-size="12">
                                            <?php echo substr($chart_data[0]['period_label'], 0, 8); ?>
                                        </text>
                                    <?php } ?>
                                    
                                    <!-- Y-axis labels -->
                                    <?php for ($i = 0; $i <= 5; $i++): 
                                        $value = round(($max_value / 5) * (5 - $i));
                                        $y = $padding + ($i * $chartHeight / 5);
                                    ?>
                                        <text x="<?php echo $padding - 10; ?>" y="<?php echo $y + 5; ?>" 
                                              text-anchor="end" fill="#666" font-size="12">
                                            <?php echo $value; ?>
                                        </text>
                                    <?php endfor; ?>
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
                                <tbody>
                                    <?php foreach ($chart_data as $data): ?>
                                        <tr>
                                            <td><?php echo $data['period_label']; ?></td>
                                            <td><strong><?php echo $data['user_count']; ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="loading">
                        <strong>No data available</strong>
                        <p>No user registration data found for the selected period.</p>
                    </div>
                <?php endif; ?>
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
                                <a href="./user.php?id=${user.user_id}" class="action-link" title="View User">
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
function changeYear(year) {
    const period = '<?php echo $current_period; ?>';
    window.location.href = `?period=${period}&year=${year}`;
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
</script>


<?php

$sportModel = new Sport();
$sports = $sportModel->getSports();

?>

<script>
document.getElementById('user-type').addEventListener('change', function () {
    const extraFields = document.getElementById('extra-fields');
    extraFields.innerHTML = '';

    if (this.value === 'SPT') {
        // Encode PHP array into JS
        const sports = <?php echo json_encode($sports ?? []); ?>;

        let options = `<option value="">Select Sport</option>`;
        sports.forEach(sport => {
            options += `<option value="${sport.sport_id}">${sport.sport_name}</option>`;
        });

        extraFields.innerHTML = `
            <div class="input-field">
                <label for="user-sport">Select Sport</label>
                <select id="user-sport" name="sport_name">
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
</script>


<?php require '../app/views/templates/admin/footer.php'; ?>

</body>
<script>
    var currentPage = document.getElementById("sidebar-users");
    currentPage.classList.add("active") 
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</html>

