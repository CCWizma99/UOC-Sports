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

    .expand-btn {
        width: 36px;
        height: 36px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #f8fafc;
        color: #334155;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .expand-btn:hover {
        background: #f3e8ff;
        border-color: #a855f7;
        color: #6b21a8;
    }

    .expand-btn i {
        transition: transform 0.2s ease;
    }

    .competition-row.is-expanded .expand-btn i {
        transform: rotate(90deg);
    }

    .competition-main-cell {
        cursor: pointer;
    }

    .competition-details-row {
        display: none;
    }

    .competition-details-row.is-visible {
        display: table-row;
    }

    .competition-details-cell {
        background: #faf5ff;
        border-top: none;
        padding: 18px 20px 20px;
    }

    .details-panel {
        border: 1px solid #eadcff;
        border-left: 4px solid #a855f7;
        border-radius: 14px;
        background: linear-gradient(145deg, #ffffff 0%, #fcf5ff 100%);
        padding: 18px;
        box-shadow: 0 8px 20px rgba(107, 33, 168, 0.1);
    }

    .details-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e2e8f0;
    }

    .details-header-title {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #6b21a8;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .details-meta {
        font-size: 12px;
        font-weight: 600;
        color: #6b21a8;
        background: #f3e8ff;
        padding: 5px 10px;
        border-radius: 999px;
    }

    .details-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 240px;
        gap: 20px;
        align-items: start;
    }

    .details-title {
        margin: 0 0 10px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #7e22ce;
    }

    .participant-items {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .participant-items li {
        background: #f3e8ff;
        color: #6b21a8;
        border: 1px solid #d8b4fe;
        border-radius: 999px;
        padding: 6px 11px;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.3;
    }

    .no-participants {
        margin: 0;
        color: #64748b;
        font-size: 14px;
        font-style: italic;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        padding: 12px;
    }

    .details-file {
        display: flex;
        justify-content: center;
    }

    .file-card {
        width: 100%;
        background: #f8fafc;
        border: 1px solid #eadcff;
        border-radius: 12px;
        padding: 14px;
    }

    .file-label {
        margin: 0 0 10px;
        font-size: 13px;
        font-weight: 700;
        color: #6b21a8;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .file-empty {
        margin: 0;
        color: #64748b;
        font-size: 13px;
        font-style: italic;
        background: #ffffff;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 10px;
    }

    .details-file .btn-view {
        width: 100%;
        text-align: center;
        justify-content: center;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        border-radius: 10px;
        font-weight: 700;
        text-decoration: none;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e40af;
        border: 2px solid #3b82f6;
        transition: all 0.2s ease;
    }

    .details-file .btn-view:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2);
    }

    @media (max-width: 900px) {
        .details-grid {
            grid-template-columns: 1fr;
        }

        .details-file {
            justify-content: stretch;
        }

        .details-header {
            flex-direction: column;
            align-items: flex-start;
        }
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
                    <th></th>
                    <th onclick="sortTable(1)">Competition Name<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(2)">Competition Date <span class="sort-indicator"></span></th>
                    <th onclick="sortTable(3)">Count<span class="sort-indicator"></span></th>
                    <th onclick="sortTable(4)">Action<span class="sort-indicator"></span></th>
                </tr>
            </thead>

            <tbody id="tableBody">
            <?php if(!empty($competitions)): ?>
                <?php foreach($competitions as $competition): ?>
                    <?php
                        $participants = [];
                        if (!empty($competition['participants'])) {
                            $participants = array_values(array_filter(array_map('trim', explode(',', $competition['participants']))));
                        }
                    ?>
                    <tr class="competition-row" data-competition-id="<?= (int)$competition['competition_id'] ?>">
                        <td>
                            <button type="button" class="expand-btn" aria-label="Expand competition details" aria-expanded="false" onclick="toggleCompetitionDetails(<?= (int)$competition['competition_id'] ?>)">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </td>
                        <td class="competition-main-cell" onclick="toggleCompetitionDetails(<?= (int)$competition['competition_id'] ?>)"><?= htmlspecialchars($competition['competition_name']) ?></td>
                        <td class="date-cell competition-main-cell" onclick="toggleCompetitionDetails(<?= (int)$competition['competition_id'] ?>)"><?= htmlspecialchars(date('M d, Y', strtotime($competition['date']))) ?></td>
                        <td class="competition-main-cell" onclick="toggleCompetitionDetails(<?= (int)$competition['competition_id'] ?>)">
                            <span class="participant-count">
                            <?php 
                            if (!empty($participants)) {
                                $participantCount = count($participants);
                                echo $participantCount;
                            } else {
                                echo '0';
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
                            </div>
                        </td>
                    </tr>
                    <tr class="competition-details-row" id="competition-details-<?= (int)$competition['competition_id'] ?>" style="display: none;">
                        <td colspan="5" class="competition-details-cell">
                            <div class="details-panel">
                                <div class="details-header">
                                    <h4 class="details-header-title"><i class="fa-solid fa-list-check"></i> Competition Details</h4>
                                    <span class="details-meta"><?= count($participants) ?> Participant<?= count($participants) === 1 ? '' : 's' ?></span>
                                </div>
                                <div class="details-grid">
                                    <div>
                                        <p class="details-title">Participants</p>
                                        <?php if (!empty($participants)): ?>
                                            <ul class="participant-items">
                                                <?php foreach($participants as $participant): ?>
                                                    <li><?= htmlspecialchars($participant) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p class="no-participants">No participants added yet.</p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="details-file">
                                        <div class="file-card">
                                            <p class="file-label"><i class="fa-solid fa-file-pdf"></i> Participant Document</p>
                                            <?php if (!empty($competition['participant_pdf'])): ?>
                                                <a href="/uoc-sports/app/internal/Sport_competitions/<?= htmlspecialchars($competition['participant_pdf']) ?>" 
                                                   target="_blank" class="btn-view">
                                                  <i class="fa-solid fa-arrow-up-right-from-square"></i> Open PDF
                                                </a>
                                            <?php else: ?>
                                                <p class="file-empty">No PDF uploaded</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                  <?php endforeach; ?>

                    <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2rem; color: #6b7280;">
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