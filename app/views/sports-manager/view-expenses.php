<?php 
require "../app/models/Budget.php";

$budgetModel = new Budget();
$transactions = $budgetModel->getAllTransactions(); // create this method to fetch all transactions
?>

<div class="container">
  <h2>Transactions</h2>
  <table>
    <thead>
      <tr>
        <th>Transaction ID</th>
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
        <td><?= htmlspecialchars($trn['transaction_id']) ?></td>
        <td><?= htmlspecialchars($trn['sport_name']) ?></td>
        <td><?= number_format($trn['amount'],0) ?> LKR</td>
        <td><?= htmlspecialchars($trn['purpose']) ?></td>
        <td><?= date('Y-m-d', strtotime($trn['timestamp'])) ?></td>
        <td>
          <button class="show-btn" data-trn='<?= json_encode($trn) ?>'>Show</button>
          <a href="../update-transaction?trn=<?= $trn['transaction_id'] ?>"><button class="update-btn">Update</button></a>
          <a href="../delete-transaction?trn=<?= $trn['transaction_id'] ?>" onclick="return confirm('Are you sure?')"><button class="delete-btn">Delete</button></a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Modal -->
<div id="modal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Transaction Details</h3>
    <p><strong>Transaction ID:</strong> <span id="modalTrnId"></span></p>
    <p><strong>Sport:</strong> <span id="modalSport"></span></p>
    <p><strong>Amount:</strong> <span id="modalAmount"></span></p>
    <p><strong>Purpose:</strong> <span id="modalPurpose"></span></p>
    <p><strong>Remarks:</strong> <span id="modalRemarks"></span></p>
    <p><strong>Date:</strong> <span id="modalDate"></span></p>
    <p><strong>Receipt:</strong></p>
    <img id="modalReceipt" src="" alt="Receipt">
  </div>
</div>

<script>
const modal = document.getElementById('modal');
const closeBtn = document.querySelector('.close');

document.querySelectorAll('.show-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const trn = JSON.parse(btn.getAttribute('data-trn'));
        document.getElementById('modalTrnId').textContent = trn.transaction_id;
        document.getElementById('modalSport').textContent = trn.sport_name;
        document.getElementById('modalAmount').textContent = Number(trn.amount).toLocaleString() + ' LKR';
        document.getElementById('modalPurpose').textContent = trn.purpose;
        document.getElementById('modalRemarks').textContent = trn.remarks || '-';
        document.getElementById('modalDate').textContent = trn.timestamp.split(' ')[0];
        document.getElementById('modalReceipt').src = '/uoc-sports/app/internal/transactions/' + trn.proof_doc;
        modal.style.display = 'block';
    });
});

closeBtn.addEventListener('click', () => modal.style.display = 'none');
window.addEventListener('click', e => { if(e.target == modal) modal.style.display = 'none'; });
</script>
