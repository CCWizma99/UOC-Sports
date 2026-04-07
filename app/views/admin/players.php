<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Records | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
        
        @import url(/uoc-sports/public/css/admin/players-page.css);
        @import url(/uoc-sports/public/css/admin/ui-improvements.css);
    </style>
</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';
?>

<div class="main-content-wrapper">
    <div class="players-grid-container">
        <div class="players-grid-left">
            <section id="search-user">
                <h2>Search Player Records</h2>
                <div class="filter-bar">
                    <h3>Filter <i class="fa-solid fa-filter"></i></h3>

                    <div class="btn" id="faculty-btn">
                        Faculty
                        <div class="dropdown" data-filter="faculty">
                            <div data-value="">All</div>
                            <?php foreach ($faculty_data as $faculty): ?>
                                <div data-value="<?= htmlspecialchars($faculty['faculty_name']) ?>">
                                    <?= htmlspecialchars($faculty['faculty_name']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="btn" id="year-btn">
                        Year
                        <div class="dropdown" data-filter="year">
                            <div data-value="">All</div>
                            <div data-value="1">1</div>
                            <div data-value="2">2</div>
                            <div data-value="3">3</div>
                            <div data-value="4">4</div>
                        </div>
                    </div>

                    <div class="btn" id="sport-btn">
                        Sport
                        <div class="dropdown" data-filter="sport">
                            <div data-value="">All</div>
                            <?php foreach ($sport_data as $s): ?>
                                <div data-value="<?= htmlspecialchars($s['sport_name']) ?>">
                                    <?= htmlspecialchars($s['sport_name']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="btn" id="public-btn">
                        Type
                        <div class="dropdown" data-filter="type">
                            <div data-value="">All</div>
                            <div data-value="Student">Student</div>
                            <div data-value="Staff">Staff</div>
                        </div>
                    </div>
                </div>

                <input type="text" name="search-user-inp" id="search-user-inp" 
                    title="Enter user ID No. or Name" placeholder="Enter User ID or Name">

                <div class="search-output">
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h3>Search Player Records</h3>
                        <p>Use the filters or search to find players</p>
                    </div>
                </div>
            </section>
        </div>

        <div class="players-grid-right">
            <!-- ===== GRANT CAPTAIN PERMISSION PANEL ===== -->
            <section id="grant-permission-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0;"><i class="fas fa-unlock-alt" style="color:#5e2d91;margin-right:8px;"></i>Allow Captain to Add Results</h2>
                    <a href="./admin-event-permissions" class="btn-history" style="font-size: 12px; color: #5e2d91; text-decoration: none; display: flex; align-items: center; gap: 5px; background: #f3f0f7; padding: 6px 12px; border-radius: 8px; font-weight: 600; transition: all 0.2s;">
                        <i class="fas fa-history"></i> View History
                    </a>
                </div>
                <p style="font-size:13px;color:#6b7280;margin:0 0 20px;">Select a tournament that has already started to grant its captain permission to submit match results.</p>

                <!-- Tournament Cards (started events) -->
                <div id="started-tournaments-loading" style="padding:20px;color:#8b5cf6;font-size:13px;">
                    <i class="fas fa-spinner fa-spin"></i> Loading started tournaments...
                </div>
                <div id="started-tournaments-list" style="display:none;"></div>
                <div id="started-tournaments-empty" style="display:none;padding:24px;text-align:center;color:#9ca3af;font-size:13px;">
                    <i class="fas fa-calendar-times" style="font-size:32px;opacity:0.3;display:block;margin-bottom:10px;"></i>
                    No tournaments have started yet.
                </div>

                <div id="grant-msg" style="display:none;margin-top:14px;"></div>
            </section>
        </div>
    </div>
</div>

<!-- Player Match History Modal -->
<div id="match-history-modal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-trophy"></i> Match History</h2>
            <button class="modal-close" onclick="closeMatchHistoryModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="player-info-header"></div>
            <div id="match-history-content">
                <div class="loading-spinner">Loading...</div>
            </div>
        </div>
    </div>
</div>

<!-- Search Match Modal -->
<div id="search-match-modal" class="modal-overlay" style="display:none;">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2><i class="fas fa-search"></i> Search Matches</h2>
            <button class="modal-close" onclick="closeSearchMatchModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="search-match-filters">
                <input type="text" id="search-match-query" placeholder="Type to search matches..." oninput="debounceMatchSearch()">
                <select id="search-match-sport" onchange="performMatchSearch()">
                    <option value="">All Sports</option>
                </select>
            </div>
            <div class="search-match-container">
                <div id="search-match-results">
                    <p class="hint-text">Start typing to search matches...</p>
                </div>
                <div id="match-details-panel" class="match-details-panel" style="display:none;">
                    <div class="details-header">
                        <button class="btn-back" onclick="hideMatchDetails()"><i class="fas fa-arrow-left"></i> Back</button>
                        <h3 id="details-match-name"></h3>
                    </div>
                    <div id="match-details-content">
                        <!-- Match details will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Button for Search Match -->
<button class="fab-search-match" onclick="openSearchMatchModal()" title="Search Matches">
    <i class="fas fa-search"></i>
</button>

<!-- Custom Confirmation Modal -->
<div id="custom-confirm-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content" style="max-width: 420px; animation: modalSlideIn 0.2s ease;">
        <div class="modal-header" style="padding: 18px 22px;">
            <h3 id="confirm-modal-title" style="margin: 0; color: #1e1e2e; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-circle-question" style="color: #4b0082;"></i> Confirm Action
            </h3>
            <span class="close-modal" onclick="closeConfirmModal()" style="cursor: pointer; font-size: 22px; color: #9ca3af; line-height: 1;">&times;</span>
        </div>
        <div class="modal-body" style="padding: 22px; font-size: 14px; color: #4b5563; white-space: pre-line; line-height: 1.5;" id="confirm-modal-message">
            Are you sure?
        </div>
        <div class="modal-footer" style="padding: 16px 22px; display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #f3f4f6; background: #faf9fc; border-radius: 0 0 1.5rem 1.5rem;">
            <button onclick="closeConfirmModal()" style="background: #e5e7eb; color: #374151; border: none; padding: 10px 18px; border-radius: 10px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s;">
                Cancel
            </button>
            <button id="confirm-modal-action-btn" style="border: none; padding: 10px 18px; border-radius: 10px; font-weight: 700; font-size: 13px; cursor: pointer; color: white; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);">
                Confirm
            </button>
        </div>
    </div>
</div>


<script>
// Search Player Records Script
const filters = { faculty: '', year: '', sport: '', type: '' };

// Store original button labels for reset
document.querySelectorAll('.filter-bar .btn').forEach(btn => {
    btn.setAttribute('data-original', btn.childNodes[0].textContent.trim());
});

// Toggle dropdown visibility
document.querySelectorAll('.filter-bar .btn').forEach(btn => {
    btn.addEventListener('click', e => {
        e.stopPropagation();
        document.querySelectorAll('.dropdown').forEach(dd => {
            if (dd.parentElement !== btn) dd.classList.remove('show');
        });
        btn.querySelector('.dropdown').classList.toggle('show');
    });
});

// Select filter value
document.querySelectorAll('.dropdown div').forEach(option => {
    option.addEventListener('click', e => {
        const value = e.target.getAttribute('data-value');
        const filterType = e.target.parentElement.getAttribute('data-filter');
        const btn = e.target.closest('.btn');

        filters[filterType] = value;

        const labelNode = btn.childNodes[0]; 
        const originalLabel = btn.getAttribute('data-original');

        if (value === '') {
            labelNode.textContent = originalLabel;
        } else {
            labelNode.textContent = value;
        }

        e.target.closest('.dropdown').classList.remove('show');
        performSearch();
    });
});

// Close dropdowns when clicking outside
document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown').forEach(dd => dd.classList.remove('show'));
});

