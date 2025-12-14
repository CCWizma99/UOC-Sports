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
    require "../app/views/sports-manager/header-subnav.php";
?>
<div class="report-container">

    <div class="report-header">
        <h2>Practice Sessions</h2>
      </div>

     
       <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search Requests...">
  

   
        <a href="/uoc-sports/public/sport-manager/add-practice">
            <button class="btn-add">
              
            Add New Practice Session 
            </button>
        </a>
    </div>


    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Booking ID<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(1)">User ID<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(2)">Sport ID<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(3)">Date<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(4)">Practice start time<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(5)">Practice end time<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(6)">Location<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(7)">Action<span class="sort-indicator"></span></th>

                    
                </tr>
            </thead>

            <tbody id="tableBody">
            <?php if(!empty($Schedules)): ?>
                <?php foreach($Schedules as $sch): ?>
                    <tr>
                        <td><?= $sch['booking_id'] ?></td>
                        <td><?= $sch['user_id'] ?></td>
                        <td><?= $sch['facility_id'] ?></td>
                        <td><?= $sch['date'] ?></td>
                        <td><?= $sch['slot'] ?></td>
                        <td><?= $sch['purpose'] ?></td>
                        <td><?= $sch['status'] ?></td>
                        <td><?= $sch['payment'] ?></td>
                        <td><?= $sch['action'] ?></td>
              
                     </tr>
                  <?php endforeach; ?>

                    <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 2rem; color: #6b7280;">
                                    No practices Scheduled. <a href="/uoc-sports/public/sports-manager/add-practice.php">Add your first practice session</a>
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