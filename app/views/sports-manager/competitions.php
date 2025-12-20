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
        <h2>Upcoming Competitions</h2>
        <p>Manage scheduled competitions</p>
      </div>

         
       <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search competition...">
  
    </div>

    <!-- Add Participants Form (Initially Hidden) -->
    <div class="form-container" id="addParticipantsForm">
        <div class="page-header">
            <div>
                <h2>Add Participants</h2>
                <p>Add participants to the competition</p>
            </div>
        </div>
        <form id="participantsForm" class="form" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="sport">Sport *</label>
                    <select id="sport" name="sport" required>
                        <option value="">Select Sport</option>
                        <option value="Athletics">Athletics</option>
                        <option value="Rugby">Rugby</option>
                        <option value="Tennis">Tennis</option>
                        <option value="Weightlifting">Weightlifting</option>
                        <option value="Basketball">Basketball</option>
                        <option value="Carrom">Carrom</option>
                        <option value="Scrabble">Scrabble</option>
                        <option value="Chess">Chess</option>
                        <option value="Football">Football</option>
                        <option value="Baseball">Baseball</option>
                        <option value="Rowing">Rowing</option>
                        <option value="Netball">Netball</option>
                        <option value="Teakwondo">Teakwondo</option>
                        <option value="Hockey">Hockey</option>
                        <option value="Elle">Elle</option>
                        <option value="Cricket">Cricket</option>
                        <option value="Kabaddi">Kabaddi</option>
                        <option value="Wrestling">Wrestling</option>
                        <option value="Badminton">Badminton</option>
                        <option value="Table Tennis">Table Tennis</option>
                        <option value="Volleyball">Volleyball</option>
                        <option value="Boxing">Boxing</option>
                        <option value="Karate">Karate</option>
                        <option value="Swimming">Swimming</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="competitionName">Competition Name *</label>
                    <input type="text" id="competitionName" name="competitionName" placeholder="Enter competition name" required>
                </div>

                <div class="form-group full-width">
                    <label for="participants">Participant Image *</label>
                    <input type="file" id="participants" name="participants" required>
                </div>
            </div>
                    
            <div class="form-actions">
                <button type="button" class="view-all-link" onclick="toggleAddParticipantsForm()">
                   Cancel
                </button>
                <button type="submit" class="view-all-link">
                   Add Participants
                </button>
            </div>
        </form>
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
                    <th onclick="sortTable(5)">Participants Count<span class="sort-indicator"></span></th>
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
                            <button class="btn-action btn-primary" onclick="toggleAddParticipantsForm()">Add Participants</button>
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
                                    <button class="btn-add" onclick="toggleAddParticipantsForm()">Add Participants</button>
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
                                    <button class="btn-add" onclick="toggleAddParticipantsForm()">Add Participants</button>
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
                                    <button class="btn-add" onclick="toggleAddParticipantsForm()">Add Participants</button>
                                </td>
                            </tr>
                           
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>

</div>

<script>
function toggleAddParticipantsForm() {
    const form = document.getElementById('addParticipantsForm');
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        // Scroll to the form
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        form.style.display = 'none';
    }
}
</script>

</body>
</html>

            <?php
    require "../app/views/templates/general/footer.php";
?>