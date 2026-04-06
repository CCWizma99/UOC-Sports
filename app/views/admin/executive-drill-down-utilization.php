<?php
/**
 * Executive Dashboard - Utilization Trends Drill-down View
 * Shows facility and equipment usage analytics
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilization Analysis - Executive Dashboard</title>
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
            height: 350px;
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
                <h1>Utilization Trends Analysis</h1>
                <p style="opacity: 0.9;">Facility bookings and equipment procurements</p>
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
                <button onclick="loadUtilizationData()">Apply Filters</button>
            </div>
        </div>
        
        <div class="content">
            <div id="error-container"></div>
            <div id="data-container" class="loading">Loading utilization data...</div>
        </div>
    </div>

    <script>
        const API_BASE = '/uoc-sports/public';
        const initialFacultyId = new URLSearchParams(window.location.search).get('faculty_id');
        let facilityChart = null;
        let equipmentChart = null;
        
        function loadUtilizationData() {
            const startDate = document.getElementById('start-date').value;
            const endDate = document.getElementById('end-date').value;
            
            let url = `${API_BASE}/api/drill-down/utilization?start_date=${startDate}&end_date=${endDate}`;
            if (initialFacultyId) {
                url += `&faculty_id=${initialFacultyId}`;
            }
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderUtilizationData(data.data);
                    } else {
                        showError(data.message || 'Failed to load data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Error loading data: ' + error.message);
                });
        }
        
        function renderUtilizationData(data) {
            const container = document.getElementById('data-container');
            
            let html = `
                <div class="section">
                    <div class="section-title">Facility Bookings by Sport</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Sport</th>
                                <th>Facility</th>
                                <th>Total Bookings</th>
                                <th>Approved</th>
                                <th>Rejected</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            if (data.facility_usage && data.facility_usage.length > 0) {
                data.facility_usage.forEach(item => {
                    html += `
                        <tr>
                            <td>${item.sport_name || 'Unknown Sport'}</td>
                            <td>${item.facility_name}</td>
                            <td>${item.total_bookings}</td>
                            <td>${item.approved_bookings}</td>
                            <td>${item.rejected_bookings}</td>
                        </tr>
                    `;
                });
            } else {
                html += `<tr><td colspan="5" style="text-align: center; color: #999;">No facility data</td></tr>`;
            }
            
            html += `
                        </tbody>
                    </table>
                </div>
            `;
            
            // Equipment Procurement Section
            html += `
                <div class="section">
                    <div class="section-title">Equipment Procurements by Sport</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Sport</th>
                                <th>Equipment</th>
                                <th>Proc. Count</th>
                                <th>Total Quantity</th>
                                <th>Total Cost</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            if (data.equipment_activity && data.equipment_activity.length > 0) {
                data.equipment_activity.forEach(item => {
                    const cost = parseFloat(item.total_cost) || 0;
                    html += `
                        <tr>
                            <td>${item.sport_name}</td>
                            <td>${item.equipment_name}</td>
                            <td>${item.procurement_count}</td>
                            <td>${item.total_quantity_received}</td>
                            <td>Rs. ${cost.toFixed(2)}</td>
                        </tr>
                    `;
                });
            } else {
                html += `<tr><td colspan="5" style="text-align: center; color: #999;">No equipment data</td></tr>`;
            }
            
            html += `
                        </tbody>
                    </table>
                </div>
            `;
            
            // Charts section if data available
            if (data.facility_usage && data.facility_usage.length > 0) {
                html += `
                    <div class="section">
                        <div class="section-title">Facility Bookings Chart</div>
                        <div class="chart-container">
                            <canvas id="facility-chart"></canvas>
                        </div>
                    </div>
                `;
            }
            
            if (data.equipment_activity && data.equipment_activity.length > 0) {
                html += `
                    <div class="section">
                        <div class="section-title">Equipment Procurement Cost</div>
                        <div class="chart-container">
                            <canvas id="equipment-chart"></canvas>
                        </div>
                    </div>
                `;
            }
            
            container.innerHTML = html;
            
            // Render charts
            if (data.facility_usage && data.facility_usage.length > 0) {
                renderFacilityChart(data.facility_usage);
            }
            if (data.equipment_activity && data.equipment_activity.length > 0) {
                renderEquipmentChart(data.equipment_activity);
            }
        }
        
        function renderFacilityChart(facilityData) {
            const ctx = document.getElementById('facility-chart').getContext('2d');
            
            const labels = facilityData.map(f => `${f.sport_name} - ${f.facility_name}`);
            const bookings = facilityData.map(f => f.total_bookings);
            
            if (facilityChart) {
                facilityChart.destroy();
            }
            
            facilityChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Bookings',
                        data: bookings,
                        backgroundColor: '#4b0082'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y'
                }
            });
        }
        
        function renderEquipmentChart(equipmentData) {
            const ctx = document.getElementById('equipment-chart').getContext('2d');
            
            const labels = equipmentData.map(e => `${e.sport_name} - ${e.equipment_name}`);
            const costs = equipmentData.map(e => parseFloat(e.total_cost) || 0);
            
            if (equipmentChart) {
                equipmentChart.destroy();
            }
            
            equipmentChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Cost (Rs.)',
                        data: costs,
                        backgroundColor: '#764ba2'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y'
                }
            });
        }
        
        function showError(message) {
            const errorContainer = document.getElementById('error-container');
            errorContainer.innerHTML = `<div class="error">${message}</div>`;
            document.getElementById('data-container').innerHTML = '';
        }
        
        // Load data on page load
        document.addEventListener('DOMContentLoaded', loadUtilizationData);
    </script>
</body>
</html>
