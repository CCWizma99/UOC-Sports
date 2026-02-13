<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>sport Manager - expense management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/sports-manager/sub-nav.css");
    @import url("/uoc-sports/public/css/general/footer.css");

    @import url("/uoc-sports/public/css/sports-manager/report.css");
  </style>
</head>
<body>
<?php
    require "../app/views/templates/general/header.php";
    require "../app/views/sports-manager/header-subnav.php";
?>
<div class="page-container">

    <div class="container-header">
        <h2>Expenses</h2>
        <p>Manage expenses of the sport</p>
      </div>

      <?php if (isset($_SESSION['success_message'])): ?>
        <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin: 1rem 2rem; border-left: 4px solid #10b981;">
            <strong>Success!</strong> <?php echo htmlspecialchars($_SESSION['success_message']); ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin: 1rem 2rem; border-left: 4px solid #dc2626;">
            <strong>Error!</strong> <?php echo htmlspecialchars($_SESSION['error_message']); ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

     
       <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search Expenses...">
  
                <a href="/uoc-sports/public/sport-manager/add-expense<?= isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '' ?>">
            <button class="view-all-link">
              
            Add New Expense 
            </button>
        </a>
    </div>


    <div class="data-table">
        <table>
            <thead>
                <tr>
                    
                    
                    <th onclick="sortTable(3)">Expense Title<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(3)">Date & Time<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(4)">Amount (Rs) <span class="sort-indicator"></span></th>
                    <th onclick="sortTable(5)">Receipt<span class="sort-indicator"></span></th>


                    
                </tr>
            </thead>

            <tbody id="tableBody">
            <?php if(!empty($expenses)): ?>
                <?php foreach($expenses as $expense): ?>
                    <tr>

                       
                        <td><?= htmlspecialchars($expense['expense_title']) ?></td>
                        <td><?= htmlspecialchars($expense['expense_date']) ?></td>
                        <td>Rs <?= number_format($expense['amount'], 2) ?></td>

                        <td>
                            <?php if (!empty($expense['receipt'])): ?>
                                <a href="/uoc-sports/app/internal/sport_exp_receipt/<?= htmlspecialchars($expense['receipt']) ?>" target="_blank" class="btn-view">
                                    
                                    View Receipt
                                </a>
                            <?php else: ?>
                                <span>No receipt</span>
                            <?php endif; ?>
                        </td>
                     </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem; color: #6b7280;">No expenses found.</td>
                    </tr>
                <?php endif; ?>

                    </tbody>
                </table>
            </div>

            </div>

</body>

<script>
// Search functionality - case insensitive, searches by first letter of expense title
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase(); // Case insensitive
    const tableBody = document.getElementById('tableBody');
    const rows = tableBody.getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        
        if (cells.length > 0) {
            // Get the expense title (first column, index 0)
            const expenseTitle = (cells[0].textContent || cells[0].innerText).trim().toLowerCase();
            
            // Check if expense title starts with the search term (case-insensitive)
            if (expenseTitle.startsWith(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }
});
</script>

</html>

            <?php
    require "../app/views/templates/general/footer.php";
?>