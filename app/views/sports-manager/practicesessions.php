<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <title>Sports manager - practice sessions management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/sports-manager/sub-nav.css");
    @import url("/uoc-sports/public/css/general/footer.css");

    @import url("/uoc-sports/public/css/sports-manager/report.css");
    @import url("/uoc-sports/public/css/sports-manager/dynamic-background.css");

    /* Alert Messages */
    .alert {
        padding: 14px 20px;
        margin: 20px 0;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideDown 0.3s ease-out;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .alert strong {
        font-weight: 600;
    }

    .alert-success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
        border-left: 4px solid #28a745;
    }

    .alert-success::before {
        content: "✓";
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        background-color: #28a745;
        color: white;
        border-radius: 50%;
        font-weight: bold;
        font-size: 16px;
    }

    .alert-error {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    .alert-error::before {
        content: "✕";
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        background-color: #dc3545;
        color: white;
        border-radius: 50%;
        font-weight: bold;
        font-size: 16px;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Date Filter Dropdown */
    .filter-container {
        display: flex;
        align-items: center;
        gap: 15px;
       
        margin: 0 1rem 0 0;
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-label {
        font-weight: 600;
        color: #374151;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .filter-label::before {
       
        font-size: 18px;
    }

    .date-filter-select, .date-picker-input {
        padding: 12px 18px;
        border: 2px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        background: linear-gradient(to bottom, #ffffff, #f9fafb);
        color: #374151;
        min-width: 200px;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .date-picker-input {
        font-family: inherit;
    }

    .date-picker-input::-webkit-calendar-picker-indicator {
        cursor: pointer;
        filter: brightness(0.8);
        transition: filter 0.3s ease;
    }

    .date-picker-input::-webkit-calendar-picker-indicator:hover {
        filter: brightness(1.2);
    }

    .date-filter-select:hover, .date-picker-input:hover {
        border-color: #7c3aed;
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.2);
        transform: translateY(-1px);
    }

    .date-filter-select:focus, .date-picker-input:focus {
        outline: none;
        border-color: #7c3aed;
        background: white;
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.15);
        transform: translateY(-1px);
    }

    .date-picker-input:not(:placeholder-shown) {
        border-color: rgba(0, 0, 0, 0.441);
        background: linear-gradient(to bottom, #faf5ff, #ffffff);
        color: #6b21a8;
        font-weight: 600;
    }

    .clear-filter-btn {
        padding: 12px 24px;
        background: linear-gradient(135deg, #7c3aed 0%, #6b21a8 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 6px rgba(124, 58, 237, 0.3);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .clear-filter-btn::before {
        
        font-size: 14px;
    }

    .clear-filter-btn:hover {
        background: linear-gradient(135deg, #6b21a8 0%, #581c87 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(124, 58, 237, 0.4);
    }

    .clear-filter-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 6px rgba(124, 58, 237, 0.3);
    }

    /* Filter Info Badges */
    .filter-info {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-left: 4px solid #3b82f6;
        border-radius: 8px;
        margin-top: 10px;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
        font-size: 13px;
        color: #1e40af;
        font-weight: 500;
    }

    .filter-info-warning {
        background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
        border-left: 4px solid #f59e0b;
        color: #92400e;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.15);
    }

    .info-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        background: white;
        border-radius: 6px;
        font-weight: 600;
        color: #1e40af;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .info-badge.warning {
        color: #92400e;
    }

    .info-badge-icon {
        font-size: 16px;
    }

    .info-separator {
        color: #60a5fa;
        font-weight: 400;
    }

    .info-separator.warning {
        color: #fbbf24;
    }

  </style>
  <script>
    window.selectedSportName = '<?= htmlspecialchars($sportName ?? '') ?>';
  </script>
  <script src="/uoc-sports/public/js/sports-manager/dynamic-background.js"></script>
</head>
<body>
<?php
    require "../app/views/templates/general/header.php";
    require "../app/views/sports-manager/header-subnav.php";
?>
<div class="page-container">

    <!-- Success Message -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <strong>Success!</strong> <?php echo htmlspecialchars($_SESSION['success_message']); ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <!-- Error Message -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-error">
            <strong>Error!</strong> <?php echo htmlspecialchars($_SESSION['error_message']); ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="container-header">
        <h2>Practice Sessions</h2>
        <p>Manage and schedule practice sessions</p>
        <?php if (isset($selectedSportId) && $selectedSportId): ?>
            <div class="filter-info">
                <span class="info-badge">
                    <span class="info-badge-icon"></span>
                    Sport ID: <strong><?= htmlspecialchars($selectedSportId) ?></strong>
                </span>
                <span class="info-separator">•</span>
                <span class="info-badge">
                    <span class="info-badge-icon"></span>
                   Total Sessions: <strong><?= count($sessions) ?></strong>
                </span>
                <?php if (count($sessions) > 0): ?>
                    <span class="info-separator">•</span>
                    <span class="info-badge">
                        <span class="info-badge-icon"></span>
                        <?= htmlspecialchars($sessions[0]['sport_name'] ?? 'N/A') ?>
                    </span>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="filter-info filter-info-warning">
                <span class="info-badge warning">
                    <span class="info-badge-icon">⚠️</span>
                    No Filter Active
                </span>
                <span class="info-separator warning">•</span>
                <span class="info-badge warning">
                    <span class="info-badge-icon">📊</span>
                    Total: <strong><?= count($sessions) ?></strong> sessions
                </span>
            </div>
        <?php endif; ?>
      </div>

     
       <div class="search-container">
        <div class="filter-container">
            <div class="filter-group">
                <label class="filter-label" for="dateFilter">Select Date:</label>
                <input type="date" id="dateFilter" class="date-picker-input" />
            </div>
            <button id="clearFilter" class="view-all-link">Show All</button>
        </div>
  

   
        <a href="/uoc-sports/public/sport-manager/add-practice<?= isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '' ?>">
            <button class="view-all-link">
              
            Add New Practice Session 
            </button>
        </a>
    </div>


    <div class="data-table">
        <table>
            <thead>
                <tr>
                    
       
                    <th onclick="sortTable(2)">Date<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(3)">Start Time<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(4)">End Time<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(5)">Location<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(6)">Need Equipment<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(7)">Status<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(8)">Action<span class="sort-indicator"></span></th>
                </tr>
            </thead>

            <tbody id="tableBody">
            <?php if(!empty($sessions)): ?>
                <?php foreach($sessions as $session): ?>
                    <tr>
                        
                       
                        <td><?= htmlspecialchars($session['session_date']) ?></td>
                        <td><?= htmlspecialchars($session['start_time']) ?></td>
                        <td><?= htmlspecialchars($session['end_time']) ?></td>
                        <td><?= htmlspecialchars($session['location']) ?></td>
                        <td><?= htmlspecialchars($session['need_equipment'] ?? 'No') ?></td>
                        <td>
                            <select class="status-select status-<?= strtolower($session['status']) ?>" 
                                    data-session-id="<?= $session['id'] ?>" 
                                    onchange="updateStatus(this)">
                                <option value="PENDING" <?= $session['status'] === 'PENDING' ? 'selected' : '' ?>>PENDING</option>
                                <option value="ACCEPTED" <?= $session['status'] === 'ACCEPTED' ? 'selected' : '' ?>>ACCEPTED</option>
                                <option value="CANCELED" <?= $session['status'] === 'CANCELED' ? 'selected' : '' ?>>CANCELED</option>
                                <option value="ACTIVE" <?= $session['status'] === 'ACTIVE' ? 'selected' : '' ?>>ACTIVE</option>

                                <option value="COMPLETED" <?= $session['status'] === 'COMPLETED' ? 'selected' : '' ?>>COMPLETED</option>
                            </select>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="/uoc-sports/public/sport-manager/edit-practice?id=<?= $session['id'] ?>" class="action-btn edit-btn">Edit</a>
                                <form method="POST" action="/uoc-sports/public/sport-manager/delete-practice" style="display: inline; margin: 0;">
                                    <input type="hidden" name="id" value="<?= $session['id'] ?>">
                                    <button type="submit" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this practice session?')">Delete</button>
                                </form>
                            </div>
                        </td>
                     </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem; color: #6b7280;">No practice sessions found.</td>
                    </tr>
                <?php endif; ?>
 <script>
function updateStatus(selectElement) {
    const sessionId = selectElement.getAttribute('data-session-id');
    const newStatus = selectElement.value;
    
    // Update the class for styling
    selectElement.className = 'status-select status-' + newStatus.toLowerCase();
    
    // Send AJAX request to update status
    fetch('/uoc-sports/public/sport-manager/update-practice-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + sessionId + '&status=' + newStatus
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message (you can customize this)
            console.log('Status updated successfully');
        } else {
            alert('Failed to update status: ' + (data.message || 'Unknown error'));
            // Reload page to reset the dropdown
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating status');
        // Reload page to reset the dropdown
        location.reload();
    });
}

// Date Filter Functionality
document.addEventListener('DOMContentLoaded', function() {
    const dateFilter = document.getElementById('dateFilter');
    const clearFilter = document.getElementById('clearFilter');
    const tableBody = document.getElementById('tableBody');
    
    if (dateFilter && tableBody) {
        // Filter by selected date
        dateFilter.addEventListener('change', function() {
            const selectedDate = this.value;
            
            if (!selectedDate) {
                showAllRows();
                return;
            }
            
            filterByDate(selectedDate);
        });
        
        // Clear filter button
        if (clearFilter) {
            clearFilter.addEventListener('click', function() {
                dateFilter.value = '';
                showAllRows();
            });
        }
    }
    
    function filterByDate(selectedDate) {
        const rows = tableBody.getElementsByTagName('tr');
        let visibleCount = 0;
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            // Skip the "no sessions" message row
            if (row.cells.length < 2) continue;
            
            // Get the date cell (index 1 - second column)
            const dateCell = row.cells[1];
            const rowDate = dateCell.textContent.trim();
            
            if (rowDate === selectedDate) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        }
        
        console.log('Filtered to ' + visibleCount + ' sessions for date: ' + selectedDate);
    }
    
    function showAllRows() {
        const rows = tableBody.getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            rows[i].style.display = '';
        }
        
        console.log('Showing all practice sessions');
    }
});
</script>


                    </tbody>
                </table>
            </div>

            </div>

</body>
</html>

            <?php
    require "../app/views/templates/general/footer.php";
?>