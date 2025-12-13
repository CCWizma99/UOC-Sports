<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Update Transaction</title>
  <style>
      @import url(/uoc-sports/public/css/global.css);
      @import url(/uoc-sports/public/css/general/header.css);
      @import url(/uoc-sports/public/css/sports-manager/sub-nav.css);
      @import url(/uoc-sports/public/css/sports-manager/add-expenses.css);
      @import url(/uoc-sports/public/css/general/footer.css);

      .form-container {
          max-width: 600px;
          margin: 30px auto;
          padding: 20px;
          border: 1px solid #ddd;
          border-radius: 8px;
          background: #fff;
      }
      .form-container h2 { text-align: center; margin-bottom: 20px; }
      .form-container label { display: block; margin-top: 15px; font-weight: bold; }
      .form-container input, .form-container textarea, .form-container select {
          width: 100%;
          padding: 8px;
          margin-top: 5px;
          border: 1px solid #ccc;
          border-radius: 4px;
      }
      .form-container .buttons {
          margin-top: 20px;
          display: flex;
          justify-content: space-between;
      }
      .form-container button {
          padding: 10px 20px;
          border: none;
          border-radius: 4px;
          cursor: pointer;
      }
      .update-btn { background-color: #4CAF50; color: #fff; }
      .reset-btn { background-color: #f44336; color: #fff; }
      #remainingBudget { margin-top: 5px; font-weight: bold; }
  </style>
</head>
<body>

<?php 
require "../app/views/templates/general/header.php";
require "../app/views/sports-manager/header-subnav.php";
require "../app/models/Budget.php";

$budgetModel = new Budget();
$transactionId = $_GET['trn'] ?? '';
$transaction = $budgetModel->getTransactionById($transactionId);

if (!$transaction) {
    echo "<h2>Transaction not found</h2>";
    exit;
}
?>

<div class="form-container">
    <h2>Update Transaction</h2>
    <form action="update-transaction" method="post" enctype="multipart/form-data">
        <input type="hidden" name="transaction_id" value="<?= htmlspecialchars($transaction['transaction_id']) ?>">
        <input type="hidden" name="sport_id" id="sport_id" value="<?= htmlspecialchars($transaction['sport_id']) ?>">
        <input type="hidden" name="budget_id" id="budget_id" value="<?= htmlspecialchars($transaction['budget_id']) ?>">

        <label>Transaction ID</label>
        <input type="text" name="transaction_id" value="<?= htmlspecialchars($transaction['transaction_id']) ?>" disabled>

        <label>Sport</label>
        <input type="text" value="<?= htmlspecialchars($transaction['sport_name']) ?>" disabled>

        <label>Amount</label>
        <input type="number" id="amount" name="amount" min="50" value="<?= htmlspecialchars($transaction['amount']) ?>" required>
        <p id="remainingBudget"></p>

        <label>Purpose</label>
        <input type="text" name="Title" value="<?= htmlspecialchars($transaction['purpose']) ?>" required>

        <label>Remarks</label>
        <textarea name="Remarks" required><?= htmlspecialchars($transaction['remarks'] ?? '') ?></textarea>

        <label>Upload New Receipt (optional)</label>
        <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf">

        <label>Reason for Change</label>
        <textarea name="change_reason" placeholder="Explain why this transaction is being updated..." required></textarea>

        <div class="buttons">
            <button type="reset" class="reset-btn">Reset</button>
            <button type="submit" class="update-btn">Update</button>
        </div>
    </form>
</div>

<?php require "../app/views/templates/general/footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const amountInput = document.getElementById('amount');
    const remainingBudgetEl = document.getElementById('remainingBudget');
    const sport = document.getElementById('sport_id');
    const oldAmount = parseInt(<?= (int)$transaction['amount'] ?>);

    function checkBudgetAvailability() {
        const sport_id = sport.value;
        const newAmount = parseInt(amountInput.value) || 0;

        fetch(`budget/remaining?sid=${sport_id}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const remaining = parseInt(data.remaining_amount, 10) + oldAmount;
                    if (newAmount > remaining) {
                        remainingBudgetEl.textContent = `Not enough budget! Remaining: ${remaining} LKR`;
                        remainingBudgetEl.style.color = 'red';
                    } else {
                        remainingBudgetEl.textContent = `Remaining Budget: ${remaining - newAmount} LKR`;
                        remainingBudgetEl.style.color = 'green';
                    }
                } else {
                    remainingBudgetEl.textContent = 'Remaining Budget: Not available';
                    remainingBudgetEl.style.color = 'gray';
                }
            })
            .catch(err => {
                console.error(err);
                remainingBudgetEl.textContent = 'Remaining Budget: Error fetching';
                remainingBudgetEl.style.color = 'gray';
            });
    }

    amountInput.addEventListener('input', checkBudgetAvailability);

    // Initial check on page load
    checkBudgetAvailability();
});
</script>

</body>
</html>
