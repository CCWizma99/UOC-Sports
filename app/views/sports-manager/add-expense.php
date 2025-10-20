<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Send a Message</title>
  <link rel="stylesheet" type="text/css" href="form.css">
 
  
</head> 
<div class ="header">
   <?php require 'header-nav.php'; 

   ?>

</div>
   <div class="header-subnav">
     
       <a href="inbox.php" class="back">Back</a>
      <div class="nav-right">
        <a href="sent.php" class="message">Expenses</a>
      <a href="drafts.php" class="new">New Expense +</a>
</div>
    
   </div>
<body>


<form class="form" action="" method="post">
    <h2>Add a New Expense</h2>
    
    <label>Selected Sport</label>
    <select name="sport" required>
  
  <option value="cricket">Cricket</option>
  <option value="football">Football</option>
  <option value="badminton">Badminton</option>
</select>


    <label>Add the Sports Manager's ID</label>
    <input type="text" name="ManagerID" placeholder="Add Sports Manager's ID" required>

    <label>Title</label>
    <input type="text" name="Title" placeholder="Add Title" required>

    <label>Amount</label>
    <input type="number" min="50" name="Amount" placeholder="Add Amount" required>
    
    <label for="receipt">Upload Receipt</label>
  <input type="file" id="receipt" name="receipt" accept=".jpg,.jpeg,.png,.pdf" required>
  

    <div class="buttons">
      <button type="reset" class="reset-btn">Reset</button>
      <button type="submit" class="submit-btn">Submit</button>
    </div>

</form>

<?php require "../app/views/templates/equipment-manager/footer.php"; ?>
</body>
</html>