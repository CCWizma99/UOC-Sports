<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Booking Requests</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/equipment-manager/sub-nav.css");
    @import url("/uoc-sports/public/css/general/footer.css");

    @import url("/uoc-sports/public/css/equipment-manager/report.css");
  </style>
</head>
<body>
<?php
    require "../app/views/templates/general/header.php";
    require "../app/views/equipment-manager/header-subnav.php";
?>
<div class="report-container">

    <div class="container-header">
        <h2>Equipment Booking Requests</h2>
        <p>Manage equipment booking requests</p>
        
        <!-- Debug Info (Remove in production) -->
        <?php if (isset($_GET['debug'])): ?>
        <div style="background: #f3f4f6; padding: 1rem; margin: 1rem 0; border-radius: 8px; font-size: 0.8rem;">
            <strong>Debug Info:</strong><br>
            Total Requests: <?= count($requests ?? []) ?><br>
            Total Categories: <?= count($categories ?? []) ?><br>
            Total Sports: <?= count($sports ?? []) ?><br>
            <?php if (!empty($requests)): ?>
                Sample Request: <pre><?= print_r($requests[0], true) ?></pre>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($statistics)): ?>
        <div style="display: flex; gap: 2rem; margin-top: 1rem; font-size: 0.9rem;">
            <span><strong>Total:</strong> <?= $statistics['total_requests'] ?? 0 ?></span>
            <span style="color: #f59e0b;"><strong>Pending:</strong> <?= $statistics['pending_count'] ?? 0 ?></span>
            <span style="color: #10b981;"><strong>Active:</strong> <?= $statistics['active_count'] ?? 0 ?></span>
            <span style="color: #6b7280;"><strong>Completed:</strong> <?= $statistics['completed_count'] ?? 0 ?></span>
            <span style="color: #ef4444;"><strong>Rejected:</strong> <?= $statistics['rejected_count'] ?? 0 ?></span>
        </div>
        <?php endif; ?>
    </div>

     
       <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search Booking Requests...">
        
        <!-- Filters -->
        <select id="statusFilter" onchange="filterRequests()">
            <option value="">All Status</option>
            <option value="PENDING" <?= ($filters['status'] ?? '') === 'PENDING' ? 'selected' : '' ?>>Pending</option>
            <option value="ACTIVE" <?= ($filters['status'] ?? '') === 'ACTIVE' ? 'selected' : '' ?>>Active</option>
            <option value="COMPLETED" <?= ($filters['status'] ?? '') === 'COMPLETED' ? 'selected' : '' ?>>Completed</option>
            <option value="REJECTED" <?= ($filters['status'] ?? '') === 'REJECTED' ? 'selected' : '' ?>>Rejected</option>
        </select>
        
        <select id="sportFilter" onchange="filterRequests()">
            <option value="">All Sports</option>
            <?php if (isset($sports)): foreach($sports as $sport): ?>
                <option value="<?= $sport['sport_id'] ?>" <?= ($filters['sport_id'] ?? '') === $sport['sport_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sport['sport_name']) ?>
                </option>
            <?php endforeach; endif; ?>
        </select>

        <a href="/uoc-sports/public/equipment-manager/add-booking">
            <button class="btn-add">
                <i class="fas fa-plus"></i> Add Booking
            </button>
        </a>
    </div>


    <div class="data-table">
        <table>
            <thead>
                <tr>
                    
                    <th onclick="sortTable(1)">User ID<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(2)">Student Name<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(3)">Sport<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(4)">Equipment Category<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(5)">Requested Date<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(6)">Start Time<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(7)">End Time<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(8)">Location<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(9)">Notes<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(10)">Status<span class="sort-indicator"></span></th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="tableBody">
            <?php 
            // Debug output
            if (isset($_GET['debug'])) {
                echo "<tr><td colspan='12' style='background: yellow; padding: 1rem;'>";
                echo "Requests variable exists: " . (isset($requests) ? 'YES' : 'NO') . "<br>";
                echo "Requests is array: " . (is_array($requests) ? 'YES' : 'NO') . "<br>";
                echo "Requests count: " . (isset($requests) ? count($requests) : '0') . "<br>";
                if (isset($requests) && !empty($requests)) {
                    echo "<pre>" . print_r($requests[0], true) . "</pre>";
                }
                echo "</td></tr>";
            }
            
            if(!empty($requests)): ?>
                <?php foreach($requests as $request): 
                    $statusClass = match($request['status']) {
                        'PENDING' => 'status-pending',
                        'ACTIVE' => 'status-active',
                        'COMPLETED' => 'status-completed',
                        'REJECTED' => 'status-rejected',
                        default => ''
                    };
                ?>
                    <tr>
                 
                        <td><?= htmlspecialchars($request['student_id'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($request['student_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($request['sport_name'] ?? 'N/A') ?></td>
                        <td>
                            <?php 
                            // Display equipment items from JSON or fallback to category_name
                            if (!empty($request['equipment_items'])) {
                                $items = json_decode($request['equipment_items'], true);
                                if (is_array($items) && count($items) > 0) {
                                    echo '<div style="display: flex; flex-direction: column; gap: 2px;">';
                                    foreach ($items as $item) {
                                        $equipName = htmlspecialchars($item['equipment_name'] ?? '');
                                        $qty = htmlspecialchars($item['quantity'] ?? 1);
                                        echo '<span style="font-size: 0.9em;">• ' . $equipName . ' <strong>(×' . $qty . ')</strong></span>';
                                    }
                                    echo '</div>';
                                } else {
                                    echo htmlspecialchars($request['category_name'] ?? 'N/A');
                                }
                            } else {
                                echo htmlspecialchars($request['category_name'] ?? 'N/A');
                            }
                            ?>
                        </td>
                        <td><?= htmlspecialchars($request['request_date']) ?></td>
                        <td><?= date('h:i A', strtotime($request['start_time'])) ?></td>
                        <td><?= date('h:i A', strtotime($request['end_time'])) ?></td>
                        <td><?= htmlspecialchars($request['reserved_location'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($request['notes'] ?? 'N/A') ?></td>
                        <td>
                            <select class="status-dropdown" 
                                    data-request-id="<?= $request['request_id'] ?>" 
                                    data-original-status="<?= $request['status'] ?>"
                                    onchange="updateStatus('<?= $request['request_id'] ?>', this.value, this)">
                                <option value="PENDING" <?= $request['status'] === 'PENDING' ? 'selected' : '' ?>>PENDING</option>
                                <option value="ACTIVE" <?= $request['status'] === 'ACTIVE' ? 'selected' : '' ?>>ACTIVE</option>
                                <option value="COMPLETED" <?= $request['status'] === 'COMPLETED' ? 'selected' : '' ?>>COMPLETED</option>
                                <option value="REJECTED" <?= $request['status'] === 'REJECTED' ? 'selected' : '' ?>>REJECTED</option>
                            </select>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem; justify-content: center; align-items: center;">

                                <button class="btn-delete" onclick="deleteRequest('<?= $request['request_id'] ?>')">Delete</button>
                            </div>
                        </td>
                     </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12" style="text-align: center; padding: 2rem; color: #6b7280;">
                            No booking requests found.
                        </td>
                    </tr>
                <?php endif; ?>

                    </tbody>
                </table>
            </div>

            </div>

<script>
function updateStatus(requestId, newStatus, dropdownElement) {
    console.log('updateStatus called with:', requestId, newStatus);
    
    const originalStatus = dropdownElement.getAttribute('data-original-status');
    
    if (!confirm('Are you sure you want to update the status to ' + newStatus + '?')) {
        console.log('User cancelled status update');
        // Reset dropdown to original value
        dropdownElement.value = originalStatus;
        return;
    }
    
    console.log('Sending request to:', '/uoc-sports/public/equipment-manager/update-booking-status');
    
    fetch('/uoc-sports/public/equipment-manager/update-booking-status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            request_id: requestId, 
            status: newStatus 
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            alert('Status updated successfully!');
            // Update the original status data attribute
            dropdownElement.setAttribute('data-original-status', newStatus);
        } else {
            alert('Error: ' + data.message);
            // Reset dropdown to original value
            dropdownElement.value = originalStatus;
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Error updating status: ' + error.message);
        // Reset dropdown to original value
        dropdownElement.value = originalStatus;
    });
}

function editRequest(requestId) {
    window.location.href = '/uoc-sports/public/equipment-manager/edit-booking-request?id=' + requestId;
}

function deleteRequest(requestId) {
    if (!confirm('Are you sure you want to delete this request? This action cannot be undone.')) {
        return;
    }
    
    fetch('/uoc-sports/public/equipment-manager/delete-booking-request', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ request_id: requestId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Request deleted successfully');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error deleting request');
        console.error('Error:', error);
    });
}

function filterRequests() {
    const status = document.getElementById('statusFilter').value;
    const sport = document.getElementById('sportFilter').value;
    const url = new URL(window.location.href);
    
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    
    if (sport) {
        url.searchParams.set('sport_id', sport);
    } else {
        url.searchParams.delete('sport_id');
    }
    
    window.location.href = url.toString();
}

// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('#tableBody tr');
    
    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});

// Sort table function
let sortDirection = {};
function sortTable(columnIndex) {
    const table = document.querySelector('.data-table table');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    if (!sortDirection[columnIndex]) {
        sortDirection[columnIndex] = 'asc';
    } else {
        sortDirection[columnIndex] = sortDirection[columnIndex] === 'asc' ? 'desc' : 'asc';
    }
    
    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex]?.textContent.trim() || '';
        const bValue = b.cells[columnIndex]?.textContent.trim() || '';
        
        if (sortDirection[columnIndex] === 'asc') {
            return aValue.localeCompare(bValue, undefined, { numeric: true });
        } else {
            return bValue.localeCompare(aValue, undefined, { numeric: true });
        }
    });
    
    rows.forEach(row => tbody.appendChild(row));
}
</script>

<style>
.status-badge {
    padding: 0.4rem 0.85rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 700;
    display: inline-block;
    text-transform: uppercase;
}

.status-pending {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
}

.status-active {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
}

.status-completed {
    background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
    color: #374151;
}

.status-rejected {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
}

.btn-approve {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-approve:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    transform: translateY(-2px);
}

.btn-reject {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-reject:hover {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    transform: translateY(-2px);
}

.btn-complete {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-complete:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    transform: translateY(-2px);
}

#statusFilter, #sportFilter, .status-dropdown {
    padding: 0.5rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    cursor: pointer;
    background: white;
    pointer-events: auto;
}

.status-dropdown:hover {
    border-color: #5e2d91;
}

.status-dropdown:focus {
    outline: none;
    border-color: #5e2d91;
    box-shadow: 0 0 0 3px rgba(94, 45, 145, 0.1);
}
</style>

<?php
    require "../app/views/templates/general/footer.php";
?>
</body>
</html>
