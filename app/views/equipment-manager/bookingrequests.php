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
      </div>

     
       <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search Booking Requests...">
  

   
        <a href="/uoc-sports/public/equipment-manager/add-booking">
            <button class="btn-add">
              
            Add New Booking 
            </button>
        </a>
    </div>


    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th onclick="sortTable(0)">User ID<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(1)">Equipment<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(2)">Sport<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(3)">Quantity<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(4)">Request Date<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(5)">Start Time<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(6)">End Time<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(7)">Purpose<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(8)">Status<span class="sort-indicator"></span></th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="tableBody">
            <?php if(!empty($bookingRequests)): ?>
                <?php foreach($bookingRequests as $request): ?>
                    <tr>
                        <td><?= htmlspecialchars($request['student_id']) ?></td>
                        <td><?= htmlspecialchars($request['equipment_name']) ?></td>
                        <td><?= htmlspecialchars($request['sport_name']) ?></td>
                        <td><?= htmlspecialchars($request['quantity']) ?></td>
                        <td><?= htmlspecialchars($request['request_date']) ?></td>
                        <td><?= htmlspecialchars(date('h:i A', strtotime($request['start_time']))) ?></td>
                        <td><?= htmlspecialchars(date('h:i A', strtotime($request['end_time']))) ?></td>
                        <td><?= htmlspecialchars($request['purpose']) ?></td>
                        <td>
                            <select class="status-dropdown" onchange="updateStatus('<?= $request['request_id'] ?>', this.value)">
                                <option value="PENDING" <?= $request['status'] === 'PENDING' ? 'selected' : '' ?>>Pending</option>
                                <option value="ACTIVE" <?= $request['status'] === 'ACTIVE' ? 'selected' : '' ?>>Active</option>
                                <option value="APPROVED" <?= $request['status'] === 'APPROVED' ? 'selected' : '' ?>>Approved</option>
                                <option value="REJECTED" <?= $request['status'] === 'REJECTED' ? 'selected' : '' ?>>Rejected</option>
                                <option value="COMPLETED" <?= $request['status'] === 'COMPLETED' ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <button class="btn-edit" onclick="editBooking('<?= $request['request_id'] ?>')">Edit</button>
                                <button class="btn-delete" onclick="deleteBooking('<?= $request['request_id'] ?>')">Delete</button>
                            </div>
                        </td>
                     </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                    <!-- Sample Booking Requests Data -->
                    <tr>
                        <td>23000000</td>
                        <td>Cricket Bat</td>
                        <td>Cricket</td>
                        <td>2</td>
                        <td>2025-12-29</td>
                        <td>08:00 AM</td>
                        <td>10:00 AM</td>
                        <td>Provincial matches practice</td>
                        <td>
                            <select class="status-dropdown" onchange="alert('Status update: ' + this.value)">
                                <option value="PENDING">Pending</option>
                                <option value="ACTIVE" selected>Active</option>
                                <option value="APPROVED">Approved</option>
                                <option value="REJECTED">Rejected</option>
                                <option value="COMPLETED">Completed</option>
                            </select>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <button class="btn-edit" onclick="alert('Edit functionality coming soon')">Edit</button>
                                <button class="btn-delete" onclick="alert('Delete functionality coming soon')">Delete</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>23000001</td>
                        <td>Basketball</td>
                        <td>Basketball</td>
                        <td>3</td>
                        <td>2025-12-20</td>
                        <td>02:00 PM</td>
                        <td>04:00 PM</td>
                        <td>Team practice session</td>
                        <td>
                            <select class="status-dropdown" onchange="alert('Status update: ' + this.value)">
                                <option value="PENDING">Pending</option>
                                <option value="ACTIVE">Active</option>
                                <option value="APPROVED" selected>Approved</option>
                                <option value="REJECTED">Rejected</option>
                                <option value="COMPLETED">Completed</option>
                            </select>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <button class="btn-edit" onclick="alert('Edit functionality coming soon')">Edit</button>
                                <button class="btn-delete" onclick="alert('Delete functionality coming soon')">Delete</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>23000002</td>
                        <td>Football</td>
                        <td>Football</td>
                        <td>1</td>
                        <td>2025-12-18</td>
                        <td>10:00 AM</td>
                        <td>12:00 PM</td>
                        <td>Tournament preparation</td>
                        <td>
                            <select class="status-dropdown" onchange="alert('Status update: ' + this.value)">
                                <option value="PENDING">Pending</option>
                                <option value="ACTIVE" selected>Active</option>
                                <option value="APPROVED">Approved</option>
                                <option value="REJECTED">Rejected</option>
                                <option value="COMPLETED">Completed</option>
                            </select>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <button class="btn-edit" onclick="alert('Edit functionality coming soon')">Edit</button>
                                <button class="btn-delete" onclick="alert('Delete functionality coming soon')">Delete</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>23000003</td>
                        <td>Tennis Racket</td>
                        <td>Tennis</td>
                        <td>5</td>
                        <td>2025-12-16</td>
                        <td>06:00 AM</td>
                        <td>08:00 AM</td>
                        <td>Morning practice</td>
                        <td>
                            <select class="status-dropdown" onchange="alert('Status update: ' + this.value)">
                                <option value="PENDING">Pending</option>
                                <option value="ACTIVE">Active</option>
                                <option value="APPROVED">Approved</option>
                                <option value="REJECTED">Rejected</option>
                                <option value="COMPLETED" selected>Completed</option>
                            </select>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <button class="btn-edit" onclick="alert('Edit functionality coming soon')">Edit</button>
                                <button class="btn-delete" onclick="alert('Delete functionality coming soon')">Delete</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>23000004</td>
                        <td>Badminton Racket</td>
                        <td>Badminton</td>
                        <td>4</td>
                        <td>2025-12-22</td>
                        <td>04:00 PM</td>
                        <td>06:00 PM</td>
                        <td>Competition training</td>
                        <td>
                            <select class="status-dropdown" onchange="alert('Status update: ' + this.value)">
                                <option value="PENDING">Pending</option>
                                <option value="ACTIVE">Active</option>
                                <option value="APPROVED">Approved</option>
                                <option value="REJECTED" selected>Rejected</option>
                                <option value="COMPLETED">Completed</option>
                            </select>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <button class="btn-edit" onclick="alert('Edit functionality coming soon')">Edit</button>
                                <button class="btn-delete" onclick="alert('Delete functionality coming soon')">Delete</button>
                            </div>
                        </td>
                    </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>

            </div>

<script>
function updateStatus(requestId, newStatus) {
    // TODO: Implement AJAX call to update status
    console.log('Update status:', requestId, newStatus);
    alert('Status update functionality will be implemented soon');
}

function editBooking(requestId) {
    // TODO: Redirect to edit page
    window.location.href = '/uoc-sports/public/equipment-manager/edit-booking?id=' + requestId;
}

function deleteBooking(requestId) {
    if (!confirm('Are you sure you want to delete this booking request?')) {
        return;
    }
    
    // TODO: Implement AJAX call to delete booking
    console.log('Delete booking:', requestId);
    alert('Delete functionality will be implemented soon');
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
    
    // Initialize sort direction for this column
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

<?php
    require "../app/views/templates/general/footer.php";
?>
</body>
</html>
