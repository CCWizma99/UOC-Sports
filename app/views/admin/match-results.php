<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Match Results Approval | Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/uoc-sports/public/css/admin/results-page.css">
</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';
?>

    <div class="main-content-wrapper" id="main-content">
        <h2 style="margin-bottom: 25px; color: #1e1e2e; font-weight: 800; display: flex; align-items: center; gap: 12px;">
            <i class="fa-solid fa-list-check" style="color: #4b0082; font-size: 22px;"></i> Match Results Approval
        </h2>

        <div id="toast-msg" style="display:none; padding:15px 25px; border-radius:12px; margin-bottom:24px; font-size:14px; font-weight:600; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid transparent;"></div>

        <!-- Filters -->
        <div class="record-filters">
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" id="search-inp" placeholder="Search by tournament, match, or sport..." oninput="filterResults()">
            </div>
            
            <div class="select-container">
                <select id="status-filter" onchange="filterResults()">
                    <option value="">All Statuses</option>
                    <option value="1">Published Results</option>
                    <option value="0">Pending Review</option>
                </select>
            </div>
        </div>

        <div class="record-card">
            <div class="table-responsive">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Tournament / Event</th>
                            <th>Match Description</th>
                            <th>Outcome & Date</th>
                            <th>Submitted By</th>
                            <th>Status</th>
                            <th style="width: 250px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="results-tbody">
                        <tr><td colspan="6" style="text-align:center; padding:60px; color:#64748b;"><i class="fas fa-circle-notch fa-spin"></i> Loading official results...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div id="custom-confirm-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content" style="max-width: 420px;">
            <div class="modal-header" style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9;">
                <h3 id="confirm-modal-title" style="margin: 0; color: #1e1e2e; font-size: 17px; display: flex; align-items: center; gap: 10px; font-weight: 800;">
                    <i class="fa-solid fa-circle-question" style="color: #4b0082;"></i> Confirm Action
                </h3>
            </div>
            <div class="modal-body" style="padding: 24px; font-size: 15px; color: #475569; white-space: pre-line; line-height: 1.6;" id="confirm-modal-message">
                Are you sure?
            </div>
            <div class="modal-footer" style="padding: 16px 24px; display: flex; justify-content: flex-end; gap: 12px; background: #faf9fc; border-top: 1px solid #f1f5f9;">
                <button class="btn-action btn-unpub" onclick="closeConfirmModal()">Cancel</button>
                <button id="confirm-modal-action-btn" class="btn-action" style="color: white; border: none;">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Match Details Info Modal -->
    <div id="details-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header" style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9;">
                <h3 id="details-modal-title" style="margin: 0; color: #1e1e2e; font-size: 17px; display: flex; align-items: center; gap: 10px; font-weight: 800;">
                    <i class="fa-solid fa-clipboard-list" style="color: #4b0082;"></i> Match Details
                </h3>
                <span class="close-modal" onclick="document.getElementById('details-modal').style.display='none'" style="cursor: pointer; font-size: 24px; color: #94a3b8;">&times;</span>
            </div>
            <div class="modal-body" style="padding: 0; max-height: 70vh; overflow-y: auto;">
                <div id="details-modal-content">
                    Loading details...
                </div>
            </div>
            <div class="modal-footer" style="padding: 16px 24px; background: #faf9fc; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end;">
                <button class="btn-action btn-unpub" onclick="document.getElementById('details-modal').style.display='none'">Close Details</button>
            </div>
        </div>
    </div>