// Search on typing
document.getElementById('search-user-inp').addEventListener('input', performSearch);

function performSearch() {
    const query = document.getElementById('search-user-inp').value.trim();
    if (query.length === 0 && Object.values(filters).every(f => f === '')) {
        document.querySelector('.search-output').innerHTML = '';
        return;
    }

    const params = new URLSearchParams({ q: query, ...filters });

    fetch(`/uoc-sports/public/api/search-user.php?${params.toString()}`)
        .then(res => res.json())
        .then(data => {
            const outputDiv = document.querySelector('.search-output');
            if (data.length > 0) {
                let html = `
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                data.forEach(user => {
                    html += `
                        <tr>
                            <td>${user.user_id}</td>
                            <td>${user.fname} ${user.lname}</td>
                            <td>${user.type}</td>
                            <td class="action-buttons">
                                <button class="btn-icon btn-history" onclick="openMatchHistoryModal('${user.user_id}', '${user.fname} ${user.lname}')" title="View Match History">
                                    <i class="fas fa-trophy"></i>
                                </button>
                                <a href="./admin-user-profile?id=${user.user_id}" class="action-link" title="View Profile">
                                    <i class="fa-solid fa-circle-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                });
                html += `</tbody></table>`;
                outputDiv.innerHTML = html;
            } else {
                outputDiv.innerHTML = '<p>No users found.</p>';
            }
        })
        .catch(err => {
            console.error('Search error:', err);
            document.querySelector('.search-output').innerHTML = '<p>Error occurred.</p>';
        });
}
</script>

