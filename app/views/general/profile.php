<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | UOC Sports E-Portal</title>
    <meta name="description" content="View and manage your UOC Sports E-Portal profile.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/general/header.css);
        @import url(/uoc-sports/public/css/general/profile.css?v=3.0);
        @import url(/uoc-sports/public/css/general/footer.css);
    </style>
</head>
<body>
<?php
    require '../app/views/templates/general/header.php';

    // Determine user type
    $userType    = strtoupper($userDetails['type'] ?? 'PUBLIC');
    $isStudent   = in_array($userType, ['STUDENT', 'CAPTAIN']);
    $fullName    = htmlspecialchars($userDetails['full_name'] ?? '');
    $email       = htmlspecialchars($userDetails['email'] ?? '');
    $userId      = htmlspecialchars($userDetails['user_id'] ?? '');
    $contactNo   = htmlspecialchars($userDetails['contact_no'] ?? '');
    $faculty     = htmlspecialchars($userDetails['faculty_name'] ?? '');
    $sportName   = htmlspecialchars($userDetails['sport_name'] ?? '');
    $staffRoles  = ['ADMIN', 'REG', 'SPT', 'EQP', 'EXECUTIVE', 'COACH'];
    $canDelete   = !in_array($userType, $staffRoles);

    $profileImg  = htmlspecialchars($userDetails['profile_image_url'] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=400');
    $joinedRaw   = $userDetails['joined_date'] ?? '';

    // Sport icon mapping
    $sportIcons = [
        'cricket'    => '<i class="fas fa-baseball-bat-ball"></i>',
        'football'   => '<i class="fas fa-futbol"></i>',
        'basketball' => '<i class="fas fa-basketball"></i>',
        'volleyball' => '<i class="fas fa-volleyball"></i>',
        'tennis'     => '<i class="fas fa-table-tennis-paddle-ball"></i>',
        'badminton'  => '<i class="fas fa-award"></i>',
        'swimming'   => '<i class="fas fa-person-swimming"></i>',
        'athletics'  => '<i class="fas fa-person-running"></i>',
        'rugby'      => '<i class="fas fa-rugby-ball"></i>',
        'netball'    => '<i class="fas fa-volleyball"></i>',
        'chess'      => '<i class="fas fa-chess"></i>',
        'default'    => '<i class="fas fa-medal"></i>',
    ];
    function getSportIcon($name, $map) {
        $key = strtolower(trim($name));
        foreach ($map as $k => $icon) {
            if (str_contains($key, $k)) return $icon;
        }
        return $map['default'];
    }
?>

<div class="profile-layout-container">

    <!-- ── Cover Image ─────────────────────────────────────────── -->
    <div class="profile-cover">
        <img src="https://images.unsplash.com/photo-1461896836934-ffe607ba8211?q=80&w=2940&auto=format&fit=crop" alt="Cover Image">
        <button class="btn-edit-cover"><i class="fas fa-camera"></i> Edit Cover</button>
    </div>

    <!-- ── Main Wrapper ────────────────────────────────────────── -->
    <div class="profile-wrapper">

        <?php if ($isStudent): ?>
        <!-- ══════════════════════════════════════════════════════
             STUDENT / CAPTAIN — 3-column grid
             ══════════════════════════════════════════════════════ -->
        <div class="profile-grid-student">

            <!-- ── 1. Left Sidebar Card ──────────────────────── -->
            <aside class="profile-sidebar-card">

                <div class="profile-picture-wrapper">
                    <img id="profilePicture" src="<?= $profileImg ?>" alt="Profile" class="profile-picture">
                    <label for="profile-upload" class="change-picture-btn" title="Change Photo">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" id="profile-upload" accept="image/jpeg,image/jpg,image/png,image/gif">
                    <div id="upload-status"></div>
                </div>

                <h2 class="user-name" id="userName"><?= $fullName ?></h2>
                <span class="user-role-badge" id="accountTypeBadge"><?= $userType ?></span>




                <hr class="sidebar-divider">

                <!-- Info Tags -->
                <p class="section-label">Stats &amp; Info</p>
                <div class="info-tags-wrap">

                    <span class="info-tag"><i class="fas fa-calendar-alt"></i> Joined: <span id="joinedDate">—</span></span>
                    <?php if ($faculty): ?>
                    <span class="info-tag"><i class="fas fa-university"></i> <?= $faculty ?></span>
                    <?php endif; ?>
                    <?php if (!empty($userDetails['student_id'])): ?>
                    <span class="info-tag"><i class="fas fa-graduation-cap"></i> <?= htmlspecialchars($userDetails['student_id']) ?></span>
                    <?php endif; ?>
                    <span class="info-tag email"><i class="fas fa-envelope"></i> <span id="userEmail"><?= $email ?></span></span>
                </div>

                <hr class="sidebar-divider">

                <!-- Actions -->
                <button class="btn btn-primary" onclick="handleLogout()" style="width:100%; justify-content:center; margin-bottom:0.8rem;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
                <?php if ($canDelete): ?>
                <div style="text-align: center;">
                    <button class="btn-danger-text" onclick="confirmDelete()">Delete Account</button>
                </div>
                <?php endif; ?>

            </aside>

            <!-- ── Right: Combined Attendance + Performance Card ── -->
            <div class="combined-card">

                <!-- ── Attendance Section ───────────────── -->
                <div class="combined-section attend-section">
                    <div class="card-header-row">
                        <div class="card-icon-badge cyan"><i class="fas fa-calendar-check"></i></div>
                        <div>
                            <h3 class="card-title">Attendance</h3>
                            <p class="card-subtitle">Enrolled sports &amp; sessions</p>
                        </div>
                    </div>

                    <?php if (!empty($enrolledSports)): ?>
                    <div class="sport-attend-list">
                        <?php foreach ($enrolledSports as $sp):
                            $pct = isset($sp['attendance_pct']) ? $sp['attendance_pct'] : 0;
                            $icon = getSportIcon($sp['sport_name'], $sportIcons);
                            $joinedFormatted = date('M Y', strtotime($sp['joined_date']));
                        ?>
                        <div class="sport-attend-item" style="cursor: pointer; transition: background 0.2s; border-radius: 8px;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'" onclick="showAttendanceDetails('<?= htmlspecialchars($sp['sport_id']) ?>', '<?= htmlspecialchars($sp['sport_name']) ?>', <?= $pct ?>)">
                            <div class="sport-icon-circle"><?= $icon ?></div>
                            <div class="sport-attend-details">
                                <div class="sport-attend-name"><?= htmlspecialchars($sp['sport_name']) ?></div>
                                <div class="sport-attend-meta">
                                    <?php if (!empty($sp['coach_name'])): ?>
                                        Coach: <?= htmlspecialchars($sp['coach_name']) ?> &bull;
                                    <?php endif; ?>
                                    Joined <?= $joinedFormatted ?>
                                </div>
                            </div>
                            <div class="attend-mini-bar-wrap">
                                <span class="attend-pct"><?= $pct ?>%</span>
                                <div class="attend-bar">
                                    <div class="attend-bar-fill" style="width:<?= $pct ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="attend-summary-strip">
                        <div class="attend-strip-item">
                            <div class="attend-strip-num"><?= count($enrolledSports) ?></div>
                            <div class="attend-strip-lbl">Sports Enrolled</div>
                        </div>
                    </div>

                    <?php else: ?>
                    <div class="no-data-state">
                        <i class="fas fa-running"></i>
                        <p>No sports enrolled yet.<br>Join a team to see your attendance here.</p>
                    </div>
                    <?php endif; ?>
                </div><!-- /.attend-section -->

                <!-- ── Vertical Divider ──────────────────── -->
                <div class="combined-divider"></div>

                <!-- ── Performance Section ──────────────── -->
                <div class="combined-section perf-section">
                    <div class="card-header-row">
                        <div class="card-icon-badge amber"><i class="fas fa-chart-line"></i></div>
                        <div>
                            <h3 class="card-title">Performance & Achievements</h3>
                            <p class="card-subtitle">Points, awards & records</p>
                        </div>
                    </div>

                    <!-- Dynamic Stats Grid -->
                    <div class="perf-stats-grid">
                        <div class="perf-stat-box">
                            <span class="perf-stat-icon">🏅</span>
                            <div class="perf-stat-value" id="perf-total-points">-</div>
                            <div class="perf-stat-label">Total Points</div>
                        </div>
                        <div class="perf-stat-box">
                            <span class="perf-stat-icon">⚡</span>
                            <div class="perf-stat-value" id="perf-match-count">-</div>
                            <div class="perf-stat-label">Matches</div>
                        </div>
                        <div class="perf-stat-box">
                            <span class="perf-stat-icon">🏆</span>
                            <div class="perf-stat-value" id="perf-award-count">-</div>
                            <div class="perf-stat-label">Awards</div>
                        </div>
                    </div>

                    <!-- Points Breakdown Bar -->
                    <div style="margin:12px 0 8px;">
                        <p style="font-size:11px;color:#94a3b8;margin:0 0 6px;font-weight:600;">Points Breakdown</p>
                        <div id="perf-breakdown-bar" style="display:flex;height:18px;border-radius:9px;overflow:hidden;background:#f3f4f6;"></div>
                        <div id="perf-breakdown-legend" style="display:flex;gap:10px;margin-top:4px;font-size:10px;color:#6b7280;"></div>
                    </div>

                    <!-- Awards & Achievements lists -->
                    <div id="perf-awards-list" style="margin-top:10px;"></div>
                    <div id="perf-achievements-list" style="margin-top:8px;"></div>

                </div><!-- /.perf-section -->


        </div><!-- /.profile-grid-student -->



        <?php else: ?>
        <!-- ══════════════════════════════════════════════════════
             NON-STUDENT — horizontal single-card layout
             ══════════════════════════════════════════════════════ -->
        <div class="profile-horizontal-wrap">

            <div class="profile-card-horizontal">

                <!-- Profile Picture -->
                <div class="pic-col">
                    <div class="profile-picture-wrapper size-lg">
                        <img id="profilePicture" src="<?= $profileImg ?>" alt="Profile" class="profile-picture">
                        <label for="profile-upload" class="change-picture-btn" title="Change Photo">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" id="profile-upload" accept="image/jpeg,image/jpg,image/png,image/gif">
                    </div>
                    <div id="upload-status" style="margin-top:8px; text-align:center; font-size:0.8rem;"></div>
                </div>

                <!-- Name / Info -->
                <div class="info-col">
                    <h2 class="user-name" id="userName"><?= $fullName ?></h2>
                    <span class="user-role-badge" id="accountTypeBadge"><?= $userType ?></span>


                    <div class="h-info-grid">

                        <div class="h-info-item">
                            <i class="fas fa-envelope"></i>
                            <div class="h-item-content">
                                <label>Email Address</label>
                                <span id="userEmail"><?= $email ?></span>
                            </div>
                        </div>
                        <?php if ($contactNo): ?>
                        <div class="h-info-item">
                            <i class="fas fa-phone-alt"></i>
                            <div class="h-item-content">
                                <label>Contact No</label>
                                <span><?= $contactNo ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($faculty): ?>
                        <div class="h-info-item">
                            <i class="fas fa-university"></i>
                            <div class="h-item-content">
                                <label>Faculty</label>
                                <span><?= $faculty ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($sportName): ?>
                        <div class="h-info-item">
                            <i class="fas fa-medal"></i>
                            <div class="h-item-content">
                                <label>Assigned Sport</label>
                                <span><?= $sportName ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="h-info-item">
                            <i class="fas fa-calendar-alt"></i>
                            <div class="h-item-content">
                                <label>Member Since</label>
                                <span id="joinedDate">—</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="actions-col">
                    <button class="btn btn-primary" onclick="handleLogout()">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                    <?php if ($canDelete): ?>
                    <button class="btn-danger-text" onclick="confirmDelete()">Delete Account</button>
                    <?php endif; ?>
                </div>

            </div><!-- /.profile-card-horizontal -->

        </div><!-- /.profile-horizontal-wrap -->
        <?php endif; ?>

    </div><!-- /.profile-wrapper -->
</div><!-- /.profile-layout-container -->


<!-- ── Attendance Details Modal ──────────────────────────────────── -->
<div id="attendanceModal" class="modal">
    <div class="modal-content" style="max-width: 500px; text-align: left; padding: 24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
            <h3 id="attendanceModalTitle" style="margin: 0;">Attendance Details</h3>
            <button onclick="hideAttendanceModal()" style="background:none;border:none;font-size:1.5rem;cursor:pointer;color:#94a3b8;">&times;</button>
        </div>
        <div style="margin-bottom: 20px; font-size: 14px; color: #475569;">
            <strong>Overall Attendance:</strong> <span id="attendanceModalPct" style="font-weight: 600; color: #0ea5e9;">-</span>
        </div>
        <div style="max-height: 300px; overflow-y: auto; padding-right: 8px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr style="border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 8px; text-align: left; color: #64748b; font-weight: 600;">Date</th>
                        <th style="padding: 8px; text-align: left; color: #64748b; font-weight: 600;">Time</th>
                        <th style="padding: 8px; text-align: right; color: #64748b; font-weight: 600;">Status</th>
                    </tr>
                </thead>
                <tbody id="attendanceModalBody">
                    <tr><td colspan="3" style="text-align: center; padding: 20px;">Loading...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="modal-actions" style="margin-top: 24px; justify-content: flex-end;">
            <button class="btn-cancel-modal" onclick="hideAttendanceModal()">Close</button>
        </div>
    </div>
</div>

<?php require '../app/views/templates/general/footer.php'; ?>

<script>
// ── Backend data ─────────────────────────────────────────────
const userData = {
    id:             <?= json_encode($userDetails['user_id']          ?? '') ?>,
    name:           <?= json_encode($userDetails['full_name']         ?? '') ?>,
    email:          <?= json_encode($userDetails['email']             ?? '') ?>,
    accountType:    <?= json_encode($userDetails['type']              ?? 'PUBLIC') ?>,
    profilePicture: <?= json_encode($userDetails['profile_image_url'] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=400') ?>,
    joinedDate:     <?= json_encode($userDetails['joined_date']       ?? '') ?>
};

// ── Init ─────────────────────────────────────────────────────
function init() {
    loadUserData();
    const uploadEl = document.getElementById('profile-upload');
    if (uploadEl) uploadEl.addEventListener('change', uploadProfileImage);
}

function loadUserData() {
    setText('userName',        userData.name);
    setText('userEmail',       userData.email);
    setText('accountTypeBadge', userData.accountType);
    setText('userId',          userData.id);

    // Profile picture
    const pic = document.getElementById('profilePicture');
    if (pic) pic.src = userData.profilePicture;

    // Joined date
    if (userData.joinedDate) {
        const d = new Date(userData.joinedDate);
        const formatted = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        setText('joinedDate', formatted);

        // "Since" year in sidebar
        const yearEl = document.getElementById('joinedYear');
        if (yearEl) yearEl.textContent = d.getFullYear();
    }
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}

// ── Profile image upload ──────────────────────────────────────
function uploadProfileImage(e) {
    const file = e.target.files[0];
    if (!file) return;

    const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!allowed.includes(file.type)) {
        showUploadStatus('Invalid type. Only JPG, PNG, GIF allowed.', 'error'); 
        e.target.value = ''; return;
    }
    if (file.size > 5 * 1024 * 1024) {
        showUploadStatus('File exceeds 5 MB limit.', 'error'); 
        e.target.value = ''; return;
    }

    // Instant preview
    const reader = new FileReader();
    reader.onload = ev => {
        document.querySelectorAll('#profilePicture').forEach(img => img.src = ev.target.result);
    };
    reader.readAsDataURL(file);

    showUploadStatus('Uploading…', 'loading');

    const form = new FormData();
    form.append('profile_image', file);

    fetch('/uoc-sports/public/profile/upload-image', { method: 'POST', body: form })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showUploadStatus('Updated!', 'success');
                setTimeout(() => {
                    document.querySelectorAll('#profilePicture').forEach(img => img.src = data.imageUrl);
                    hideUploadStatus();
                }, 1800);
            } else {
                showUploadStatus(data.message || 'Upload failed.', 'error');
                document.querySelectorAll('#profilePicture').forEach(img => img.src = userData.profilePicture);
            }
        })
        .catch(() => {
            showUploadStatus('Upload failed.', 'error');
            document.querySelectorAll('#profilePicture').forEach(img => img.src = userData.profilePicture);
        });

    e.target.value = '';
}

