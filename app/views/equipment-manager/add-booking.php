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
    @import url("/uoc-sports/public/css/general/footer.css");

    @import url("/uoc-sports/public/css/equipment-manager/report.css");
    @import url("/uoc-sports/public/css/equipment-manager/page.css");
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
                    <h2><?= isset($isEdit) && $isEdit ? 'Edit Booking' : 'Reserve Equipment' ?></h2>
                    <p><?= isset($isEdit) && $isEdit ? 'Update equipment reservation' : 'Manage equipment reservations' ?></p>
                </div>
               
            </div>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert-message alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert-message alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <form id="addBookingForm" class="form" method="POST" action="/uoc-sports/public/equipment-manager/save-booking">
                <?php if (isset($isEdit) && $isEdit && isset($editData)): ?>
                    <input type="hidden" name="request_id" value="<?= htmlspecialchars($editData['request_id']) ?>">
                    <input type="hidden" name="status" value="<?= htmlspecialchars($editData['status']) ?>">
                <?php endif; ?>
                <div class="form-row">
                    <div class="form-group">
                        <label for="user_id">User ID/ Student ID/ Staff ID* </label>
                        <input type="text" id="user_id" name="student_id" placeholder="Enter user ID" value="<?= isset($editData) ? htmlspecialchars($editData['student_id'] ?? '') : '' ?>" required>    
                    </div>
                    
                    <div class="form-group">
                        <label for="requester_name">Requester First Name & Last Name </label>
                        <input type="text" id="requester_name" name="requester_name" placeholder="Enter requester name" value="<?= isset($editData) ? htmlspecialchars($editData['requester_name'] ?? '') : '' ?>">    
                    </div>

                    <div class="form-group">
                        <label for="sport">Sport *</label>
                        <select id="sport" name="sport" required>
                            <option value="">Select Sport</option>
                            <?php 
                            $selectedSport = isset($editData) ? $editData['sport_id'] : '';
                            $sports = [
                                'ATH' => 'Athletics', 'RUG' => 'Rugby', 'TEN' => 'Tennis', 'WEI' => 'Weightlifting',
                                'BAS' => 'Basketball', 'CAR' => 'Carrom', 'SCR' => 'Scrabble', 'CHE' => 'Chess',
                                'FOO' => 'Football', 'BSB' => 'Baseball', 'ROW' => 'Rowing', 'NET' => 'Netball',
                                'TEA' => 'Teakwondo', 'HOC' => 'Hockey', 'ELL' => 'Elle', 'CRI' => 'Cricket',
                                'KAB' => 'Kabaddi', 'WRE' => 'Wrestling', 'BAD' => 'Badminton', 'TBT' => 'Table Tennis',
                                'VOL' => 'Volleyball', 'BOX' => 'Boxing', 'KAR' => 'Karate', 'SWI' => 'Swimming'
                            ];
                            foreach ($sports as $code => $name):
                            ?>
                                <option value="<?= $code ?>" <?= $selectedSport === $code ? 'selected' : '' ?>><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="reservation_date">Requested Date *</label>
                        <input type="date" id="reservation_date" name="request_date" value="<?= isset($editData) ? htmlspecialchars($editData['request_date'] ?? '') : '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="start_time">Start Time *</label>
                        <input type="time" id="start_time" name="start_time" value="<?= isset($editData) ? htmlspecialchars($editData['start_time'] ?? '') : '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="end_time">End Time *</label>
                        <input type="time" id="end_time" name="end_time" value="<?= isset($editData) ? htmlspecialchars($editData['end_time'] ?? '') : '' ?>" required>      
                    </div>

                    <div class="form-group equipment-selection-container">
                        <label>Equipment Selection *</label>
                        <div id="equipment-checkboxes">
                            <p class="equipment-empty-message">Select a sport first to see available equipment</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reserved_location">Reserved Location</label>
                        <select id="reserved_location" name="reserved_location">
                            <?php 
                            $selectedLocation = isset($editData) ? $editData['reserved_location'] : '';
                            $locations = ['', 'Badminton Court', 'Tennis Court', 'Baseball Pitch', 'Cricket Pitch', 
                                         'Football Ground', 'Basketball Court', 'Volleyball Court', 'Swimming Pool', 'Gym', 'Ground'];
                            foreach ($locations as $location):
                            ?>
                                <option value="<?= $location ?>" <?= $selectedLocation === $location ? 'selected' : '' ?>>
                                    <?= $location === '' ? 'Select Location' : $location ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>



                    <div class="form-group full-width">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" rows="4" placeholder="Additional notes"><?= isset($editData) ? htmlspecialchars($editData['notes'] ?? '') : '' ?></textarea>
   
                    </div>
                    
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-add" onclick="window.location.href='/uoc-sports/public/equipment-manager/bookingrequests'">
                       Cancel
                    </button>
                    <button type="submit" class="btn-add">
                        <?= isset($isEdit) && $isEdit ? 'Update Booking' : 'Save Booking' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
