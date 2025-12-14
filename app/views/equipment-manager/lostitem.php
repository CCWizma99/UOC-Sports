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

    <div class="report-header">
        <h2>Found Items</h2>
      </div>

     
       <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search Found Items...">
  

   
        <a href="/project/uoc-sports/app/views/equipment-manager/add-lostitem.php">
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
                        <td><?= $lst['status'] ?></td>
                        <td> <form action="/uoc-sports/app/views/equipment-manager/delete-l.php" method="POST">
                            <input type="hidden" name="lostItem_id" value="<?= $lst['lostItem_id'] ?>">
                            <?php if ($lst['status'] === 'unclaimed'): ?>
                                <button type="submit" class="btn-claim">Mark as Claimed</button>
                            <?php else: ?>
                                <button type="button" class="btn-claimed" disabled>Already Claimed</button>
                            <?php endif; ?>

                            <a href="/project/uoc-sports/app/views/equipment-manager/add-lostitem.php?id=<?php echo $lst['lostItem_id']; ?>" class="btn-edit">Edit</a>
                        </form></td>
                     </tr>
                  <?php endforeach; ?>

                    <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 2rem; color: #6b7280;">
                                    No lost items found. <a href="/uoc-sports/public/equipment-manager/add-lostitem.php">Add your first item</a>
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>

            </div>

</body>
</html>

            <?php
    require "../app/views/templates/general/footer.php";
?>