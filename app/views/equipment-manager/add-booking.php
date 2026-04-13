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
                    <p class="page-path">Equipment Manager / Equipment Resevation / Add Booking</p>
                    <h2><?= isset($isEdit) && $isEdit ? 'Edit Booking' : 'Reserve an Equipment' ?></h2>
                    <p><?= isset($isEdit) && $isEdit ? 'Update equipment reservation' : 'Manage equipment reservations' ?></p>
                </div>
               
            </div>

            <div id="activeReservationWarning" class="alert-message alert-error" style="display:none;">
                <i class="fas fa-exclamation-circle"></i> <span id="activeReservationWarningText"></span>
            </div>

            <div id="instantBookingError" class="alert-message alert-error" style="display:none;">
                <i class="fas fa-exclamation-circle"></i> <span id="instantBookingErrorText"></span>
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
                        <label for="user_id">User ID/ Student ID/ Staff ID <span class="required-star">*</span></label>
                        <input type="text" id="user_id" name="student_id" placeholder="Enter user ID" value="<?= isset($editData) ? htmlspecialchars($editData['student_id'] ?? '') : '' ?>" required>    
                    </div>
                    
                    <div class="form-group">
                        <label for="requester_name">Requester First Name & Last Name </label>
                        <input type="text" id="requester_name" name="requester_name" placeholder="Enter requester name" value="<?= isset($editData) ? htmlspecialchars($editData['requester_name'] ?? '') : '' ?>">    
                    </div>

                    <div class="form-group">
                        <label for="sport">Sport <span class="required-star">*</span></label>
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
                        <label for="reservation_date">Requested Date <span class="required-star">*</span></label>
                        <input type="date" id="reservation_date" name="request_date" value="<?= isset($editData) ? htmlspecialchars($editData['request_date'] ?? '') : '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="start_time">Start Time <span class="required-star">*</span></label>
                        <input type="time" id="start_time" name="start_time" value="<?= isset($editData) ? htmlspecialchars($editData['start_time'] ?? '') : '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="end_time">End Time <span class="required-star">*</span></label>
                        <input type="time" id="end_time" name="end_time" value="<?= isset($editData) ? htmlspecialchars($editData['end_time'] ?? '') : '' ?>" required>      
                    </div>

                    <div class="form-group equipment-selection-container">
                        <label>Equipment Selection <span class="required-star">*</span></label>
                        <div id="equipment-checkboxes">
                            <p class="equipment-empty-message">Select a sport first to see available equipment</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reserved_location">Reserved Location <span class="required-star">*</span></label>
                        <select id="reserved_location" name="reserved_location" required>
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
let reservationCheckTimeout = null;
let lastReservationAlertKey = '';

function showInstantBookingError(message) {
    const errorBox = document.getElementById('instantBookingError');
    const errorText = document.getElementById('instantBookingErrorText');
    errorText.textContent = message;
    errorBox.style.display = 'block';
}

function hideInstantBookingError() {
    const errorBox = document.getElementById('instantBookingError');
    const errorText = document.getElementById('instantBookingErrorText');
    errorText.textContent = '';
    errorBox.style.display = 'none';
}

function setSubmitEnabled(enabled) {
    const submitButton = document.querySelector('#addBookingForm button[type="submit"]');
    if (!submitButton) {
        return;
    }
    submitButton.disabled = !enabled;
    submitButton.style.opacity = enabled ? '1' : '0.6';
    submitButton.style.cursor = enabled ? 'pointer' : 'not-allowed';
}

function validateBookingInstantly() {
    const sportId = document.getElementById('sport').value;
    const requestDate = document.getElementById('reservation_date').value;
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;

    if (startTime && endTime && endTime <= startTime) {
        showInstantBookingError('End time must be later than start time.');
        clearCategoryConflictPopups();
        setSubmitEnabled(false);
        return false;
    }

    const hasFullSlotSelection = Boolean(sportId && requestDate && startTime && endTime);
    if (!hasFullSlotSelection) {
        hideInstantBookingError();
        clearCategoryConflictPopups();
        setSubmitEnabled(true);
        return true;
    }

    const hasCategoryConflict = renderCategoryConflictPopups();
    if (hasCategoryConflict) {
        hideInstantBookingError();
        setSubmitEnabled(false);
        return false;
    }

    hideInstantBookingError();
    setSubmitEnabled(true);
    return true;
}

