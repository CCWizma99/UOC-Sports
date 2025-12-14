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
        <h2>Upcoming Competitions</h2>
      </div>

     
       <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search competition...">
  
    </div>


    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Competition ID<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(1)">Competition Name<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(2)">Sport<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(3)">Date<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(4)">Location<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(5)">Participants count<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(6)">Participants<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(7)">Action<span class="sort-indicator"></span></th>

                    
                </tr>
            </thead>

            <tbody id="tableBody">
            <?php if(!empty($Schedules)): ?>
                <?php foreach($Schedules as $sch): ?>
                    <tr>
                        <td><?= $sch['competition_id'] ?></td>
                        <td><?= $sch['competition_name'] ?></td>
                        <td><?= $sch['sport'] ?></td> 
                        <td><?= $sch['date'] ?></td>
                        <td><?= $sch['location'] ?></td>
                        <td><?= $sch['participants_count'] ?></td>
                        <td><?= $sch['participants'] ?></td>
                        <td>
                            <button class="btn-action btn-primary">Add Participants</button>
                        </td>
                     </tr>
                  <?php endforeach; ?>

                    <?php else: ?>
                            <!-- Dummy Data Rows -->
                            <tr>
                                <td>C001</td>
                                <td>Inter-University Basketball Championship</td>
                                <td>Basketball</td>
                                <td>2025-12-20</td>
                                <td>Main Arena</td>
                                <td>12</td>
                                <td>No participants selected</td>
                                <td>
                                    <button class="btn-action btn-primary">Add Participants</button>
                                </td>
                            </tr>
                            <tr>
                                <td>C002</td>
                                <td>Annual Football Tournament</td>
                                <td>Football</td>
                                <td>2025-12-25</td>
                                <td>Stadium Field</td>
                                <td>22</td>
                                <td>No participants selected</td>
                                <td>
                                    <button class="btn-action btn-primary">Add Participants</button>
                                </td>
                            </tr>
                            <tr>
                                <td>C003</td>
                                <td>Tennis Singles Competition</td>
                                <td>Tennis</td>
                                <td>2026-01-05</td>
                                <td>Tennis Court A</td>
                                <td>8</td>
                                <td>No participants selected</td>
                                <td>
                                    <button class="btn-action btn-primary">Add Participants</button>
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