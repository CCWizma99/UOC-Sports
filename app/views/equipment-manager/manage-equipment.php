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
    @import url("/uoc-sports/public/css/general/footer.css");
    @import url("/uoc-sports/public/css/equipment-manager/report.css");
  </style>
</head>
<body>
<?php
    require "../app/views/templates/general/header.php";
?>
<div class="report-container">

    <div class="report-header">
        <h2><?= htmlspecialchars($sport ?? 'Equipment') ?> Equipment Management</h2>
        <?php if (isset($summary)): ?>
        <div style="display: flex; gap: 2rem; margin-top: 1rem; font-size: 0.9rem;">
            <span><strong>Total Equipment Types:</strong> <?= $summary['total_equipment'] ?? 0 ?></span>
            <span><strong>Total Items:</strong> <?= $summary['total_items'] ?? 0 ?></span>
            <span><strong>Usable:</strong> <?= $summary['usable_items'] ?? 0 ?></span>
            <span><strong>Active Reservations:</strong> <?= $summary['active_reservations'] ?? 0 ?></span>
        </div>
        <?php endif; ?>
      </div>

     
       <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search Equipment...">

        <button class="btn-add" onclick="window.location.href='/uoc-sports/public/equipment-manager/equipments'">
 Back to Sports
        </button>
    </div>


    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Equipment Name<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(1)">Category<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(2)">Total Quantity<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(3)">Usable Count<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(4)">Reserved Qty<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(5)">Available Count<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(6)">Active Bookings & Locations<span class="sort-indicator"></span></th>
                   
                </tr>
            </thead>

            <tbody id="tableBody">
                <?php
                if (isset($equipment) && count($equipment) > 0):
                    foreach($equipment as $item):
                        $availableCount = $item['available_count'] ?? 0;
                        $statusClass = $availableCount > 5 ? 'excellent' : ($availableCount > 0 ? 'good' : 'poor');
                ?>
                    <tr>
                        <td><?= htmlspecialchars($item['equipment_name']) ?></td>
                        <td><span style="background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); padding: 0.25rem 0.75rem; border-radius: 8px; font-weight: 600; color: #5b21b6; font-size: 0.8rem;"><?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?></span></td>
                        <td><?= htmlspecialchars($item['quantity'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($item['usable_count'] ?? 0) ?></td>
                        <td>
                            <?php if (($item['reserved_quantity'] ?? 0) > 0): ?>
                                <span style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 0.25rem 0.75rem; border-radius: 8px; font-weight: 600; color: #92400e; font-size: 0.8rem;">
                                    <?= htmlspecialchars($item['reserved_quantity']) ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #9ca3af;">0</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="condition-badge <?= $statusClass ?>">
                                <?= htmlspecialchars($availableCount) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($item['reserved_count'] > 0): ?>
                                <strong style="color: #5b21b6;"><?= $item['reserved_count'] ?> active booking(s)</strong><br>
                                <small style="color: #6b7280; line-height: 1.6;"><?= nl2br(htmlspecialchars(str_replace('; ', "\n", $item['reserved_times'] ?? 'No details'))) ?></small>
                            <?php else: ?>
                                <span style="color: #6b7280;">No active bookings</span>
                            <?php endif; ?>
                        </td>
                        
                    </tr>
                <?php 
                    endforeach;
                else:
                ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: #6b7280;">
                            No equipment found for this sport.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
function editEquipment(equipmentId, equipmentName, usableCount, maxAllow) {
    const newName = prompt('Edit Equipment Name:', equipmentName);
    const newUsable = prompt('Edit Usable Count:', usableCount);
    const newMaxAllow = prompt('Edit Max Allow per Request:', maxAllow);
    
    if (newName && newUsable !== null && newMaxAllow !== null) {
        fetch('/uoc-sports/public/equipment-manager/update-equipment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                equipmentId: equipmentId,
                equipment_name: newName,
                usable_count: parseInt(newUsable),
                max_allow: parseInt(newMaxAllow)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Equipment updated successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error updating equipment');
            console.error('Error:', error);
        });
    }
}

function deleteEquipment(equipmentId, equipmentName) {
    if (confirm('Are you sure you want to delete "' + equipmentName + '"?\n\nNote: Cannot delete if there are active reservations.')) {
        fetch('/uoc-sports/public/equipment-manager/delete-equipment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                equipmentId: equipmentId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Equipment deleted successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error deleting equipment');
            console.error('Error:', error);
        });
    }
}