// Store edit data for equipment pre-selection
const editData = <?= isset($editData) ? json_encode($editData) : 'null' ?>;
const isEdit = <?= isset($isEdit) && $isEdit ? 'true' : 'false' ?>;
const currentRequestId = editData ? editData.request_id : '';

// Auto-fill functionality for User ID and Requester Name
let userLookupTimeout = null;

console.log('Auto-fill functionality loaded');

// Lookup user by ID and fill requester name
document.getElementById('user_id').addEventListener('input', function(e) {
    const userId = e.target.value.trim();
    
    console.log('User ID input changed:', userId);
    
    // Clear previous timeout
    clearTimeout(userLookupTimeout);
    
    if (userId.length === 0) {
        return;
    }
    
    // Debounce the API call (wait 500ms after user stops typing)
    userLookupTimeout = setTimeout(() => {
        console.log('Fetching user by ID:', userId);
        fetch('/uoc-sports/public/api/lookup-user.php?user_id=' + encodeURIComponent(userId))
            .then(response => {
                console.log('Lookup response received');
                return response.json();
            })
            .then(data => {
                console.log('Lookup data:', data);
                if (data.success && data.user) {
                    // Fill the requester name field
                    document.getElementById('requester_name').value = data.user.full_name;
                    console.log('Filled requester name:', data.user.full_name);
                    
                    // Show a subtle indicator that user was found
                    const userIdField = document.getElementById('user_id');
                    userIdField.style.borderColor = '#10b981';
                    setTimeout(() => {
                        userIdField.style.borderColor = '';
                    }, 1000);
                } else {
                    console.log('User not found for ID:', userId);
                    // Clear requester name if user not found
                    // But don't interfere if user is manually entering
                }
            })
            .catch(error => {
                console.error('Error looking up user:', error);
            });
    }, 500);
});

// Lookup user by name and fill user ID
document.getElementById('requester_name').addEventListener('blur', function(e) {
    const requesterName = e.target.value.trim();
    
    console.log('Requester name blur event:', requesterName);
    
    if (requesterName.length === 0) {
        return;
    }
    
    // Only lookup if name contains at least first and last name
    const nameParts = requesterName.split(' ');
    if (nameParts.length < 2) {
        console.log('Name has fewer than 2 parts, skipping lookup');
        return;
    }
    
    console.log('Fetching user by name:', requesterName);
    fetch('/uoc-sports/public/api/lookup-user.php?name=' + encodeURIComponent(requesterName))
        .then(response => {
            console.log('Name lookup response received');
            return response.json();
        })
        .then(data => {
            console.log('Name lookup data:', data);
            if (data.success && data.user) {
                // Fill the user ID field if it's empty
                const userIdField = document.getElementById('user_id');
                if (!userIdField.value.trim()) {
                    userIdField.value = data.user.user_id;
                    console.log('Filled user ID:', data.user.user_id);
                    
                    // Show a subtle indicator that user was found
                    userIdField.style.borderColor = '#10b981';
                    setTimeout(() => {
                        userIdField.style.borderColor = '';
                    }, 1000);
                }
            } else {
                console.log('User not found for name:', requesterName);
                // User not found, but allow proceeding with manual entry
                console.log('User not found in database, proceeding with manual entry');
            }
        })
        .catch(error => {
            console.error('Error looking up user by name:', error);
        });
});

