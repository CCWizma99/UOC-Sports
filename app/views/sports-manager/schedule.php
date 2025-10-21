<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add a team member to the team</title>
  <style>
    @import url("/uoc-sports/public/css/sports-manager/form.css");
</style>
 
  
</head> 
<div class ="header">
   <?php require 'header-nav.php'; 

  ?>
  <div class="header-subnav">
     
       <a href="inbox.php" class="back">Back</a>
      <div class="nav-right">
        <a href="sent.php" class="member">Scheduled </br>Events/Practices</a>
      <a href="drafts.php" class="new">Schedule New +</a>
</div>
</div>
<body>


<form class="form" action="" id ="form" method="post">
    <h2>Schedule a New Practice Session</h2>
    <label>Event/Practice</label>
    <input type="text" name="event" placeholder="Add Name of the Event/Practice" required>

    <label>Selected Sport</label>
    <select name="sport" required>
  
  <option value="cricket">Cricket</option>
  <option value="football">Football</option>
  <option value="badminton">Badminton</option>
</select>

        <label>Scheduled Date</label>
    <input type="date" name="date" placeholder="Select Date" required>



    <div class="buttons">
      <button type="reset" class="reset-btn">Reset</button>
      <button type="submit" class="submit-btn">Submit</button>
    </div>

</form>
</body>
</html>