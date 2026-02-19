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
    @import url("/uoc-sports/public/css/sports-manager/add-participants.css");
  </style>
  <script src="/uoc-sports/public/js/sports-manager/add-participants.js" defer></script>
</head>
<?php
    require "../app/views/templates/general/header.php";
?>

        
        <div class="form-container">

            
            <!-- Add Participants Form -->
             <div class="page-header">
                <div>
                    <h2><?= isset($competition) ? 'Add More Participants' : 'Add Participants' ?></h2>
                    <p><?= isset($competition) ? 'Add more participants to "' . htmlspecialchars($competition['competition_name']) . '"' : 'Add participants to the competition by uploading a file or selecting from students' ?></p>
                </div>
            </div>

            <form id="addParticipantsForm" class="form" method="POST" action="/uoc-sports/public/sport-manager/store-competition<?= isset($selectedSport) ? '?sport=' . urlencode($selectedSport) : '' ?>" enctype="multipart/form-data">
                <div class="form-grid">
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
                               <?= isset($competition) ? 'readonly style="background-color: #f3f4f6; cursor: not-allowed;"' : '' ?>
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
                               accept=".pdf,application/pdf"
                               style="width: 100%; padding: 10px; border: 2px solid #d1d5db; border-radius: 8px; font-size: 14px; cursor: pointer; background: white;">
                        <p style="font-size: 12px; color: #6b7280; margin-top: 8px;">
                            <i class="fas fa-info-circle"></i> Only PDF files allowed (Max 5MB)
                        </p>
                    </div>
                </div>

                <!-- Select Participants Checkboxes -->
                <div class="form-group full-width">
                    <label>Select Participants</label>
                    <div style="max-height: 400px; overflow-y: auto; border: 2px solid #d1d5db; border-radius: 8px; padding: 15px; background-color: #f9fafb;">
                        <?php if (!empty($students)): ?>
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 12px;">
                                <?php foreach ($students as $student): ?>
                                    <label style="display: flex; align-items: center; padding: 10px; background: white; border: 2px solid #e5e7eb; border-radius: 6px; cursor: pointer; transition: all 0.2s ease;" 
                                           onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#faf5ff';" 
                                           onmouseout="this.style.borderColor='#e5e7eb'; this.style.backgroundColor='white';">
                                        <input type="checkbox" 
                                               name="selectedParticipants[]" 
                                               value="<?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>"
                                               style="width: 18px; height: 18px; margin-right: 10px; cursor: pointer; accent-color: #7c3aed;">
                                        <div style="flex: 1;">
                                            <div style="font-weight: 600; color: #374151; font-size: 14px;">
                                                <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                                            </div>
                                            <div style="font-size: 12px; color: #6b7280; margin-top: 2px;">
                                                ID: <?= htmlspecialchars($student['student_id']) ?> | <?= htmlspecialchars($student['email']) ?>
                                            </div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p style="text-align: center; color: #9ca3af; padding: 20px;">
                                <i class="fas fa-info-circle"></i> No students available for this sport
                            </p>
                        <?php endif; ?>
                    </div>
                    <p style="font-size: 12px; color: #6b7280; margin-top: 8px;">
                        <i class="fas fa-info-circle"></i> Select all participants you want to add to the competition
                    </p>
                </div>
                        
                <div class="form-actions">
                    <button type="button" class="view-all-link" onclick="window.location.href='/uoc-sports/public/sport-manager/competitions/'">
                       Cancel
                    </button>
                    <button type="submit" class="view-all-link">
                       Add Participants
                    </button>
                </div>

            </form>
        </div>

        <?php
    require "../app/views/templates/general/footer.php";
?>
</body>
</html>
