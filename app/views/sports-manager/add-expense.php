<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sports manager - Add new expense</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/general/footer.css");

    @import url("/uoc-sports/public/css/sports-manager/report.css");
    @import url("/uoc-sports/public/css/sports-manager/page.css");
  </style>
</head>
<body>
<?php
    require "../app/views/templates/general/header.php";
?>
<div class="main-wrapper">
        
        <div class="page-form-container">
            
            <!-- Add practice Form -->
             <div class="page-header">
                <div>
                    <p class="page-path">Sport Manager / Expenses / Add an Expense</p>
                    <h2><?php echo isset($isEdit) && $isEdit ? 'Edit' : 'Add'; ?> Expense</h2>
                    <p>Mange sports expenses</p>
                </div>
               
            </div>
            <?php
            // Get selected sport name from URL parameter
            $selectedSportFromUrl = null;
            if (isset($_GET['sport'])) {
                $db = Database::getConnection();
                $stmt = $db->prepare("SELECT sport_name FROM sport WHERE sport_id = ?");
                $stmt->execute([$_GET['sport']]);
                $sportResult = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($sportResult) {
                    $selectedSportFromUrl = $sportResult['sport_name'];
                }
            }
            ?>
            <form id="addPracticeForm" class="form" method="POST" action="/uoc-sports/public/sport-manager/add-expense<?= isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '' ?>" enctype="multipart/form-data">
                <?php if (isset($isEdit) && $isEdit && isset($editData)): ?>
                    <input type="hidden" name="expense_id" value="<?php echo htmlspecialchars($editData['expense_id']); ?>">
                <?php endif; ?>
                <?php if (isset($_GET['sport'])): ?>
                    <input type="hidden" name="sport_param" value="<?php echo htmlspecialchars($_GET['sport']); ?>">
                <?php endif; ?>

                <?php if (isset($remainingBalance)): ?>
                    <?php if ($remainingBalance === 'unallocated'): ?>
                        <div style="background-color: #f1f5f9; color: #475569; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-weight: 600; display: flex; justify-content: space-between; align-items: center; border: 1px solid #cbd5e1;">
                            <span><i class="fas fa-wallet" style="margin-right: 8px;"></i> Remaining Budget Allocation</span>
                            <span style="font-size: 1.1rem; opacity: 0.8;">No Budget Allocated</span>
                        </div>
                    <?php else: ?>
                        <?php 
                            $balanceColor = '#10b981'; // Green by default
                            $balanceBg = '#d1fae5';
                            if ($remainingBalance <= 0) {
                                $balanceColor = '#ef4444'; // Red
                                $balanceBg = '#fee2e2';
                            } elseif ($remainingBalance < 10000) {
                                $balanceColor = '#f59e0b'; // Yellow
                                $balanceBg = '#fef3c7';
                            }
                        ?>
                        <div style="background-color: <?= $balanceBg ?>; color: <?= $balanceColor ?>; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-weight: 600; display: flex; justify-content: space-between; align-items: center; border: 1px solid <?= $balanceColor ?>;">
                            <span><i class="fas fa-wallet" style="margin-right: 8px;"></i> Remaining Budget Allocation</span>
                            <span style="font-size: 1.1rem;">LKR <?= number_format((float)$remainingBalance, 2) ?></span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="form-row">
                    <div class="form-group">
                        <label for="Sport name">Sport <span class="required-star">*</span></label>
                        <?php 
                        // Priority: editData > selectedSportFromUrl > selectedSportName
                        $sportToSelect = isset($editData['sport']) ? $editData['sport'] : 
                                       (isset($selectedSportFromUrl) ? $selectedSportFromUrl : 
                                       (isset($selectedSportName) ? $selectedSportName : ''));
                        ?>
                        <input type="text" id="sport" name="sport" value="<?php echo htmlspecialchars($sportToSelect); ?>" readonly required>
                    </div>

                    <div class="form-group">
                        <label for="sportEvent">Sports Event <span class="required-star">*</span></label>
                        <select id="sportEvent" name="sportEvent" required>
                            <option value="">Select Event</option>
                            <option value="Practice Session" <?php echo (isset($editData) && $editData['sport_event'] == 'Practice Session') ? 'selected' : ''; ?>>Practice Session</option>
                            <option value="Inter-Faculty Tournament" <?php echo (isset($editData) && $editData['sport_event'] == 'Inter-Faculty Tournament') ? 'selected' : ''; ?>>Inter-Faculty Tournament</option>
                            <option value="Inter-University Tournament" <?php echo (isset($editData) && $editData['sport_event'] == 'Inter-University Tournament') ? 'selected' : ''; ?>>Inter-University Tournament</option>
                            <option value="National Championship" <?php echo (isset($editData) && $editData['sport_event'] == 'National Championship') ? 'selected' : ''; ?>>National Championship</option>

                        </select>
                    </div>

                    <div class="form-group">
                        <label for="expense">Expense Category <span class="required-star">*</span></label>
                        <select id="expense" name="expense" required>
                            <option value="">Select Expense Category</option>                            
                            <option value="Travel & Transportation" <?php echo (isset($editData) && $editData['expense_title'] == 'Travel & Transportation') ? 'selected' : ''; ?>>Travel & Transportation</option>
                            <option value="Meals & Refreshments" <?php echo (isset($editData) && $editData['expense_title'] == 'Meals & Refreshments') ? 'selected' : ''; ?>>Meals & Refreshments</option>
                            <option value="Venue & Facility Rental" <?php echo (isset($editData) && $editData['expense_title'] == 'Venue & Facility Rental') ? 'selected' : ''; ?>>Venue & Facility Rental</option>
                            <option value="Uniforms & Apparel" <?php echo (isset($editData) && $editData['expense_title'] == 'Uniforms & Apparel') ? 'selected' : ''; ?>>Uniforms & Apparel</option>
                            <option value="Medical & First Aid" <?php echo (isset($editData) && $editData['expense_title'] == 'Medical & First Aid') ? 'selected' : ''; ?>>Medical & First Aid</option>
                            <option value="Coaching & Training" <?php echo (isset($editData) && $editData['expense_title'] == 'Coaching & Training') ? 'selected' : ''; ?>>Coaching & Training</option>
                            <option value="Maintenance & Repairs" <?php echo (isset($editData) && $editData['expense_title'] == 'Maintenance & Repairs') ? 'selected' : ''; ?>>Maintenance & Repairs</option>
                            <option value="Other" <?php echo (isset($editData) && $editData['expense_title'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="amount">Amount (Rs) <span class="required-star">*</span></label>
                        <input type="number" id="amount" name="amount" placeholder="Enter amount" step="0.01" min="0" value="<?php echo isset($editData) ? htmlspecialchars($editData['amount']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="receipt">Upload Receipt (PDF) <?php echo (isset($isEdit) && $isEdit) ? '' : '<span class="required-star">*</span>'; ?></label>
                        <input type="file" id="receipt" name="receipt" accept=".pdf,application/pdf" <?php echo (isset($isEdit) && $isEdit) ? '' : 'required'; ?>>
                        <?php if (isset($editData) && !empty($editData['receipt'])): ?>
                            <small>Current: <?php echo htmlspecialchars($editData['receipt']); ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="submittedBy">Submitted By <span class="required-star">*</span></label>
                        <input type="text" id="submittedBy" name="submittedBy" value="<?php echo isset($editData) ? htmlspecialchars($editData['submitted_by']) : (isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''); ?>" readonly required>
                    </div>

                   
                </div>
                        
                <div class="form-actions">
                    <button type="button" onclick="window.location.href='/uoc-sports/public/sport-manager/expenses/<?= isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '' ?>'">
                       Cancel
                    </button>
                    <button type="submit">
                       <?php echo isset($isEdit) && $isEdit ? 'Update' : 'Add'; ?> Expense
                    </button>
                </div>

            </form>
        </div>
</div>
  <?php
    require "../app/views/templates/general/footer.php";
?>
</body>
</html>