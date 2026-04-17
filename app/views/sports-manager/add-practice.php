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
                <div class="form-row">
                    <div class="form-group">
                        <label for="Sport name">Sport *</label>
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
                        <label for="practiceSessionDate">Practice Session Date *</label>
                        <input type="date" id="practiceSessionDate" name="date" required>
                    </div>

                    <div class="form-group">
                        <label for="startTime">Start Time *</label>
                        <input type="time" id="startTime" name="stime" step="1800" required>
                        <small>Use 30-minute intervals (e.g. 08:00, 08:30)</small>
                    </div>

                    <div class="form-group">
                        <label for="endTime">End Time *</label>
                        <input type="time" id="endTime" name="etime" step="1800" required>
                        <small>Use 30-minute intervals</small>
                    </div>

                    <div class="form-group">
                        <label for="needEquipment">Need Equipment *</label>
                        <select id="needEquipment" name="need_equipment" required>
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="location">Physical Facility *</label>
                        <select id="location" name="physical_facility_id" required onchange="updateLocationName(this)">
                            <option value="">Select the Facility</option>
                            <?php if (!empty($facilities)): ?>
                                <?php foreach ($facilities as $facility): ?>
                                    <option value="<?= htmlspecialchars($facility['facility_id']) ?>" 
                                            data-name="<?= htmlspecialchars($facility['facility_name']) ?>">
                                        <?= htmlspecialchars($facility['facility_name']) ?> (<?= htmlspecialchars($facility['location']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <input type="hidden" name="location_name" id="location_name">
                        <script>
                            function updateLocationName(select) {
                                const selectedOption = select.options[select.selectedIndex];
                                const name = selectedOption.getAttribute('data-name');
                                document.getElementById('location_name').value = name || '';
                            }
                        </script>
                    </div>

                    <div class="form-group full-width">
                        <label for="expenseDescription">Special Notes *</label>
                        <textarea id="expenseDescription" name="notes" rows="4" placeholder="Enter any special notes..."></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" onclick="window.location.href='/uoc-sports/public/sport-manager/practicesessions'">
                       Cancel
                    </button>
                    <button type="submit">
                       Add Practice Session
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