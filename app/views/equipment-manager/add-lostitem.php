<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add a New Lost Item</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/general/footer.css");

    @import url("/uoc-sports/public/css/equipment-manager/report.css");
    @import url("/uoc-sports/public/css/equipment-manager/page.css");
    .lostitem-actions {
        display: flex;
        justify-content: center !important;
        gap: 2rem;
        padding-top: 2rem;
        margin-top: 1rem;
    }
  </style>
</head>
<body>
<?php
    require "../app/views/templates/general/header.php";
?>
<div class="main-wrapper">
        
<div class="page-form-container">
    <div class="page-header">
        <div>
            <p class="page-path">Equipment Manager / Lost items / Add Lost Item</p>
            <h2><?php echo $isEdit ? 'Edit Lost Item' : 'Add Lost Item'; ?></h2>
            <p><?php echo $isEdit ? 'Update the lost item details' : 'Fill in the details to add a new lost item record'; ?></p>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div style="padding: 0.75rem; background: #d1fae5; color: #065f46; border-radius: 8px; margin-bottom: 1rem;">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div style="padding: 0.75rem; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 1rem;">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <form id="addLostitemForm" class="form" method="POST" action="/uoc-sports/public/equipment-manager/add-lostitem" enctype="multipart/form-data">
        <?php if ($isEdit && $editData): ?>
            <input type="hidden" name="lostItem_id" value="<?php echo htmlspecialchars($editData['lostItem_id']); ?>">
            <input type="hidden" name="image" value="<?php echo htmlspecialchars($editData['image'] ?? ''); ?>">
        <?php endif; ?>
         <div class="form-row">
        <div class="form-group">
            <label for="item_name">Item Name <span class="required-star">*</span></label>
            <input type="text" id="item_name" name="itemName" value="<?php echo $isEdit && $editData ? htmlspecialchars($editData['itemName']) : ''; ?>" required>    
        </div>  

        <div class="form-group">
            <label for="date_found">Date Found <span class="required-star">*</span></label>
            <input type="date" id="date_found" name="foundDate" value="<?php echo $isEdit && $editData ? htmlspecialchars($editData['foundDate']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="location_found">Location Found <span class="required-star">*</span></label>
            <input type="text" id="location_found" name="foundLocation" value="<?php echo $isEdit && $editData ? htmlspecialchars($editData['foundLocation']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="found_by">Found By <span class="required-star">*</span></label>
            <input type="text" id="found_by" name="foundBy" value="<?php echo $isEdit && $editData ? htmlspecialchars($editData['foundBy']) : ''; ?>" required>      
        </div>
        
        <div class="form-group">
            <label for="contact_number">Contact Number <span class="required-star">*</span></label>
            <input type="text" id="contact_number" name="contactNumber" pattern="[0-9]{10}" value="<?php echo $isEdit && $editData ? htmlspecialchars($editData['contactNumber']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="status">Status <span class="required-star">*</span></label>
            <select id="status" name="itemStatus" required>
                <option value="unclaimed" <?php echo ($isEdit && $editData && $editData['itemStatus'] == 'unclaimed') ? 'selected' : ''; ?>>Unclaimed</option>
                <option value="claimed" <?php echo ($isEdit && $editData && $editData['itemStatus'] == 'claimed') ? 'selected' : ''; ?>>Claimed</option>
                <option value="discarded" <?php echo ($isEdit && $editData && $editData['itemStatus'] == 'discarded') ? 'selected' : ''; ?>>Discarded</option>
            </select>
        </div>

        
        
        <div class="form-group full-width">
            <label for="description">Description </label>
            <textarea id="description" name="description" rows="2" ><?php echo $isEdit && $editData ? htmlspecialchars($editData['description']) : ''; ?></textarea>
        </div>

        
          </div>
        <div class="form-actions">
            <button type="button" class="btn-add" onclick="window.location.href='/uoc-sports/public/equipment-manager/lostitem'">Back</button>
            <button type="submit" class="btn-add"><?php echo $isEdit ? 'Update Item' : 'Add Item'; ?></button>
            
            
        </div>
          
    </form>
</div>
</div>


        <?php
    require "../app/views/templates/general/footer.php";
?>
</body>
</html>