<div id="custom-confirm-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content" style="max-width: 420px; animation: modalSlideIn 0.2s ease;">
        <div class="modal-header" style="padding: 18px 22px;">
            <h3 id="confirm-modal-title" style="margin: 0; color: #1e1e2e; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-circle-question" style="color: #4b0082;"></i> Confirm Action
            </h3>
            <span class="close-modal" onclick="closeConfirmModal()" style="cursor: pointer; font-size: 22px; color: #9ca3af; line-height: 1;">&times;</span>
        </div>
        <div class="modal-body" style="padding: 22px; font-size: 14px; color: #4b5563; white-space: pre-line; line-height: 1.5;" id="confirm-modal-message">
            Are you sure?
        </div>
        <div class="modal-footer" style="padding: 16px 22px; display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #f3f4f6; background: #faf9fc; border-radius: 0 0 1.5rem 1.5rem;">
            <button onclick="closeConfirmModal()" style="background: #e5e7eb; color: #374151; border: none; padding: 10px 18px; border-radius: 10px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s;">
                Cancel
            </button>
            <button id="confirm-modal-action-btn" style="border: none; padding: 10px 18px; border-radius: 10px; font-weight: 700; font-size: 13px; cursor: pointer; color: white; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);">
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
// ========== Grant Captain Permission Script ==========
async function loadStartedTournaments() {
    try {
        const res  = await fetch('admin-tournament/started-tournaments');
        const data = await res.json();
        const loading = document.getElementById('started-tournaments-loading');
        const list    = document.getElementById('started-tournaments-list');
        const empty   = document.getElementById('started-tournaments-empty');

        loading.style.display = 'none';

        if (data.status === 'success' && data.data && data.data.length > 0) {
            let html = '<div class="tournament-grant-grid">';
            data.data.forEach(t => {
                const hasCaptain   = t.captain_id && t.captain_id !== '';
                const isActive     = t.permission_status === 'ACTIVE';
                const isRevoked    = t.permission_status === 'REVOKED';

                const startDate = t.start_date ? new Date(t.start_date).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'}) : 'N/A';

                html += `<div class="t-grant-card" id="tcard-${t.tournament_id}">`;
                html += `<div class="t-grant-card-top">`;
                html += `<div><div class="t-grant-name">${t.tournament_name}</div>`;
                html += `<div class="t-grant-meta">`;
                html += `<span class="t-meta-sport">${t.sport_name}</span>`;
                html += `<span class="t-meta-date"><i class="fas fa-calendar-alt"></i> Started: ${startDate}</span>`;
                if (hasCaptain) {
                    html += `<span class="t-meta-captain"><i class="fas fa-user-tie"></i> ${t.captain_name || 'Captain assigned'}</span>`;
                } else {
                    html += `<span class="t-meta-no-captain"><i class="fas fa-user-slash"></i> No captain assigned</span>`;
                }
                html += `</div></div>`;

                if (!hasCaptain) {
                    html += `<button class="btn-grant btn-grant-disabled" disabled title="Assign a captain first">No Captain</button>`;
                } else if (isActive) {
                    html += `<button class="btn-grant btn-grant-revoke" onclick="revokePermission(${t.permission_id}, '${t.tournament_name}')">`;
                    html += `<i class="fas fa-lock"></i> Revoke</button>`;
                } else {
                    html += `<button class="btn-grant btn-grant-allow" onclick="grantPermission('${t.tournament_id}', '${t.tournament_name}')">`;
                    html += isRevoked ? '<i class="fas fa-rotate-right"></i> Re-grant' : '<i class="fas fa-unlock"></i> Grant Access';
                    html += `</button>`;
                }
                html += `</div>`;

                if (isActive) {
                    html += `<div class="t-grant-status active-status"><i class="fas fa-check-circle"></i> Permission ACTIVE</div>`;
                } else if (isRevoked) {
                    html += `<div class="t-grant-status revoked-status"><i class="fas fa-ban"></i> Permission revoked</div>`;
                }
                html += `</div>`;
            });
            html += '</div>';
            list.innerHTML = html;
            list.style.display = 'block';
        } else {
            empty.style.display = 'block';
        }
    } catch (e) {
        console.error('Error loading started tournaments:', e);
    }
}