function clearCategoryConflictPopups() {
    document.querySelectorAll('.category-conflict-popup').forEach(popup => {
        popup.style.display = 'none';
        popup.innerHTML = '';
    });
}

function renderCategoryConflictPopups() {
    clearCategoryConflictPopups();

    let hasConflict = false;
    const selectedCheckboxes = Array.from(document.querySelectorAll('input[name="equipment[]"]:checked'));

    selectedCheckboxes.forEach(cb => {
        const card = cb.closest('.equipment-item-card');
        if (!card) {
            return;
        }

        const overlapDataRaw = cb.dataset.overlaps || '[]';
        let overlapData = [];
        try {
            overlapData = JSON.parse(overlapDataRaw);
        } catch (e) {
            overlapData = [];
        }

        if (!Array.isArray(overlapData) || overlapData.length === 0) {
            return;
        }

        const qtyInput = card.querySelector('.equipment-quantity-input');
        const selectedQty = qtyInput ? Math.max(1, parseInt(qtyInput.value || '1', 10)) : 1;

        let popup = card.querySelector('.category-conflict-popup');
        if (!popup) {
            popup = document.createElement('div');
            popup.className = 'overlap-warning category-conflict-popup';
            card.appendChild(popup);
        }

        const header = document.createElement('div');
        header.className = 'overlap-warning-header';
        header.textContent = 'Conflict for selected category';
        popup.appendChild(header);

        overlapData.forEach(slot => {
            const slotDiv = document.createElement('div');
            slotDiv.className = 'overlap-slot-item';

            const status = (slot.status || 'UNKNOWN').toUpperCase();
            const slotStart = slot.start_time ? String(slot.start_time).substring(0, 5) : '--:--';
            const slotEnd = slot.end_time ? String(slot.end_time).substring(0, 5) : '--:--';
            const source = slot.source_type === 'practice' ? 'practice session' : 'booking request';

            slotDiv.textContent = cb.value
                + ' (selected x' + selectedQty + ')'
                + ' -> ' + source
                + ' [' + status + ']'
                + ' at ' + slotStart + ' - ' + slotEnd;
            popup.appendChild(slotDiv);
        });

        popup.style.display = 'block';
        hasConflict = true;
    });

    return hasConflict;
}

function showActiveReservationWarning(message) {
    const warningBox = document.getElementById('activeReservationWarning');
    const warningText = document.getElementById('activeReservationWarningText');
    warningText.textContent = message;
    warningBox.style.display = 'block';
}

function hideActiveReservationWarning() {
    const warningBox = document.getElementById('activeReservationWarning');
    const warningText = document.getElementById('activeReservationWarningText');
    warningText.textContent = '';
    warningBox.style.display = 'none';
}

console.log('Auto-fill functionality loaded');

function checkActiveReservationEarly() {
    if (isEdit) {
        return;
    }

    const studentId = document.getElementById('user_id').value.trim();
    const requesterName = document.getElementById('requester_name').value.trim();

    if (!studentId && !requesterName) {
        lastReservationAlertKey = '';
        hideActiveReservationWarning();
        return;
    }

    const currentKey = `${studentId}|${requesterName}`;

    fetch('/uoc-sports/public/equipment-manager/check-active-reservation?student_id=' + encodeURIComponent(studentId) + '&requester_name=' + encodeURIComponent(requesterName))
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                return;
            }

            if (data.has_active_reservation) {
                if (lastReservationAlertKey !== currentKey) {
                    showActiveReservationWarning(data.message || 'This user already has an active or accepted equipment reservation.');
                    lastReservationAlertKey = currentKey;
                }
                return;
            }

            if (lastReservationAlertKey === currentKey) {
                lastReservationAlertKey = '';
            }
            hideActiveReservationWarning();
        })
        .catch(error => {
            console.error('Error checking active reservation:', error);
        });
}

