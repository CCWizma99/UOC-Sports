<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Equipment - <?= htmlspecialchars($sport ?? 'Equipment') ?></title>
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

    <div class="report-header">
        <h2><?= htmlspecialchars($sport ?? 'Equipment') ?> Equipment Management</h2>
      </div>

     
       <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search Equipment...">
  
        <button class="btn-add" onclick="window.location.href='/uoc-sports/public/equipment-manager/equipments'">
            <i class="fas fa-arrow-left"></i> Back to Sports
        </button>
    </div>


    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Equipment ID<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(1)">Equipment Name<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(2)">Usable Equipment Count<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(3)">Available Equipment Count<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(4)">Reserved Equipment Locations<span class="sort-indicator"></span></th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="tableBody">
                <?php
                // Sample data based on sport
                $equipmentData = [
                    'Athletics' => [
                        ['id' => 'ATH-001', 'name' => 'Running Shoes', 'usable' => 50, 'available' => 42, 'locations' => 'Equipment Room A, Storage 1'],
                        ['id' => 'ATH-002', 'name' => 'Starting Blocks', 'usable' => 12, 'available' => 10, 'locations' => 'Track Field Storage'],
                        ['id' => 'ATH-003', 'name' => 'Hurdles', 'usable' => 20, 'available' => 18, 'locations' => 'Track Field Storage'],
                        ['id' => 'ATH-004', 'name' => 'Javelin', 'usable' => 15, 'available' => 13, 'locations' => 'Equipment Room B'],
                        ['id' => 'ATH-005', 'name' => 'Shot Put', 'usable' => 10, 'available' => 8, 'locations' => 'Equipment Room B'],
                    ],
                    'Rugby' => [
                        ['id' => 'RUG-001', 'name' => 'Rugby Ball', 'usable' => 40, 'available' => 32, 'locations' => 'Rugby Field Storage, Locker Room 2'],
                        ['id' => 'RUG-002', 'name' => 'Tackle Bags', 'usable' => 15, 'available' => 12, 'locations' => 'Rugby Field Storage'],
                        ['id' => 'RUG-003', 'name' => 'Scrum Machine', 'usable' => 2, 'available' => 2, 'locations' => 'Rugby Practice Field'],
                        ['id' => 'RUG-004', 'name' => 'Training Cones', 'usable' => 60, 'available' => 58, 'locations' => 'Equipment Room C'],
                        ['id' => 'RUG-005', 'name' => 'Kicking Tee', 'usable' => 20, 'available' => 16, 'locations' => 'Rugby Field Storage'],
                    ],
                    'Tennis' => [
                        ['id' => 'TEN-001', 'name' => 'Tennis Racket', 'usable' => 40, 'available' => 32, 'locations' => 'Tennis Court Office, Storage Shed'],
                        ['id' => 'TEN-002', 'name' => 'Tennis Ball (Can)', 'usable' => 100, 'available' => 85, 'locations' => 'Tennis Court Office'],
                        ['id' => 'TEN-003', 'name' => 'Ball Machine', 'usable' => 3, 'available' => 3, 'locations' => 'Tennis Court 1, Court 3'],
                        ['id' => 'TEN-004', 'name' => 'Court Net', 'usable' => 6, 'available' => 6, 'locations' => 'All 6 Courts'],
                        ['id' => 'TEN-005', 'name' => 'Ball Hopper', 'usable' => 12, 'available' => 10, 'locations' => 'Tennis Court Office'],
                    ],
                    'Cricket' => [
                        ['id' => 'CRI-001', 'name' => 'Cricket Bat', 'usable' => 25, 'available' => 18, 'locations' => 'Cricket Pavilion, Equipment Room'],
                        ['id' => 'CRI-002', 'name' => 'Cricket Ball (Red)', 'usable' => 50, 'available' => 42, 'locations' => 'Cricket Pavilion'],
                        ['id' => 'CRI-003', 'name' => 'Wicket Set', 'usable' => 10, 'available' => 10, 'locations' => 'Cricket Ground Storage'],
                        ['id' => 'CRI-004', 'name' => 'Batting Gloves', 'usable' => 30, 'available' => 22, 'locations' => 'Cricket Pavilion Lockers'],
                        ['id' => 'CRI-005', 'name' => 'Helmet', 'usable' => 20, 'available' => 15, 'locations' => 'Cricket Pavilion'],
                    ],
                    'Basketball' => [
                        ['id' => 'BKT-001', 'name' => 'Basketball', 'usable' => 30, 'available' => 25, 'locations' => 'Gymnasium Storage, Court Side'],
                        ['id' => 'BKT-002', 'name' => 'Basketball Hoop', 'usable' => 4, 'available' => 4, 'locations' => 'Main Court, Practice Court'],
                        ['id' => 'BKT-003', 'name' => 'Jersey Set', 'usable' => 40, 'available' => 35, 'locations' => 'Gymnasium Locker Room'],
                        ['id' => 'BKT-004', 'name' => 'Training Cones', 'usable' => 50, 'available' => 50, 'locations' => 'Gymnasium Storage'],
                        ['id' => 'BKT-005', 'name' => 'Ball Cart', 'usable' => 5, 'available' => 4, 'locations' => 'Gymnasium Storage'],
                    ],
                    'Football' => [
                        ['id' => 'FTB-001', 'name' => 'Football', 'usable' => 35, 'available' => 28, 'locations' => 'Football Field Storage, Locker Room'],
                        ['id' => 'FTB-002', 'name' => 'Goal Net', 'usable' => 4, 'available' => 4, 'locations' => 'Main Field, Practice Field'],
                        ['id' => 'FTB-003', 'name' => 'Shin Guards', 'usable' => 50, 'available' => 40, 'locations' => 'Equipment Room D'],
                        ['id' => 'FTB-004', 'name' => 'Training Bibs', 'usable' => 60, 'available' => 58, 'locations' => 'Football Field Storage'],
                        ['id' => 'FTB-005', 'name' => 'Agility Ladder', 'usable' => 10, 'available' => 9, 'locations' => 'Practice Field Storage'],
                    ],
                    'Badminton' => [
                        ['id' => 'BDM-001', 'name' => 'Badminton Racket', 'usable' => 45, 'available' => 38, 'locations' => 'Indoor Court Storage, Equipment Desk'],
                        ['id' => 'BDM-002', 'name' => 'Shuttlecock (Tube)', 'usable' => 200, 'available' => 175, 'locations' => 'Indoor Court Storage'],
                        ['id' => 'BDM-003', 'name' => 'Badminton Net', 'usable' => 8, 'available' => 8, 'locations' => 'Courts 1-8'],                        ['id' => 'BDM-005', 'name' => 'Score Board', 'usable' => 8, 'available' => 8, 'locations' => 'All Indoor Courts'],
                    ],
                    'Volleyball' => [
                        ['id' => 'VLB-001', 'name' => 'Volleyball', 'usable' => 30, 'available' => 26, 'locations' => 'Beach Court Storage, Indoor Court'],
                        ['id' => 'VLB-002', 'name' => 'Volleyball Net', 'usable' => 6, 'available' => 6, 'locations' => 'Beach Courts, Indoor Courts'],
                        ['id' => 'VLB-003', 'name' => 'Knee Pads', 'usable' => 40, 'available' => 32, 'locations' => 'Equipment Room F'],
                        ['id' => 'VLB-004', 'name' => 'Antenna Set', 'usable' => 12, 'available' => 12, 'locations' => 'Court Storage'],
                        ['id' => 'VLB-005', 'name' => 'Ball Cart', 'usable' => 4, 'available' => 4, 'locations' => 'Indoor Court Storage'],
                    ]
                ];
                
                $currentSport = $sport ?? '';
                $equipment = $equipmentData[$currentSport] ?? [
                    ['id' => 'GEN-001', 'name' => 'Sample Equipment', 'usable' => 10, 'available' => 8, 'locations' => 'General Storage']
                ];
                
                foreach($equipment as $item):
                ?>
                    <tr>
                        <td><?= htmlspecialchars($item['id']) ?></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= htmlspecialchars($item['usable']) ?></td>
                        <td><?= htmlspecialchars($item['available']) ?></td>
                        <td><?= htmlspecialchars($item['locations']) ?></td>
                        <td>
                            <div style="display: flex; gap: 0.5rem; justify-content: center; align-items: center;">
                                <button class="btn-edit" onclick="editEquipment('<?= $item['id'] ?>')">
                                    Edit
                                </button>
                                <button class="btn-delete" onclick="deleteEquipment('<?= $item['id'] ?>', '<?= $item['name'] ?>')">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
function editEquipment(equipmentId) {
    alert('Edit functionality for ' + equipmentId + ' coming soon');
}

function deleteEquipment(equipmentId, equipmentName) {
    if (confirm('Are you sure you want to delete ' + equipmentName + '?')) {
        alert('Delete functionality for ' + equipmentId + ' coming soon');
    }
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
.condition-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
}

.condition-badge.excellent {
    background-color: #d1fae5;
    color: #065f46;
}

.condition-badge.good {
    background-color: #dbeafe;
    color: #1e40af;
}

.condition-badge.fair {
    background-color: #fef3c7;
    color: #92400e;
}

.condition-badge.poor {
    background-color: #fee2e2;
    color: #991b1b;
}
</style>

<?php
    require "../app/views/templates/general/footer.php";
?>
</body>
</html>