async function grantPermission(tournamentId, tournamentName) {
    const msg = document.getElementById('grant-msg');
    msg.style.display = 'none';

    const confirmed = await showConfirmModal('Grant Access', `Grant the captain permission to add results for:\n"${tournamentName}"?`, 'Grant', false);
    if (!confirmed) return;

    try {
        const res  = await fetch('admin-tournament/grant-captain-permission', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ tournament_id: tournamentId })
        });
        const data = await res.json();
        showGrantMsg(data.status === 'success' ? 'success' : 'error', data.message);
        if (data.status === 'success') loadStartedTournaments();
    } catch (e) {
        showGrantMsg('error', 'Network error.');
    }
}

async function revokePermission(permId, tournamentName) {
    const msg = document.getElementById('grant-msg');
    msg.style.display = 'none';

    const confirmed = await showConfirmModal('Revoke Access', `Revoke captain's permission for:\n"${tournamentName}"?`, 'Revoke', true);
    if (!confirmed) return;

    try {
        const res  = await fetch('admin-tournament/revoke-captain-permission', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ permission_id: permId })
        });
        const data = await res.json();
        showGrantMsg(data.status === 'success' ? 'success' : 'error', 'Permission revoked.');
        if (data.status === 'success') loadStartedTournaments();
    } catch (e) {
        showGrantMsg('error', 'Network error.');
    }
}

function showGrantMsg(type, text) {
    const el = document.getElementById('grant-msg');
    el.className   = type === 'success' ? 'grant-msg-success' : 'grant-msg-error';
    el.textContent = text;
    el.style.display = 'block';
    setTimeout(() => { el.style.display = 'none'; }, 5000);
}

// ========== Event Initialization ==========
document.addEventListener('DOMContentLoaded', () => {
    loadStartedTournaments();
});

// Custom Modal Logic
let confirmResolver = null;
function showConfirmModal(title, message, confirmText, isDanger) {
    document.getElementById('confirm-modal-title').innerHTML = isDanger ? 
        `<i class="fa-solid fa-circle-exclamation" style="color: #dc2626;"></i> ${title}` : 
        `<i class="fa-solid fa-circle-check" style="color: #16a34a;"></i> ${title}`;
    document.getElementById('confirm-modal-message').innerText = message;
    const actionBtn = document.getElementById('confirm-modal-action-btn');
    actionBtn.innerText = confirmText;
    if (isDanger) {
        actionBtn.style.background = '#dc2626';
    } else {
        actionBtn.style.background = 'linear-gradient(135deg, #16a34a, #22c55e)';
    }
    document.getElementById('custom-confirm-modal').style.display = 'flex';
    return new Promise(resolve => { confirmResolver = resolve; });
}
function closeConfirmModal() {
    document.getElementById('custom-confirm-modal').style.display = 'none';
    if (confirmResolver) { confirmResolver(false); confirmResolver = null; }
}
document.getElementById('confirm-modal-action-btn').addEventListener('click', () => {
    document.getElementById('custom-confirm-modal').style.display = 'none';
    if (confirmResolver) { confirmResolver(true); confirmResolver = null; }
});
</script>

<?php require '../app/views/templates/admin/footer.php'; ?>

</body>
<script>
    var currentPage = document.getElementById("sidebar-players");
    currentPage.classList.add("active") 
