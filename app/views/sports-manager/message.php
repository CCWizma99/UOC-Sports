<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Send a Message</title>
  	<style>
		@import url("/uoc-sports/public/css/global.css");
		@import url("/uoc-sports/public/css/general/header.css");
		@import url("/uoc-sports/public/css/sports-manager/messages.css");
		@import url("/uoc-sports/public/css/sports-manager/message-records.css");
		@import url("/uoc-sports/public/css/sports-manager/sub-nav.css");
		@import url("/uoc-sports/public/css/general/footer.css");
	</style>  
</head> 
<body>
<?php
    require "../app/views/templates/general/header.php";
    require "../app/views/sports-manager/header-subnav.php";  
?> 
<form class="form" action="" method="post">
    <h2>Send a Message</h2>
    <label>To</label>
<select name="To" required>
    <option value="" disabled selected>Select Recipient</option>
    <option value="captain_1">Nadith Nemal - Captain</option>
    <option value="coach_1">N S Perera - Coach</option>
    <option value="equipment_1">K S Silva - Equipment Manager</option>
</select>


    <label>Title</label>
    <input type="text" name="Title" placeholder="Add Title" required>

    <label>Message</label>
    <textarea name="Message" rows="6" cols="50" placeholder="Add Message ..." required></textarea>

    <div class="buttons">
      <button type="reset" class="reset-btn">Reset</button>
      <button type="submit" class="submit-btn">Submit</button>
    </div>

</form>
<?php 
      require "../app/views/sports-manager/message-records.php";      
      require "../app/views/templates/general/footer.php";      
      ?>
<script>
    var currentPage = document.getElementById("sub-messages");
    currentPage.classList.add("active") 
</script>
</body>
</html>