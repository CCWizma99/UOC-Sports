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
    @import url("/uoc-sports/public/css/sports-manager/sub-nav.css");
    @import url("/uoc-sports/public/css/general/footer.css");
    @import url("/uoc-sports/public/css/sports-manager/report.css");
    @import url("/uoc-sports/public/css/sports-manager/page.css");
    @import url("/uoc-sports/public/css/sports-manager/add-participants.css");
  </style>
  <script src="/uoc-sports/public/js/sports-manager/add-participants.js" defer></script>
</head>
<body>
<?php
    require "../app/views/templates/general/header.php";
    require "../app/views/sports-manager/header-subnav.php";
?>

<div class="main-wrapper">        
        <div class="page-form-container">

            
            <!-- Add Participants Form -->
             <div class="page-header">
                <div>
                    <h2><?= isset($competition) ? 'Add More Participants' : 'Add Participants' ?></h2>
                    <p><?= isset($competition) ? 'Add more participants to "' . htmlspecialchars($competition['competition_name']) . '"' : 'Add participants to the competition by uploading a file or selecting from students' ?></p>
                </div>
            </div>

            <form id="addParticipantsForm" class="form" method="POST" action="/uoc-sports/public/sport-manager/store-competition<?= isset($selectedSport) ? '?sport=' . urlencode($selectedSport) : '' ?>" enctype="multipart/form-data">
                <div class="form-row">
                    <?php if (!isset($competition)): ?>
                    <div class="form-group">
                        <label for="sport">Sport </label>
                        <select id="sport" name="sport" required>
                            <option value="">Select Sport</option>
                            <?php if (!empty($sports)): ?>
                                <?php foreach ($sports as $sport): ?>
                                    <option value="<?= htmlspecialchars($sport['sport_name']) ?>"
                                            <?php 
                                            // Select if it's from URL parameter
                                            $isSelected = (isset($selectedSport) && $selectedSport === $sport['sport_id']);
                                            echo $isSelected ? 'selected' : '';
                                            ?>>
                                        <?= htmlspecialchars($sport['sport_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                            
                            <?php endif; ?>
                        </select>
                    </div>
                    <?php else: ?>
                        <!-- Hidden field for sport when editing existing competition -->
                        <input type="hidden" name="sport" value="<?= htmlspecialchars($competition['sport_name']) ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="competitionName">Competition Name *</label>
                        <input type="text" 
                               id="competitionName" 
                               name="competitionName" 
                               placeholder="Enter competition name" 
                               value="<?= isset($competition) ? htmlspecialchars($competition['competition_name']) : '' ?>" 
                               <?= isset($competition) ? 'readonly' : '' ?>
                               required>
                        <?php if (isset($competition)): ?>
                            <input type="hidden" name="competition_id" value="<?= $competition['competition_id'] ?>">
                        <?php endif; ?>
                    </div>

                    <!-- Upload Participant List -->
                    <div class="form-group">
                        <label for="participantsFile">Upload Participant List (Optional)</label>
                        <input type="file" 
                               id="participantsFile" 
                               name="participantsFile" 
                               accept=".pdf,application/pdf">
                        <p class="file-note">
                            <i class="fas fa-info-circle"></i> Only PDF files allowed (Max 5MB)
                        </p>
                    </div>
                </div>

                <!-- Select Participants Checkboxes -->
                <div class="form-group full-width">
                    <label>Select Participants</label>
                    <div class="participants-checkbox-container">
                        <?php if (!empty($students)): ?>
                            <div class="participants-grid">
                                <?php foreach ($students as $student): ?>
                                    <label class="participant-checkbox-label">
                                        <input type="checkbox" 
                                               name="selectedParticipants[]" 
                                               value="<?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>"
                                               class="participant-checkbox">
                                        <div class="participant-info">
                                            <div class="participant-name">
                                                <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                                            </div>
                                            <div class="participant-details">
                                                ID: <?= htmlspecialchars($student['student_id']) ?> | <?= htmlspecialchars($student['email']) ?>
                                            </div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="no-students-message">
                                <i class="fas fa-info-circle"></i> No students available for this sport
                            </p>
                        <?php endif; ?>
                    </div>
                    <p class="file-note">
                        <i class="fas fa-info-circle"></i> Select all participants you want to add to the competition
                    </p>
                </div>
                        
                <div class="form-actions">
                    <button type="button" onclick="window.location.href='/uoc-sports/public/sport-manager/competitions/'">
                       Cancel
                    </button>
                    <button type="submit">
                       Add Participants
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