// Lookup user by ID and fill requester name
document.getElementById('user_id').addEventListener('input', function(e) {
    const userId = e.target.value.trim();
    
    console.log('User ID input changed:', userId);
    
    // Clear previous timeout
    clearTimeout(userLookupTimeout);
    
    if (userId.length === 0) {
        clearTimeout(reservationCheckTimeout);
        reservationCheckTimeout = setTimeout(checkActiveReservationEarly, 300);
        return;
    }

    clearTimeout(reservationCheckTimeout);
    reservationCheckTimeout = setTimeout(checkActiveReservationEarly, 300);
    
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
                    checkActiveReservationEarly();
                    
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
        checkActiveReservationEarly();
        return;
    }
    
    // Only lookup if name contains at least first and last name
    const nameParts = requesterName.split(' ');
    if (nameParts.length < 2) {
        console.log('Name has fewer than 2 parts, skipping lookup');
        checkActiveReservationEarly();
        return;
    }

    checkActiveReservationEarly();
    
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
                checkActiveReservationEarly();
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
    const hasFullSlotSelection = Boolean(sportId && requestDate && startTime && endTime);
    
    // Build API URL with parameters
    let apiUrl = '/uoc-sports/public/api/get-equipment-with-requests.php?sport_id=' + sportId;
    // Only request overlap/conflict checks after full slot selection.
    if (hasFullSlotSelection) {
        apiUrl += '&request_date=' + requestDate;
        apiUrl += '&start_time=' + startTime;
        apiUrl += '&end_time=' + endTime;
    }
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

                    const maxSelectableQty = Math.max(
                        0,
                        Number.isFinite(Number(equipment.slot_available_count))
                            ? parseInt(equipment.slot_available_count, 10)
                            : parseInt(equipment.available_count, 10) || 0
                    );
                    
                    // Main row with checkbox and label
                    const mainRow = document.createElement('div');
                    mainRow.className = 'equipment-main-row';
                    
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.id = 'eq_' + equipment.equipment_id;
                    checkbox.name = 'equipment[]';
                    checkbox.value = equipment.equipment_name;
                    checkbox.className = 'equipment-checkbox';
                    checkbox.dataset.overlaps = JSON.stringify(equipment.overlapping_slots || []);
                    
                    // Pre-select if in edit mode
                    if (preSelectedMap[equipment.equipment_name]) {
                        checkbox.checked = true;
                        itemDiv.classList.add('selected');
                    }
                    
                    const label = document.createElement('label');
                    label.htmlFor = 'eq_' + equipment.equipment_id;
                    label.textContent = equipment.equipment_name + ' (Available for selected slot: ' + maxSelectableQty + ')';
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
                    quantityInput.max = maxSelectableQty;
                    const preSelectedQty = parseInt(preSelectedMap[equipment.equipment_name] || '1', 10);
                    quantityInput.value = maxSelectableQty > 0 ? Math.min(preSelectedQty, maxSelectableQty) : '0';
                    quantityInput.disabled = !checkbox.checked;
                    quantityInput.className = 'equipment-quantity-input';

                    if (maxSelectableQty === 0) {
                        checkbox.checked = false;
                        checkbox.disabled = true;
                        quantityInput.disabled = true;
                        itemDiv.classList.remove('selected');
                    }
                    
                    // Enable/disable quantity input based on checkbox
                    checkbox.addEventListener('change', function() {
                        quantityInput.disabled = !this.checked;
                        if (this.checked) {
                            if (parseInt(quantityInput.value, 10) < 1) {
                                quantityInput.value = '1';
                            }
                            itemDiv.classList.add('selected');
                        } else {
                            itemDiv.classList.remove('selected');
                            quantityInput.value = '1';
                        }
                        validateBookingInstantly();
                    });

                    quantityInput.addEventListener('input', function() {
                        const max = parseInt(quantityInput.max, 10);
                        let value = parseInt(quantityInput.value || '1', 10);
                        if (!Number.isFinite(value) || value < 1) {
                            value = 1;
                        }
                        if (Number.isFinite(max) && max > 0 && value > max) {
                            value = max;
                        }
                        quantityInput.value = String(value);
                        validateBookingInstantly();
                    });
                    
                    quantityDiv.appendChild(quantityLabel);
                    quantityDiv.appendChild(quantityInput);
                    
                    mainRow.appendChild(checkbox);
                    mainRow.appendChild(label);
                    mainRow.appendChild(quantityDiv);
                    
                    itemDiv.appendChild(mainRow);

                    const totalPending = parseInt(equipment.pending_count || 0, 10) || 0;
                    const totalAccepted = parseInt(equipment.accepted_count || 0, 10) || 0;
                    const totalActive = parseInt(equipment.active_count || 0, 10) || 0;
                    const totalPendingQty = parseInt(equipment.pending_qty || 0, 10) || 0;
                    const totalAcceptedQty = parseInt(equipment.accepted_qty || 0, 10) || 0;
                    const totalActiveQty = parseInt(equipment.active_qty || 0, 10) || 0;
                    const requestDetails = Array.isArray(equipment.request_details) ? equipment.request_details : [];

                    let slotPending = 0;
                    let slotAccepted = 0;
                    let slotActive = 0;
                    let slotPendingQty = 0;
                    let slotAcceptedQty = 0;
                    let slotActiveQty = 0;
                    const overlapRows = Array.isArray(equipment.overlapping_slots) ? equipment.overlapping_slots : [];
                    overlapRows.forEach(slot => {
                        const st = String(slot.status || '').toUpperCase();
                        const slotQty = Math.max(0, parseInt(slot.requested_quantity || 0, 10) || 0);
                        if (st === 'PENDING') slotPending += 1;
                        if (st === 'ACCEPTED') slotAccepted += 1;
                        if (st === 'ACTIVE') slotActive += 1;
                        if (st === 'PENDING') slotPendingQty += slotQty;
                        if (st === 'ACCEPTED') slotAcceptedQty += slotQty;
                        if (st === 'ACTIVE') slotActiveQty += slotQty;
                    });

                    const hasAnyRequestHistory = (totalPending + totalAccepted + totalActive) > 0 || (totalPendingQty + totalAcceptedQty + totalActiveQty) > 0 || requestDetails.length > 0;
                    const hasSlotConflicts = hasFullSlotSelection && overlapRows.length > 0;

                    if (hasAnyRequestHistory || hasSlotConflicts) {
                        const summaryDiv = document.createElement('div');
                        summaryDiv.className = 'overlap-warning request-summary-card';

                        const summaryHeader = document.createElement('div');
                        summaryHeader.className = 'overlap-warning-header request-summary-header';
                        summaryHeader.textContent = hasSlotConflicts ? 'Request Conflicts (Category-wise)' : 'Reservation Summary';
                        summaryDiv.appendChild(summaryHeader);

                        const totalLine = document.createElement('div');
                        totalLine.className = 'summary-line';
                        totalLine.innerHTML = '<span class="summary-label">All requests:</span> '
                            + 'Pending ' + totalPending + ' (Qty ' + totalPendingQty + ') | '
                            + 'Accepted ' + totalAccepted + ' (Qty ' + totalAcceptedQty + ') | '
                            + 'Active ' + totalActive + ' (Qty ' + totalActiveQty + ')';
                        summaryDiv.appendChild(totalLine);

                        requestDetails.forEach(req => {
                            const detailLine = document.createElement('div');
                            detailLine.className = 'request-detail-row';
                            const requester = req.requester_name || 'Unknown';
                            const requestedQty = Math.max(0, parseInt(req.requested_quantity || 0, 10) || 0);
                            const reqStatus = String(req.status || 'UNKNOWN').toUpperCase();
                            const reqDate = req.request_date || '--';
                            const reqStart = req.start_time ? String(req.start_time).substring(0, 5) : '--:--';
                            const reqEnd = req.end_time ? String(req.end_time).substring(0, 5) : '--:--';
                            detailLine.innerHTML = '<strong>Requester:</strong> ' + requester
                                + ' | <strong>Requested Qty:</strong> ' + requestedQty
                                + ' | <strong>Status:</strong> ' + reqStatus
                                + ' | <strong>Date:</strong> ' + reqDate
                                + ' | <strong>Time:</strong> ' + reqStart + ' - ' + reqEnd;
                            summaryDiv.appendChild(detailLine);
                        });

                        if (hasSlotConflicts) {
                            const slotLine = document.createElement('div');
                            slotLine.className = 'slot-summary-line';
                            slotLine.innerHTML = '<span class="summary-label">Selected slot:</span> '
                                + 'Pending ' + slotPending + ' (Qty ' + slotPendingQty + ') | '
                                + 'Accepted ' + slotAccepted + ' (Qty ' + slotAcceptedQty + ') | '
                                + 'Active ' + slotActive + ' (Qty ' + slotActiveQty + ')';
                            summaryDiv.appendChild(slotLine);

                            overlapRows.forEach(slot => {
                                const slotDetails = document.createElement('div');
                                slotDetails.className = 'request-detail-row';
                                const status = String(slot.status || 'UNKNOWN').toUpperCase();
                                const source = slot.source_type === 'practice' ? 'Practice Session' : (slot.requester_name || 'Booking');
                                const start = slot.start_time ? String(slot.start_time).substring(0, 5) : '--:--';
                                const end = slot.end_time ? String(slot.end_time).substring(0, 5) : '--:--';
                                const reqQty = Math.max(0, parseInt(slot.requested_quantity || 0, 10) || 0);
                                slotDetails.innerHTML = '<strong>Source:</strong> ' + source
                                    + ' | <strong>Status:</strong> ' + status
                                    + ' | <strong>Requested Qty:</strong> ' + reqQty
                                    + ' | <strong>Time:</strong> ' + start + ' - ' + end;
                                summaryDiv.appendChild(slotDetails);
                            });
                        }

                        itemDiv.appendChild(summaryDiv);
                    }

                    // Popup container for category-wise instant conflict messages.
                    const categoryConflictPopup = document.createElement('div');
                    categoryConflictPopup.className = 'overlap-warning category-conflict-popup';
                    categoryConflictPopup.style.display = 'none';
                    itemDiv.appendChild(categoryConflictPopup);
                    
                    equipmentContainer.appendChild(itemDiv);
                });
            } else {
                // Show message if no equipment available
                const message = document.createElement('p');
                message.className = 'equipment-empty-message';
                message.textContent = 'No equipment available for this sport';
                equipmentContainer.appendChild(message);
            }

            validateBookingInstantly();
        })
        .catch(error => {
            console.error('Error fetching equipment:', error);
            equipmentContainer.innerHTML = '<p class="equipment-empty-message" style="color: #dc2626;">Error loading equipment. Please try again.</p>';
            validateBookingInstantly();
        });
}