function viewDetails(equipmentId) {
    fetch('/uoc-sports/public/equipment-manager/equipment-details?id=' + equipmentId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const equipment = data.equipment;
                const reservations = data.reservations || [];
                
                let reservationsHtml = '<h4>Active Reservations:</h4>';
                if (reservations.length > 0) {
                    reservationsHtml += '<table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">';
                    reservationsHtml += '<thead><tr style="background: #f3f4f6;"><th style="padding: 0.5rem; border: 1px solid #e5e7eb;">Date</th><th style="padding: 0.5rem; border: 1px solid #e5e7eb;">Time</th><th style="padding: 0.5rem; border: 1px solid #e5e7eb;">Student</th><th style="padding: 0.5rem; border: 1px solid #e5e7eb;">Purpose</th></tr></thead><tbody>';
                    reservations.forEach(res => {
                        reservationsHtml += `<tr>
                            <td style="padding: 0.5rem; border: 1px solid #e5e7eb;">${res.request_date}</td>
                            <td style="padding: 0.5rem; border: 1px solid #e5e7eb;">${res.start_time} - ${res.end_time}</td>
                            <td style="padding: 0.5rem; border: 1px solid #e5e7eb;">${res.student_name} (${res.student_id})</td>
                            <td style="padding: 0.5rem; border: 1px solid #e5e7eb;">${res.purpose}</td>
                        </tr>`;
                    });
                    reservationsHtml += '</tbody></table>';
                } else {
                    reservationsHtml += '<p style="color: #6b7280; margin-top: 0.5rem;">No active reservations</p>';
                }
                
                const detailsHtml = `
                    <h3 style="margin-top: 0; color: #5e2d91;">${equipment.equipment_name}</h3>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin: 1rem 0;">
                        <p><strong>Equipment ID:</strong> ${equipment.equipment_id}</p>
                        <p><strong>Sport ID:</strong> ${equipment.sport_id}</p>
                        <p><strong>Total Quantity:</strong> ${equipment.total_quantity || 0}</p>
                        <p><strong>Usable Count:</strong> ${equipment.usable_count || 0}</p>
                        <p><strong>Max Allow:</strong> ${equipment.max_allow || 1} per request</p>
                    </div>
                    ${reservationsHtml}
                `;
                
                const modal = document.createElement('div');
                modal.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); z-index: 1000; max-width: 700px; width: 90%; max-height: 80vh; overflow-y: auto;';
                modal.innerHTML = detailsHtml + '<button onclick="this.parentElement.remove(); document.getElementById(\'modal-overlay\').remove();" style="margin-top: 1.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #5e2d91, #4a2370); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Close</button>';
                
                const overlay = document.createElement('div');
                overlay.id = 'modal-overlay';
                overlay.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 999;';
                overlay.onclick = () => { modal.remove(); overlay.remove(); };
                
                document.body.appendChild(overlay);
                document.body.appendChild(modal);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error fetching equipment details');
            console.error('Error:', error);
        });
}

// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('#tableBody tr');
    
    tableRows.forEach(row => {
        const cells = row.getElementsByTagName('td');
        if (cells.length > 0) {
            const equipmentName = cells[0].textContent.toLowerCase();
            const categoryName = cells[1].textContent.toLowerCase();
            const matches = equipmentName.includes(searchValue) || categoryName.includes(searchValue);
            row.style.display = matches ? '' : 'none';
        }
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
    padding: 0.4rem 0.85rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 700;
    display: inline-block;
}

.condition-badge.excellent {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);
}

.condition-badge.good {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
    box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);
}

.condition-badge.fair {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    box-shadow: 0 2px 6px rgba(245, 158, 11, 0.3);
}

.condition-badge.poor {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
    box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3);
}

.btn-view {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);
}

.btn-view:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.5);
    transform: translateY(-2px);
}
</style>

<?php
    require "../app/views/templates/general/footer.php";
?>
</body>
</html>
