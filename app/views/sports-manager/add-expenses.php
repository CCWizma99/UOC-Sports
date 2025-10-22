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
      @import url(/uoc-sports/public/css/sports-manager/view-expenses.css);
      @import url(/uoc-sports/public/css/sports-manager/add-expenses.css);
      @import url(/uoc-sports/public/css/general/footer.css);
    </style>  
</head> 
<body>
<?php 
  require "../app/views/templates/general/header.php";
  require "../app/views/sports-manager/header-subnav.php";
  require "../app/models/SportManager.php";
  require "../app/views/sports-manager/view-expenses.php";

  $sptModel = new SportManager();
  $data = $sptModel->getSports();
  ?>

<form class="form" action="../add-expenses" method="post" enctype="multipart/form-data">
    <h2>Add a New Expense</h2>
    
    <label>Select a Sport</label>
    <select id="sportSelect" name="sport" required style="margin-bottom: 2px;">
  <?php if (!empty($data)): ?>
      <?php foreach ($data as $sport): ?>
          <option value="<?= htmlspecialchars($sport['sport_id']) ?>">
              <?= htmlspecialchars($sport['sport_name']) ?>
          </option>
      <?php endforeach; ?>
  <?php else: ?>
      <option disabled>No sports available</option>
  <?php endif; ?>
</select>
<p id="remainingBudget" style="margin-top:2px; margin-bottom: 8px; font-weight:bold;">
    Remaining Budget: -
</p>
<input type="hidden" name="budget_id" id="budget_id">


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
<script>
const sportSelect = document.getElementById('sportSelect');
const remainingBudget = document.getElementById('remainingBudget');

// Fetch budget on page load for the first sport
if(sportSelect.value) {
    fetchRemainingBudget(sportSelect.value);
}

sportSelect.addEventListener('change', () => {
    const sportId = sportSelect.value;
    fetchRemainingBudget(sportId);
});

function fetchRemainingBudget(sportId) {
    fetch(`../budget/remaining?sid=${sportId}`)
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                remainingBudget.textContent = `Remaining Budget: ${data.remaining_amount} LKR`;
                document.getElementById('budget_id').value = data.budget_id;
            } else {
                remainingBudget.textContent = 'Remaining Budget: Not available';
            }
        })
        .catch(err => {
            console.error(err);
            remainingBudget.textContent = 'Remaining Budget: Error fetching';
        });
}
</script>

</body>
</html>