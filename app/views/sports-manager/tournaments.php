<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sports Manager - Sport Events</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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

    .manage-btn {
        background: linear-gradient(135deg, #7c3aed 0%, #6b21a8 100%);
        color: white;
        border: 2px solid #6b21a8;
        box-shadow: 0 2px 6px rgba(124, 58, 237, 0.3);
    }

    .manage-btn:hover {
        background: linear-gradient(135deg, #6b21a8 0%, #581c87 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.4);
    }

    /* Status badges */
    .status-badge {
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-upcoming { background: #dbeafe; color: #1e40af; }
    .status-ongoing { background: #dcfce7; color: #166534; }
    .status-completed { background: #f3f4f6; color: #374151; }
    .status-postponed { background: #fee2e2; color: #991b1b; }

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
  <script src="/uoc-sports/public/js/sports-manager/dynamic-background.js"></script>
</head>
<body>
<?php
    require "../app/views/templates/general/header.php";
?>
<div class="page-container">

    <!-- Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div style="padding: 14px 20px; margin: 20px 0; border-radius: 8px; font-size: 15px; font-weight: 500; display: flex; align-items: center; gap: 10px; background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); color: #155724; border-left: 4px solid #28a745;">
            <span style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background-color: #28a745; color: white; border-radius: 50%; font-weight: bold;">✓</span>
            <?= htmlspecialchars($_SESSION['success_message']) ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div style="padding: 14px 20px; margin: 20px 0; border-radius: 8px; font-size: 15px; font-weight: 500; display: flex; align-items: center; gap: 10px; background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); color: #721c24; border-left: 4px solid #dc3545;">
            <span style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background-color: #dc3545; color: white; border-radius: 50%; font-weight: bold;">✕</span>
            <?= htmlspecialchars($_SESSION['error_message']) ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="container-header">
        <h2>Sport Events</h2>
        <p>View upcoming tournaments and manage team registrations</p>
        <?php if (isset($selectedSportId) && $selectedSportId): ?>
            <div class="filter-info">
                <span style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; background: white; border-radius: 6px; font-weight: 600;">
                     Sport: <strong><?= htmlspecialchars($selectedSportId) ?></strong>
                </span>
                <span style="color: #60a5fa;">•</span>
                <span style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; background: white; border-radius: 6px; font-weight: 600;">
                    Total Events: <strong><?= count($tournaments) ?></strong>
                </span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tournaments Table -->
    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th>Tournament Name</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Team Size</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="tableBody">
            <?php if(!empty($tournaments)): ?>
                <?php 
                $model = new TournamentParticipant();
                foreach($tournaments as $tournament): 
                    $participantCount = count($model->getParticipants($tournament['tournament_id']));
                ?>
                    <tr>
                        <td>
                            <div style="font-weight: 600;"><?= htmlspecialchars($tournament['tournament_name']) ?></div>
                            <div style="font-size: 11px; color: #6b7280;"><?= htmlspecialchars($tournament['tournament_id']) ?></div>
                        </td>
                        <td class="date-cell"><?= htmlspecialchars(date('M d, Y', strtotime($tournament['date']))) ?></td>
                        <td>
                            <?php 
                                $status = strtolower($tournament['status']);
                                $statusClass = "status-" . $status;
                            ?>
                            <span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($tournament['status']) ?></span>
                        </td>
                        <td>
                            <span class="participant-count"><?= $participantCount ?></span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="/uoc-sports/public/sport-manager/add-participants?tournament_id=<?= urlencode($tournament['tournament_id']) ?><?= isset($selectedSportId) ? '&sport=' . urlencode($selectedSportId) : '' ?>" 
                                   class="view-all-link" style="padding: 6px 15px;">
                                     Manage Participants
                                </a>
                            </div>
                        </td>
                     </tr>
                  <?php endforeach; ?>

            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: #6b7280;">
                        <i class="fas fa-calendar-times" style="font-size: 2rem; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                        No upcoming tournaments found for this sport. 
                        <br><span style="font-size: 12px;">Contact system administrator to create new sport events.</span>
                    </td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>

<?php
    require "../app/views/templates/general/footer.php";
?>

</body>
</html>