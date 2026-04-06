<?php

/**
 * ReportService - Handles export of dashboard data to various formats
 */
class ReportService {
    
    /**
     * Export dashboard metrics to CSV format
     * 
     * @param array $dashboardData The complete dashboard data array
     * @param string $facultyName Optional faculty name for the report
     * @return string CSV formatted string
     */
    public static function exportToCsv($dashboardData, $facultyName = null) {
        $output = fopen('php://memory', 'r+');
        
        // Header row
        $headers = [
            'Report Date' => date('Y-m-d H:i:s'),
            'Faculty' => $facultyName ?? 'All Faculties',
            'Report Type' => 'Executive Dashboard Summary'
        ];
        
        foreach ($headers as $key => $value) {
            fputcsv($output, [$key, $value]);
        }
        
        // Blank line
        fputcsv($output, []);
        
        // KPI SECTION
        fputcsv($output, ['=== KEY PERFORMANCE INDICATORS ===']);
        if (isset($dashboardData['users'])) {
            fputcsv($output, ['Metric', 'Value']);
            fputcsv($output, ['Total Users', $dashboardData['users']['total'] ?? 0]);
            fputcsv($output, ['New Users This Month', $dashboardData['users']['new_this_month'] ?? 0]);
        }
        
        if (isset($dashboardData['reservations'])) {
            fputcsv($output, ['Total Reservations', $dashboardData['reservations']['total'] ?? 0]);
            fputcsv($output, ['Pending Approvals', $dashboardData['reservations']['pending'] ?? 0]);
            fputcsv($output, ['Average Utilization %', $dashboardData['reservations']['avg_utilization'] ?? 0]);
        }
        
        if (isset($dashboardData['equipment'])) {
            fputcsv($output, ['Equipment Types', $dashboardData['equipment']['total_types'] ?? 0]);
            fputcsv($output, ['Total Quantity', $dashboardData['equipment']['total_quantity'] ?? 0]);
            fputcsv($output, ['Items Needing Attention', $dashboardData['equipment']['needs_attention'] ?? 0]);
        }
        
        if (isset($dashboardData['events'])) {
            fputcsv($output, ['Total Events', $dashboardData['events']['total'] ?? 0]);
            fputcsv($output, ['Active Events', $dashboardData['events']['active'] ?? 0]);
            fputcsv($output, ['Completed This Year', $dashboardData['events']['completed_this_year'] ?? 0]);
            fputcsv($output, ['Upcoming (30 days)', $dashboardData['events']['upcoming'] ?? 0]);
        }
        
        if (isset($dashboardData['budget'])) {
            fputcsv($output, ['Budget Allocated', 'Rs. ' . number_format($dashboardData['budget']['allocated'] ?? 0, 2)]);
            fputcsv($output, ['Budget Spent', 'Rs. ' . number_format($dashboardData['budget']['spent'] ?? 0, 2)]);
            fputcsv($output, ['Budget Remaining', 'Rs. ' . number_format($dashboardData['budget']['remaining'] ?? 0, 2)]);
            fputcsv($output, ['Percent Used', ($dashboardData['budget']['percent_used'] ?? 0) . '%']);
        }
        
        // Blank line
        fputcsv($output, []);
        
        // BUDGET EFFICIENCY
        if (isset($dashboardData['insights']['budget_efficiency'])) {
            fputcsv($output, ['=== BUDGET EFFICIENCY ===']);
            $budgetData = $dashboardData['insights']['budget_efficiency'];
            if (isset($budgetData['sports']) && is_array($budgetData['sports'])) {
                fputcsv($output, ['Sport Name', 'Allocated', 'Spent', 'Utilization %']);
                foreach ($budgetData['sports'] as $sport) {
                    fputcsv($output, [
                        $sport['sport_name'] ?? 'N/A',
                        'Rs. ' . number_format($sport['allocated_amount'] ?? 0, 2),
                        'Rs. ' . number_format($sport['spent_amount'] ?? 0, 2),
                        ($sport['utilization'] ?? 0) . '%'
                    ]);
                }
            }
            fputcsv($output, []);
        }
        
        // FACILITY DEMAND
        if (isset($dashboardData['insights']['facility_demand'])) {
            fputcsv($output, ['=== FACILITY DEMAND ===']);
            $facilityData = $dashboardData['insights']['facility_demand'];
            if (isset($facilityData['top_facilities']) && is_array($facilityData['top_facilities'])) {
                fputcsv($output, ['Facility Name', 'Total Bookings', 'Accepted', 'Rejected', 'Pending', 'Approval Rate %']);
                foreach ($facilityData['top_facilities'] as $facility) {
                    fputcsv($output, [
                        $facility['facility_name'] ?? 'N/A',
                        $facility['total_bookings'] ?? 0,
                        $facility['accepted'] ?? 0,
                        $facility['rejected'] ?? 0,
                        $facility['pending'] ?? 0,
                        ($facility['approval_rate'] ?? 0) . '%'
                    ]);
                }
            }
            fputcsv($output, []);
        }
        
        // ATHLETE ENGAGEMENT
        if (isset($dashboardData['insights']['athlete_engagement'])) {
            fputcsv($output, ['=== ATHLETE ENGAGEMENT ===']);
            $engagementData = $dashboardData['insights']['athlete_engagement'];
            fputcsv($output, ['Metric', 'Value']);
            fputcsv($output, ['Total Athletes', $engagementData['total_athletes'] ?? 0]);
            fputcsv($output, ['Total Students', $engagementData['total_students'] ?? 0]);
            fputcsv($output, ['Participation Rate %', ($engagementData['participation_rate'] ?? 0) . '%']);
            fputcsv($output, ['Multi-Sport Athletes', $engagementData['multi_sport_athletes'] ?? 0]);
            fputcsv($output, ['Active Sports', $engagementData['active_sports'] ?? 0]);
            fputcsv($output, []);
        }
        
        // ACTION ITEMS
        if (isset($dashboardData['insights']['action_required'])) {
            fputcsv($output, ['=== ACTION ITEMS ===']);
            $actionData = $dashboardData['insights']['action_required'];
            fputcsv($output, ['Item', 'Count']);
            fputcsv($output, ['Pending Reservations', $actionData['pending_reservations'] ?? 0]);
            fputcsv($output, ['Unresolved Inquiries', $actionData['unresolved_inquiries'] ?? 0]);
            fputcsv($output, ['Upcoming Events', $actionData['upcoming_events'] ?? 0]);
            fputcsv($output, ['Low Stock Items', $actionData['low_stock_items'] ?? 0]);
            fputcsv($output, ['Pending Equipment Requests', $actionData['pending_equipment_requests'] ?? 0]);
            fputcsv($output, ['TOTAL ACTIONS', $actionData['total_actions'] ?? 0]);
            fputcsv($output, []);
        }
        
        // ACHIEVEMENTS
        if (isset($dashboardData['achievements'])) {
            fputcsv($output, ['=== ACHIEVEMENTS SUMMARY ===']);
            $achievementData = $dashboardData['achievements'];
            fputcsv($output, ['Metric', 'Value']);
            fputcsv($output, ['Total Achievements', $achievementData['total'] ?? 0]);
            fputcsv($output, []);
            
            if (isset($achievementData['by_sport']) && is_array($achievementData['by_sport'])) {
                fputcsv($output, ['Sport', 'Count', 'Total Points']);
                foreach ($achievementData['by_sport'] as $sport) {
                    fputcsv($output, [
                        $sport['sport_name'] ?? 'N/A',
                        $sport['count'] ?? 0,
                        $sport['total_points'] ?? 0
                    ]);
                }
            }
            fputcsv($output, []);
        }
        
        // COMMUNITY STATS
        if (isset($dashboardData['community'])) {
            fputcsv($output, ['=== COMMUNITY ENGAGEMENT ===']);
            $communityData = $dashboardData['community'];
            if (isset($communityData['post_stats'])) {
                fputcsv($output, ['Metric', 'Value']);
                fputcsv($output, ['Total Posts', $communityData['post_stats']['total_posts'] ?? 0]);
                fputcsv($output, ['Active Posts', $communityData['post_stats']['active_posts'] ?? 0]);
                fputcsv($output, ['Posts with Comments', $communityData['post_stats']['commenting_enabled'] ?? 0]);
            }
            if (isset($communityData['inquiry_stats'])) {
                fputcsv($output, ['Total Inquiries', $communityData['inquiry_stats']['total'] ?? 0]);
                fputcsv($output, ['Resolved', $communityData['inquiry_stats']['resolved'] ?? 0]);
                fputcsv($output, ['Unresolved', $communityData['inquiry_stats']['unresolved'] ?? 0]);
            }
            fputcsv($output, []);
        }
        
        // Footer
        fputcsv($output, ['Report Generated', date('Y-m-d H:i:s')]);
        fputcsv($output, ['System', 'UOC Sports Management System']);
        
        rewind($output);
        $csvContent = stream_get_clean();
        
        // Use stream_get_contents instead
        $csvContent = '';
        rewind($output);
        while (!feof($output)) {
            $csvContent .= fgets($output);
        }
        fclose($output);
        
        return $csvContent;
    }
    
