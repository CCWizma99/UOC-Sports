<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Transactions</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
      @import url(/uoc-sports/public/css/global.css);
      @import url(/uoc-sports/public/css/sports-manager/expenses.css);
      @import url(/uoc-sports/public/css/general/header.css);
      @import url(/uoc-sports/public/css/sports-manager/sub-nav.css);
      @import url(/uoc-sports/public/css/general/footer.css);
    </style>  
</head> 
<body>
<?php 
  require "../app/views/templates/general/header.php";
  require "../app/views/sports-manager/header-subnav.php";
  require "../app/models/SportManager.php";

  $sptModel = new SportManager();
  $data = $sptModel->getSports();
  ?>
<?php 
require "../app/models/Budget.php";

$budgetModel = new Budget();
$transactions = $budgetModel->getAllTransactions(); // create this method to fetch all transactions
?>


<div class="main-content">
  <!-- Page Header -->
  <div class="page-header">
    <h1>Transactions</h1>
    <p>Manage and track all your sports expense transactions</p>
  </div>

  <!-- Content Grid -->
  <div class="content-grid">
    <!-- Transactions Section -->
    <div class="transactions-section">
      <div class="section-header">
        <h2>Recent Transactions</h2>
        <div class="section-badge"><?= count($transactions) ?> Total</div>
      </div>

      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Sport</th>
              <th>Amount</th>
              <th>Purpose</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($transactions as $trn): ?>
            <tr>
              <td><span class="trn-id"><?= htmlspecialchars($trn['transaction_id']) ?></span></td>
              <td><span class="sport-badge"><?= htmlspecialchars($trn['sport_name']) ?></span></td>
              <td><span class="amount">₨<?= number_format($trn['amount'],0) ?></span></td>
              <td><span class="purpose-text"><?= htmlspecialchars($trn['purpose']) ?></span></td>
              <td><span class="date-cell"><?= date('M d, Y', strtotime($trn['timestamp'])) ?></span></td>
              <td>
                <div class="action-buttons">
                  <button class="btn-small btn-show" data-trn='<?= json_encode($trn) ?>'>View</button>
                  <a href="../update-transaction?trn=<?= $trn['transaction_id'] ?>"><button class="btn-small btn-update">Update</button></a>
                  <a href="../delete-transaction?trn=<?= $trn['transaction_id'] ?>" onclick="return confirm('Are you sure?')"><button class="btn-small btn-delete">Remove</button></a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add Expense Form Section -->
    <div class="form-section">
      <h2>➕ Add Expense</h2>
      <form class="form" action="../add-expenses" method="post" enctype="multipart/form-data">
        
        <div class="form-group">
          <label>Select Sport</label>
          <select id="sportSelect" name="sport" required>
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
        </div>

        <div class="budget-info">
          <strong>Remaining Budget:</strong><br>
          <span id="remainingBudget">-</span>
        </div>
        <input type="hidden" name="budget_id" id="budget_id">

        <div class="form-group">
          <label>Expense Title</label>
          <input type="text" name="Title" placeholder="e.g., Cricket Bats Purchase" required>
        </div>

        <div class="form-group">
          <label>Amount (LKR)</label>
          <input type="number" min="50" name="Amount" placeholder="e.g., 5000" required>
        </div>

        <div class="form-group">
          <label>Remarks</label>
          <textarea name="Remarks" placeholder="Add any additional notes or comments..." required></textarea>
        </div>

        <div class="form-group">
          <label>Receipt (JPG, PNG, PDF)</label>
          <div class="file-input-wrapper">
            <input type="file" id="receipt" name="receipt" accept=".jpg,.jpeg,.png,.pdf" required>
            <label for="receipt" class="file-input-label">📎 Choose File or Drag & Drop</label>
          </div>
        </div>

        <div class="form-buttons">
          <button type="reset" class="btn-reset">Reset</button>
          <button type="submit" class="btn-submit">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal -->
<div id="modal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Transaction Details</h3>
      <span class="close">&times;</span>
    </div>
    <div class="modal-body">
      <div class="modal-item">
        <strong>Transaction ID</strong>
        <span id="modalTrnId">-</span>
      </div>
      <div class="modal-item">
        <strong>Sport</strong>
        <span id="modalSport">-</span>
      </div>
      <div class="modal-item">
        <strong>Amount</strong>
        <span id="modalAmount">-</span>
      </div>
      <div class="modal-item">
        <strong>Purpose</strong>
        <span id="modalPurpose">-</span>
      </div>
      <div class="modal-item">
        <strong>Remarks</strong>
        <span id="modalRemarks">-</span>
      </div>
      <div class="modal-item">
        <strong>Date</strong>
        <span id="modalDate">-</span>
      </div>
    </div>
    <p style="margin-bottom: 15px; font-weight: 600; color: #2d3748;">Receipt</p>
    <img id="modalReceipt" src="" alt="Receipt">
  </div>
</div>

<?php require "../app/views/templates/general/footer.php"; ?>

<script>
const modal = document.getElementById('modal');
const closeBtn = document.querySelector('.close');

document.querySelectorAll('.btn-show').forEach(btn => {
    btn.addEventListener('click', () => {
        const trn = JSON.parse(btn.getAttribute('data-trn'));
        document.getElementById('modalTrnId').textContent = trn.transaction_id;
        document.getElementById('modalSport').textContent = trn.sport_name;
        document.getElementById('modalAmount').textContent = '₨' + Number(trn.amount).toLocaleString();
        document.getElementById('modalPurpose').textContent = trn.purpose;
        document.getElementById('modalRemarks').textContent = trn.remarks || '-';
        document.getElementById('modalDate').textContent = trn.timestamp.split(' ')[0];
        document.getElementById('modalReceipt').src = '/uoc-sports/app/internal/transactions/' + trn.proof_doc;
        modal.style.display = 'block';
    });
});

closeBtn.addEventListener('click', () => modal.style.display = 'none');
window.addEventListener('click', e => { if(e.target == modal) modal.style.display = 'none'; });

const sportSelect = document.getElementById('sportSelect');
const remainingBudget = document.getElementById('remainingBudget');

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
                remainingBudget.textContent = `₨${Number(data.remaining_amount).toLocaleString()}`;
                document.getElementById('budget_id').value = data.budget_id;
            } else {
                remainingBudget.textContent = 'Not available';
            }
        })
        .catch(err => {
            console.error(err);
            remainingBudget.textContent = 'Error fetching';
        });
}

// Mark current page as active
var currentPage = document.getElementById("sub-expenses");
if(currentPage) currentPage.classList.add("active");
</script>


</body>
</html>