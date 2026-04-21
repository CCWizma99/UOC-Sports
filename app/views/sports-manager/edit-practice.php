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
                        <input type="date" id="practiceSessionDate" name="date" min="<?= date('Y-m-d') ?>"
                               value="<?= htmlspecialchars($session['session_date']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="startTime">Start Time *</label>
                        <input type="time" id="startTime" name="stime" min="06:00" max="20:00" step="1800"
                               value="<?= htmlspecialchars($session['start_time']) ?>" required>
                        <small>Use 30-minute intervals (e.g. 08:00, 08:30)</small>
                    </div>

                    <div class="form-group">
                        <label for="endTime">End Time *</label>
                        <input type="time" id="endTime" name="etime" min="06:00" max="20:00" step="1800"
                               value="<?= htmlspecialchars($session['end_time']) ?>" required>
                        <small>Use 30-minute intervals</small>
                    </div>

                    <div class="form-group">
                        <label for="needEquipment">Need Equipment *</label>
                        <select id="needEquipment" name="need_equipment" required>
                            <option value="No" <?= ($session['need_equipment'] ?? 'No') === 'No' ? 'selected' : '' ?>>No</option>
                            <option value="Yes" <?= ($session['need_equipment'] ?? 'No') === 'Yes' ? 'selected' : '' ?>>Yes</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="location">Physical Facility *</label>
                        <select id="location" name="physical_facility_id" required onchange="updateLocationName(this)">
                            <option value="">Select the Facility</option>
                            <?php if (!empty($facilities)): ?>
                                <?php foreach ($facilities as $facility): ?>
                                    <option value="<?= htmlspecialchars($facility['facility_id']) ?>" 
                                            data-name="<?= htmlspecialchars($facility['facility_name']) ?>"
                                            <?= $session['physical_facility_id'] === $facility['facility_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($facility['facility_name']) ?> (<?= htmlspecialchars($facility['location']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <input type="hidden" name="location_name" id="location_name" value="<?= htmlspecialchars($session['location']) ?>">
                        <script>
                            function updateLocationName(select) {
                                const selectedOption = select.options[select.selectedIndex];
                                const name = selectedOption.getAttribute('data-name');
                                document.getElementById('location_name').value = name || '';
                            }
                        </script>
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

 <?php
    require "../app/views/templates/general/footer.php";
?>

</body>
</html>
