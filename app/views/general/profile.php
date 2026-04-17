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
    $profileImg  = htmlspecialchars($userDetails['profile_image_url'] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=400');
    $joinedRaw   = $userDetails['joined_date'] ?? '';

    // Sport icon mapping
    $sportIcons = [
        'cricket'    => '🏏', 'football'   => '⚽', 'basketball' => '🏀',
        'volleyball' => '🏐', 'tennis'     => '🎾', 'badminton'  => '🏸',
        'swimming'   => '🏊', 'athletics'  => '🏃', 'rugby'      => '🏉',
        'netball'    => '🏐', 'chess'      => '♟️', 'default'    => '🏅',
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
                <p class="user-location"><i class="fas fa-map-marker-alt"></i> Colombo, Sri Lanka</p>



                <hr class="sidebar-divider">

                <!-- Info Tags -->
                <p class="section-label">Stats &amp; Info</p>
                <div class="info-tags-wrap">
                    <span class="info-tag"><i class="fas fa-id-badge"></i> <span id="userId"><?= $userId ?></span></span>
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
                <div style="text-align: center;">
                    <button class="btn-danger-text" onclick="showDeleteModal()">Delete Account</button>
                </div>

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
                            $pct = rand(72, 98);
                            $icon = getSportIcon($sp['sport_name'], $sportIcons);
                            $joinedFormatted = date('M Y', strtotime($sp['joined_date']));
                        ?>
                        <div class="sport-attend-item">
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
                            <h3 class="card-title">Performance</h3>
                            <p class="card-subtitle">Stats &amp; achievements</p>
                        </div>
                    </div>

                    <div class="perf-stats-grid">
                        <div class="perf-stat-box">
                            <span class="perf-stat-icon">🏅</span>
                            <div class="perf-stat-value"><?= count($enrolledSports ?? []) ?></div>
                            <div class="perf-stat-label">Sports Enrolled</div>
                        </div>
                        <div class="perf-stat-box">
                            <span class="perf-stat-icon">🏆</span>
                            <div class="perf-stat-value"><?= count($enrolledSports ?? []) ?></div>
                            <div class="perf-stat-label">Achievements</div>
                        </div>
                    </div>

                    <?php if (!empty($enrolledSports)): ?>
                    <p class="achievement-section-title"><i class="fas fa-trophy" style="color:#f59e0b;"></i> Enrolled Sports</p>
                    <div class="achievement-list">
                        <?php foreach ($enrolledSports as $sp):
                            $icon = getSportIcon($sp['sport_name'], $sportIcons);
                            $joinedFmt = date('F Y', strtotime($sp['joined_date']));
                        ?>
                        <div class="achievement-entry">
                            <span class="ach-emoji"><?= $icon ?></span>
                            <div class="ach-details">
                                <h4><?= htmlspecialchars($sp['sport_name']) ?></h4>
                                <p>Since <?= $joinedFmt ?><?php if (!empty($sp['coach_name'])): ?> &bull; Coach: <?= htmlspecialchars($sp['coach_name']) ?><?php endif; ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="no-data-state">
                        <i class="fas fa-medal"></i>
                        <p>No performance data yet.<br>Start participating to build your record.</p>
                    </div>
                    <?php endif; ?>
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
                    <p class="user-location">
                        <i class="fas fa-map-marker-alt"></i> Colombo, Sri Lanka
                    </p>

                    <div class="h-info-grid">
                        <div class="h-info-item">
                            <i class="fas fa-id-badge"></i>
                            <div class="h-item-content">
                                <label>User ID</label>
                                <span id="userId"><?= $userId ?></span>
                            </div>
                        </div>
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
                    <button class="btn-danger-text" onclick="showDeleteModal()">Delete Account</button>
                </div>

            </div><!-- /.profile-card-horizontal -->

        </div><!-- /.profile-horizontal-wrap -->
        <?php endif; ?>

    </div><!-- /.profile-wrapper -->
</div><!-- /.profile-layout-container -->

<!-- ── Delete Account Modal ──────────────────────────────────── -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-icon"><i class="fas fa-trash"></i></div>
        <h3>Delete Account?</h3>
        <p>This action cannot be undone. All your data will be permanently deleted.</p>
        <div class="modal-actions">
            <button class="btn-cancel-modal" onclick="hideDeleteModal()">Cancel</button>
            <button class="btn-confirm-delete" onclick="confirmDelete()">Delete</button>
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
function showDeleteModal()   { document.getElementById('deleteModal').classList.add('show'); }
function hideDeleteModal()   { document.getElementById('deleteModal').classList.remove('show'); }
function confirmDelete()     { alert('Account deletion requested.'); hideDeleteModal(); }
function payNow(id)          { window.location.href = '/uoc-sports/public/payment?booking_id=' + id; }

init();
</script>

<script>
    var currentPage = document.getElementById("nav-pro");
    if (currentPage) currentPage.classList.add("active");
</script>
</body>
</html>