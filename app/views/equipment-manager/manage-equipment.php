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
                    <th onclick="sortTable(2)">Quantity<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(3)">Available<span class="sort-indicator"></span></th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="tableBody">
                <?php
                // Sample data based on sport
                $equipmentData = [
                    'Cricket' => [
                        ['id' => 'EQ001', 'name' => 'Cricket Bat', 'quantity' => 25, 'available' => 18, 'condition' => 'Good', 'updated' => '2025-12-10'],
                        ['id' => 'EQ002', 'name' => 'Cricket Ball', 'quantity' => 50, 'available' => 42, 'condition' => 'Excellent', 'updated' => '2025-12-12'],
                        ['id' => 'EQ003', 'name' => 'Wicket Stumps', 'quantity' => 10, 'available' => 10, 'condition' => 'Good', 'updated' => '2025-12-08'],
                        ['id' => 'EQ004', 'name' => 'Batting Gloves', 'quantity' => 30, 'available' => 22, 'condition' => 'Fair', 'updated' => '2025-12-05'],
                        ['id' => 'EQ005', 'name' => 'Helmet', 'quantity' => 20, 'available' => 15, 'condition' => 'Good', 'updated' => '2025-12-11'],
                    ],
                    'Basketball' => [
                        ['id' => 'EQ010', 'name' => 'Basketball', 'quantity' => 30, 'available' => 25, 'condition' => 'Excellent', 'updated' => '2025-12-13'],
                        ['id' => 'EQ011', 'name' => 'Basketball Hoop', 'quantity' => 4, 'available' => 4, 'condition' => 'Good', 'updated' => '2025-12-01'],
                        ['id' => 'EQ012', 'name' => 'Jersey Set', 'quantity' => 40, 'available' => 35, 'condition' => 'Excellent', 'updated' => '2025-12-14'],
                        ['id' => 'EQ013', 'name' => 'Training Cones', 'quantity' => 50, 'available' => 50, 'condition' => 'Good', 'updated' => '2025-12-10'],
                    ],
                    'Football' => [
                        ['id' => 'EQ020', 'name' => 'Football', 'quantity' => 35, 'available' => 28, 'condition' => 'Good', 'updated' => '2025-12-12'],
                        ['id' => 'EQ021', 'name' => 'Goal Net', 'quantity' => 4, 'available' => 4, 'condition' => 'Excellent', 'updated' => '2025-12-05'],
                        ['id' => 'EQ022', 'name' => 'Shin Guards', 'quantity' => 50, 'available' => 40, 'condition' => 'Good', 'updated' => '2025-12-11'],
                        ['id' => 'EQ023', 'name' => 'Training Bibs', 'quantity' => 60, 'available' => 58, 'condition' => 'Excellent', 'updated' => '2025-12-13'],
                    ],
                    'Tennis' => [
                        ['id' => 'EQ030', 'name' => 'Tennis Racket', 'quantity' => 40, 'available' => 32, 'condition' => 'Good', 'updated' => '2025-12-09'],
                        ['id' => 'EQ031', 'name' => 'Tennis Ball', 'quantity' => 100, 'available' => 85, 'condition' => 'Excellent', 'updated' => '2025-12-14'],
                        ['id' => 'EQ032', 'name' => 'Tennis Net', 'quantity' => 6, 'available' => 6, 'condition' => 'Good', 'updated' => '2025-12-02'],
                    ],
                    'Badminton' => [
                        ['id' => 'EQ040', 'name' => 'Badminton Racket', 'quantity' => 45, 'available' => 38, 'condition' => 'Good', 'updated' => '2025-12-10'],
                        ['id' => 'EQ041', 'name' => 'Shuttlecock', 'quantity' => 200, 'available' => 175, 'condition' => 'Excellent', 'updated' => '2025-12-15'],
                        ['id' => 'EQ042', 'name' => 'Badminton Net', 'quantity' => 8, 'available' => 8, 'condition' => 'Good', 'updated' => '2025-12-03'],
                    ]
                ];
                
                $currentSport = $sport ?? '';
                $equipment = $equipmentData[$currentSport] ?? [
                    ['id' => 'EQ999', 'name' => 'Sample Equipment', 'quantity' => 10, 'available' => 8, 'condition' => 'Good', 'updated' => '2025-12-15']
                ];
                
                foreach($equipment as $item):
                ?>
                    <tr>
                        <td><?= htmlspecialchars($item['id']) ?></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td><?= htmlspecialchars($item['available']) ?></td>
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
