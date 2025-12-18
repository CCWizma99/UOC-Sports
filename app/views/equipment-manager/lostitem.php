<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Equipment Report</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/sports-manager/sub-nav.css");
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
        <h2>Found Items</h2>
        <p>Manage lost and found items</p>
      </div>

     
       <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search Found Items...">
  

   
        <a href="/uoc-sports/public/equipment-manager/add-lostitem">
            <button class="btn-add">
              
            Add Lost Item
            </button>
        </a>
    </div>


    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Item ID<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(1)">Item Name<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(2)">Found Date<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(3)">Description<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(4)">Found Location<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(5)">Found By<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(6)">Contact Number<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(7)">Item Image<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(8)">Status<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(9)">Action<span class="sort-indicator"></span></th>
                </tr>
            </thead>

            <tbody id="tableBody">
            <?php if(!empty($lostitems)): ?>
                <?php foreach($lostitems as $lst): ?>
                    <tr>
                        <td><?= $lst['lostItem_id'] ?></td>
                        <td><?= $lst['itemName'] ?></td>
                        <td><?= $lst['foundDate'] ?></td>
                        <td><?= $lst['description'] ?></td>
                        <td><?= $lst['foundLocation'] ?></td>
                        <td><?= $lst['foundBy'] ?></td>
                        <td><?= $lst['contactNumber'] ?></td>
                        <td>
                            <?php if (!empty($lst['image'])): ?>
                                <img src="/uoc-sports/app/internal/lostitem/<?= $lst['image'] ?>" alt="Item Image" class="image">
                              <?php else: ?>
                                No Image
                              <?php endif; ?>
                        </td>
                        <td>
                            <select class="status-dropdown" onchange="updateStatus('<?= $lst['lostItem_id'] ?>', this.value)">
                                <option value="unclaimed" <?= $lst['status'] === 'unclaimed' ? 'selected' : '' ?>>Unclaimed</option>
                                <option value="claimed" <?= $lst['status'] === 'claimed' ? 'selected' : '' ?>>Claimed</option>
                            </select>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem; justify-content: center; align-items: center;">
                                <button class="btn-edit" onclick="editItem('<?= $lst['lostItem_id'] ?>')">Edit</button>
                                <button class="btn-delete" onclick="deleteItem('<?= $lst['lostItem_id'] ?>', '<?= $lst['itemName'] ?>')">Delete</button>
                            </div>
                        </td>
                     </tr>
                  <?php endforeach; ?>

                    <?php else: ?>
                        <!-- Sample Data -->
                        <tr>
                            <td>LI001</td>
                            <td>Blue Backpack</td>
                            <td>2025-12-15</td>
                            <td>Blue backpack with laptop compartment</td>
                            <td>Main Sports Hall</td>
                            <td>John Doe</td>
                            <td>0771234567</td>
                            <td>No Image</td>
                            <td>
                                <select class="status-dropdown" onchange="updateStatus('LI001', this.value)">
                                    <option value="unclaimed" selected>Unclaimed</option>
                                    <option value="claimed">Claimed</option>
                                </select>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; justify-content: center; align-items: center;">
                                    <button class="btn-edit" onclick="editItem('LI001')">Edit</button>
                                    <button class="btn-delete" onclick="deleteItem('LI001', 'Blue Backpack')">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>LI002</td>
                            <td>Water Bottle</td>
                            <td>2025-12-14</td>
                            <td>Stainless steel water bottle with UOC logo</td>
                            <td>Basketball Court</td>
                            <td>Jane Smith</td>
                            <td>0777654321</td>
                            <td>No Image</td>
                            <td>
                                <select class="status-dropdown" onchange="updateStatus('LI002', this.value)">
                                    <option value="unclaimed">Unclaimed</option>
                                    <option value="claimed" selected>Claimed</option>
                                </select>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; justify-content: center; align-items: center;">
                                    <button class="btn-edit" onclick="editItem('LI002')">Edit</button>
                                    <button class="btn-delete" onclick="deleteItem('LI002', 'Water Bottle')">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>LI003</td>
                            <td>ID Card</td>
                            <td>2025-12-16</td>
                            <td>Student ID card - ID number partially visible</td>
                            <td>Swimming Pool</td>
                            <td>Mike Johnson</td>
                            <td>0769876543</td>
                            <td>No Image</td>
                            <td>
                                <select class="status-dropdown" onchange="updateStatus('LI003', this.value)">
                                    <option value="unclaimed" selected>Unclaimed</option>
                                    <option value="claimed">Claimed</option>
                                </select>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; justify-content: center; align-items: center;">
                                    <button class="btn-edit" onclick="editItem('LI003')">Edit</button>
                                    <button class="btn-delete" onclick="deleteItem('LI003', 'ID Card')">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>LI004</td>
                            <td>Tennis Racket</td>
                            <td>2025-12-13</td>
                            <td>Wilson tennis racket with red grip</td>
                            <td>Tennis Court A</td>
                            <td>Sarah Williams</td>
                            <td>0763456789</td>
                            <td>No Image</td>
                            <td>
                                <select class="status-dropdown" onchange="updateStatus('LI004', this.value)">
                                    <option value="unclaimed" selected>Unclaimed</option>
                                    <option value="claimed">Claimed</option>
                                </select>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; justify-content: center; align-items: center;">
                                    <button class="btn-edit" onclick="editItem('LI004')">Edit</button>
                                    <button class="btn-delete" onclick="deleteItem('LI004', 'Tennis Racket')">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>LI005</td>
                            <td>Gym Towel</td>
                            <td>2025-12-17</td>
                            <td>White gym towel with blue stripes</td>
                            <td>Fitness Center</td>
                            <td>David Brown</td>
                            <td>0756789012</td>
                            <td>No Image</td>
                            <td>
                                <select class="status-dropdown" onchange="updateStatus('LI005', this.value)">
                                    <option value="unclaimed" selected>Unclaimed</option>
                                    <option value="claimed">Claimed</option>
                                </select>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; justify-content: center; align-items: center;">
                                    <button class="btn-edit" onclick="editItem('LI005')">Edit</button>
                                    <button class="btn-delete" onclick="deleteItem('LI005', 'Gym Towel')">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>

            </div>

</body>

<script>
function updateStatus(itemId, newStatus) {
    console.log('Updating item ' + itemId + ' to status: ' + newStatus);
    // Add AJAX call here to update status in database
    alert('Status update functionality will be implemented');
}

function editItem(itemId) {
    window.location.href = '/uoc-sports/public/equipment-manager/add-lostitem?id=' + itemId;
}

function deleteItem(itemId, itemName) {
    if (confirm('Are you sure you want to delete "' + itemName + '"?')) {
        console.log('Deleting item: ' + itemId);
        // Add AJAX call or form submission here to delete from database
        alert('Delete functionality will be implemented');
    }
}
</script>

</html>

            <?php
    require "../app/views/templates/general/footer.php";
?>