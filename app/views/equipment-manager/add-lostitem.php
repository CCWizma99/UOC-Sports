<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Equipment Report</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/equipment-manager/sub-nav.css");
    @import url("/uoc-sports/public/css/general/footer.css");

    @import url("/uoc-sports/public/css/equipment-manager/report.css");
    @import url("/uoc-sports/public/css/equipment-manager/page.css");
  </style>
</head>
<body>
<?php
    require "../app/views/templates/general/header.php";
    require "../app/views/equipment-manager/header-subnav.php";
?>

        
        <div class="form-container">

            
            <!-- Add practice Form -->
             <div class="page-header">
                <div>
                    <h2>Add Found Item</h2>
                    <p>Manage lost and found items.</p>
                </div>
               
            </div>
            <form id="addPracticeForm" class="form" method="POST" enctype="multipart/form-data">
                <div class="form-row">
  <div class="form-group">
                    <label for="item_name">Item Name *</label>
                    <input type="text" id="item_name" name="itemName" required>    
                </div>  

                <div class="form-group">
                    <label for="date_found">Date Found *</label>
                    <input type="date" id="date_found" name="foundDate" required>
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label for="location_found">Location Found *</label>
                    <input type="text" id="location_found" name="foundLocation" required>
                </div>

                <div class="form-group">
                    <label for="found_by">Found By *</label>
                    <input type="text" id="found_by" name="foundBy" required>      
                </div>
                
                <div class="form-group">
                    <label for="contact_number">Contact Number *</label>
                    <input type="text" id="contact_number" name="contactNumber" >
                </div>
                

                        <div class="form-group">
                        <label for="competitionName">Competition Name</label>
                        <input type="text" id="competitionName" name="competitionName" placeholder="Enter competition name" required>
                    </div>

                    <div class="form-group">
                        <label for="item">Upload Found Item *</label>
                        <input type="file" id="item" name="item" required>
                    </div>
                        
                    <div class="form-actions">
                    <button type="button" class="view-all-link" onclick="window.location.href='/uoc-sports/public/equipment-manager/lostitem/'">
                       Back
                    </button>
                    <button type="submit" class="view-all-link">
                       Add Found Item
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