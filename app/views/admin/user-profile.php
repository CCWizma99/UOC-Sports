<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);

        .profile-container {
            width: calc(100vw - 280px);
            margin-left: 260px;
            margin-top: 20px;
            padding: 25px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: #3a0066;
            transform: translateY(-2px);
        }

        .profile-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-header {
            background: linear-gradient(135deg, #111 0%, #4b0082 100%);
            padding: 30px;
            display: flex;
            align-items: center;
            gap: 25px;
            color: white;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            font-weight: bold;
            overflow: hidden;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-name {
            flex: 1;
        }

        .profile-name h1 {
            margin: 0 0 8px 0;
            font-size: 26px;
            font-weight: 400;
        }

        .profile-name .user-type {
            display: inline-block;
            padding: 5px 15px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        .btn-edit {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-edit:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .btn-deactivate {
            background: #ff4757;
            color: white;
        }

        .btn-deactivate:hover {
            background: #ee5a6f;
        }

        .btn-activate {
            background: #28a745;
            color: white;
        }

        .btn-activate:hover {
            background: #218838;
        }

        .profile-body {
            padding: 30px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            padding: 15px 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid var(--primary-color);
        }

        .info-item label {
            display: block;
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .info-item span {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .section-title {
            font-size: 18px;
            font-weight: 400;
            color: #333;
            margin: 30px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--primary-color);
        }

        .sports-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .sport-card {
            padding: 15px;
            background: linear-gradient(135deg, #111 0%, #4b0082 100%);
            border-radius: 10px;
            color: white;
        }

        .sport-card h4 {
            margin: 0 0 10px 0;
            font-size: 16px;
            font-weight: 500;
        }

        .sport-card p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }

        .not-found {
            text-align: center;
            padding: 60px 20px;
        }

        .not-found i {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 20px;
        }

        .not-found h2 {
            color: #666;
            margin-bottom: 10px;
            font-weight: 400;
        }

        .not-found p {
            color: #999;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, #111 0%, #4b0082 100%);
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            font-weight: 400;
        }

        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            opacity: 0.8;
        }

        .modal-close:hover {
            opacity: 1;
        }

        .modal-body {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .modal-footer {
            padding: 15px 25px;
            background: #f8f9fa;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-radius: 0 0 12px 12px;
        }

        .btn-cancel {
            padding: 10px 20px;
            background: #e0e0e0;
            color: #333;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-save {
            padding: 10px 20px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-save:hover {
            background: #3a0066;
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease;
        }

        .toast.success {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .toast.error {
            background: linear-gradient(135deg, #dc3545, #ff6b6b);
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @media (max-width: 1200px) {
            .profile-container {
                width: calc(100% - 280px);
                margin-left: 260px;
            }
        }

        @media (max-width: 768px) {
            .profile-container {
                width: calc(100% - 40px);
                margin-left: 20px;
                margin-right: 20px;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .header-actions {
                width: 100%;
                justify-content: center;
            }
            
            .profile-name h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';
?>

<div class="profile-container">
    <a href="./admin-users" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back to Users
    </a>

    <?php if ($user_data): ?>
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php if (!empty($user_data['profile_img'])): ?>
                    <img src="/uoc-sports/public/images/profile/<?= htmlspecialchars($user_data['profile_img']) ?>" alt="Profile">
                <?php else: ?>
                    <?= strtoupper(substr($user_data['fname'], 0, 1)) ?>
                <?php endif; ?>
            </div>
            <div class="profile-name">
                <h1><?= htmlspecialchars($user_data['fname'] . ' ' . $user_data['lname']) ?></h1>
                <span class="user-type"><?= htmlspecialchars($user_data['type']) ?></span>
            </div>
            <div class="header-actions">
                <button class="btn-action btn-edit" onclick="openEditModal()">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <?php if ($user_data['status'] === 'ACTIVE'): ?>
                <button class="btn-action btn-deactivate" onclick="toggleUserStatus('INACTIVE')">
                    <i class="fas fa-user-slash"></i> Deactivate
                </button>
                <?php else: ?>
                <button class="btn-action btn-activate" onclick="toggleUserStatus('ACTIVE')">
                    <i class="fas fa-user-check"></i> Activate
                </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="profile-body">
            <div class="info-grid">
                <div class="info-item">
                    <label>User ID</label>
                    <span><?= htmlspecialchars($user_data['user_id']) ?></span>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <span id="display-email"><?= htmlspecialchars($user_data['email']) ?></span>
                </div>
                <div class="info-item">
                    <label>Contact Number</label>
                    <span id="display-contact"><?= $user_data['contact_no'] ? htmlspecialchars($user_data['contact_no']) : 'Not provided' ?></span>
                </div>
                <div class="info-item">
                    <label>Joined Date</label>
                    <span><?= date('M d, Y', strtotime($user_data['joined_date'])) ?></span>
                </div>
                <?php if (!empty($user_data['student_id'])): ?>
                <div class="info-item">
                    <label>Student ID</label>
                    <span id="display-student-id"><?= htmlspecialchars($user_data['student_id']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($user_data['faculty_name'])): ?>
                <div class="info-item">
                    <label>Faculty</label>
                    <span><?= htmlspecialchars($user_data['faculty_name']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($user_data['sport_name'])): ?>
                <div class="info-item">
                    <label>Sport</label>
                    <span><?= htmlspecialchars($user_data['sport_name']) ?></span>
                </div>
                <?php endif; ?>
                <div class="info-item">
                    <label>Status</label>
                    <span id="display-status" class="status-badge <?= $user_data['status'] === 'ACTIVE' ? 'status-active' : 'status-inactive' ?>">
                        <?= htmlspecialchars($user_data['status']) ?>
                    </span>
                </div>
            </div>

            <?php if (!empty($enrolled_sports)): ?>
            <h3 class="section-title"><i class="fas fa-running"></i> Enrolled Sports</h3>
            <div class="sports-list">
                <?php foreach ($enrolled_sports as $sport): ?>
                <div class="sport-card">
                    <h4><?= htmlspecialchars($sport['sport_name']) ?></h4>
                    <p>Joined: <?= date('M d, Y', strtotime($sport['joined_date'])) ?></p>
                    <?php if (!empty($sport['coach_name'])): ?>
                    <p>Coach: <?= htmlspecialchars($sport['coach_name']) ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="profile-card">
        <div class="not-found">
            <i class="fas fa-user-slash"></i>
            <h2>User Not Found</h2>
            <p>The requested user could not be found in the system.</p>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Edit User Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit User</h3>
            <button class="modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editUserForm">
                <input type="hidden" id="edit-user-id" value="<?= htmlspecialchars($user_data['user_id'] ?? '') ?>">
                
                <div class="form-group">
                    <label for="edit-fname">First Name</label>
                    <input type="text" id="edit-fname" value="<?= htmlspecialchars($user_data['fname'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="edit-lname">Last Name</label>
                    <input type="text" id="edit-lname" value="<?= htmlspecialchars($user_data['lname'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="edit-email">Email</label>
                    <input type="email" id="edit-email" value="<?= htmlspecialchars($user_data['email'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="edit-contact">Contact Number</label>
                    <input type="tel" id="edit-contact" value="<?= htmlspecialchars($user_data['contact_no'] ?? '') ?>" placeholder="+94XXXXXXXXX">
                </div>

                <?php if (!empty($user_data['student_id'])): ?>
                <div class="form-group">
                    <label for="edit-student-id">Student ID</label>
                    <input type="text" id="edit-student-id" value="<?= htmlspecialchars($user_data['student_id'] ?? '') ?>">
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="edit-type">User Type</label>
                    <select id="edit-type" required>
                        <option value="STUDENT" <?= $user_data['type'] === 'STUDENT' ? 'selected' : '' ?>>Student</option>
                        <option value="COACH" <?= $user_data['type'] === 'COACH' ? 'selected' : '' ?>>Coach</option>
                        <option value="SPT" <?= $user_data['type'] === 'SPT' ? 'selected' : '' ?>>Sport Manager</option>
                        <option value="EQP" <?= $user_data['type'] === 'EQP' ? 'selected' : '' ?>>Equipment Manager</option>
                        <option value="REG" <?= $user_data['type'] === 'REG' ? 'selected' : '' ?>>Registrar</option>
                        <option value="ADMIN" <?= $user_data['type'] === 'ADMIN' ? 'selected' : '' ?>>Admin</option>
                        <option value="PUBLIC" <?= $user_data['type'] === 'PUBLIC' ? 'selected' : '' ?>>External User</option>
                    </select>
                </div>

                <div id="dynamic-edit-fields">
                    <!-- Initially populated based on PHP, updated via JS -->
                    <?php if (in_array($user_data['type'], ['COACH', 'SPT'])): ?>
                    <div class="form-group">
                        <label for="edit-sport">Assigned Sport</label>
                        <select id="edit-sport">
                            <option value="">Select Sport</option>
                            <?php foreach ($sport_data as $sport): ?>
                                <option value="<?= $sport['sport_id'] ?>" <?= $user_data['sport_id'] === $sport['sport_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sport['sport_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <?php if ($user_data['type'] === 'REG' || $user_data['type'] === 'STUDENT'): ?>
                    <div class="form-group">
                        <label for="edit-faculty">Faculty</label>
                        <select id="edit-faculty">
                            <option value="">Select Faculty</option>
                            <?php foreach ($faculty_data as $faculty): ?>
                                <option value="<?= $faculty['faculty_id'] ?>" <?= $user_data['faculty_id'] === $faculty['faculty_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($faculty['faculty_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeEditModal()">Cancel</button>
            <button class="btn-save" onclick="saveUser()">Save Changes</button>
        </div>
    </div>
</div>

<script>
const userId = '<?= htmlspecialchars($user_data['user_id'] ?? '') ?>';

function openEditModal() {
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

async function saveUser() {
    const data = {
        user_id: document.getElementById('edit-user-id').value,
        fname: document.getElementById('edit-fname').value,
        lname: document.getElementById('edit-lname').value,
        email: document.getElementById('edit-email').value,
        contact_no: document.getElementById('edit-contact').value,
        type: document.getElementById('edit-type').value
    };

    const studentIdField = document.getElementById('edit-student-id');
    if (studentIdField) {
        data.student_id = studentIdField.value;
    }

    const sportField = document.getElementById('edit-sport');
    if (sportField) {
        data.sport_id = sportField.value;
    }

    const facultyField = document.getElementById('edit-faculty');
    if (facultyField) {
        data.faculty_id = facultyField.value;
    }

    try {
        const response = await fetch('/uoc-sports/public/admin-api/user/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            UI.showToast('User updated successfully!', 'success');
            closeEditModal();
            // Update displayed values
            document.getElementById('display-email').textContent = data.email;
            document.getElementById('display-contact').textContent = data.contact_no || 'Not provided';
            if (studentIdField) {
                document.getElementById('display-student-id').textContent = data.student_id;
            }
            // Reload to show updated name
            setTimeout(() => location.reload(), 1500);
        } else {
            UI.showToast(result.message || 'Failed to update user', 'error');
        }
    } catch (error) {
        UI.showToast('An error occurred while updating', 'error');
    }
}

async function toggleUserStatus(newStatus) {
    const action = newStatus === 'INACTIVE' ? 'deactivate' : 'activate';
    
    UI.confirm(`Are you sure you want to ${action} this user?`, async () => {
        try {
            const response = await fetch('/uoc-sports/public/api/user/toggle-status', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId, status: newStatus })
            });
            
            const result = await response.json();
            
            if (result.status === 'success') {
                UI.showToast(result.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                UI.showToast(result.message || 'Failed to update status', 'error');
            }
        } catch (error) {
            UI.showToast('An error occurred while updating status', 'error');
        }
    }, null, newStatus === 'INACTIVE'); // Danger theme for deactivation
}



// Dynamic fields for edit modal
document.getElementById('edit-type').addEventListener('change', function() {
    const container = document.getElementById('dynamic-edit-fields');
    const type = this.value;
    container.innerHTML = '';

    if (type === 'COACH' || type === 'SPT') {
        const sports = <?= json_encode($sport_data) ?>;
        let options = '<option value="">Select Sport</option>';
        sports.forEach(s => {
            options += `<option value="${s.sport_id}">${s.sport_name}</option>`;
        });
        container.innerHTML = `
            <div class="form-group">
                <label for="edit-sport">Assigned Sport</label>
                <select id="edit-sport">${options}</select>
            </div>
        `;
    } else if (type === 'REG' || type === 'STUDENT') {
        const faculties = <?= json_encode($faculty_data) ?>;
        let options = '<option value="">Select Faculty</option>';
        faculties.forEach(f => {
            options += `<option value="${f.faculty_id}">${f.faculty_name}</option>`;
        });
        container.innerHTML = `
            <div class="form-group">
                <label for="edit-faculty">Faculty</label>
                <select id="edit-faculty">${options}</select>
            </div>
        `;
    }
});

// Close modal on outside click
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>

<?php require '../app/views/templates/admin/footer.php'; ?>

</body>
<script>
    var currentPage = document.getElementById("sidebar-users");
    if (currentPage) currentPage.classList.add("active");

    // Auto-open modal if edit=true is in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('edit') === 'true') {
        openEditModal();
    }
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</html>
