<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />
    
    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
        @import url(/uoc-sports/public/css/admin/audit-logs.css?v=1.1);
    </style>
</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';
?>

<div class="main-content-wrapper">
    <div class="audit-logs-container">
        <div class="audit-header">
            <h2>System Audit Logs</h2>
            <div class="audit-filters">
                <select id="filter-table" onchange="fetchLogs(1)">
                    <option value="">All Tables</option>
                    <option value="user">User / Identity</option>
                    <option value="facility-booking">Facility Booking</option>
                    <option value="budget">Budget Allocations</option>
                    <option value="sport_expenses">Sport Expenses</option>
                    <option value="equipment_inventory">Equipment Inventory</option>
                    <option value="tournament">Tournaments</option>
                </select>
                <select id="filter-action" onchange="fetchLogs(1)">
                    <option value="">All Actions</option>
                    <option value="INSERT">Insert</option>
                    <option value="UPDATE">Update</option>
                    <option value="DELETE">Delete</option>
                </select>
                <input type="date" id="filter-date" onchange="fetchLogs(1)" title="Filter by date">
            </div>
        </div>

        <table class="audit-table">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>User</th>
                    <th>Table</th>
                    <th>Record ID</th>
                    <th>Action</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody id="audit-logs-body">
                <tr>
                    <td colspan="6" style="text-align:center;">Loading audit logs...</td>
                </tr>
            </tbody>
        </table>

        <div class="pagination" id="pagination-controls">
            <!-- Pagination buttons will be injected here -->
        </div>
    </div>
</div>

<script>
    let currentPage = 1;

    function fetchLogs(page = 1) {
        currentPage = page;
        const tableName = document.getElementById('filter-table').value;
        const action = document.getElementById('filter-action').value;
        const date = document.getElementById('filter-date').value;
        
        const params = new URLSearchParams({
            page: page,
            limit: 15,
            table_name: tableName,
            action: action,
            date: date
        });

        fetch(`/uoc-sports/public/admin-api/audit/logs?${params.toString()}`)
            .then(response => response.json())
            .then(res => {
                if(res.status === 'success') {
                    renderLogs(res.data.logs);
                    renderPagination(res.data.pagination);
                } else {
                    document.getElementById('audit-logs-body').innerHTML = `<tr><td colspan="6" style="text-align:center;color:red;">Error loading logs.</td></tr>`;
                }
            })
            .catch(error => {
                console.error("Error fetching audit logs", error);
                document.getElementById('audit-logs-body').innerHTML = `<tr><td colspan="6" style="text-align:center;color:red;">Error loading logs.</td></tr>`;
            });
    }

    function renderLogs(logs) {
        const tbody = document.getElementById('audit-logs-body');
        
        if (!logs || logs.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;">No audit logs found matching criteria.</td></tr>`;
            return;
        }

        let html = '';
        logs.forEach(log => {
            let badgeClass = log.action.toLowerCase();
            let detailsHtml = '';
            
            if(log.details && log.details.length > 0) {
                detailsHtml = `
                    <table class="details-table">
                        <tr><th>Column</th><th>Old Value</th><th>New Value</th></tr>
                        ${log.details.map(d => `
                            <tr>
                                <td>${d.column_name}</td>
                                <td>${d.old_value !== null ? d.old_value : '<em>NULL</em>'}</td>
                                <td>${d.new_value !== null ? d.new_value : '<em>NULL</em>'}</td>
                            </tr>
                        `).join('')}
                    </table>
                `;
            } else {
                detailsHtml = `<em>No specific details recorded.</em>`;
            }

            html += `
                <tr>
                    <td>${log.changed_at}</td>
                    <td>${log.changed_by || 'SYSTEM'}</td>
                    <td><strong>${log.table_name}</strong></td>
                    <td>${log.record_id}</td>
                    <td><span class="badge ${badgeClass}">${log.action}</span></td>
                    <td>${detailsHtml}</td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    }

    function renderPagination(pagination) {
        const controls = document.getElementById('pagination-controls');
        let html = '';
        
        if (pagination.total_pages <= 1) {
            controls.innerHTML = '';
            return;
        }

        for (let i = 1; i <= pagination.total_pages; i++) {
            html += `<button class="${i === pagination.current_page ? 'active' : ''}" onclick="fetchLogs(${i})">${i}</button>`;
        }
        
        controls.innerHTML = html;
    }

    // Initial fetch
    document.addEventListener("DOMContentLoaded", () => {
        fetchLogs(1);
    });
</script>

<?php require '../app/views/templates/admin/footer.php'; ?>

</body>
<script>
    // Note: Assuming there will be an ID for this on sidebar
    var currentPageElement = document.getElementById("sidebar-audit");
    if(currentPageElement) currentPageElement.classList.add("active");
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</html>