document.getElementById('sport').addEventListener('change', function() {
    loadEquipmentBySport(this.value);
    validateBookingInstantly();
});

// Reload equipment when date or time changes to update overlaps
document.getElementById('reservation_date').addEventListener('change', function() {
    const sportId = document.getElementById('sport').value;
    if (sportId) {
        loadEquipmentBySport(sportId);
    }
    validateBookingInstantly();
});

document.getElementById('start_time').addEventListener('change', function() {
    const sportId = document.getElementById('sport').value;
    if (sportId) {
        loadEquipmentBySport(sportId);
    }
    validateBookingInstantly();
});

document.getElementById('end_time').addEventListener('change', function() {
    const sportId = document.getElementById('sport').value;
    if (sportId) {
        loadEquipmentBySport(sportId);
    }
    validateBookingInstantly();
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

['user_id', 'requester_name', 'reserved_location'].forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (field) {
        field.addEventListener('input', validateBookingInstantly);
        field.addEventListener('change', validateBookingInstantly);
    }
});

// Form validation to ensure at least one equipment is selected
document.getElementById('addBookingForm').addEventListener('submit', function(e) {
    const isValid = validateBookingInstantly();
    if (!isValid) {
        e.preventDefault();
        return false;
    }

    const checkboxes = document.querySelectorAll('input[name="equipment[]"]:checked');
    if (checkboxes.length === 0) {
        e.preventDefault();
        showInstantBookingError('Please select at least one equipment item.');
        setSubmitEnabled(false);
        return false;
    }

    hideInstantBookingError();
    setSubmitEnabled(true);
});

validateBookingInstantly();
</script>

<?php
    require "../app/views/templates/general/footer.php";
?>
</body>
</html>