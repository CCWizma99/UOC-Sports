<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Practice Session</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/general/footer.css");

    @import url("/uoc-sports/public/css/sports-manager/report.css");
    @import url("/uoc-sports/public/css/sports-manager/page.css");

        .conflict-alert {
            display: none;
            margin-bottom: 14px;
            padding: 12px 14px;
            border-radius: 8px;
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-left: 4px solid #dc2626;
            color: #991b1b;
            font-weight: 600;
            font-size: 14px;
            align-items: center;
            gap: 8px;
        }
  </style>
</head>
<body>
<?php
    require "../app/views/templates/general/header.php";
?>

        <div class="main-wrapper">
        <div class="page-form-container">

            
            <!-- Edit practice Form -->
             <div class="page-header">
                <div>
                    <h2>Edit Practice Session</h2>
                    <p>Update practice session details</p>
                </div>
               
            </div>
            <form id="editPracticeForm" class="form" method="POST" action="/uoc-sports/public/sport-manager/update-practice<?= isset($selectedSport) ? '?sport=' . urlencode($selectedSport) : '' ?>">
                <input type="hidden" name="id" value="<?= htmlspecialchars($session['id']) ?>">
                <?php if (isset($selectedSport)): ?>
                    <input type="hidden" name="sport_param" value="<?= htmlspecialchars($selectedSport) ?>">
                <?php endif; ?>
                <div id="practiceConflictAlert" class="conflict-alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span id="practiceConflictAlertText"></span>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="sport">Sport *</label>
                        <select id="sport" name="sport" required>
                            <option value="">Select Sport</option>
                            <?php if (!empty($sports)): ?>
                                <?php foreach ($sports as $sport): ?>
                                    <option value="<?= htmlspecialchars($sport['sport_name']) ?>" 
                                            <?= $session['sport_name'] === $sport['sport_name'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($sport['sport_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="practiceSessionDate">Practice Session Date *</label>
                        <input type="date" id="practiceSessionDate" name="date" 
                               value="<?= htmlspecialchars($session['session_date']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="startTime">Start Time *</label>
                        <input type="time" id="startTime" name="stime" 
                               value="<?= htmlspecialchars($session['start_time']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="endTime">End Time *</label>
                        <input type="time" id="endTime" name="etime" 
                               value="<?= htmlspecialchars($session['end_time']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="needEquipment">Need Equipment *</label>
                        <select id="needEquipment" name="need_equipment" required>
                            <option value="No" <?= ($session['need_equipment'] ?? 'No') === 'No' ? 'selected' : '' ?>>No</option>
                            <option value="Yes" <?= ($session['need_equipment'] ?? 'No') === 'Yes' ? 'selected' : '' ?>>Yes</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="location">Location *</label>
                        <select id="location" name="location" required>
                            <option value="">Select the Location</option>
                            <option value="Indoor Court" <?= $session['location'] === 'Indoor Court' ? 'selected' : '' ?>>Indoor Tennis Court</option>
                            <option value="Indoor court" <?= $session['location'] === 'Indoor court' ? 'selected' : '' ?>>Indoor Badminton Court</option>
                            <option value="Outdoor Court" <?= $session['location'] === 'Outdoor Court' ? 'selected' : '' ?>>Outdoor Basketball court</option>
                            <option value="Outdoor Field" <?= $session['location'] === 'Outdoor Field' ? 'selected' : '' ?>>Outdoor Baseball court</option>
                            <option value="Outdoor Field" <?= $session['location'] === 'Outdoor Field' ? 'selected' : '' ?>>Indoor volleyball court</option>
                            <option value="Outdoor Field" <?= $session['location'] === 'Outdoor Field' ? 'selected' : '' ?>>Outdoor Cricket Field</option>
                            <option value="Swimming Pool" <?= $session['location'] === 'Swimming Pool' ? 'selected' : '' ?>>Elle Field</option>
                            <option value="Carrom room" <?= $session['location'] === 'Carrom room' ? 'selected' : '' ?>>Carrom Room</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status">Status *</label>
                        <select id="status" name="status" required>
                            <option value="ACTIVE" <?= $session['status'] === 'ACTIVE' ? 'selected' : '' ?>>ACTIVE</option>
                            <option value="ACCEPTED" <?= $session['status'] === 'ACCEPTED' ? 'selected' : '' ?>>ACCEPTED</option>
                            <option value="CANCELED" <?= $session['status'] === 'CANCELED' ? 'selected' : '' ?>>CANCELED</option>
                            <option value="PENDING" <?= $session['status'] === 'PENDING' ? 'selected' : '' ?>>PENDING</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="notes">Special Notes</label>
                        <textarea id="notes" name="notes" rows="4" placeholder="Enter any special notes..."><?= htmlspecialchars($session['notes'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" onclick="window.location.href='/uoc-sports/public/sport-manager/practicesessions'">
                       Cancel
                    </button>
                    <button type="submit">
                       Update Practice Session
                    </button>
                </div>
            </form>
        </div>
</div>

<script>
const editPracticeForm = document.getElementById('editPracticeForm');
const locationField = document.getElementById('location');
const dateField = document.getElementById('practiceSessionDate');
const startTimeField = document.getElementById('startTime');
const endTimeField = document.getElementById('endTime');
const conflictAlert = document.getElementById('practiceConflictAlert');
const conflictAlertText = document.getElementById('practiceConflictAlertText');
const currentSessionId = <?= (int)$session['id'] ?>;
let hasPracticeConflict = false;
let conflictCheckTimer = null;

function showPracticeConflict(message) {
    hasPracticeConflict = true;
    conflictAlertText.textContent = message;
    conflictAlert.style.display = 'flex';
}

function hidePracticeConflict() {
    hasPracticeConflict = false;
    conflictAlertText.textContent = '';
    conflictAlert.style.display = 'none';
}

function checkPracticeConflictLive() {
    const location = locationField.value.trim();
    const date = dateField.value.trim();
    const startTime = startTimeField.value.trim();
    const endTime = endTimeField.value.trim();

    if (!location || !date || !startTime || !endTime) {
        hidePracticeConflict();
        return;
    }

    const params = new URLSearchParams({
        location: location,
        date: date,
        start_time: startTime,
        end_time: endTime,
        exclude_id: String(currentSessionId)
    });

    fetch('/uoc-sports/public/sport-manager/check-practice-conflict?' + params.toString())
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                return;
            }

            if (data.has_conflict) {
                showPracticeConflict(data.message || 'This facility is already booked for the selected date and time.');
            } else {
                hidePracticeConflict();
            }
        })
        .catch(() => {
            // Keep silent for network errors, avoid noisy UX
        });
}

function queuePracticeConflictCheck() {
    clearTimeout(conflictCheckTimer);
    conflictCheckTimer = setTimeout(checkPracticeConflictLive, 250);
}

[locationField, dateField, startTimeField, endTimeField].forEach((field) => {
    field.addEventListener('change', queuePracticeConflictCheck);
    field.addEventListener('input', queuePracticeConflictCheck);
});

queuePracticeConflictCheck();

editPracticeForm.addEventListener('submit', function(e) {
    if (hasPracticeConflict) {
        e.preventDefault();
        conflictAlert.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});
</script>

 <?php
    require "../app/views/templates/general/footer.php";
?>

</body>
</html>
