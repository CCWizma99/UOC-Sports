<?php
/**
 * Executive Dashboard - Budget Trends Drill-down View
 * Shows budget analysis by sport with date range filtering
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Trends Analysis - Executive Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #4b0082 0%, #1a1a2e 100%);
            color: white;
            padding: 30px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 { font-size: 28px; margin-bottom: 8px; }
        .header-back {
            background: rgba(255,255,255,0.2);
            border: 1px solid white;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .header-back:hover { background: rgba(255,255,255,0.3); }
        
        .controls {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: flex-end;
        }
        .control-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .control-group label {
            font-weight: bold;
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        .control-group input, .control-group select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .control-group button {
            background: #4b0082;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .control-group button:hover { background: #6a0dad; }
        
        .content {
            padding: 30px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #4b0082;
            margin-bottom: 15px;
            border-bottom: 2px solid #4b0082;
            padding-bottom: 10px;
        }
        
        .chart-container {
            position: relative;
            height: 400px;
            margin-top: 20px;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 4px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th {
            background: #e8e4f3;
            color: #1a1a2e;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #4b0082;
        }
        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        table tr:hover { background: #f9f9f9; }
        
        .budget-efficiency {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .efficiency-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 4px;
            padding: 15px;
        }
        .efficiency-card h3 { color: #4b0082; margin-bottom: 10px; }
        .efficiency-card p { margin: 5px 0; font-size: 14px; }
        
        .loading { text-align: center; padding: 40px; color: #666; }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Budget Trends Analysis</h1>
                <p style="opacity: 0.9;">Compare budget allocation and spending across sports</p>
            </div>
            <a href="/uoc-sports/public/executive-dashboard" class="header-back">← Back to Dashboard</a>
        </div>
        
        <div class="controls">
            <div class="control-group" style="flex: 1; min-width: 150px;">
                <label>Start Date</label>
                <input type="date" id="start-date" value="<?php echo $startDate; ?>">
            </div>
            <div class="control-group" style="flex: 1; min-width: 150px;">
                <label>End Date</label>
                <input type="date" id="end-date" value="<?php echo $endDate; ?>">
            </div>
            <div class="control-group">
                <button onclick="loadBudgetData()">Apply Filters</button>
            </div>
        </div>
        
        <div class="content">
            <div id="error-container"></div>
            <div id="data-container" class="loading">Loading budget data...</div>
        </div>
    </div>

    <script>
        const API_BASE = '/uoc-sports/public';
        let budgetChart = null;
        let monthlyChart = null;
        
        function loadBudgetData() {
            const startDate = document.getElementById('start-date').value;
            const endDate = document.getElementById('end-date').value;
            
            let url = `${API_BASE}/api/drill-down/budget-trends?start_date=${startDate}&end_date=${endDate}`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderBudgetData(data.data);
                    } else {
                        showError(data.message || 'Failed to load data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Error loading data: ' + error.message);
                });
        }
        
        function renderBudgetData(data) {
            const container = document.getElementById('data-container');
            
            let html = `
                <div class="section">
                    <div class="section-title">Budget by Sport</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Sport</th>
                                <th>Allocated Budget</th>
                                <th>Spent Amount</th>
                                <th>Remaining</th>
                                <th>Utilization %</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            let totalAllocated = 0, totalSpent = 0, totalRemaining = 0;
            
            if (data.by_sport && data.by_sport.length > 0) {
                data.by_sport.forEach(sport => {
                    const allocated = parseFloat(sport.allocated_budget) || 0;
                    const spent = parseFloat(sport.spent_amount) || 0;
                    const remaining = parseFloat(sport.remaining) || 0;
                    const utilization = allocated > 0 ? ((spent / allocated) * 100).toFixed(1) : 0;
                    
                    totalAllocated += allocated;
                    totalSpent += spent;
                    totalRemaining += remaining;
                    
                    html += `
                        <tr>
                            <td><strong>${sport.sport_name}</strong></td>
                            <td>Rs. ${allocated.toFixed(2)}</td>
                            <td>Rs. ${spent.toFixed(2)}</td>
                            <td>Rs. ${remaining.toFixed(2)}</td>
                            <td>${utilization}%</td>
                        </tr>
                    `;
                });
            }
            
            html += `
                        <tr style="background: #e8e4f3; font-weight: bold;">
                            <td>TOTAL</td>
                            <td>Rs. ${totalAllocated.toFixed(2)}</td>
                            <td>Rs. ${totalSpent.toFixed(2)}</td>
                            <td>Rs. ${totalRemaining.toFixed(2)}</td>
                            <td>${totalAllocated > 0 ? ((totalSpent / totalAllocated) * 100).toFixed(1) : 0}%</td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            `;
            
            // Monthly Trend Chart
            if (data.monthly_trend && data.monthly_trend.length > 0) {
                html += `
                    <div class="section">
                        <div class="section-title">Monthly Spending Trend</div>
                        <div class="chart-container">
                            <canvas id="monthly-chart"></canvas>
                        </div>
                    </div>
                `;
            }
            
            container.innerHTML = html;
            
            // Render monthly trend chart if data available
            if (data.monthly_trend && data.monthly_trend.length > 0) {
                renderMonthlyChart(data.monthly_trend);
            }
        }
        
        function renderMonthlyChart(monthlyData) {
            const ctx = document.getElementById('monthly-chart').getContext('2d');
            
            // Group data by month
            const monthlyByMonth = {};
            monthlyData.forEach(item => {
                if (!monthlyByMonth[item.month]) {
                    monthlyByMonth[item.month] = 0;
                }
                monthlyByMonth[item.month] += parseFloat(item.monthly_expense) || 0;
            });
            
            const months = Object.keys(monthlyByMonth).sort();
            const expenses = months.map(m => monthlyByMonth[m]);
            
            if (monthlyChart) {
                monthlyChart.destroy();
            }
            
            monthlyChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Monthly Spending',
                        data: expenses,
                        borderColor: '#4b0082',
                        backgroundColor: 'rgba(75, 0, 130, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#4b0082'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rs. ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
        
        function showError(message) {
            const errorContainer = document.getElementById('error-container');
            errorContainer.innerHTML = `<div class="error">${message}</div>`;
            document.getElementById('data-container').innerHTML = '';
        }
        
        // Load data on page load
        document.addEventListener('DOMContentLoaded', loadBudgetData);
    </script>
</body>
</html>
