<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Records | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
        
        @import url(/uoc-sports/public/css/admin/budget-page.css);
        @import url(/uoc-sports/public/css/admin/ui-improvements.css);

        .expense-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .btn-back {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            background: var(--primary-purple);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: #3b1a6e;
            transform: translateX(-5px);
        }

        .expense-table-container {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            overflow-x: auto;
        }

        .expense-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .expense-table th {
            padding: 1rem;
            border-bottom: 2px solid #f3f4f6;
            color: #4b5563;
            font-weight: 600;
            background: #f9fafb;
        }

        .expense-table td {
            padding: 1rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .expense-table tr:hover {
            background: #f9fafb;
        }

        .badge-sport {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            background: #f3eaff;
            color: #6a0dad;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .btn-details {
            padding: 0.5rem 1rem;
            background: #f3f4f6;
            color: #1f2937;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .btn-details:hover {
            background: #e5e7eb;
            border-color: #9ca3af;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            padding: 2rem;
            position: relative;
            animation: modalSlide 0.3s ease-out;
            max-height: 90vh;
            overflow-y: auto;
        }

        @keyframes modalSlide {
            from { transform: translateY(-30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .close-modal {
            position: absolute;
            top: 1rem;
            right: 1.5rem;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6b7280;
        }

        .modal-header h2 {
            margin-bottom: 1.5rem;
            color: var(--primary-purple);
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 0.5rem;
        }

        .detail-row {
            display: grid;
            grid-template-columns: 140px 1fr;
            margin-bottom: 1rem;
            gap: 1rem;
        }

        .detail-label {
            font-weight: 600;
            color: #6b7280;
        }

        .detail-value {
            color: #1f2937;
        }

        .receipt-preview {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px dashed #d1d5db;
        }

        .btn-view-receipt {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #5e2d91;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .search-bar {
            margin-bottom: 2rem;
            width: 100%;
            max-width: 400px;
            position: relative;
        }

        .search-bar input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            outline: none;
            transition: all 0.3s;
        }

        .search-bar input:focus {
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 4px rgba(94, 45, 145, 0.1);
        }

        .search-bar i {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }
    </style>
</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';
?>

<div class="main-content-wrapper">
    <div class="expense-container">
        <div class="header-actions">
            <div>
                <h1 style="color: var(--primary-purple); font-size: 1.8rem;">Sport Expense Monitoring</h1>
                <p style="color: #6b7280;">Review all expenditure entries submitted by sport managers</p>
            </div>
            <a href="/uoc-sports/public/admin-budget" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Budget
            </a>
        </div>

        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="expense-search" placeholder="Search by sport or title...">
        </div>

        <div class="expense-table-container">
            <table class="expense-table">
                <thead>
                    <tr>
                        <th>Sport</th>
                        <th>Expense Title</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Submitted By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="expense-body">
                    <?php if (!empty($expenses)): ?>
                        <?php foreach ($expenses as $expense): ?>
                            <tr class="expense-row">
                                <td><span class="badge-sport"><?= htmlspecialchars($expense['sport']) ?></span></td>
                                <td class="searchable-title"><?= htmlspecialchars($expense['expense_title']) ?></td>
                                <td style="font-weight: 600;">Rs. <?= number_format($expense['amount'], 2) ?></td>
                                <td><?= date('Y-m-d', strtotime($expense['updated_at'])) ?></td>
                                <td><?= htmlspecialchars($expense['submitted_by']) ?></td>
                                <td>
                                    <button class="btn-details" 
                                            onclick='showExpenseDetails(<?= json_encode($expense) ?>)'>
                                        <i class="fas fa-eye"></i> Details
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #9ca3af;">
                                <i class="fas fa-inbox fa-3x" style="display: block; margin-bottom: 1rem;"></i>
                                No expense records found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Expense Details Modal -->
<div id="detailsModal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <div class="modal-header">
            <h2>Expense Details</h2>
        </div>
        <div id="modal-details-body">
            <!-- Dynamic Content -->
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('detailsModal');
    const closeModal = document.querySelector('.close-modal');

    function showExpenseDetails(data) {
        const body = document.getElementById('modal-details-body');
        const dateFormatted = new Date(data.updated_at).toLocaleString();
        
        let html = `
            <div class="detail-row">
                <div class="detail-label">Sport</div>
                <div class="detail-value"><span class="badge-sport">${data.sport}</span></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Title</div>
                <div class="detail-value" style="font-weight: 600;">${data.expense_title}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Purpose/Event</div>
                <div class="detail-value">${data.sport_event || '<span style="color: #9ca3af;">Not specified</span>'}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Amount</div>
                <div class="detail-value" style="color: #059669; font-weight: 700;">Rs. ${parseFloat(data.amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Submitted By</div>
                <div class="detail-value">${data.submitted_by}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Recorded On</div>
                <div class="detail-value">${dateFormatted}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Internal ID</div>
                <div class="detail-value">#EXP-${data.expense_id.toString().padStart(4, '0')}</div>
            </div>
        `;

        if (data.receipt) {
            html += `
                <div class="receipt-preview">
                    <div class="detail-label" style="margin-bottom: 0.5rem;">Attached Receipt</div>
                    <a href="/uoc-sports/app/internal/sport_exp_receipt/${data.receipt}" target="_blank" class="btn-view-receipt">
                        <i class="fas fa-file-invoice-dollar"></i> View Receipt File
                    </a>
                </div>
            `;
        }

        body.innerHTML = html;
        modal.style.display = 'flex';
    }

    closeModal.onclick = () => modal.style.display = 'none';
    window.onclick = (event) => {
        if (event.target == modal) modal.style.display = 'none';
    }

    // Search Logic
    document.getElementById('expense-search').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.expense-row');
        
        rows.forEach(row => {
            const sport = row.querySelector('.badge-sport').textContent.toLowerCase();
            const title = row.querySelector('.searchable-title').textContent.toLowerCase();
            
            if (sport.includes(query) || title.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
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
