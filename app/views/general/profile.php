<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | UOC Sports E-Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/general/header.css);
        @import url(/uoc-sports/public/css/general/profile.css?v=1.9);
        @import url(/uoc-sports/public/css/general/footer.css);

    </style>
</head>
<body>
    <?php
        require '../app/views/templates/general/header.php';
?>

<div class="profile-layout-container">
        <!-- Cover Image Section -->
        <div class="profile-cover">
            <img src="https://images.unsplash.com/photo-1461896836934-ffe607ba8211?q=80&w=2940&auto=format&fit=crop" alt="Cover Image">
            <button class="btn-edit-cover"><i class="fas fa-camera"></i> Edit Cover</button>
        </div>

        <div class="profile-grid">
            <!-- Left Sidebar -->
            <aside class="profile-sidebar">
                <div class="sidebar-card user-card">
                    <div class="profile-picture-wrapper">
                        <img id="profilePicture" src="<?php echo htmlspecialchars($userDetails['profile_image_url'] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=400'); ?>" alt="Profile" class="profile-picture">
                        <label for="profile-upload" class="change-picture-btn" title="Change Profile Picture">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" id="profile-upload" accept="image/jpeg,image/jpg,image/png,image/gif">
                        <div id="upload-status" style="display: none; margin-top: 10px; text-align: center; font-size: 14px;"></div>
                    </div>
                    
                    <div class="user-info">
                        <h2 id="userName">John Doe</h2>
                        <p class="user-role" id="accountTypeBadge">STUDENT</p>
                        <p class="user-location"><i class="fas fa-map-marker-alt"></i> Colombo, Sri Lanka</p>
                    </div>

                    <div class="user-actions">
                        <button class="btn btn-outline" onclick="handleLogout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
                    </div>

                    <hr class="sidebar-divider">

                    <div class="skills-section">
                        <h3>Stats & Info</h3>
                        <div class="expert-tags">
                            <span class="tag">ID: <span id="userId">STU2024001</span></span>
                            <span class="tag">Joined: <span id="joinedDate">Jan 15, 2024</span></span>
                            <span class="tag email-tag" id="userEmail" title="Email">john.doe@uoc.lk</span>
                        </div>
                        <div style="margin-top: 1.5rem; text-align: center;">
                             <button class="text-btn" onclick="showDeleteModal()" style="color: #f44336; font-size: 0.9rem;">Delete Account</button>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="profile-main">
                <div class="content-card">
                    <!-- Content will go here -->
                </div>
            </main>
        </div>
    </div>

    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-icon"><i class="fas fa-trash"></i></div>
            <h3>Delete Account?</h3>
            <p>This action cannot be undone. All your data will be permanently deleted.</p>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="hideDeleteModal()">Cancel</button>
                <button class="btn-confirm-delete" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>

    <?php
        require '../app/views/templates/general/footer.php';
    ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Backend data from PHP
        const userData = {
            id: <?php echo json_encode($userDetails['user_id'] ?? ''); ?>,
            name: <?php echo json_encode($userDetails['full_name'] ?? ''); ?>,
            email: <?php echo json_encode($userDetails['email'] ?? ''); ?>,
            accountType: <?php echo json_encode($userDetails['type'] ?? 'PUBLIC'); ?>,
            profilePicture: <?php echo json_encode($userDetails['profile_image_url'] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=400'); ?>,
            joinedDate: <?php echo json_encode($userDetails['joined_date'] ?? ''); ?>
        };

        function init() {
            loadUserData();
            document.getElementById('profile-upload').addEventListener('change', uploadProfileImage);
        }


        function loadUserData() {
            document.getElementById('userName').textContent = userData.name;
            // document.getElementById('userNameOverview').textContent = userData.name; // Overview removed
            document.getElementById('userEmail').textContent = userData.email;
            document.getElementById('accountTypeBadge').textContent = userData.accountType;
            document.getElementById('userId').textContent = userData.id;
            document.getElementById('profilePicture').src = userData.profilePicture;
            
            if (userData.joinedDate) {
                const joinedDate = new Date(userData.joinedDate);
                document.getElementById('joinedDate').textContent = joinedDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            }
        }


        function uploadProfileImage(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                showUploadStatus('Invalid file type. Only JPG, PNG, and GIF images are allowed.', 'error');
                e.target.value = '';
                return;
            }

            // Validate file size (5MB max)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                showUploadStatus('File size exceeds 5MB limit.', 'error');
                e.target.value = '';
                return;
            }

            // Show preview immediately
            const reader = new FileReader();
            reader.onload = ev => document.getElementById('profilePicture').src = ev.target.result;
            reader.readAsDataURL(file);

            // Upload to server
            const formData = new FormData();
            formData.append('profile_image', file);

            showUploadStatus('Uploading...', 'loading');

            fetch('/uoc-sports/public/profile/upload-image', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showUploadStatus('Profile image updated successfully!', 'success');
                    // Update image with new URL to ensure it's the server version
                    setTimeout(() => {
                        document.getElementById('profilePicture').src = data.imageUrl;
                        hideUploadStatus();
                    }, 2000);
                } else {
                    showUploadStatus(data.message || 'Upload failed', 'error');
                    // Revert to original image
                    document.getElementById('profilePicture').src = userData.profilePicture;
                }
            })
            .catch(error => {
                showUploadStatus('Upload failed: ' + error.message, 'error');
                // Revert to original image
                document.getElementById('profilePicture').src = userData.profilePicture;
            });

            // Clear file input
            e.target.value = '';
        }

        function showUploadStatus(message, type) {
            const statusDiv = document.getElementById('upload-status');
            statusDiv.textContent = message;
            statusDiv.style.display = 'block';
            
            if (type === 'success') {
                statusDiv.style.color = '#28a745';
            } else if (type === 'error') {
                statusDiv.style.color = '#dc3545';
            } else {
                statusDiv.style.color = '#007bff';
            }
        }

        function hideUploadStatus() {
            const statusDiv = document.getElementById('upload-status');
            setTimeout(() => {
                statusDiv.style.display = 'none';
            }, 1000);
        }

        function handleLogout() { 
            window.location.href = '/uoc-sports/public/logout';
        }
        
        function showDeleteModal() { 
            document.getElementById('deleteModal').classList.add('show'); 
        }
        
        function hideDeleteModal() { 
            document.getElementById('deleteModal').classList.remove('show'); 
        }
        
        function confirmDelete() { 
            alert('Account deletion requested...'); 
            hideDeleteModal(); 
        }
        
        function payNow(id) { 
            window.location.href = '/uoc-sports/public/payment?booking_id=' + id;
        }

        init();
    </script>
</body>
<script>
    var currentPage = document.getElementById("nav-pro");
    currentPage.classList.add("active") 
</script>
</html>