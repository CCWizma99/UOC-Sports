<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Equipment Requests</title>
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

<div class="main-wrapper">        
    <div class="page-form-container">

            
            <!-- Add practice Form -->
             <div class="page-header">
                <div>
                    <h2>Reserve Equipment</h2>
                    <p>Manage equipment reservations</p>
                </div>
               
            </div>
            <form id="addBookingForm" class="form" method="POST" action="/uoc-sports/public/equipment-manager/save-booking">
                <div class="form-row">
                    <div class="form-group">
                        <label for="user_id">User ID </label>
                        <input type="text" id="user_id" name="student_id" placeholder="Enter user ID" >    
                    </div>
                    
                    <div class="form-group">
                        <label for="requester_name">Requester Name *</label>
                        <input type="text" id="requester_name" name="requester_name" placeholder="Enter requester name" required>    
                    </div>

                    <div class="form-group">
                        <label for="sport">Sport *</label>
                        <select id="sport" name="sport" required>
                            <option value="">Select Sport</option>
                            <option value="ATH">Athletics</option>
                            <option value="RUG">Rugby</option>
                            <option value="TEN">Tennis</option>
                            <option value="WEI">Weightlifting</option>
                            <option value="BAS">Basketball</option>
                            <option value="CAR">Carrom</option>
                            <option value="SCR">Scrabble</option>
                            <option value="CHE">Chess</option>
                            <option value="FOO">Football</option>
                            <option value="BSB">Baseball</option>
                            <option value="ROW">Rowing</option>
                            <option value="NET">Netball</option>
                            <option value="TEA">Teakwondo</option>
                            <option value="HOC">Hockey</option>
                            <option value="ELL">Elle</option>
                            <option value="CRI">Cricket</option>
                            <option value="KAB">Kabaddi</option>
                            <option value="WRE">Wrestling</option>
                            <option value="BAD">Badminton</option>
                            <option value="TBT">Table Tennis</option>
                            <option value="VOL">Volleyball</option>
                            <option value="BOX">Boxing</option>
                            <option value="KAR">Karate</option>
                            <option value="SWI">Swimming</option>
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Equipment Selection *</label>
                        <div id="equipment-checkboxes" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 10px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
                            <p style="grid-column: 1 / -1; color: #666; margin: 0;">Select a sport first to see available equipment</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reservation_date">Reservation Date *</label>
                        <input type="date" id="reservation_date" name="request_date" required>
                    </div>

                    <div class="form-group">
                        <label for="start_time">Start Time *</label>
                        <input type="time" id="start_time" name="start_time" required>
                    </div>

                    <div class="form-group">
                        <label for="end_time">End Time *</label>
                        <input type="time" id="end_time" name="end_time" required>      
                    </div>

                    <div class="form-group">
                        <label for="reserved_location">Reserved Location</label>
                        <select id="reserved_location" name="reserved_location">
                            <option value="">Select Location</option>
                            <option value="Badminton Court">Badminton Court</option>
                            <option value="Tennis Court">Tennis Court</option>
                            <option value="Baseball Pitch">Baseball Pitch</option>
                            <option value="Cricket Pitch">Cricket Pitch</option>
                            <option value="Football Ground">Football Ground</option>
                            <option value="Basketball Court">Basketball Court</option>
                            <option value="Volleyball Court">Volleyball Court</option>
                            <option value="Swimming Pool">Swimming Pool</option>
                            <option value="Gym">Gym</option>
                            <option value="Ground">Ground</option>
                        </select>
                    </div>



                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Additional notes"></textarea>
   
                    </div>
                    
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-add" onclick="window.location.href='/uoc-sports/public/equipment-manager/bookingrequests'">
                       Cancel
                    </button>
                    <button type="submit" class="btn-add">
                       <i class="fas fa-save"></i> Save Booking
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
document.getElementById('sport').addEventListener('change', function() {
    const sportId = this.value;
    const equipmentContainer = document.getElementById('equipment-checkboxes');
    
    // Clear existing content
    equipmentContainer.innerHTML = '<p style="color: #666; margin: 0;">Loading equipment...</p>';
    
    if (!sportId) {
        equipmentContainer.innerHTML = '<p style="grid-column: 1 / -1; color: #666; margin: 0;">Select a sport first to see available equipment</p>';
        return;
    }
    
    // Fetch equipment from the database
    fetch('/uoc-sports/public/api/get-equipment-by-sport.php?sport_id=' + sportId)
        .then(response => response.json())
        .then(data => {
            equipmentContainer.innerHTML = '';
            
            if (data.success && data.equipment && data.equipment.length > 0) {
                // Populate with equipment from database
                data.equipment.forEach(equipment => {
                    const itemDiv = document.createElement('div');
                    itemDiv.style.cssText = 'display: flex; align-items: center; gap: 10px; padding: 10px; border: 1px solid #e0e0e0; border-radius: 4px; background-color: white;';
                    
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.id = 'eq_' + equipment.equipment_id;
                    checkbox.name = 'equipment[]';
                    checkbox.value = equipment.equipment_name;
                    checkbox.style.cssText = 'width: 18px; height: 18px; cursor: pointer;';
                    
                    const label = document.createElement('label');
                    label.htmlFor = 'eq_' + equipment.equipment_id;
                    label.textContent = equipment.equipment_name + ' (Available: ' + equipment.available_count + ')';
                    label.style.cssText = 'flex: 1; cursor: pointer; font-weight: 500;';
                    
                    const quantityDiv = document.createElement('div');
                    quantityDiv.style.cssText = 'display: flex; align-items: center; gap: 5px;';
                    
                    const quantityLabel = document.createElement('span');
                    quantityLabel.textContent = 'Qty:';
                    quantityLabel.style.cssText = 'font-size: 14px; color: #666;';
                    
                    const quantityInput = document.createElement('input');
                    quantityInput.type = 'number';
                    quantityInput.id = 'qty_' + equipment.equipment_id;
                    quantityInput.name = 'quantity[' + equipment.equipment_name + ']';
                    quantityInput.min = '1';
                    quantityInput.max = equipment.available_count;
                    quantityInput.value = '1';
                    quantityInput.disabled = true;
                    quantityInput.style.cssText = 'width: 70px; padding: 5px; border: 1px solid #ddd; border-radius: 4px;';
                    
                    // Enable/disable quantity input based on checkbox
                    checkbox.addEventListener('change', function() {
                        quantityInput.disabled = !this.checked;
                        if (!this.checked) {
                            quantityInput.value = '1';
                        }
                    });
                    
                    quantityDiv.appendChild(quantityLabel);
                    quantityDiv.appendChild(quantityInput);
                    
                    itemDiv.appendChild(checkbox);
                    itemDiv.appendChild(label);
                    itemDiv.appendChild(quantityDiv);
                    
                    equipmentContainer.appendChild(itemDiv);
                });
            } else {
                // Show message if no equipment available
                const message = document.createElement('p');
                message.style.cssText = 'grid-column: 1 / -1; color: #999; margin: 0; font-style: italic;';
                message.textContent = 'No equipment available for this sport';
                equipmentContainer.appendChild(message);
            }
        })
        .catch(error => {
            console.error('Error fetching equipment:', error);
            equipmentContainer.innerHTML = '<p style="color: #d32f2f; margin: 0;">Error loading equipment. Please try again.</p>';
        });
});

// Form validation to ensure at least one equipment is selected
document.getElementById('addBookingForm').addEventListener('submit', function(e) {
    const checkboxes = document.querySelectorAll('input[name="equipment[]"]:checked');
    if (checkboxes.length === 0) {
        e.preventDefault();
        alert('Please select at least one equipment item');
        return false;
    }
});
</script>

<?php
    require "../app/views/templates/general/footer.php";
?>
</body>
</html>