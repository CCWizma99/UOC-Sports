<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lost Item Report</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/general/footer.css");

    @import url("/uoc-sports/public/css/equipment-manager/report.css");
      
  </style>
  <script src="/uoc-sports/public/js/equipment-manager/page.js" defer></script>
</head>
<body>
<?php
    require "../app/views/templates/general/header.php";
?>
<div class="report-container">

    <div class="container-header">
        <h2>Reported Lost Items</h2>
        <p>Manage reported lost items</p>
      </div>

            <?php if (isset($_SESSION['lostitem_success_message']) || isset($_SESSION['success_message'])): ?>
                <div class="alert-message alert-success" style="margin-bottom: 1rem;">
                        <i class="fas fa-check-circle"></i>
                        <?= htmlspecialchars($_SESSION['lostitem_success_message'] ?? $_SESSION['success_message']) ?>
                </div>
                <?php unset($_SESSION['lostitem_success_message'], $_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['lostitem_error_message']) || isset($_SESSION['error_message'])): ?>
                <div class="alert-message alert-error" style="margin-bottom: 1rem;">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($_SESSION['lostitem_error_message'] ?? $_SESSION['error_message']) ?>
                </div>
                <?php unset($_SESSION['lostitem_error_message'], $_SESSION['error_message']); ?>
            <?php endif; ?>

     
       <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search Reported Lost Items...">
  

   
        <a href="/uoc-sports/public/equipment-manager/add-lostitem">
            <button class="btn-add">
             <i class="fas fa-plus"></i> 
            Report Lost Item
            </button>
        </a>
    </div>


    <div class="data-table">
        <table>
            <thead>
                <tr>
                    
                    <th onclick="sortTable(1)">Item Name<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(2)">Item Lost Date<span class="sort-indicator"></span></th>
                    
                    
                    <th onclick="sortTable(3)">Reported By<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(4)">Contact Number<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(5)">Item Lost Location<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(6)">Item Image<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(7)">Item Description<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(8)">Status<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(9)">Action<span class="sort-indicator"></span></th>
                </tr>
            </thead>

            <tbody id="tableBody">
            <?php if(!empty($lostitems)): ?>
                <?php foreach($lostitems as $lst): ?>
                    <tr>
                        
                        <td><?= $lst['item_name'] ?></td>
                        <td><?= $lst['lost_date'] ?></td>
                        <td><?= $lst['reported_by'] ?></td>
                        <td><?= $lst['contact_number'] ?></td>
                        
                        <td><?= $lst['lost_location'] ?></td>
                        
                        <td>
                            <?php if (!empty($lst['image'])): ?>
                                <img src="/uoc-sports/app/internal/lostitem/<?= htmlspecialchars($lst['image']) ?>" alt="Item Image" class="image" style="width:80px; height:100px;">
                              <?php else: ?>
                                No Image
                              <?php endif; ?>
                        </td>
                        <td><?= $lst['description'] ?></td>
                        <td>
                            <select class="status-dropdown" onchange="updateStatus('<?= $lst['lostItem_id'] ?>', this.value)">
                                <option value="unclaimed" <?= $lst['item_status'] === 'unclaimed' ? 'selected' : '' ?>>Unclaimed</option>
                                <option value="claimed" <?= $lst['item_status'] === 'claimed' ? 'selected' : '' ?>>Claimed</option>
                            </select>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem; justify-content: center; align-items: center;">
                                <button class="btn-edit" onclick="editItem('<?= $lst['lostItem_id'] ?>')">Edit</button>
                                <button class="btn-delete" onclick="deleteItem('<?= $lst['lostItem_id'] ?>', '<?= $lst['item_name'] ?>')">Delete</button>
                            </div>
                        </td>
                     </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 2rem;">No lost items found</td>
                    </tr>
                <?php endif; ?>
                    </tbody>
                </table>
            </div>

            </div>

<script>
function updateStatus(itemId, newStatus) {
    if (!confirm('Are you sure you want to update the status?')) {
        return;
    }
    
    fetch('/uoc-sports/public/equipment-manager/update-lostitem-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            itemId: itemId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Status updated successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the status.');
    });
}

function editItem(itemId) {
    window.location.href = '/uoc-sports/public/equipment-manager/add-lostitem?id=' + itemId;
}

function deleteItem(itemId, itemName) {
    if (!confirm('Are you sure you want to delete "' + itemName + '"? This action cannot be undone.')) {
        return;
    }
    
    fetch('/uoc-sports/public/equipment-manager/delete-lostitem', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            itemId: itemId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Item deleted successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the item.');
    });
}
</script>

</body>

</html>

            <?php
    require "../app/views/templates/general/footer.php";
?>