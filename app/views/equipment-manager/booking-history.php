<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Booking History | UOC Sports E-Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/general/footer.css");
    @import url("/uoc-sports/public/css/equipment-manager/report.css");

    /* Pagination Styling (Page Specific) */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 0 35px 35px 35px;
        padding-top: 1.5rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .pagination-info {
        font-size: 0.9rem;
        color: #6b7280;
    }
    
    .pagination-controls {
        display: flex;
        gap: 0.5rem;
    }
    
    .page-link {
        padding: 0.5rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: white;
        color: #374151;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    
    .page-link:hover {
        background: #f9fafb;
        border-color: #9ca3af;
    }
    
    .page-link.active {
        background: #2b0c4d;
        color: white;
        border-color: #2b0c4d;
    }
    
    .page-link.disabled {
        opacity: 0.5;
        pointer-events: none;
    }

    #statusFilter, #sportFilter, .status-dropdown {
        padding: 0.5rem 1rem;
        border: 3px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.875rem;
        cursor: pointer;
        background: white;
        pointer-events: auto;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .status-dropdown:hover {
        border-color: #5e2d91;
    }

    .status-dropdown:focus {
        outline: none;
        border-color: #5e2d91;
        box-shadow: 0 0 0 3px rgba(94, 45, 145, 0.1);
    }

    /* Status color classes */
    .status-dropdown.status-pending { color: #92400e; border: 2px solid #f59e0b; }
    .status-dropdown.status-accepted { color: #065f46; border: 2px solid #10b981; }
    .status-dropdown.status-active { color: #1e40af; border: 2px solid #3b82f6; }
    .status-dropdown.status-completed { color: #374151; border: 2px solid #6b7280; }
    .status-dropdown.status-rejected { color: #991b1b; border: 2px solid #ef4444; }
  </style>
</head>
<body>
<?php 
    require "../app/views/templates/general/header.php"; 
?>

<div class="report-container">
    <div class="container-header">
        <h2>Equipment Booking History</h2>
        <p>View and manage all past and future booking requests</p>
        
        <?php if (isset($statistics)): ?>
        <div style="display: flex; gap: 2rem; margin-top: 1rem; font-size: 0.9rem;">
            <span><strong>Total:</strong> <?= $statistics['total_requests'] ?? 0 ?></span>
            <span style="color: #f59e0b;"><strong>Pending:</strong> <?= $statistics['pending_count'] ?? 0 ?></span>
            <span style="color: #10b981;"><strong>Accepted:</strong> <?= $statistics['accepted_count'] ?? 0 ?></span>
            <span style="color: #0ea5e9;"><strong>Active:</strong> <?= $statistics['active_count'] ?? 0 ?></span>
            <span style="color: #6b7280;"><strong>Completed:</strong> <?= $statistics['completed_count'] ?? 0 ?></span>
            <span style="color: #ef4444;"><strong>Rejected:</strong> <?= $statistics['rejected_count'] ?? 0 ?></span>
        </div>
        <?php endif; ?>
    </div>

    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search History...">
        
        <!-- Filters -->
        <select id="statusFilter" onchange="filterRequests()">
            <option value="">All Status</option>
            <option value="PENDING" <?= ($filters['status'] ?? '') === 'PENDING' ? 'selected' : '' ?>>Pending</option>
            <option value="ACCEPTED" <?= ($filters['status'] ?? '') === 'ACCEPTED' ? 'selected' : '' ?>>Accepted</option>
            <option value="ACTIVE" <?= ($filters['status'] ?? '') === 'ACTIVE' ? 'selected' : '' ?>>Active</option>
            <option value="COMPLETED" <?= ($filters['status'] ?? '') === 'COMPLETED' ? 'selected' : '' ?>>Completed</option>
            <option value="REJECTED" <?= ($filters['status'] ?? '') === 'REJECTED' ? 'selected' : '' ?>>Rejected</option>
        </select>
        
        <select id="sportFilter" onchange="filterRequests()">
            <option value="">All Sports</option>
            <?php if (isset($sports)): foreach($sports as $sport): ?>
                <option value="<?= $sport['sport_id'] ?>" <?= ($filters['sport_id'] ?? '') === $sport['sport_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sport['sport_name']) ?>
                </option>
            <?php endforeach; endif; ?>
        </select>

        <a href="/uoc-sports/public/equipment-manager/bookingrequests">
            <button class="btn-add">
                <i class="fas fa-arrow-left"></i> Current Requests
            </button>
        </a>
    </div>

    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Requester ID</th>
                    <th onclick="sortTable(1)">Requester Name</th>
                    <th onclick="sortTable(2)">Sport</th>
                    <th onclick="sortTable(3)">Equipment Category</th>
                    <th onclick="sortTable(4)">Requested Date</th>
                    <th onclick="sortTable(5)">Start Time</th>
                    <th onclick="sortTable(6)">End Time</th>
                    <th onclick="sortTable(7)">Location</th>
                    <th onclick="sortTable(8)">Status</th>
                    <th>Special Requests</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="tableBody">
            <?php if(!empty($requests)): ?>
                <?php foreach($requests as $request): ?>
                    <tr>
                        <td><?= htmlspecialchars($request['student_id'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($request['student_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($request['sport_name'] ?? 'N/A') ?></td>
                        <td>
                            <?php 
                            if (!empty($request['equipment_items'])) {
                                $items = json_decode($request['equipment_items'], true);
                                if (is_array($items) && count($items) > 0) {
                                    echo '<div style="display: flex; flex-direction: column; gap: 2px;">';
                                    foreach ($items as $item) {
                                        echo '<span style="font-size: 0.9em;">• ' . htmlspecialchars($item['equipment_name']) . ' <strong>(×' . htmlspecialchars($item['quantity'] ?? 1) . ')</strong></span>';
                                    }
                                    echo '</div>';
                                } else {
                                    echo htmlspecialchars($request['category_name'] ?? 'N/A');
                                }
                            } else {
                                echo htmlspecialchars($request['category_name'] ?? 'N/A');
                            }
                            ?>
                        </td>
                        <td><?= htmlspecialchars($request['request_date']) ?></td>
                        <td><?= date('h:i A', strtotime($request['start_time'])) ?></td>
                        <td><?= date('h:i A', strtotime($request['end_time'])) ?></td>
                        <td><?= htmlspecialchars($request['reserved_location'] ?? 'N/A') ?></td>
                        <td>
                            <select class="status-dropdown status-<?= strtolower($request['status']) ?>" 
                                    data-request-id="<?= $request['request_id'] ?>" 
                                    data-original-status="<?= $request['status'] ?>"
                                    data-student-id="<?= htmlspecialchars($request['student_id'] ?? '') ?>"
                                    data-requester-name="<?= htmlspecialchars($request['requester_name'] ?? ($request['student_name'] ?? '')) ?>"
                                    onchange="updateStatus('<?= $request['request_id'] ?>', this.value, this)">
                                <option value="PENDING" <?= $request['status'] === 'PENDING' ? 'selected' : '' ?>>PENDING</option>
                                <option value="ACCEPTED" <?= $request['status'] === 'ACCEPTED' ? 'selected' : '' ?>>ACCEPTED</option>
                                <option value="ACTIVE" <?= $request['status'] === 'ACTIVE' ? 'selected' : '' ?>>ACTIVE</option>
                                <option value="COMPLETED" <?= $request['status'] === 'COMPLETED' ? 'selected' : '' ?>>COMPLETED</option>
                                <option value="REJECTED" <?= $request['status'] === 'REJECTED' ? 'selected' : '' ?>>REJECTED</option>
                            </select>
                        </td>
                        <td><?= htmlspecialchars($request['notes'] ?? 'N/A') ?></td>
                        <td>
                            <div style="display: flex; gap: 0.5rem; justify-content: center; align-items: center;">
                                <button class="btn-edit" 
                                        title="Send Special Notification"
                                        onclick='openNotificationModal(<?= json_encode($request["request_id"]) ?>, <?= json_encode($request["student_id"] ?? "") ?>, <?= json_encode($request["requester_name"] ?? ($request["student_name"] ?? "")) ?>)'>
                                    <i class="fas fa-bell"></i>
                                </button>
                                <button class="btn-edit" onclick="window.location.href='/uoc-sports/public/equipment-manager/add-booking?id=<?= $request['request_id'] ?>'">
                                    Edit
                                </button>
                                <button class="btn-delete" onclick="deleteRequest('<?= $request['request_id'] ?>')">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" style="text-align: center; padding: 2rem; color: #6b7280;">
                        No history records found.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination-container">
        <div class="pagination-info">
            Showing <?= count($requests) ?> of <?= $totalCount ?> results
        </div>
        <div class="pagination-controls">
            <a href="?page=<?= max(1, $currentPage - 1) . ($filters['status'] ? '&status='.$filters['status'] : '') . ($filters['sport_id'] ? '&sport_id='.$filters['sport_id'] : '') ?>" 
               class="page-link <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                Previous
            </a>
            
            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i . ($filters['status'] ? '&status='.$filters['status'] : '') . ($filters['sport_id'] ? '&sport_id='.$filters['sport_id'] : '') ?>" 
                   class="page-link <?= $currentPage == $i ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <a href="?page=<?= min($totalPages, $currentPage + 1) . ($filters['status'] ? '&status='.$filters['status'] : '') . ($filters['sport_id'] ? '&sport_id='.$filters['sport_id'] : '') ?>" 
               class="page-link <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                Next
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Special Notification Modal -->
<div id="notificationModal" style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 1200; align-items: center; justify-content: center;">
    <div style="background: #fff; width: min(92vw, 560px); border-radius: 10px; box-shadow: 0 16px 40px rgba(0,0,0,0.25); overflow: hidden;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding: 1rem 1.2rem; background: #2b0c4d; color: #fff;">
            <h3 id="notificationModalTitle" style="margin:0; font-size:1rem;">Rejected Reason</h3>
            <button type="button" onclick="closeNotificationModal()" style="background:transparent; border:none; color:#fff; font-size:1.2rem; cursor:pointer;">&times;</button>
        </div>

        <div style="padding: 1rem 1.2rem; display:flex; flex-direction:column; gap:0.75rem;">
            <input id="notificationRequestId" type="hidden">
            <input id="notificationMode" type="hidden" value="notification">

            <div>
                <label for="notificationStudentId" style="display:block; font-weight:600; margin-bottom:0.25rem;">Requester ID</label>
                <input id="notificationStudentId" type="text" readonly style="width:100%; padding:0.55rem; border:1px solid #d1d5db; border-radius:6px; background:#f9fafb;">
            </div>

            <div>
                <label for="notificationRequesterName" style="display:block; font-weight:600; margin-bottom:0.25rem;">Requester Name</label>
                <input id="notificationRequesterName" type="text" readonly style="width:100%; padding:0.55rem; border:1px solid #d1d5db; border-radius:6px; background:#f9fafb;">
            </div>

            <div>
                <label id="notificationMessageLabel" for="notificationMessage" style="display:block; font-weight:600; margin-bottom:0.25rem;">Rejected Reason *</label>
                <textarea id="notificationMessage" rows="4" placeholder="Type rejected reason for this requester..." style="width:100%; padding:0.55rem; border:1px solid #d1d5db; border-radius:6px; resize:vertical;"></textarea>
            </div>

            <div id="notificationHistory" style="display:none;"></div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:0.6rem; padding: 0.9rem 1.2rem 1.1rem; border-top:1px solid #e5e7eb;">
            <button type="button" onclick="closeNotificationModal()" style="padding:0.5rem 0.9rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer;">Cancel</button>
            <button id="notificationSubmitBtn" type="button" onclick="sendSpecialNotification()" style="padding:0.5rem 0.9rem; border:none; border-radius:6px; background:#2b0c4d; color:#fff; cursor:pointer;">
                <i class="fas fa-paper-plane"></i> Submit Reason & Reject
            </button>
        </div>
    </div>
</div>

<script src="/uoc-sports/public/js/equipment-manager/bookingrequest.js"></script>

<?php require "../app/views/templates/general/footer.php"; ?>
</body>
</html>
