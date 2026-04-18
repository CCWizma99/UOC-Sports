<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Report a New Lost Item</title>
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
    $minLostDate = date('Y-m-d', strtotime('-1 day'));
    $maxLostDate = date('Y-m-d', strtotime('today'));
?>
<div class="main-wrapper">
        
<div class="page-form-container">
    <div class="page-header">
        <div>
            <p class="page-path">Equipment Manager / Lost items / Report Lost Item</p>
            <h2><?php echo $isEdit ? 'Edit Lost Item' : 'Report Lost Item'; ?></h2>
            <p><?php echo $isEdit ? 'Update the lost item details' : 'Fill in the details to report a new lost item'; ?></p>
        </div>
    </div>

    <?php if (isset($_SESSION['lostitem_success_message']) || isset($_SESSION['success_message'])): ?>
        <div style="padding: 0.75rem; background: #d1fae5; color: #065f46; border-radius: 8px; margin-bottom: 1rem;">
            <?php echo htmlspecialchars($_SESSION['lostitem_success_message'] ?? $_SESSION['success_message']); ?>
        </div>
        <?php unset($_SESSION['lostitem_success_message'], $_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['lostitem_error_message']) || isset($_SESSION['error_message'])): ?>
        <div style="padding: 0.75rem; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 1rem;">
            <?php echo htmlspecialchars($_SESSION['lostitem_error_message'] ?? $_SESSION['error_message']); ?>
        </div>
        <?php unset($_SESSION['lostitem_error_message'], $_SESSION['error_message']); ?>
    <?php endif; ?>

    <form id="addLostitemForm" class="form" method="POST" action="/uoc-sports/public/equipment-manager/add-lostitem" enctype="multipart/form-data">
        <?php if ($isEdit && $editData): ?>
            <input type="hidden" name="lostItem_id" value="<?php echo htmlspecialchars($editData['lostItem_id']); ?>">
            <input type="hidden" name="image" value="<?php echo htmlspecialchars($editData['image'] ?? ''); ?>">
        <?php endif; ?>
         <div class="form-row">
        <div class="form-group">
            <label for="item_name">Item Name <span class="required-star">*</span></label>
            <input type="text" id="item_name" name="itemName" placeholder="e.g., Water bottle" value="<?php echo $isEdit && $editData ? htmlspecialchars($editData['item_name']) : ''; ?>" required>    
        </div>  

        <div class="form-group">
            <label for="date_found">Item Lost Date <span class="required-star">*</span></label>
            <input type="date" id="date_found" name="foundDate" min="<?php echo htmlspecialchars($minLostDate); ?>" max="<?php echo htmlspecialchars($maxLostDate); ?>" value="<?php echo $isEdit && $editData ? htmlspecialchars($editData['lost_date']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="found_by">Reported By <span class="required-star">*</span></label>
            <input type="text" id="found_by" name="foundBy" placeholder="Enter reporter name" value="<?php echo $isEdit && $editData ? htmlspecialchars($editData['reported_by']) : ''; ?>" required>      
        </div>
        
        <div class="form-group">
            <label for="contact_number">Contact Number <span class="required-star">*</span></label>
            <input type="text" id="contact_number" name="contactNumber" pattern="[0-9]{10}" placeholder="e.g., 0771234567" value="<?php echo $isEdit && $editData ? htmlspecialchars($editData['contact_number']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="location_found">Item Lost Location <span class="required-star"></span></label>
            <input type="text" id="location_found" name="foundLocation" placeholder="e.g., Main gym entrance" value="<?php echo $isEdit && $editData ? htmlspecialchars($editData['lost_location']) : ''; ?>" >
        </div>

        <div class="form-group">
            <label for="location_found">Item Image <span class="required-star"> *</span></label>
            <input type="file" id="receipt" name="image" accept=".png, .jpg, .jpeg, image/png, imagejpg, image/jpeg" <?php echo (isset($isEdit) && $isEdit) ? '' : 'required'; ?>>
                        <?php if (isset($editData) && !empty($editData['image'])): ?>
                            <small>Current: <?php echo htmlspecialchars($editData['image']); ?></small>
                        <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="status">Status </label>
            <?php $currentStatus = ($isEdit && $editData) ? ($editData['item_status'] ?? $editData['itemStatus'] ?? 'Not Found') : 'Not Found'; ?>
            <select id="status" name="itemStatus" required>
                <option value="Not Found" <?php echo $currentStatus === 'Not Found' ? 'selected' : ''; ?>>Not Found</option>
                <option value="Found" <?php echo $currentStatus === 'Found' ? 'selected' : ''; ?>>Found</option>
                
            </select>
        </div>

        
        
        <div class="form-group full-width">
            <label for="description">Item Description </label>
            <textarea id="description" name="description" rows="2" placeholder="Add color, brand, or any identifying details"><?php echo $isEdit && $editData ? htmlspecialchars($editData['description']) : ''; ?></textarea>
        </div>

        
          </div>
        <div class="form-actions">
            <button type="button" class="btn-add" onclick="window.location.href='/uoc-sports/public/equipment-manager/lostitem'">Back</button>
            <button type="submit" class="btn-add"><?php echo $isEdit ? 'Update Item' : 'Report Lost Item'; ?></button>
            
            
        </div>
          
    </form>
</div>
</div>


        <?php
    require "../app/views/templates/general/footer.php";
?>
</body>
</html>