</script>
<script src="/uoc-sports/public/js/search-keyboard-nav.js"></script>
<script>
    SearchKeyboardNav.init({
        inputSelector: '#search-user-inp',
        resultsSelector: '.search-output',
        itemSelector: 'tbody tr',
        actionSelector: '.action-link'
    });
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>

<script>
// ========== Match History Modal Functions ==========
function openMatchHistoryModal(userId, playerName) {
    const modal = document.getElementById('match-history-modal');
    const playerHeader = document.getElementById('player-info-header');
    const content = document.getElementById('match-history-content');
    
    playerHeader.innerHTML = `<h3>${playerName}</h3>`;
    content.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading match history...</div>';
    modal.style.display = 'flex';
    
    fetch(`admin-sport/player-match-history?user_id=${userId}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.data.length > 0) {
                let html = '<div class="match-history-list">';
                data.data.forEach(match => {
                    const outcomeClass = match.outcome === 'WON' ? 'outcome-won' : 'outcome-participated';
                    const date = new Date(match.match_date).toLocaleDateString('en-US', { 
                        year: 'numeric', month: 'short', day: 'numeric' 
                    });
                    html += `
                        <div class="match-card">
                            <div class="match-card-header">
                                <span class="sport-badge">${match.sport_name}</span>
                                <span class="outcome-badge ${outcomeClass}">${match.outcome}</span>
                            </div>
                            <h4>${match.match_name}</h4>
                            <p class="tournament-name">${match.tournament_name}</p>
                            <div class="match-meta">
                                <span><i class="fas fa-calendar"></i> ${date}</span>
                                ${match.player_score ? `<span><i class="fas fa-star"></i> Score: ${match.player_score}</span>` : ''}
                            </div>
                            ${match.winner_score && match.loser_score ? 
                                `<div class="match-score">Final: ${match.winner_score} - ${match.loser_score}</div>` : ''}
                        </div>
                    `;
                });
                html += '</div>';
                content.innerHTML = html;
            } else {
                content.innerHTML = '<p class="no-data">No match history found for this player.</p>';
            }
        })
        .catch(err => {
            console.error('Error fetching match history:', err);
            content.innerHTML = '<p class="error-text">Failed to load match history.</p>';
        });
}

function closeMatchHistoryModal() {
    document.getElementById('match-history-modal').style.display = 'none';
}

// ========== Search Match Modal Functions ==========
async function openSearchMatchModal() {
    const modal = document.getElementById('search-match-modal');
    const sportSelect = document.getElementById('search-match-sport');
    
    modal.style.display = 'flex';
    
    // Load sports if not already loaded
    if (sportSelect.options.length <= 1) {
        try {
            const res = await fetch('admin-sport/get-sports');
            const data = await res.json();
            if (data.status === 'success') {
                data.data.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.sport_id;
                    opt.textContent = s.sport_name;
                    sportSelect.appendChild(opt);
                });
            }
        } catch(err) { console.error('Error loading sports:', err); }
    }
}

function closeSearchMatchModal() {
    document.getElementById('search-match-modal').style.display = 'none';
    hideMatchDetails();
}

// Debounce timer
let matchSearchTimer = null;

function debounceMatchSearch() {
    clearTimeout(matchSearchTimer);
    matchSearchTimer = setTimeout(performMatchSearch, 300);
}

function performMatchSearch() {
    const query = document.getElementById('search-match-query').value.trim();
    const sportId = document.getElementById('search-match-sport').value;
    const resultsDiv = document.getElementById('search-match-results');
    
    // Hide details panel when searching
    hideMatchDetails();
    
    if (query.length < 2 && !sportId) {
        resultsDiv.innerHTML = '<p class="hint-text">Start typing to search matches...</p>';
        return;
    }
    
    resultsDiv.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
    
    const params = new URLSearchParams();
    if (query) params.append('q', query);
    if (sportId) params.append('sport_id', sportId);
    
    fetch(`admin-sport/search-matches?${params.toString()}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.data.length > 0) {
                let html = '<div class="match-cards-grid">';
                data.data.forEach(m => {
                    const date = new Date(m.match_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    html += `<div class="match-card" onclick="viewMatchDetails('${m.match_id}', '${m.match_name.replace(/'/g, "\\'")}')">
                        <div class="match-card-header">
                            <span class="sport-tag">${m.sport_name}</span>
                            <span class="status-badge status-${m.result_status.toLowerCase()}">${m.result_status}</span>
                        </div>
                        <h4 class="match-card-title">${m.match_name}</h4>
                        <div class="match-card-meta">
                            <span class="tournament"><i class="fas fa-trophy"></i> ${m.tournament_name}</span>
                            <span class="date"><i class="fas fa-calendar"></i> ${date}</span>
                        </div>
                    </div>`;
                });
                html += '</div>';
                resultsDiv.innerHTML = html;
            } else {
                resultsDiv.innerHTML = '<p class="no-data">No matches found.</p>';
            }
        })
        .catch(err => {
            console.error('Error searching matches:', err);
            resultsDiv.innerHTML = '<p class="error-text">Failed to search matches.</p>';
        });
}

