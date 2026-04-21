<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Past Events History | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
        
        @import url(/uoc-sports/public/css/admin/ui-improvements.css);

        :root {
            --profile-primary: #4b0082;
            --profile-gradient: linear-gradient(135deg, #111 0%, #4b0082 100%);
            --profile-accent: rgba(75, 0, 130, 0.05);
            --profile-shadow: 0 4px 30px rgba(0, 0, 0, 0.08);
            --profile-border: 2px solid rgba(75, 0, 130, 0.1);
            --profile-bg: #fdfbff;
        }

        .main-content-wrapper {
            position: fixed;
            top: 100px;
            left: 240px;
            right: 0;
            bottom: 40px;
            overflow-y: auto;
            padding: 24px;
            background-color: var(--profile-bg);
        }

        .past-events-container {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header-flex h2 {
            margin: 0;
            color: #1e293b;
            font-size: 24px;
            font-weight: 800;
        }

        .search-container {
            position: relative;
            width: 350px;
        }

        .search-container input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-container input:focus {
            outline: none;
            border-color: #4b0082;
            box-shadow: 0 0 0 4px rgba(75, 0, 130, 0.1);
        }

        .search-container i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        /* Table Styles */
        .custom-table-wrapper {
            overflow-x: auto;
            border-radius: 15px;
            border: 1px solid #e2e8f0;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .custom-table th {
            background: #f8fafc;
            padding: 15px 20px;
            color: #64748b;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e2e8f0;
        }

        .custom-table td {
            padding: 18px 20px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 14px;
        }

        .custom-table tr:last-child td {
            border-bottom: none;
        }

        .custom-table tr:hover td {
            background: #f8fafc;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-complete {
            background: #dcfce7;
            color: #166534;
        }

        .level-badge {
            font-size: 12px;
            padding: 4px 8px;
            background: #f1f5f9;
            border-radius: 6px;
            color: #475569;
            font-weight: 600;
        }

        /* Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }

        .pagination-info {
            color: #64748b;
            font-size: 14px;
        }

        .pagination-controls {
            display: flex;
            gap: 8px;
        }

        .page-btn {
            padding: 8px 16px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #475569;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }

        .page-btn:hover:not(:disabled) {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #1e293b;
        }

        .page-btn.active {
            background: #4b0082;
            color: white;
            border-color: #4b0082;
        }

        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-view-results {
            color: #4b0082;
            text-decoration: none;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: color 0.2s;
        }

        .btn-view-results:hover {
            color: #6a1b9a;
        }

        .no-data-msg {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
            font-style: italic;
        }
    </style>
</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';
?>

<div class="main-content-wrapper">
    <div class="past-events-container">
        <div class="header-flex">
            <h2>Past Events History</h2>
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" id="pastEventSearch" placeholder="Search by name or sport...">
            </div>
        </div>

        <div class="custom-table-wrapper">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Event Name</th>
                        <th>Sport</th>
                        <th>Level</th>
                        <th>Started</th>
                        <th>Ended</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="pastEventsBody">
                    <!-- Dynamic Content -->
                </tbody>
            </table>
            <div id="noDataMessage" class="no-data-msg" style="display: none;">
                No completed tournaments found.
            </div>
        </div>

        <div class="pagination-container" id="paginationContainer" style="display: none;">
            <div class="pagination-info" id="paginationInfo">
                Showing 0 to 0 of 0 events
            </div>
            <div class="pagination-controls" id="paginationBody">
                <!-- Dynamic Buttons -->
            </div>
        </div>
    </div>
</div>

<script>
    let currentPage = 1;
    let currentSearch = '';

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        loadPastEvents();

        // Search input handler with debounce
        let searchTimeout;
        document.getElementById('pastEventSearch').addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentSearch = e.target.value.trim();
                currentPage = 1; // Reset to page 1 on search
                loadPastEvents();
            }, 500);
        });
    });

    async function loadPastEvents() {
        const body = document.getElementById('pastEventsBody');
        const noData = document.getElementById('noDataMessage');
        const pagination = document.getElementById('paginationContainer');
        
        body.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin"></i> Loading historical data...</td></tr>';
        noData.style.display = 'none';

        try {
            const response = await fetch(`/uoc-sports/public/admin-tournament/past-list?page=${currentPage}&search=${encodeURIComponent(currentSearch)}`);
            const data = await response.json();

            if (data.status === 'success' && data.data && data.data.length > 0) {
                body.innerHTML = '';
                data.data.forEach((event, index) => {
                    const rowNumber = (data.pagination.current_page - 1) * data.pagination.limit + index + 1;
                    const startDate = event.start_date ? new Date(event.start_date).toLocaleDateString() : 'N/A';
                    const endDate = event.end_date ? new Date(event.end_date).toLocaleDateString() : 'N/A';
                    
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td style="color: #94a3b8; font-weight: 600;">#${String(rowNumber).padStart(2, '0')}</td>
                        <td style="font-weight: 700;">${event.tournament_name}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-medal" style="color: #4b0082;"></i>
                                ${event.sport_name || 'N/A'}
                            </div>
                        </td>
                        <td><span class="level-badge">${event.match_level}</span></td>
                        <td>${startDate}</td>
                        <td>${endDate}</td>
                        <td>
                            <a href="./admin-results?tournament_id=${event.tournament_id}" class="btn-view-results">
                                <i class="fas fa-poll"></i> View Results
                            </a>
                        </td>
                    `;
                    body.appendChild(tr);
                });

                updatePagination(data.pagination);
                pagination.style.display = 'flex';
            } else {
                body.innerHTML = '';
                noData.style.display = 'block';
                pagination.style.display = 'none';
            }
        } catch (error) {
            console.error('Error loading past events:', error);
            body.innerHTML = '<tr><td colspan="7" style="text-align: center; color: #ef4444; padding: 40px;">Failed to load data. Please try again.</td></tr>';
        }
    }

    function updatePagination(pagination) {
        const info = document.getElementById('paginationInfo');
        const body = document.getElementById('paginationBody');
        
        const start = (pagination.current_page - 1) * pagination.limit + 1;
        const end = Math.min(start + pagination.limit - 1, pagination.total_count);
        
        info.textContent = `Showing ${start} to ${end} of ${pagination.total_count} events`;
        
        body.innerHTML = '';
        
        // Previous Button
        const prevBtn = document.createElement('button');
        prevBtn.className = 'page-btn';
        prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prevBtn.disabled = pagination.current_page === 1;
        prevBtn.onclick = () => { if (currentPage > 1) { currentPage--; loadPastEvents(); } };
        body.appendChild(prevBtn);

        // Page Numbers (Simplification: show current, and one before/after)
        for (let i = 1; i <= pagination.total_pages; i++) {
            if (i === 1 || i === pagination.total_pages || (i >= pagination.current_page - 1 && i <= pagination.current_page + 1)) {
                const btn = document.createElement('button');
                btn.className = `page-btn ${i === pagination.current_page ? 'active' : ''}`;
                btn.textContent = i;
                btn.onclick = () => { currentPage = i; loadPastEvents(); };
                body.appendChild(btn);
            } else if (i === pagination.current_page - 2 || i === pagination.current_page + 2) {
                const span = document.createElement('span');
                span.textContent = '...';
                span.style.padding = '8px';
                body.appendChild(span);
            }
        }

        // Next Button
        const nextBtn = document.createElement('button');
        nextBtn.className = 'page-btn';
        nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
        nextBtn.disabled = pagination.current_page === pagination.total_pages;
        nextBtn.onclick = () => { if (currentPage < pagination.total_pages) { currentPage++; loadPastEvents(); } };
        body.appendChild(nextBtn);
    }
</script>

<?php require '../app/views/templates/admin/footer.php'; ?>

<script>
    var currentPageElement = document.getElementById("sidebar-events");
    if (currentPageElement) {
        currentPageElement.classList.add("active");
    }
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</body>
</html>