<script>
    document.getElementById('sidebar-results').classList.add('active');
    
    let allResults = [];
    let confirmResolver = null;

    // Custom confirm modal
    function showConfirmModal(title, message, confirmText, isDanger) {
        document.getElementById('confirm-modal-title').innerHTML = isDanger ? 
            `<i class="fa-solid fa-circle-exclamation" style="color: #dc2626;"></i> ${title}` : 
            `<i class="fa-solid fa-circle-check" style="color: #16a34a;"></i> ${title}`;
        document.getElementById('confirm-modal-message').innerText = message;
        
        const btn = document.getElementById('confirm-modal-action-btn');
        btn.innerText = confirmText;
        if(isDanger) {
            btn.style.background = '#dc2626'; btn.style.boxShadow = '0 3px 10px rgba(220, 38, 38, 0.3)';
        } else {
            btn.style.background = 'linear-gradient(135deg, #16a34a, #22c55e)'; btn.style.boxShadow = '0 3px 10px rgba(22, 163, 74, 0.3)';
        }
        document.getElementById('custom-confirm-modal').style.display = 'flex';
        return new Promise(resolve => confirmResolver = resolve);
    }
    function closeConfirmModal() { document.getElementById('custom-confirm-modal').style.display = 'none'; if(confirmResolver) confirmResolver(false); }
    document.getElementById('confirm-modal-action-btn').addEventListener('click', () => { document.getElementById('custom-confirm-modal').style.display = 'none'; if(confirmResolver) confirmResolver(true); });

    function showToast(status, text) {
        const msg = document.getElementById('toast-msg');
        msg.style.display = 'block';
        msg.textContent = text;
        msg.style.background = status === 'success' ? '#dcfce7' : '#fee2e2';
        msg.style.color = status === 'success' ? '#16a34a' : '#dc2626';
        msg.style.border = `1px solid ${status === 'success' ? '#86efac' : '#fca5a5'}`;
        setTimeout(() => msg.style.display = 'none', 5000);
    }

    async function loadResults() {
        document.getElementById('results-tbody').innerHTML = '<tr><td colspan="6" style="text-align:center; padding:40px;"><i class="fas fa-spinner fa-spin"></i> Fetching records...</td></tr>';
        try {
            const res = await fetch('admin-results/get-all');
            const data = await res.json();
            if(data.status === 'success') {
                allResults = data.data;
                filterResults();
            } else {
                showToast('error', data.message);
            }
        } catch(e) { showToast('error', 'Network error loading results.'); }
    }

    function filterResults() {
        const q = document.getElementById('search-inp').value.toLowerCase();
        const s = document.getElementById('status-filter').value;
        const tbody = document.getElementById('results-tbody');
        tbody.innerHTML = '';

        const subset = allResults.filter(r => {
            if(s !== '' && String(r.is_published) !== s) return false;
            if(q && !r.tournament_name.toLowerCase().includes(q) && !r.match_name.toLowerCase().includes(q)) return false;
            return true;
        });

        if(subset.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:40px; color:#64748b;">No matching results found.</td></tr>';
            return;
        }

        subset.forEach(r => {
            const pub = parseInt(r.is_published);
            const statusBadge = pub ? `<span class="status-badge status-published"><i class="fas fa-check-circle"></i> Published</span>` 
                                    : `<span class="status-badge status-unpublished"><i class="fas fa-clock"></i> Pending</span>`;
            
            const detailsBtn = `<button class="btn-action btn-view" onclick="viewDetails('${r.match_id}', '${r.match_name.replace(/'/g, "\\'")}')"><i class="fas fa-eye"></i> Details</button>`;

            const actionBtn = pub 
                ? `${detailsBtn}<button class="btn-action btn-unpub" onclick="togglePublish('${r.match_id}', 0, '${r.match_name.replace(/'/g, "\\'")}')"><i class="fas fa-ban"></i> Unpublish</button>`
                : `${detailsBtn}<button class="btn-action btn-pub" onclick="togglePublish('${r.match_id}', 1, '${r.match_name.replace(/'/g, "\\'")}')"><i class="fas fa-globe"></i> Publish</button>`;

            const releaseEvtBtn = !pub ? `<button class="btn-action btn-release" onclick="publishTournament('${r.tournament_id}', '${r.tournament_name.replace(/'/g, "\\'")}')" title="Release Tournament Results"><i class="fas fa-bolt"></i> Release Event</button>` : '';

            const dateF = r.match_date ? new Date(r.match_date).toLocaleDateString() : 'N/A';
            
            // Unified Winner Display
            let winnerHtml = '';
            if (r.winner_type === 'DRAW') {
                winnerHtml = `<span style="color:#64748b; font-weight:700;"><i class="fas fa-equals"></i> DRAW</span>`;
            } else if (r.winner_display_name) {
                const icon = r.winner_type === 'TEAM' ? 'fa-users' : 'fa-user';
                winnerHtml = `<strong style="color:#4b0082;"><i class="fas ${icon}" style="font-size:10px; margin-right:4px;"></i>${r.winner_display_name}</strong>`;
            } else {
                winnerHtml = `<span style="color:#94a3b8; font-style:italic;">Pending</span>`;
            }

            // Score Summary (Placeholder for now, logic can be expanded)
            let scoreSummary = `<span style="font-size:12px; color:#64748b;">${r.result_status}</span>`;

            tbody.innerHTML += `
                <tr>
                    <td>
                        <strong style="color:#1e1e2e; display:block; margin-bottom:4px;">${r.tournament_name}</strong>
                        <span class="sport-badge-inline" style="font-size:11px; color:#6366f1; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;"><i class="fas fa-medal"></i> ${r.sport_name}</span>
                    </td>
                    <td>
                        <strong style="color:#1e293b;">${r.match_name}</strong><br>
                        ${scoreSummary}
                    </td>
                    <td>
                        <div style="font-size:13px; margin-bottom:4px;"><i class="far fa-calendar-alt"></i> ${dateF}</div>
                        <div style="font-size:14px;">${winnerHtml}</div>
                    </td>
                    <td><span class="role-badge">${r.submitted_by || 'ADMIN'}</span></td>
                    <td>${statusBadge}</td>
                    <td class="action-row">
                        ${actionBtn}
                        ${releaseEvtBtn}
                    </td>
                </tr>
            `;
        });
    }

    async function togglePublish(matchId, status, matchName) {
        const txt = status === 1 ? `Publish "${matchName}" so it appears on the public portal?` : `Unpublish "${matchName}"? It will be hidden from the public portal.`;
        const ans = await showConfirmModal(status === 1 ? 'Publish Result' : 'Unpublish Result', txt, status === 1 ? 'Publish' : 'Unpublish', status === 0);
        if(!ans) return;

        try {
            const res = await fetch('admin-results/toggle-publish', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ match_id: matchId, status: status })
            });
            const data = await res.json();
            showToast(data.status, data.status === 'success' ? (status ? '✓ Published publicly' : '✓ Un-published publicly') : data.message);
            if(data.status === 'success') loadResults();
        } catch(e) { showToast('error', 'Network error.'); }
    }

    async function publishTournament(tid, tname) {
        const ans = await showConfirmModal('Release Entire Event', `This will permanently publish ALL pending matches for "${tname}" and REVOKE the captain's edit permissions immediately.\n\nProceed?`, 'Yes, Release Event', true);
        if(!ans) return;

        try {
            const res = await fetch('admin-results/publish-tournament', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ tournament_id: tid })
            });
            const data = await res.json();
            showToast(data.status, data.status === 'success' ? '✓ ' + data.message : '✗ ' + data.message);
            if(data.status === 'success') loadResults();
        } catch(e) { showToast('error', 'Network error.'); }
    }

    async function viewDetails(matchId, matchName) {
        document.getElementById('details-modal-title').innerHTML = `<i class="fa-solid fa-clipboard-list" style="color: #4b0082;"></i> Details: ${matchName}`;
        document.getElementById('details-modal-content').innerHTML = '<div style="text-align:center; padding: 20px;"><i class="fas fa-circle-notch fa-spin"></i> Loading...</div>';
        document.getElementById('details-modal').style.display = 'flex';

        try {
            const res = await fetch(`admin-results/details/${matchId}`);
            const data = await res.json();
            
            if (data.status === 'success') {
                const match = data.data;
                const details = match.details || {};
                
                // Determine winner display for modal
                const winnerDisplay = match.winner_display_name || 'N/A';

                let html = `
                    <div style="background:#f8fafc; padding:15px; border-radius:12px; margin-bottom:20px; border:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <strong style="display:block; margin-bottom:4px; color:#1e293b; font-size:16px;"><i class="fas fa-trophy" style="color:#eab308;"></i> ${match.tournament_name}</strong>
                            <span style="font-size:13px; color:#64748b;"><i class="fas fa-running"></i> ${match.sport_name}</span>
                        </div>
                        <div style="text-align:right;">
                            <span style="font-size:11px; color:#94a3b8; text-transform:uppercase; font-weight:700; display:block; margin-bottom:4px; letter-spacing:1px;">Outcome</span>
                            <strong style="color:#4b0082; font-size:15px;">${winnerDisplay}</strong>
                        </div>
                    </div>
                    <table style="width:100%; border-collapse: collapse; border-radius:8px; overflow:hidden; border:1px solid #f1f5f9;">
                        <tbody>
                `;
                
                if (Object.keys(details).length === 0) {
                    html += `<tr><td style="padding: 10px; color: #64748b; text-align: center;">No rich scoring details available for this match.</td></tr>`;
                } else {
                    for (const [key, val] of Object.entries(details)) {
                        const niceKey = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                        let displayVal = val;
                        // Beautifully format nested objects/arrays if present
                        if (typeof val === 'object' && val !== null) {
                            displayVal = '<pre style="margin:0; background:#f1f5f9; padding:8px; border-radius:4px; font-size:11px;">' + JSON.stringify(val, null, 2) + '</pre>';
                        } else if (val === null || val === '') {
                            displayVal = '<span style="color:#94a3b8;">-</span>';
                        }
                        
                        html += `
                            <tr class="detail-row">
                                <td class="detail-label">${niceKey}</td>
                                <td class="detail-value">${displayVal}</td>
                            </tr>
                        `;
                    }
                }
                html += `</tbody></table>`;
                
                document.getElementById('details-modal-content').innerHTML = html;
            } else {
                document.getElementById('details-modal-content').innerHTML = `<p style="color:#dc2626;">Error loading details: ${data.message || 'Unknown error'}</p>`;
            }
        } catch(e) {
            document.getElementById('details-modal-content').innerHTML = `<p style="color:#dc2626;">Network error while fetching match details.</p>`;
        }
    }

    loadResults();
</script>

<?php require '../app/views/templates/admin/footer.php'; ?>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</body>
</html>
