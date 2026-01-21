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
<div class="main-wrapper">
        
        <div class="page-form-container">
            
            <!-- Add practice Form -->
             <div class="page-header">
                <div>
                    <h2><?php echo isset($isEdit) && $isEdit ? 'Edit' : 'Add'; ?> Expense</h2>
                    <p>Mange sports expenses</p>
                </div>
               
            </div>
            <form id="addPracticeForm" class="form" method="POST" action="/uoc-sports/public/sport-manager/add-expense" enctype="multipart/form-data">
                <?php if (isset($isEdit) && $isEdit && isset($editData)): ?>
                    <input type="hidden" name="expense_id" value="<?php echo htmlspecialchars($editData['expense_id']); ?>">
                <?php endif; ?>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="Sport name">Sport *</label>
                        <select id="sport" name="sport" required>
                            <option value="">Select Sport</option>
                            <option value="Athletics" <?php echo (isset($editData) && $editData['sport'] == 'Athletics') ? 'selected' : ''; ?>>Athletics</option>
                            <option value="Rugby" <?php echo (isset($editData) && $editData['sport'] == 'Rugby') ? 'selected' : ''; ?>>Rugby</option>
                            <option value="Tennis" <?php echo (isset($editData) && $editData['sport'] == 'Tennis') ? 'selected' : ''; ?>>Tennis</option>
                            <option value="Weightlifting" <?php echo (isset($editData) && $editData['sport'] == 'Weightlifting') ? 'selected' : ''; ?>>Weightlifting</option>
                            <option value="Basketball" <?php echo (isset($editData) && $editData['sport'] == 'Basketball') ? 'selected' : ''; ?>>Basketball</option>
                            <option value="Carrom" <?php echo (isset($editData) && $editData['sport'] == 'Carrom') ? 'selected' : ''; ?>>Carrom</option>
                            <option value="Scrabble" <?php echo (isset($editData) && $editData['sport'] == 'Scrabble') ? 'selected' : ''; ?>>Scrabble</option>
                            <option value="Chess" <?php echo (isset($editData) && $editData['sport'] == 'Chess') ? 'selected' : ''; ?>>Chess</option>
                            <option value="Football" <?php echo (isset($editData) && $editData['sport'] == 'Football') ? 'selected' : ''; ?>>Football</option>
                            <option value="Baseball" <?php echo (isset($editData) && $editData['sport'] == 'Baseball') ? 'selected' : ''; ?>>Baseball</option>
                            <option value="Rowing" <?php echo (isset($editData) && $editData['sport'] == 'Rowing') ? 'selected' : ''; ?>>Rowing</option>
                            <option value="Netball" <?php echo (isset($editData) && $editData['sport'] == 'Netball') ? 'selected' : ''; ?>>Netball</option>
                            <option value="Teakwondo" <?php echo (isset($editData) && $editData['sport'] == 'Teakwondo') ? 'selected' : ''; ?>>Teakwondo</option>
                            <option value="Hockey" <?php echo (isset($editData) && $editData['sport'] == 'Hockey') ? 'selected' : ''; ?>>Hockey</option>
                            <option value="Elle" <?php echo (isset($editData) && $editData['sport'] == 'Elle') ? 'selected' : ''; ?>>Elle</option>
                            <option value="Cricket" <?php echo (isset($editData) && $editData['sport'] == 'Cricket') ? 'selected' : ''; ?>>Cricket</option>
                            <option value="Kabaddi" <?php echo (isset($editData) && $editData['sport'] == 'Kabaddi') ? 'selected' : ''; ?>>Kabaddi</option>
                            <option value="Wrestling" <?php echo (isset($editData) && $editData['sport'] == 'Wrestling') ? 'selected' : ''; ?>>Wrestling</option>
                            <option value="Badminton" <?php echo (isset($editData) && $editData['sport'] == 'Badminton') ? 'selected' : ''; ?>>Badminton</option>
                            <option value="Table Tennis" <?php echo (isset($editData) && $editData['sport'] == 'Table Tennis') ? 'selected' : ''; ?>>Table Tennis</option>
                            <option value="Volleyball" <?php echo (isset($editData) && $editData['sport'] == 'Volleyball') ? 'selected' : ''; ?>>Volleyball</option>
                            <option value="Boxing" <?php echo (isset($editData) && $editData['sport'] == 'Boxing') ? 'selected' : ''; ?>>Boxing</option>
                            <option value="Karate" <?php echo (isset($editData) && $editData['sport'] == 'Karate') ? 'selected' : ''; ?>>Karate</option>
                            <option value="Swimming" <?php echo (isset($editData) && $editData['sport'] == 'Swimming') ? 'selected' : ''; ?>>Swimming</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="expense">Expense Title *</label>
                        <input type="text" id="expense" name="expense" placeholder="Enter expense purpose" value="<?php echo isset($editData) ? htmlspecialchars($editData['expense_title']) : ''; ?>" required>
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
                        <input type="text" id="submittedBy" name="submittedBy" value="<?php echo isset($editData) ? htmlspecialchars($editData['submitted_by']) : (isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''); ?>" readonly required style="background-color: #f3f4f6; color: #6b7280; cursor: not-allowed;">
                    </div>

                    <div class="form-group full-width">
                        <label for="notes">Special Notes </label>
                        <textarea id="notes" name="notes" placeholder="Enter special notes" rows="3" ><?php echo isset($editData) ? htmlspecialchars($editData['notes']) : ''; ?></textarea>
                    </div>
                </div>
                        
                <div class="form-actions">
                    <button type="button" class="view-all-link" onclick="window.location.href='/uoc-sports/public/sport-manager/expenses/'">
                       Cancel
                    </button>
                    <button type="submit" class="view-all-link">
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