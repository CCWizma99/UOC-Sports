
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Special Events and Competitions</title>
  <style>
    @import url("/uoc-sports/public/css/sports-manager/record.css");
</style>
</head>
<body>
  <div class="table-container">
    <h2>Events and Schedules Records</h2>

    <table>
      <thead>
        <tr>
          <th>Event Code</th>
          <th>Date</th>
          <th>Event/Competition Name</th>
          <th>Location</th>
          <th>Team Members</th>
          <th>Individual Achievements</th>
          <th>Team Achievements</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
         
        <tr>
          <td>43210</td>
          <td>2025/06/25</td>
          <td>Inter Faculty Meet</td>
          <td>Map</td>
          <td>S001, S002, S003</td>
          <td>S001 won 2nd place in kumite</td>
          <td>Overall Girl's in 2nd place</td>
          
          <td class="action-buttons">
            <button class="edit-btn">Edit</button>
            <button class="delete-btn">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</body>
</html>
<?php require "../app/views/templates/equipment-manager/footer.php"; ?>