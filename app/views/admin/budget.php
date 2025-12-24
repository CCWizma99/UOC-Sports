<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
        
        @import url(/uoc-sports/public/css/admin/budget-page.css);
        @import url(/uoc-sports/public/css/admin/ui-improvements.css);
    </style>
</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';

require_once '../core/Database.php';

$year = '2025';

try {
    $db = Database::getConnection();

    $stmt = $db->prepare("
        SELECT 
            SUM(spent_amount) AS total_spent, 
            SUM(allocated_amount - spent_amount) AS total_remaining
        FROM budget
        WHERE year = ?
    ");
    $stmt->execute([$year]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $result = ['total_spent' => 0, 'total_remaining' => 0];
}
?>

<div class="main-content-wrapper">
    <div class="budget-grid-container">
        <div class="budget-grid-left">
            <!-- Budget Overview -->
            <section id="budget-card" class="bg-theme">
                <h2>Budget Overview</h2>
                <div class="flex y-center graph-content-container">
                    <div class="graph">
                        <canvas id="pieChart" width="200" height="200"></canvas>
                        <div class="red-box">
                            <span></span> Spent Amount
                        </div>
                        <div class="blue-box">
                            <span></span> Remaining Amount
                        </div>
                    </div>
                    <div class="content">
                        <table>
                            <tr>
                                <td>Total Allocated Budget</td>
                                <td>Rs. <?= number_format($result['total_spent'] + $result['total_remaining']) ?></td>
                            </tr>
                            <tr>
                                <td>Total Expenditure</td>
                                <td>Rs. <?= number_format($result['total_spent']) ?></td>
                            </tr>
                            <tr>
                                <td>Total Remaining</td>
                                <td>Rs. <?= number_format($result['total_remaining']) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Search Budget -->
            <section id="search-budget">
                <h2>Search Sport Budget</h2>
                <input type="text" id="sport-inp" placeholder="Type a sport name...">
                <div class="output-div" id="budget-results"></div>
            </section>
        </div>

        <div class="budget-grid-right">
            <!-- Add Budget Form -->
            <form id="budgetForm" novalidate>
                <h2>Add Budget</h2>
                <p class="required-note"><span>*</span> Required fields</p>
                
                <label for="sport">Sport <span class="required">*</span></label>
                <select id="sport" name="sport_id" aria-required="true" required>
                    <option value="">Select Sport</option>
                    <option value="1">Cricket</option>
                    <option value="2">Football</option>
                    <option value="3">Basketball</option>
                </select>

                <label for="year">Year <span class="required">*</span></label>
                <input type="number" id="year" name="year" placeholder="2025" 
                       inputmode="numeric" aria-required="true" required>

                <label for="allocated_amount">Allocated Amount <span class="required">*</span></label>
                <input type="number" id="allocated_amount" name="allocated_amount" placeholder="100000" 
                       inputmode="numeric" aria-required="true" required>

                <label for="spent_amount">Spent Amount</label>
                <input type="number" id="spent_amount" name="spent_amount" placeholder="0" value="0"
                       inputmode="numeric">

                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" placeholder="Enter description"></textarea>

                <button type="submit">Allocate Budget</button>
            </form>
        </div>
    </div>
</div>

<script>
// Pie Chart
const canvas = document.getElementById('pieChart');
const ctx = canvas.getContext('2d');

const data = [
    { label: "Spent", value: <?=$result['total_spent'] ?? 0?>, color: "#000" },
    { label: "Remaining", value: <?=$result['total_remaining'] ?? 0?>, color: "#5e2d91" }
];

const total = data.reduce((sum, d) => sum + d.value, 0);

if (total > 0) {
    let startAngle = 0;
    data.forEach((d) => {
        const sliceAngle = (d.value / total) * 2 * Math.PI;
        ctx.beginPath();
        ctx.moveTo(100, 100);
        ctx.arc(100, 100, 80, startAngle, startAngle + sliceAngle);
        ctx.closePath();
        ctx.fillStyle = d.color;
        ctx.fill();  
        startAngle += sliceAngle;
    });
} else {
    ctx.font = "14px Arial";
    ctx.fillStyle = "#999";
    ctx.textAlign = "center";
    ctx.fillText("No data", 100, 100);
}
</script>

<script>
// Search Budget Script
document.getElementById('sport-inp').addEventListener('input', function() {
    const query = this.value.trim();
    const resultsDiv = document.getElementById('budget-results');

    if (query.length < 2) {
        resultsDiv.innerHTML = '';
        return;
    }

    fetch(`./admin-budget/search-budget?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            resultsDiv.innerHTML = '';
            if (data.status === 'success' && data.data.length > 0) {
                let grouped = {};

                // Group by budget_id
                data.data.forEach(item => {
                    if (!grouped[item.budget_id]) {
                        grouped[item.budget_id] = {
                            sport_name: item.sport_name,
                            allocation_date: item.allocation_date,
                            manager_name: item.manager_name,
                            manager_contact: item.manager_contact,
                            allocated_amount: item.allocated_amount,
                            spent_amount: item.spent_amount,
                            remaining_amount: item.remaining_amount,
                            transactions: []
                        };
                    }
                    if (item.transaction_id) {
                        grouped[item.budget_id].transactions.push(item);
                    }
                });

                for (let id in grouped) {
                    const b = grouped[id];
                    let transactionsHTML = '';

                    if (b.transactions.length > 0) {
                        transactionsHTML = `
                            <table class="transactions">
                                <tr>
                                    <th>ID</th>
                                    <th>Purpose</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                                ${b.transactions.map(t => `
                                    <tr>
                                        <td>${t.transaction_id}</td>
                                        <td>${t.purpose}</td>
                                        <td>Rs. ${t.transaction_amount}</td>
                                        <td>${t.timestamp}</td>
                                    </tr>
                                `).join('')}
                            </table>
                        `;
                    } else {
                        transactionsHTML = '<p>No transactions recorded.</p>';
                    }

                    resultsDiv.innerHTML += `
                        <div class="budget-card">
                            <div class="sport-name">Sport: ${b.sport_name}</div>
                            <div class="allocation-date">Budget Allocated On - ${b.allocation_date}</div>
                            <div class="sport-manager">
                                Sport Manager - ${b.manager_name} (${b.manager_contact})
                            </div>
                            <div class="allocated-budget">Allocated Budget - Rs. ${b.allocated_amount}</div>
                            <div class="total-exp">Expenditure - Rs. ${b.spent_amount}</div>
                            <strong class="total-rem">Remaining Amount - Rs. ${b.remaining_amount}</strong>
                            <div class="transaction-container">
                                <h3>Transactions - ${b.sport_name}</h3>
                                ${transactionsHTML}
                            </div>
                        </div>
                    `;
                }
            } else {
                resultsDiv.innerHTML = '<p>No results found.</p>';
            }
        })
        .catch(err => {
            console.error(err);
            resultsDiv.innerHTML = '<p>Error fetching data.</p>';
        });
});
</script>

<script>
// Add Budget Form Script
document.getElementById('budgetForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch('./admin-budget/add-budget', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.status === 'success') {
            showNotification('Budget allocated successfully!', 'success');
            form.reset();
        } else {
            showNotification(result.message || 'Failed to allocate budget.', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('An error occurred.', 'error');
    }
});
</script>

<?php require '../app/views/templates/admin/footer.php'; ?>

</body>
<script>
    var currentPage = document.getElementById("sidebar-budget");
    currentPage.classList.add("active") 
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</html>
