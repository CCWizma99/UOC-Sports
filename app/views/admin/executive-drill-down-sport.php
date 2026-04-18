<?php
/**
 * Executive Dashboard - Sport Performance Drill-down View
 * Shows detailed performance metrics for a specific sport
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sport Performance Analysis - Executive Dashboard</title>
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
        
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .kpi-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 4px;
            padding: 15px;
            text-align: center;
        }
        .kpi-value {
            font-size: 24px;
            font-weight: bold;
            color: #4b0082;
            margin: 8px 0;
        }
        .kpi-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
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
        
        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 20px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-critical { background: #ff6b6b; color: white; }
        .status-low { background: #ffa500; color: white; }
        .status-sufficient { background: #51cf66; color: white; }
        
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
                <h1>Sport Performance Analysis</h1>
                <p id="sport-name" style="opacity: 0.9;">Loading...</p>
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
                <button onclick="loadSportData()">Apply Filters</button>
            </div>
        </div>
        
        <div class="content">
            <div id="error-container"></div>
            <div id="data-container" class="loading">Loading performance data...</div>
        </div>
    </div>

    <script>
        const API_BASE = '/uoc-sports/public';
        const sportId = new URLSearchParams(window.location.search).get('sport_id');
        
        function loadSportData() {
            const startDate = document.getElementById('start-date').value;
            const endDate = document.getElementById('end-date').value;
            
            let url = `${API_BASE}/api/drill-down/sport-performance?sport_id=${sportId}&start_date=${startDate}&end_date=${endDate}`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderSportData(data.data);
                    } else {
                        showError(data.message || 'Failed to load data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Error loading data: ' + error.message);
                });
        }
        
        function renderSportData(data) {
            const container = document.getElementById('data-container');
            const sportName = data.sport?.sport_name || 'Unknown Sport';
            document.getElementById('sport-name').textContent = sportName;
            
            let html = '';
            
            // Budget Section
            if (data.budget && data.budget.length > 0) {
                const budget = data.budget[0];
                html += `
                    <div class="section">
                        <div class="section-title">Budget Summary</div>
                        <div class="kpi-grid">
                            <div class="kpi-card">
                                <div class="kpi-label">Allocated Budget</div>
                                <div class="kpi-value">Rs. ${parseFloat(budget.allocated).toFixed(2)}</div>
                            </div>
                            <div class="kpi-card">
                                <div class="kpi-label">Spent Amount</div>
                                <div class="kpi-value">Rs. ${parseFloat(budget.spent).toFixed(2)}</div>
                            </div>
                            <div class="kpi-card">
                                <div class="kpi-label">Remaining</div>
                                <div class="kpi-value">Rs. ${parseFloat(budget.remaining).toFixed(2)}</div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Events Section
            if (data.events && data.events.length > 0) {
                html += `
                    <div class="section">
                        <div class="section-title">Events & Tournaments</div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Tournament Name</th>
                                    <th>Date</th>
                                    <th>Total Matches</th>
                                    <th>Completed</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                data.events.forEach(event => {
                    html += `
                        <tr>
                            <td>${event.tournament_name}</td>
                            <td>${new Date(event.event_date).toLocaleDateString()}</td>
                            <td>${event.total_matches}</td>
                            <td>${event.completed_matches}</td>
                        </tr>
                    `;
                });
                html += `
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                html += '<div class="section"><p style="color: #999;">No events in this period</p></div>';
            }
            
            // Achievements Section
            if (data.achievements && data.achievements.length > 0) {
                html += `
                    <div class="section">
                        <div class="section-title">Achievements</div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Achieved By</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                data.achievements.forEach(achievement => {
                    html += `
                        <tr>
                            <td>${achievement.title}</td>
                            <td>${achievement.achieve_category}</td>
                            <td>${achievement.achieved_by}</td>
                            <td>${new Date(achievement.date_achieved).toLocaleDateString()}</td>
                        </tr>
                    `;
                });
                html += `
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                html += '<div class="section"><p style="color: #999;">No achievements in this period</p></div>';
            }
            
            // Equipment Section
            if (data.equipment && data.equipment.length > 0) {
                html += `
                    <div class="section">
                        <div class="section-title">Equipment Status</div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Equipment</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                data.equipment.forEach(item => {
                    const statusClass = `status-${item.stock_status.toLowerCase()}`;
                    html += `
                        <tr>
                            <td>${item.equipment_name}</td>
                            <td>${item.quantity}</td>
                            <td><span class="status-badge ${statusClass}">${item.stock_status}</span></td>
                        </tr>
                    `;
                });
                html += `
                            </tbody>
                        </table>
                    </div>
                `;
            }
            
            container.innerHTML = html;
        }
        
        function showError(message) {
            const errorContainer = document.getElementById('error-container');
            errorContainer.innerHTML = `<div class="error">${message}</div>`;
            document.getElementById('data-container').innerHTML = '';
        }
        
        // Load data on page load
        document.addEventListener('DOMContentLoaded', loadSportData);
    </script>
</body>
</html>
