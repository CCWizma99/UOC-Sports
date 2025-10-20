
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lost Item-Report</title>

  <style>
    @import url("/uoc-sports/public/css/equipment-manager/footer.css");
    @import url("/uoc-sports/public/css/equipment-manager/header.css"); 
    @import url("/uoc-sports/public/css/equipment-manager/report.css");
  </style>
</head>

<!-- header-->
<?php
   require "../app/views/templates/equipment-manager/header.php";
?>

<body>
  <div class="table-container">
    <h2>Lost Item Records</h2>

    <table>
      <thead>
        <tr>
          <th>Item Code</th>
          <th>Item</th>
          <th>Found Location</th>
          <th>Received Person</th>
          <th>Image</th>
          <th>Received Date</th>
          <th>Claimed Date</th>
          <th>Claimed Status</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
        
        <tr>
          <td>43210</td>
          <td>Notebook</td>
          <td>Map</td>
          <td>Nimali</td>
          <td>Image</td>
          <td>2024-12-05</td>
          <td>2024-12-05</td>
          <td>Yes</td>
          <td class="action-buttons">
            <button class="edit-btn">Edit</button>
            <button class="delete-btn">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</body>

<!-- footer-->
<?php
    require "../app/views/templates/equipment-manager/footer.php";
?>

</html>