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
 <div class="header-subnav">
     
       <a href="inbox.php" class="back">Back</a>
      <div class="nav-right">
        <a href="sent.php" class="message">Message</a>
      <a href="drafts.php" class="new">New Message</a>
</div>
</div>
<body>


<form class="form" action="" method="post">
    <h2>Send a Message</h2>
    <label>To</label>
    <input type="text" name="To" placeholder="Add Receivers" required>

    <label>Title</label>
    <input type="text" name="Title" placeholder="Add Title" required>

    <label>Message</label>
    <textarea name="Message" rows="6" cols="50" placeholder="Add Message ..." required></textarea>

    <div class="buttons">
      <button type="reset" class="reset-btn">Reset</button>
      <button type="submit" class="submit-btn">Submit</button>
    </div>

</form>

 <?php require "../app/views/templates/equipment-manager/footer.php"; ?>
</body>
</html>