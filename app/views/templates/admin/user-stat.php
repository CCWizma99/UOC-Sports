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

$connection_error = 1;

if (isset($connection_error)) {
    // Sample data for demonstration
    $sample_data = [
        'monthly' => [
            ['period_label' => 'January', 'user_count' => 45],
            ['period_label' => 'February', 'user_count' => 52],
            ['period_label' => 'March', 'user_count' => 38],
            ['period_label' => 'April', 'user_count' => 67],
            ['period_label' => 'May', 'user_count' => 71],
            ['period_label' => 'June', 'user_count' => 58],
            ['period_label' => 'July', 'user_count' => 82],
            ['period_label' => 'August', 'user_count' => 76]
        ],
        'weekly' => [
            ['period_label' => 'Week 1', 'user_count' => 12],
            ['period_label' => 'Week 2', 'user_count' => 18],
            ['period_label' => 'Week 3', 'user_count' => 15],
            ['period_label' => 'Week 4', 'user_count' => 22],
            ['period_label' => 'Week 5', 'user_count' => 19],
            ['period_label' => 'Week 6', 'user_count' => 25],
            ['period_label' => 'Week 7', 'user_count' => 16],
            ['period_label' => 'Week 8', 'user_count' => 21]
        ],
        'annually' => [
            ['period_label' => '2021', 'user_count' => 324],
            ['period_label' => '2022', 'user_count' => 567],
            ['period_label' => '2023', 'user_count' => 689],
            ['period_label' => '2024', 'user_count' => 489]
        ]
    ];
    $chart_data = $sample_data[$current_period];
} else {
    $chart_data = getUserRegistrationData($pdo, $current_period, $selected_year) ?? [];
}

// Calculate max value for chart scaling
$max_value = !empty($chart_data) ? max(array_column($chart_data, 'user_count')) : 100;
$total_users = !empty($chart_data) ? array_sum(array_column($chart_data, 'user_count')) : 0;
$avg_users = !empty($chart_data) ? round($total_users / count($chart_data), 1) : 0;
?>

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
                    
                    <div class="chart-type-selector">
                        <button class="chart-type-btn active" onclick="showChart('bar')">Bar Chart</button>
                        <button class="chart-type-btn" onclick="showChart('line')">Line Chart</button>
                    </div>

                    <div id="barChart" class="chart-display">
                        <div class="bar-chart">
                            <?php foreach ($chart_data as $data): ?>
                                <div class="bar" 
                                     style="height: <?php echo ($data['user_count'] / $max_value) * 100; ?>%; animation-delay: <?php echo array_search($data, $chart_data) * 0.1; ?>s;"
                                     data-value="<?php echo $data['user_count']; ?> users"
                                     data-label="<?php echo $data['period_label']; ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="chart-labels">
                            <?php foreach ($chart_data as $data): ?>
                                <div class="chart-label"><?php echo $data['period_label']; ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Line Chart -->
                    <div id="lineChart" class="chart-display" style="display: none;">
                        <div class="line-chart">
                            <svg class="line-svg" viewBox="0 0 800 300">
                                <defs>
                                    <linearGradient id="gradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" style="stop-color:#5e2d91;stop-opacity:0.3" />
                                        <stop offset="100%" style="stop-color:#5e2d91;stop-opacity:0.05" />
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
                                
                                $pathData = "M " . implode(" L ", $points);
                                $areaData = "M " . implode(" L ", $areaPoints) . " Z";
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
                                <?php foreach ($chart_data as $index => $data): 
                                    $x = $padding + ($index * ($chartWidth / ($dataCount - 1)));
                                    $y = $padding + ($chartHeight - (($data['user_count'] / $max_value) * $chartHeight));
                                ?>
                                    <circle cx="<?php echo $x; ?>" cy="<?php echo $y; ?>" r="6" class="data-point">
                                        <title><?php echo $data['period_label'] . ': ' . $data['user_count']; ?> users</title>
                                    </circle>
                                <?php endforeach; ?>
                                
                                <!-- X-axis labels -->
                                <?php foreach ($chart_data as $index => $data): 
                                    $x = $padding + ($index * ($chartWidth / ($dataCount - 1)));
                                ?>
                                    <text x="<?php echo $x; ?>" y="<?php echo $height - 20; ?>" 
                                          text-anchor="middle" fill="#666" font-size="12">
                                        <?php echo substr($data['period_label'], 0, 8); ?>
                                    </text>
                                <?php endforeach; ?>
                                
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
                </div>
            <?php else: ?>
                <div class="loading">
                    <strong>No data available</strong>
                    <p>No user registration data found for the selected period.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>