function loadEquipmentBySport(sportId, preSelectItems = null) {
    const equipmentContainer = document.getElementById('equipment-checkboxes');
    
    // Clear existing content
    equipmentContainer.innerHTML = '<p class="equipment-loading">Loading equipment...</p>';
    
    if (!sportId) {
        equipmentContainer.innerHTML = '<p class="equipment-empty-message">Select a sport first to see available equipment</p>';
        return;
    }
    
    // Parse pre-selected items if provided
    let preSelectedMap = {};
    if (preSelectItems && Array.isArray(preSelectItems)) {
        preSelectItems.forEach(item => {
            preSelectedMap[item.equipment_name] = item.quantity;
        });
    }
    
    // Get date and time values for overlap checking
    const requestDate = document.getElementById('reservation_date').value;
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;
    
    // Build API URL with parameters
    let apiUrl = '/uoc-sports/public/api/get-equipment-with-requests.php?sport_id=' + sportId;
    if (requestDate) apiUrl += '&request_date=' + requestDate;
    if (startTime) apiUrl += '&start_time=' + startTime;
    if (endTime) apiUrl += '&end_time=' + endTime;
    if (currentRequestId) apiUrl += '&current_request_id=' + currentRequestId;
    
    // Fetch equipment from the database
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            console.log('API Response:', data);
            equipmentContainer.innerHTML = '';
            
            if (data.success && data.equipment && data.equipment.length > 0) {
                // Populate with equipment from database
                data.equipment.forEach(equipment => {
                    console.log('Equipment item:', equipment.equipment_name, 'Overlapping slots:', equipment.overlapping_slots);
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'equipment-item-card';
                    
                    // Main row with checkbox and label
                    const mainRow = document.createElement('div');
                    mainRow.className = 'equipment-main-row';
                    
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.id = 'eq_' + equipment.equipment_id;
                    checkbox.name = 'equipment[]';
                    checkbox.value = equipment.equipment_name;
                    checkbox.className = 'equipment-checkbox';
                    
                    // Pre-select if in edit mode
                    if (preSelectedMap[equipment.equipment_name]) {
                        checkbox.checked = true;
                        itemDiv.classList.add('selected');
                    }
                    
                    const label = document.createElement('label');
                    label.htmlFor = 'eq_' + equipment.equipment_id;
                    label.textContent = equipment.equipment_name + ' (Usable Count: ' + equipment.available_count + ')';
                    label.className = 'equipment-label';
                    
                    const quantityDiv = document.createElement('div');
                    quantityDiv.className = 'equipment-quantity-section';
                    
                    const quantityLabel = document.createElement('span');
                    quantityLabel.textContent = 'Qty:';
                    quantityLabel.className = 'equipment-quantity-label';
                    
                    const quantityInput = document.createElement('input');
                    quantityInput.type = 'number';
                    quantityInput.id = 'qty_' + equipment.equipment_id;
                    quantityInput.name = 'quantity[' + equipment.equipment_name + ']';
                    quantityInput.min = '1';
                    quantityInput.max = equipment.available_count;
                    quantityInput.value = preSelectedMap[equipment.equipment_name] || '1';
                    quantityInput.disabled = !checkbox.checked;
                    quantityInput.className = 'equipment-quantity-input';
                    
                    // Enable/disable quantity input based on checkbox
                    checkbox.addEventListener('change', function() {
                        quantityInput.disabled = !this.checked;
                        if (this.checked) {
                            itemDiv.classList.add('selected');
                        } else {
                            itemDiv.classList.remove('selected');
                            quantityInput.value = '1';
                        }
                    });
                    
                    quantityDiv.appendChild(quantityLabel);
                    quantityDiv.appendChild(quantityInput);
                    
                    mainRow.appendChild(checkbox);
                    mainRow.appendChild(label);
                    mainRow.appendChild(quantityDiv);
                    
                    itemDiv.appendChild(mainRow);
                    
                    // Show overlapping time slots if any
                    if (equipment.overlapping_slots && equipment.overlapping_slots.length > 0) {
                        console.log('Found overlapping slots for', equipment.equipment_name, ':', equipment.overlapping_slots);
                        const overlapDiv = document.createElement('div');
                        overlapDiv.className = 'overlap-warning';
                        
                        const overlapHeader = document.createElement('div');
                        overlapHeader.className = 'overlap-warning-header';
                        overlapHeader.textContent = '⚠️ Time Slot Conflicts:';
                        overlapDiv.appendChild(overlapHeader);
                        
                        equipment.overlapping_slots.forEach(slot => {
                            console.log('Processing slot:', slot);
                            const slotDiv = document.createElement('div');
                            slotDiv.className = 'overlap-slot-item';
                            
                            const startTime = slot.start_time.substring(0, 5);
                            const endTime = slot.end_time.substring(0, 5);
                            
                            // Check if this is a practice session or booking request
                            if (slot.source_type === 'practice') {
                                console.log('Practice session detected!', slot);
                                slotDiv.innerHTML = `<strong>Practice Session</strong> - ${startTime} - ${endTime} (${slot.status}) - Reserved: ${slot.requested_quantity} (all equipment)`;
                                slotDiv.style.color = '#92400e';
                                slotDiv.style.fontWeight = '500';
                            } else {
                                const requestedQty = slot.requested_quantity || 1;
                                slotDiv.textContent = `${slot.requester_name || 'Booking'} - ${startTime} - ${endTime} (${slot.status}) - Requested: ${requestedQty}`;
                            }
                            
                            overlapDiv.appendChild(slotDiv);
                        });
                        
                        itemDiv.appendChild(overlapDiv);
                    }
                    
                    equipmentContainer.appendChild(itemDiv);
                });
            } else {
                // Show message if no equipment available
                const message = document.createElement('p');
                message.className = 'equipment-empty-message';
                message.textContent = 'No equipment available for this sport';
                equipmentContainer.appendChild(message);
            }
        })
        .catch(error => {
            console.error('Error fetching equipment:', error);
            equipmentContainer.innerHTML = '<p class="equipment-empty-message" style="color: #dc2626;">Error loading equipment. Please try again.</p>';
        });
}

document.getElementById('sport').addEventListener('change', function() {
    loadEquipmentBySport(this.value);
});

// Reload equipment when date or time changes to update overlaps
document.getElementById('reservation_date').addEventListener('change', function() {
    const sportId = document.getElementById('sport').value;
    if (sportId) {
        loadEquipmentBySport(sportId);
    }
});

document.getElementById('start_time').addEventListener('change', function() {
    const sportId = document.getElementById('sport').value;
    if (sportId) {
        loadEquipmentBySport(sportId);
    }
});

document.getElementById('end_time').addEventListener('change', function() {
    const sportId = document.getElementById('sport').value;
    if (sportId) {
        loadEquipmentBySport(sportId);
    }
});

// Load equipment on page load if editing
if (isEdit && editData && editData.sport_id) {
    // Parse equipment items from edit data
    let equipmentItems = [];
    if (editData.equipment_items) {
        try {
            equipmentItems = JSON.parse(editData.equipment_items);
        } catch (e) {
            console.error('Error parsing equipment items:', e);
        }
    }
    
    // Load equipment with pre-selected items
    loadEquipmentBySport(editData.sport_id, equipmentItems);
}

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