    /**
     * Generate a simple PDF report using basic HTML/CSS
     * For a production system, use TCPDF or similar
     * 
     * @param array $dashboardData The complete dashboard data array
     * @param string $facultyName Optional faculty name for the report
     * @return string HTML content that can be rendered or converted to PDF
     */
    public static function exportToPdfHtml($dashboardData, $facultyName = null) {
        $facultyDisplay = $facultyName ?? 'All Faculties';
        $generatedDate = date('F d, Y H:i:s');
        
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Executive Dashboard Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container { max-width: 900px; margin: 0 auto; padding: 20px; }
        .header {
            text-align: center;
            border-bottom: 3px solid #4b0082;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 { font-size: 28px; color: #1a1a2e; margin: 10px 0; }
        .header p { color: #666; font-size: 14px; }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #666;
            margin-top: 10px;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #4b0082;
            border-bottom: 2px solid #4b0082;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .kpi-card {
            background: #f8f9ff;
            border-left: 4px solid #4b0082;
            padding: 15px;
            border-radius: 4px;
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
            background: #f0f0ff;
            color: #1a1a2e;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #d0d0d0;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        table tr:nth-child(even) {
            background: #fafafa;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
        
        @media print {
            body { margin: 0; }
            .section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Executive Dashboard Report</h1>
            <p>University of Colombo - Sports Management System</p>
            <div class="info-row">
                <span><strong>Faculty:</strong> {$facultyDisplay}</span>
                <span><strong>Generated:</strong> {$generatedDate}</span>
            </div>
        </div>
        
        <!-- KPI Summary -->
        <div class="section">
            <div class="section-title">Key Performance Indicators</div>
            <div class="kpi-grid">
HTML;
        
        // Add KPI Cards
        if (isset($dashboardData['users'])) {
            $userTotal = $dashboardData['users']['total'] ?? 0;
            $html .= <<<HTML
                <div class="kpi-card">
                    <div class="kpi-label">Total Users</div>
                    <div class="kpi-value">{$userTotal}</div>
                </div>
HTML;
        }
        
        if (isset($dashboardData['reservations'])) {
            $resTotal = $dashboardData['reservations']['total'] ?? 0;
            $html .= <<<HTML
                <div class="kpi-card">
                    <div class="kpi-label">Reservations</div>
                    <div class="kpi-value">{$resTotal}</div>
                </div>
HTML;
        }
        
        if (isset($dashboardData['equipment'])) {
            $equipTypes = $dashboardData['equipment']['total_types'] ?? 0;
            $html .= <<<HTML
                <div class="kpi-card">
                    <div class="kpi-label">Equipment Types</div>
                    <div class="kpi-value">{$equipTypes}</div>
                </div>
HTML;
        }
        
        if (isset($dashboardData['events'])) {
            $eventActive = $dashboardData['events']['active'] ?? 0;
            $html .= <<<HTML
                <div class="kpi-card">
                    <div class="kpi-label">Active Events</div>
                    <div class="kpi-value">{$eventActive}</div>
                </div>
HTML;
        }
        
        if (isset($dashboardData['budget'])) {
            $percentUsed = $dashboardData['budget']['percent_used'] ?? 0;
            $html .= <<<HTML
                <div class="kpi-card">
                    <div class="kpi-label">Budget Used</div>
                    <div class="kpi-value">{$percentUsed}%</div>
                </div>
HTML;
        }
        
        if (isset($dashboardData['achievements'])) {
            $achTotal = $dashboardData['achievements']['total'] ?? 0;
            $html .= <<<HTML
                <div class="kpi-card">
                    <div class="kpi-label">Achievements</div>
                    <div class="kpi-value">{$achTotal}</div>
                </div>
HTML;
        }
        
        $html .= <<<HTML
            </div>
        </div>
HTML;
        
        // Budget Details
        if (isset($dashboardData['budget'])) {
            $budget = $dashboardData['budget'];
            $allocated = number_format($budget['allocated'] ?? 0, 2);
            $spent = number_format($budget['spent'] ?? 0, 2);
            $remaining = number_format($budget['remaining'] ?? 0, 2);
            $html .= <<<HTML
            <div class="section">
                <div class="section-title">Budget Summary</div>
                <table>
                    <tr>
                        <th>Budget Category</th>
                        <th>Amount</th>
                    </tr>
                    <tr>
                        <td>Allocated Budget</td>
                        <td>Rs. {$allocated}</td>
                    </tr>
                    <tr>
                        <td>Spent</td>
                        <td>Rs. {$spent}</td>
                    </tr>
                    <tr>
                        <td>Remaining</td>
                        <td>Rs. {$remaining}</td>
                    </tr>
                </table>
            </div>
HTML;
        }
        
        // Budget Efficiency
        if (isset($dashboardData['insights']['budget_efficiency']['sports'])) {
            $sports = $dashboardData['insights']['budget_efficiency']['sports'];
            $html .= <<<HTML
            <div class="section">
                <div class="section-title">Budget Efficiency by Sport</div>
                <table>
                    <tr>
                        <th>Sport</th>
                        <th>Allocated</th>
                        <th>Spent</th>
                        <th>Utilization %</th>
                    </tr>
HTML;
            foreach ($sports as $sport) {
                $allocated = number_format($sport['allocated_amount'] ?? 0, 2);
                $spent = number_format($sport['spent_amount'] ?? 0, 2);
                $util = $sport['utilization'] ?? 0;
                $sportName = $sport['sport_name'];
                $html .= <<<HTML
                    <tr>
                        <td>{$sportName}</td>
                        <td>Rs. {$allocated}</td>
                        <td>Rs. {$spent}</td>
                        <td>{$util}%</td>
                    </tr>
HTML;
            }
            $html .= <<<HTML
                </table>
            </div>
HTML;
        }
        
        // Action Items
        if (isset($dashboardData['insights']['action_required'])) {
            $actions = $dashboardData['insights']['action_required'];
            $pending = $actions['pending_reservations'] ?? 0;
            $unresolved = $actions['unresolved_inquiries'] ?? 0;
            $upcoming = $actions['upcoming_events'] ?? 0;
            $lowStock = $actions['low_stock_items'] ?? 0;
            $pendingEq = $actions['pending_equipment_requests'] ?? 0;
            $html .= <<<HTML
            <div class="section">
                <div class="section-title">Action Items Requiring Attention</div>
                <table>
                    <tr>
                        <th>Action Item</th>
                        <th>Count</th>
                    </tr>
                    <tr>
                        <td>Pending Reservations</td>
                        <td>{$pending}</td>
                    </tr>
                    <tr>
                        <td>Unresolved Inquiries</td>
                        <td>{$unresolved}</td>
                    </tr>
                    <tr>
                        <td>Upcoming Events</td>
                        <td>{$upcoming}</td>
                    </tr>
                    <tr>
                        <td>Low Stock Items</td>
                        <td>{$lowStock}</td>
                    </tr>
                    <tr>
                        <td>Pending Equipment Requests</td>
                        <td>{$pendingEq}</td>
                    </tr>
                </table>
            </div>
HTML;
        }
        
        // Footer
        $currentYear = date('Y');
        $reportDate = date('F d, Y H:i:s');
        $html .= <<<HTML
        <div class="footer">
            <p>&copy; {$currentYear} University of Colombo - Sports Management System</p>
            <p>Report generated on {$reportDate}</p>
        </div>
    </div>
</body>
</html>
HTML;
        
        return $html;
    }
}
