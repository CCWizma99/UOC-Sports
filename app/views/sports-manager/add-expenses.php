<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Send a Message</title>
  <style>
      @import url(/uoc-sports/public/css/global.css);
      @import url(/uoc-sports/public/css/general/header.css);
      @import url(/uoc-sports/public/css/sports-manager/sub-nav.css);
      @import url(/uoc-sports/public/css/sports-manager/add-expenses.css);
      @import url(/uoc-sports/public/css/general/footer.css);
    </style>  
</head> 
<body>
<?php 
  require "../app/views/templates/general/header.php";
  require "../app/views/sports-manager/header-subnav.php";
  ?>

<form class="form" action="" method="post">
    <h2>Add a New Expense</h2>
    
    <label>Selected Sport</label>
    <select name="sport" required>
      <option value="cricket">Cricket</option>
      <option value="football">Football</option>
      <option value="badminton">Badminton</option>
    </select>

    <label>Title</label>
    <input type="text" name="Title" placeholder="Add Title" required>

    <label>Amount</label>
    <input type="number" min="50" name="Amount" placeholder="Add Amount" required>

    <label>Title</label>
    <textarea name="Remarks" placeholder="Any Remarks..." required></textarea>
    
    <label for="receipt">Upload Receipt</label>
    <input type="file" id="receipt" name="receipt" accept=".jpg,.jpeg,.png,.pdf" required>
  

    <div class="buttons">
      <button type="reset" class="reset-btn">Reset</button>
      <button type="submit" class="submit-btn">Submit</button>
    </div>
</form>

<?php require "../app/views/templates/general/footer.php"; ?>
<script>
    var currentPage = document.getElementById("sub-expenses");
    currentPage.classList.add("active") 
</script>
</body>
</html>