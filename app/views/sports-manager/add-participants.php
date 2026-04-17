<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tournament Registration - Sports Manager</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/general/footer.css");
    @import url("/uoc-sports/public/css/sports-manager/report.css");
    @import url("/uoc-sports/public/css/sports-manager/page.css");
    @import url("/uoc-sports/public/css/sports-manager/add-participants.css");

    .tournament-selector {
        padding: 12px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        background-color: #f9fafb;
        margin-bottom: 20px;
    }

    .tournament-info-box {
        padding: 15px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13px;
    }
  </style>
</head>
<body>
<?php
    require "../app/views/templates/general/header.php";
?>

<div class="main-wrapper">        
        <div class="page-form-container">

            <?php
            $cancelUrl = '/uoc-sports/public/sport-manager/tournaments';
            if (!empty($selectedSport)) {
                $cancelUrl .= '?sport=' . urlencode($selectedSport);
            }
            ?>

            <div class="page-header">
                <div>
                    <p class="page-path">Sport Manager / Tournaments / Event Registration</p>
                    <h2>Event Registration</h2>
                    <p>Link students from your team to official tournaments created by the Administration.</p>
                </div>
            </div>

            <form id="addParticipantsForm" class="form" method="POST" action="/uoc-sports/public/sport-manager/store-participants">
                <input type="hidden" name="sport_id" value="<?= htmlspecialchars($selectedSport) ?>">

                <div class="tournament-selector">
                    <div class="form-group full-width">
                        <label for="tournament_id">Select Tournament *</label>
                        <select id="tournament_id" name="tournament_id" required onchange="this.form.method='GET'; this.form.action='/uoc-sports/public/sport-manager/add-participants'; this.form.submit();">
                            <option value="">-- Select an Event --</option>
                            <?php foreach ($tournaments as $t): ?>
                                <option value="<?= htmlspecialchars($t['tournament_id']) ?>" <?= (isset($tournament) && $tournament['tournament_id'] === $t['tournament_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['tournament_name']) ?> (<?= htmlspecialchars($t['date']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if (isset($tournament)): ?>
                        <div class="tournament-info-box">
                            <strong><i class="fas fa-info-circle"></i> Event Details:</strong><br>
                            Date: <?= htmlspecialchars(date('M d, Y', strtotime($tournament['date']))) ?> | 
                            Status: <span style="text-transform: lowercase;"><?= htmlspecialchars($tournament['status']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Select Participants Checkboxes -->
                <div class="form-group full-width">
                    <label>Select Team Members</label>
                    <div class="participants-checkbox-container" style="max-height: 400px; overflow-y: auto;">
                        <?php if (!empty($students)): ?>
                            <div class="participants-grid">
                                <?php foreach ($students as $student): ?>
                                    <?php
                                    $isParticipantSelected = in_array($student['user_id'], $existingParticipants);
                                    ?>
                                    <label class="participant-checkbox-label">
                                        <input type="checkbox" 
                                               name="selectedParticipants[]" 
                                               value="<?= htmlspecialchars($student['user_id']) ?>"
                                               <?= $isParticipantSelected ? 'checked' : '' ?>
                                               class="participant-checkbox">
                                        <div class="participant-info">
                                            <div class="participant-name">
                                                <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                                            </div>
                                            <div class="participant-details">
                                                ID: <?= htmlspecialchars($student['student_id']) ?>
                                            </div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="no-students-message">
                                <i class="fas fa-info-circle"></i> Please select a tournament or ensure your team has active students.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                        
                <div class="form-actions">
                    <button type="button" onclick="window.location.href='<?= htmlspecialchars($cancelUrl) ?>'" style="background: #ef4444; border: none; color: white;">
                       Cancel
                    </button>
                    <button type="submit" <?= !isset($tournament) ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '' ?>>
                       <i class="fas fa-save"></i> Save Registration
                    </button>
                </div>

            </form>
        </div>
</div>

<?php require "../app/views/templates/general/footer.php"; ?>
</body>
</html>
