<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add new expense</title>
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
                <div class="form-row">
                    <div class="form-group">
                        <label for="Sport name">Sport *</label>
                        <?php 
                        // Priority: editData > selectedSportFromUrl > selectedSportName
                        $sportToSelect = isset($editData['sport']) ? $editData['sport'] : 
                                       (isset($selectedSportFromUrl) ? $selectedSportFromUrl : 
                                       (isset($selectedSportName) ? $selectedSportName : ''));
                        ?>
                        <input type="text" id="sport" name="sport" value="<?php echo htmlspecialchars($sportToSelect); ?>" readonly required>
                    </div>

                    <div class="form-group">
                        <label for="sportEvent">Sports Event *</label>
                        <select id="sportEvent" name="sportEvent" required>
                            <option value="">Select Event</option>
                            <option value="Practice Session" <?php echo (isset($editData) && $editData['sport_event'] == 'Practice Session') ? 'selected' : ''; ?>>Practice Session</option>
                            <option value="Inter-Faculty Tournament" <?php echo (isset($editData) && $editData['sport_event'] == 'Inter-Faculty Tournament') ? 'selected' : ''; ?>>Inter-Faculty Tournament</option>
                            <option value="Inter-University Tournament" <?php echo (isset($editData) && $editData['sport_event'] == 'Inter-University Tournament') ? 'selected' : ''; ?>>Inter-University Tournament</option>
                            <option value="National Championship" <?php echo (isset($editData) && $editData['sport_event'] == 'National Championship') ? 'selected' : ''; ?>>National Championship</option>

                        </select>
                    </div>

                    <div class="form-group">
                        <label for="expense">Expense Category *</label>
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
                        <label for="amount">Amount (Rs) *</label>
                        <input type="number" id="amount" name="amount" placeholder="Enter amount" step="0.01" min="0" value="<?php echo isset($editData) ? htmlspecialchars($editData['amount']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="receipt">Upload Receipt (PDF) <?php echo (isset($isEdit) && $isEdit) ? '' : '*'; ?></label>
                        <input type="file" id="receipt" name="receipt" accept=".pdf,application/pdf" <?php echo (isset($isEdit) && $isEdit) ? '' : 'required'; ?>>
                        <?php if (isset($editData) && !empty($editData['receipt'])): ?>
                            <small>Current: <?php echo htmlspecialchars($editData['receipt']); ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="submittedBy">Submitted By *</label>
                        <input type="text" id="submittedBy" name="submittedBy" value="<?php echo isset($editData) ? htmlspecialchars($editData['submitted_by']) : (isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''); ?>" readonly required>
                    </div>

                    <div class="form-group full-width">
                        <label for="notes">Special Notes </label>
                        <textarea id="notes" name="notes" placeholder="Enter special notes" rows="4" ><?php echo isset($editData) ? htmlspecialchars($editData['notes']) : ''; ?></textarea>
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