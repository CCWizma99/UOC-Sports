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
                </div>
            <?php else: ?>
                <div class="loading">
                    <strong>No data available</strong>
                    <p>No user registration data found for the selected period.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>