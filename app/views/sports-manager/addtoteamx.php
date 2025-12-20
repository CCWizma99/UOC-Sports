<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add a team member to the team</title>
  <link rel="stylesheet" type="text/css" href="form.css">
 
  
</head> 
<div class ="header">
   <?php require 'header-nav.php'; 

   ?>
   <div class="header-subnav">
     
       <a href="inbox.php" class="back">Back</a>
      <div class="nav-right">
        <a href="sent.php" class="member">Team Members</a>
      <a href="drafts.php" class="new">Add New +</a>
</div>
</div>
<body>


<form class="form" action="" method="post">
    <h2>Add a New Team Member</h2>
    <label>Student ID</label>
    <input type="text" name="To" placeholder="Add Student ID Number" required>

    <label>Student's E-mail</label>
    <input type="text" name="Title" placeholder="Add a Student's Email" required>

    <label>Student's Name</label>
    <input type="text" name="Name" placeholder="Ex : K A P Silva " required></input>

    <label>Student's NIC Number</label>
    <input type="text" name="NIC" placeholder="Student's NIC Number " required></input>
     
    <label>Student's Contact Number</label>
    <input type="text" name="Contact" placeholder="Student's Contact Number " required></input>


    <label>Selected Sport</label>
    <select name="sport" required>
  
  <option value="cricket">Cricket</option>
  <option value="football">Football</option>
  <option value="badminton">Badminton</option>
</select>

    <div class="buttons">
      <button type="reset" class="reset-btn">Reset</button>
      <button type="submit" class="submit-btn">Submit</button>
    </div>

</form>
</body>

<?php require "../app/views/templates/equipment-manager/footer.php"; ?>
</html>