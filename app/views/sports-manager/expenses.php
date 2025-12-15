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

    @import url("/uoc-sports/public/css/sports-manager/report.css");
  </style>
</head>
<body>
<?php
    require "../app/views/templates/general/header.php";
    require "../app/views/sports-manager/header-subnav.php";
?>
<div class="page-container">

    <div class="container-header">
        <h2>Expenses</h2>
        <p>Manage expenses of the sport</p>
      </div>

     
       <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search Expenses...">
  
                <a href="/uoc-sports/public/sport-manager/add-expense">
            <button class="view-all-link">
              
            Add New Expense 
            </button>
        </a>
    </div>


    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Expense ID<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(1)">Sport's Manger<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(2)">Sport<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(3)">Date<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(4)">Amount<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(5)">Receipt<span class="sort-indicator"></span></th>


                    
                </tr>
            </thead>

            <tbody id="tableBody">
            <?php if(!empty($Schedules)): ?>
                <?php foreach($Schedules as $sch): ?>
                    <tr>
                        <td><?= $sch['expense_id'] ?></td>
                        <td><?= $sch['sports_manager'] ?></td>
                        <td><?= $sch['sport'] ?></td> 
                        <td><?= $sch['date'] ?></td>
                        <td><?= $sch['amount'] ?></td>
                        <td><?= $sch['receipt'] ?></td>
                       
                     </tr>
                  <?php endforeach; ?>

                    <?php else: ?>
                            <!-- Dummy Data Rows -->
                            <tr>
                                <td>C001</td>
                                <td>K P Silva</td>
                              
                                <td>Basketball</td>
                                <td>2025-12-20</td>
                                <td>1200.00</td>
                                <td>receipt1.jpg</td>
                            </tr>
                            <tr>
                                <td>C002</td>
                                <td>K P Silva</td>
                                <td>Football</td>
                                <td>2025-12-25</td>
                                <td>1200.00</td>
                                <td>receipt2.jpg</td>
                                
                            </tr>
                            <tr>
                                <td>C003</td>
                                <td>K P Silva</td>
                                <td>Tennis</td>
                                <td>2026-01-05</td>
                                <td>5000.00</td>
                                <td>receipt3.jpg</td>
                               
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