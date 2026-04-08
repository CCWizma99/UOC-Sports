<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sports Manager - Competitions</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/general/footer.css");
    @import url("/uoc-sports/public/css/sports-manager/report.css");
    @import url("/uoc-sports/public/css/sports-manager/dynamic-background.css");

    /* Action buttons styling */
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
        align-items: center;
    }

    .action-btn {
        padding: 8px 18px;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .add-participant-btn {
        background: linear-gradient(135deg, #7c3aed 0%, #6b21a8 100%);
        color: white;
        border: 2px solid #6b21a8;
        box-shadow: 0 2px 6px rgba(124, 58, 237, 0.3);
    }

    .add-participant-btn:hover {
        background: linear-gradient(135deg, #6b21a8 0%, #581c87 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.4);
    }

    .view-pdf-btn {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: white;
        border: 2px solid #0284c7;
        box-shadow: 0 2px 6px rgba(14, 165, 233, 0.3);
    }

    .view-pdf-btn:hover {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4);
    }


    /* Table styling enhancements */
    .data-table table {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .data-table td {
        vertical-align: middle;
    }

    .participant-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
        color: #6b21a8;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        border: 2px solid #a855f7;
    }

    .participant-list {
        font-size: 13px;
        color: #4b5563;
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .date-cell {
        font-weight: 500;
        color: #374151;
    }

    .filter-info {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-left: 4px solid #3b82f6;
        border-radius: 8px;
        margin-top: 10px;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
        font-size: 13px;
        color: #1e40af;
        font-weight: 500;
    }
  </style>
  <script>
    window.selectedSportName = '<?= htmlspecialchars($sportName ?? '') ?>';
  </script>
  <script src="/uoc-sports/public/js/sports-manager/dynamic-background.js"></script>
  <script src="/uoc-sports/public/js/sports-manager/competitions.js" defer></script>
</head>
<body>
<?php
    require "../app/views/templates/general/header.php";
?>
<div class="page-container">

    <!-- Success Message -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div style="padding: 14px 20px; margin: 20px 0; border-radius: 8px; font-size: 15px; font-weight: 500; display: flex; align-items: center; gap: 10px; background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); color: #155724; border-left: 4px solid #28a745;">
            <span style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background-color: #28a745; color: white; border-radius: 50%; font-weight: bold;">✓</span>
            <?= htmlspecialchars($_SESSION['success_message']) ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <!-- Warning Message -->
    <?php if (isset($_SESSION['warning_message'])): ?>
        <div style="padding: 14px 20px; margin: 20px 0; border-radius: 8px; font-size: 15px; font-weight: 500; display: flex; align-items: center; gap: 10px; background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%); color: #856404; border-left: 4px solid #ffc107;">
            <span style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background-color: #ffc107; color: white; border-radius: 50%; font-weight: bold;">!</span>
            <?= htmlspecialchars($_SESSION['warning_message']) ?>
        </div>
        <?php unset($_SESSION['warning_message']); ?>
    <?php endif; ?>

    <!-- Error Message -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <div style="padding: 14px 20px; margin: 20px 0; border-radius: 8px; font-size: 15px; font-weight: 500; display: flex; align-items: center; gap: 10px; background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); color: #721c24; border-left: 4px solid #dc3545;">
            <span style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background-color: #dc3545; color: white; border-radius: 50%; font-weight: bold;">✕</span>
            <?= htmlspecialchars($_SESSION['error_message']) ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="container-header">
        <h2>Competitions</h2>
        <p>Manage competition participants</p>
        <?php if (isset($selectedSportId) && $selectedSportId): ?>
            <div class="filter-info" style="display: inline-flex; align-items: center; gap: 12px; padding: 12px 20px; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-left: 4px solid #3b82f6; border-radius: 8px; margin-top: 10px; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15); font-size: 13px; color: #1e40af; font-weight: 500;">
                <span style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; background: white; border-radius: 6px; font-weight: 600;">
                     Sport: <strong><?= htmlspecialchars($selectedSportId) ?></strong>
                </span>
                <span style="color: #60a5fa;">•</span>
                <span style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; background: white; border-radius: 6px; font-weight: 600;">
                    Total: <strong><?= count($competitions) ?></strong>
                </span>
            </div>
        <?php endif; ?>
      </div>

         


    <!-- Add Participants Form (Initially Hidden) -->
    <div class="form-container" id="addParticipantsForm" style="display: none;">
        <div class="page-header">
            <div>
                <h2>Add Participants</h2>
                <p>Add participants to the competition</p>
            </div>
        </div>
        <form id="participantsForm" class="form" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="sport">Sport *</label>
                    
                </div>

                <div class="form-group">
                    <label for="competitionName">Competition Name *</label>
                    <input type="text" id="competitionName" name="competitionName" placeholder="Inter university Basketball competition" required>
                </div>

               <!-- <div class="form-group full-width"> -->
                <div class="form-group">
                    <label for="participants">Participant Document (PDF)</label>
                    <input type="file" id="participants" name="participants" accept=".pdf">
                </div>
                   
                <div class="form-group">
                    <label for="participant">Select From the Team</label>
                    <select id="participant" name="participant">
                        <option value="">Select From the Team</option>
                        <option value="KS Perera">KS Perera</option>
                        <option value="RS Gamage">RS Gamage</option>
                        <option value="PMW Jayathunga">PMW Jayathunga</option>
                    </select>
                </div>
            </div>
                    
            <div class="form-actions">
                <button type="button" class="view-all-link" onclick="toggleAddParticipantsForm()">
                   Cancel
                </button>
                <button type="button" class="view-all-link">
                   Add Participants
                </button>
            </div>
        </form>
    </div>

    <!-- Competitions Table -->
    <div class="data-table">
        <table>
            <thead>
                <tr>
                    
                    <th onclick="sortTable(1)">Competition Name<span class="sort-indicator"></span></th>
                    
                    <th onclick="sortTable(3)">Competition Date <span class="sort-indicator"></span></th>
                    <th onclick="sortTable(4)">Participant File<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(5)">Count<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(6)">Participants<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(7)">Action<span class="sort-indicator"></span></th>
                </tr>
            </thead>

            <tbody id="tableBody">
            <?php if(!empty($competitions)): ?>
                <?php foreach($competitions as $competition): ?>
                    <tr>
                   
                        <td><?= htmlspecialchars($competition['competition_name']) ?></td>
                       
                        <td class="date-cell"><?= htmlspecialchars(date('M d, Y', strtotime($competition['date']))) ?></td>
                        <td>
                            <?php if (!empty($competition['participant_pdf'])): ?>
                                <a href="/uoc-sports/app/internal/Sport_competitions/<?= htmlspecialchars($competition['participant_pdf']) ?>" 
                                   target="_blank" class="btn-view">
                                  View PDF
                                </a>
                            <?php else: ?>
                                <span style="color: #9ca3af; font-style: italic;">No file</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="participant-count">
                            <?php 
                            if (!empty($competition['participants'])) {
                                $participantCount = count(explode(',', $competition['participants']));
                                echo $participantCount;
                            } else {
                                echo '0';
                            }
                            ?>
                            </span>
                        </td>
                        <td>
                            <span class="participant-list" title="<?= htmlspecialchars($competition['participants'] ?? 'N/A') ?>">
                            <?php 
                            if (!empty($competition['participants'])) {
                                echo htmlspecialchars($competition['participants']);
                            } else {
                                echo '<span style="color: #9ca3af; font-style: italic;">Empty Selection</span>';
                            }
                            ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="/uoc-sports/public/sport-manager/add-participants?competition_id=<?= $competition['competition_id'] ?><?= isset($selectedSportId) ? '&sport=' . urlencode($selectedSportId) : '' ?>" 
                                   class="view-all-link">
                                     Add Participant
                                </a>
                                <form method="POST" action="/uoc-sports/public/sport-manager/delete-competition" style="display: inline; margin: 0;">
                                    <input type="hidden" name="id" value="<?= $competition['competition_id'] ?>">
                                    <button type="button" class="delete-btn" onclick="return confirm('Are you sure you want to delete this competition?')">
                                         Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                     </tr>
                  <?php endforeach; ?>

                    <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 2rem; color: #6b7280;">
                                    No competitions found. <a href="/uoc-sports/public/sport-manager/add-participants">Add your first competition</a>
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>

</div>

</body>
</html>

            <?php
    require "../app/views/templates/general/footer.php";
?>