function showUploadStatus(msg, type) {
    const el = document.getElementById('upload-status');
    if (!el) return;
    el.textContent = msg;
    el.style.display = 'block';
    el.style.color = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#4b0082';
}
function hideUploadStatus() {
    setTimeout(() => {
        const el = document.getElementById('upload-status');
        if (el) el.style.display = 'none';
    }, 800);
}

// ── Auth / Account ────────────────────────────────────────────
function handleLogout()      { window.location.href = '/uoc-sports/public/logout'; }
function confirmDelete() {
    UI.confirm('Are you sure you want to delete your account? You will be unable to log in until an administrator re-activates your account.', async () => {
        try {
            const response = await fetch('/uoc-sports/public/profile/deactivate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            });
            const data = await response.json();
            
            if (data.status === 'success') {
                UI.showToast(data.message, 'success');
                setTimeout(() => window.location.href = '/uoc-sports/public/sign-in', 2000);
            } else {
                UI.showToast(data.message, 'error');
            }
        } catch (error) {
            UI.showToast('Failed to process request. Please try again.', 'error');
        }
    }, null, true);
}
function payNow(id)          { window.location.href = '/uoc-sports/public/payment?booking_id=' + id; }

// ── Attendance Modal ──────────────────────────────────────────
async function showAttendanceDetails(sportId, sportName, pct) {
    document.getElementById('attendanceModalTitle').innerText = 'Attendance Details: ' + sportName;
    document.getElementById('attendanceModalPct').innerText = pct + '%';
    document.getElementById('attendanceModal').classList.add('show');
    
    const tbody = document.getElementById('attendanceModalBody');
    tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px;">Loading history...</td></tr>';

    try {
        const res = await fetch('/uoc-sports/public/api/attendance/student-history/' + sportId);
        const json = await res.json();
        
        if (json.status === 'success') {
            const data = json.data;
            if (data && data.length > 0) {
                let html = '';
                data.forEach(row => {
                    let d = new Date(row.session_date);
                    let formattedDate = d.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
                    
                    let statusColor = row.status === 'PRESENT' ? '#10b981' : '#ef4444';
                    let statusText = row.status === 'PRESENT' ? 'Present' : 'Absent';
                    let statusIcon = row.status === 'PRESENT' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-times-circle"></i>';

                    html += `<tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 10px 8px;">${formattedDate}</td>
                        <td style="padding: 10px 8px; color: #64748b;">${row.start_time}</td>
                        <td style="padding: 10px 8px; text-align: right; color: ${statusColor}; font-weight: 500;">
                           ${statusIcon} ${statusText}
                        </td>
                    </tr>`;
                });
                tbody.innerHTML = html;
            } else {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #64748b;">No practice sessions found for this sport.</td></tr>';
            }
        } else {
            tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #ef4444;">Failed to load attendance history.</td></tr>';
        }
    } catch (e) {
        tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #ef4444;">Error connecting to the server.</td></tr>';
    }
}

function hideAttendanceModal() {
    document.getElementById('attendanceModal').classList.remove('show');
}

init();

// ── Load Achievements ────────────────────────────────────────
async function loadAchievements() {
    try {
        const res = await fetch('/uoc-sports/public/captain/get-student-achievements');
        const data = await res.json();
        if (data.status !== 'success' || !data.data) return;

        const d = data.data;
        const bd = d.breakdown || {};

        // Update stat cards
        const setVal = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
        setVal('perf-total-points', d.total_points || bd.total || 0);
        setVal('perf-match-count', bd.match_count || 0);
        setVal('perf-award-count', bd.award_count || 0);

        // Breakdown bar
        const total = (bd.participation || 0) + (bd.wins || 0) + (bd.awards || 0);
        if (total > 0) {
            const bar = document.getElementById('perf-breakdown-bar');
            const leg = document.getElementById('perf-breakdown-legend');
            if (bar) {
                const pct = v => Math.round((v/total)*100);
                bar.innerHTML = `
                    <div style="width:${pct(bd.participation)}%;background:#a855f7;" title="Participation: ${bd.participation}"></div>
                    <div style="width:${pct(bd.wins)}%;background:#3b82f6;" title="Wins: ${bd.wins}"></div>
                    <div style="width:${pct(bd.awards)}%;background:#f59e0b;" title="Awards: ${bd.awards}"></div>
                `;
            }
            if (leg) {
                leg.innerHTML = `
                    <span><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#a855f7;margin-right:3px;"></span>Participation (${bd.participation})</span>
                    <span><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#3b82f6;margin-right:3px;"></span>Wins (${bd.wins})</span>
                    <span><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#f59e0b;margin-right:3px;"></span>Awards (${bd.awards})</span>
                `;
            }
        }

        // Awards list
        const awardsEl = document.getElementById('perf-awards-list');
        if (awardsEl && d.awards && d.awards.length > 0) {
            let html = '<p class="achievement-section-title" style="margin:0 0 6px;"><i class="fas fa-trophy" style="color:#f59e0b;"></i> Tournament Awards</p>';
            d.awards.forEach(a => {
                html += `<div class="achievement-entry" style="margin-bottom:6px;">
                    <span class="ach-emoji"><i class="fas fa-award"></i></span>
                    <div class="ach-details">
                        <h4 style="margin:0;font-size:13px;">${a.award_title}</h4>
                        <p style="margin:0;font-size:11px;color:#94a3b8;">${a.tournament_name || ''} · ${a.sport_name || ''} · ${a.points} pts</p>
                    </div>
                </div>`;
            });
            awardsEl.innerHTML = html;
        }

        // All achievements list (recent 5)
        const achEl = document.getElementById('perf-achievements-list');
        if (achEl && d.achievements && d.achievements.length > 0) {
            const icons = { 
                'Participant': '<i class="fas fa-user-check"></i>', 
                'Match Winner': '<i class="fas fa-trophy"></i>' 
            };
            let html = '<p class="achievement-section-title" style="margin:0 0 6px;"><i class="fas fa-list" style="color:#6366f1;"></i> Recent Activity</p>';
            d.achievements.slice(0, 5).forEach(a => {
                const icon = icons[a.achievement] || '<i class="fas fa-star"></i>';
                html += `<div class="achievement-entry" style="margin-bottom:4px;">
                    <span class="ach-emoji" style="font-size:14px;">${icon}</span>
                    <div class="ach-details">
                        <h4 style="margin:0;font-size:12px;">${a.achievement}</h4>
                        <p style="margin:0;font-size:10px;color:#94a3b8;">${a.tournament_name || ''} · ${a.sport_name || ''} · +${a.points} pt</p>
                    </div>
                </div>`;
            });
            achEl.innerHTML = html;
        }

    } catch (e) {
        console.error('Failed to load achievements:', e);
    }
}
loadAchievements();
</script>

<script>
    var currentPage = document.getElementById("nav-pro");
    if (currentPage) currentPage.classList.add("active");
</script>
</body>
</html>