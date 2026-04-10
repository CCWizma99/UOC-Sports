<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sports manager - Add Practice Session</title>
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

            
            <!-- Add practice Form -->
             <div class="page-header">
                <div>

                    <p class="page-path">Sport Manager / Practice Sessions / Add Practice Session</p>

                    <h2>Add Practice Session</h2>
                    <p>Schedule a new practice session</p>
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
            <form id="addPracticeForm" class="form" method="POST" action="/uoc-sports/public/sport-manager/store-practice<?= isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '' ?>">
                <?php if (isset($_GET['sport'])): ?>
                    <input type="hidden" name="sport_param" value="<?php echo htmlspecialchars($_GET['sport']); ?>">
                <?php endif; ?>
                <div id="practiceConflictAlert" class="conflict-alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span id="practiceConflictAlertText"></span>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="Sport name">Sport <span class="required-star">*</span></label>
                        <select id="sport" name="sport" required>
                            <option value="">Select Sport</option>
                            <?php if (!empty($sports)): ?>
                                <?php foreach ($sports as $sport): ?>
                                    <option value="<?= htmlspecialchars($sport['sport_name']) ?>"
                                            <?= (isset($selectedSportFromUrl) && $selectedSportFromUrl === $sport['sport_name']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($sport['sport_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="Athletics">Athletics</option>
                                <option value="Rugby">Rugby</option>
                                <option value="Tennis">Tennis</option>
                                <option value="Weightlifting">Weightlifting</option>
                                <option value="Basketball">Basketball</option>
                                <option value="Carrom">Carrom</option>
                                <option value="Scrabble">Scrabble</option>
                                <option value="Chess">Chess</option>
                                <option value="Football">Football</option>
                                <option value="Baseball">Baseball</option>
                                <option value="Rowing">Rowing</option>
                                <option value="Netball">Netball</option>
                                <option value="Taekwondo">Taekwondo</option>
                                <option value="Hockey">Hockey</option>
                                <option value="Elle">Elle</option>
                                <option value="Cricket">Cricket</option>
                                <option value="Kabaddi">Kabaddi</option>
                                <option value="Wrestling">Wrestling</option>
                                <option value="Badminton">Badminton</option>
                                <option value="Table Tennis">Table Tennis</option>
                                <option value="Volleyball">Volleyball</option>
                                <option value="Boxing">Boxing</option>
                                <option value="Karate">Karate</option>
                                <option value="Swimming">Swimming</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="practiceSessionDate">Practice Session Date <span class="required-star">*</span></label>
                        <input type="date" id="practiceSessionDate" name="date" required>
                    </div>

                    <div class="form-group">
                        <label for="startTime">Start Time <span class="required-star">*</span></label>
                        <input type="time" id="startTime" name="stime" required>
                    </div>

                    <div class="form-group">
                        <label for="endTime">End Time <span class="required-star">*</span></label>
                        <input type="time" id="endTime" name="etime" required>
                    </div>

                    <div class="form-group">
                        <label for="needEquipment">Need Equipment <span class="required-star">*</span></label>
                        <select id="needEquipment" name="need_equipment" required>
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="location">Location <span class="required-star">*</span></label>
                        <select id="location" name="location" required>
                            <option>Select the Location</option>
                            <option value="Indoor Court">Indoor Tennis Court</option>
                            <option value="Indoor court">Indoor Badminton Court</option>
                            <option value="Outdoor Court">Outdoor Basketball court</option>
                            <option value="Outdoor Field">Outdoor Baseball court</option>
                            <option value="Outdoor Field">Indoor volleyball court</option>
                            <option value="Outdoor Field">Outdoor Cricket Field</option>
                            <option value="Swimming Pool">Elle Field</option>
                            <option value="Carrom room">Carrom Room</option>
                        </select>
                    </div>


                </div>

                <div class="form-actions">
                          <button type="button" onclick="window.location.href='/uoc-sports/public/sport-manager/practicesessions<?= isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '' ?>'">
                       Cancel
                    </button>
                    <button type="submit">
                       Add Practice Session
                    </button>
                </div>
            </form>
        </div>
</div>

<script>
const addPracticeForm = document.getElementById('addPracticeForm');
const locationField = document.getElementById('location');
const dateField = document.getElementById('practiceSessionDate');
const startTimeField = document.getElementById('startTime');
const endTimeField = document.getElementById('endTime');
const conflictAlert = document.getElementById('practiceConflictAlert');
const conflictAlertText = document.getElementById('practiceConflictAlertText');
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
        end_time: endTime
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

addPracticeForm.addEventListener('submit', function(e) {
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