function viewMatchDetails(matchId, matchName) {
    const detailsPanel = document.getElementById('match-details-panel');
    const resultsDiv = document.getElementById('search-match-results');
    const detailsContent = document.getElementById('match-details-content');
    const matchTitle = document.getElementById('details-match-name');
    
    matchTitle.textContent = matchName;
    detailsContent.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading details...</div>';
    
    resultsDiv.style.display = 'none';
    detailsPanel.style.display = 'block';
    
    fetch(`admin-sport/match-details?match_id=${matchId}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.data) {
                const match = data.data;
                const date = new Date(match.match_date).toLocaleDateString('en-US', { 
                    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
                });
                
                let html = `
                    <div class="match-info-grid">
                        <div class="info-item">
                            <label>Tournament</label>
                            <span>${match.tournament_name}</span>
                        </div>
                        <div class="info-item">
                            <label>Sport</label>
                            <span>${match.sport_name}</span>
                        </div>
                        <div class="info-item">
                            <label>Date</label>
                            <span>${date}</span>
                        </div>
                        <div class="info-item">
                            <label>Status</label>
                            <span class="status-badge status-${match.result_status.toLowerCase()}">${match.result_status}</span>
                        </div>
                        ${match.winner_name ? `<div class="info-item"><label>Winner</label><span class="winner-name"><i class="fas fa-trophy"></i> ${match.winner_name}</span></div>` : ''}
                    </div>
                `;
                
                // Sport-specific details
                if (match.details) {
                    html += '<div class="sport-details-section"><h4>Match Details</h4>';
                    html += renderSportDetails(match.details, match.sport_category);
                    html += '</div>';
                }
                
                // Participants
                if (match.participants && match.participants.length > 0) {
                    html += '<div class="participants-section"><h4>Participants</h4><div class="participants-list">';
                    match.participants.forEach(p => {
                        html += `<div class="participant-card">
                            <span class="name">${p.player_name}</span>
                            ${p.team ? `<span class="team">Team ${p.team}</span>` : ''}
                            ${p.score ? `<span class="score">Score: ${p.score}</span>` : ''}
                        </div>`;
                    });
                    html += '</div></div>';
                }
                
                detailsContent.innerHTML = html;
            } else {
                detailsContent.innerHTML = '<p class="error-text">Match details not found.</p>';
            }
        })
        .catch(err => {
            console.error('Error loading match details:', err);
            detailsContent.innerHTML = '<p class="error-text">Failed to load match details.</p>';
        });
}

function hideMatchDetails() {
    document.getElementById('match-details-panel').style.display = 'none';
    document.getElementById('search-match-results').style.display = 'block';
}

function renderSportDetails(details, category) {
    let html = '<div class="details-grid">';
    
    for (const [key, value] of Object.entries(details)) {
        if (value === null || value === undefined || key === 'id' || key === 'match_id') continue;
        
        const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        
        // Handle JSON fields
        if (typeof value === 'object') {
            html += `<div class="detail-item full-width"><label>${label}</label><span>${JSON.stringify(value)}</span></div>`;
        } else {
            html += `<div class="detail-item"><label>${label}</label><span>${value}</span></div>`;
        }
    }
    
    html += '</div>';
    return html;
}

// Close modals on overlay click
document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});

// Close modals on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
    }
});
</script>
</html>
