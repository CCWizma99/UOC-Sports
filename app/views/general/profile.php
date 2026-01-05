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
        @import url(/uoc-sports/public/css/general/profile.css);
        @import url(/uoc-sports/public/css/general/footer.css);

        .mesh-sporty {
            background: 
                linear-gradient(rgba(94, 45, 145, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(94, 45, 145, 0.05) 1px, transparent 1px),
                linear-gradient(135deg, #faf9fc 0%, #f3f1f7 100%);
            background-size: 40px 40px, 40px 40px, 100% 100%;
        }
    </style>
</head>
<body class="mesh-sporty">
    <?php
        require '../app/views/templates/general/header.php';
?>

<div class="container">
        <div class="page-header">
            <h1>My Profile</h1>
            <p>Manage your account and view your activities</p>
        </div>

        <div class="profile-card">
            <div class="profile-content">
                <div class="profile-picture-container">
                    <img id="profilePicture" src="<?php echo htmlspecialchars($userDetails['profile_image_url'] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=400'); ?>" alt="Profile" class="profile-picture">
                    <label for="profile-upload" class="change-picture-btn">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" id="profile-upload" accept="image/jpeg,image/jpg,image/png,image/gif">
                    <div id="upload-status" style="display: none; margin-top: 10px; text-align: center; font-size: 14px;"></div>
                </div>
                <div class="profile-details">
                    <h2 id="userName">John Doe</h2>
                    <p class="email" id="userEmail">john.doe@uoc.lk</p>
                    <div class="profile-badges">
                        <span class="badge badge-primary"><i class="fas fa-user"></i><span id="accountType">STUDENT</span></span>
                        <span class="badge badge-secondary"><i class="fas fa-id-card"></i>ID: <span id="userId">STU2024001</span></span>
                        <span class="badge badge-success"><i class="fas fa-calendar-check"></i>Joined: <span id="joinedDate">Jan 15, 2024</span></span>
                    </div>
                </div>
                <div class="profile-actions">
                    <button class="btn btn-logout" onclick="handleLogout()"><i class="fas fa-sign-out-alt"></i>Logout</button>
                    <button class="btn btn-delete" onclick="showDeleteModal()"><i class="fas fa-trash"></i>Delete Account</button>
                </div>
            </div>
        </div>

        <div id="sportsSection">
            <div class="section-header"><i class="fas fa-trophy"></i><h2>Enrolled Sports</h2></div>
            <div class="sports-grid" id="sportsGrid"></div>
        </div>

        <div class="bookings-section">
            <div class="section-header"><i class="fas fa-calendar-alt"></i><h2>Booked Facilities</h2></div>
            <div class="bookings-controls">
                <div class="sort-container">
                    <label><i class="fas fa-sort"></i>Sort by:</label>
                    <select id="sortBookings" onchange="sortBookings()">
                        <option value="date-asc">Date (Earliest First)</option>
                        <option value="date-desc">Date (Latest First)</option>
                        <option value="price-asc">Price (Low to High)</option>
                        <option value="price-desc">Price (High to Low)</option>
                        <option value="status">Status</option>
                    </select>
                </div>
            </div>
            <div class="booking-list" id="bookingList"></div>
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

        const enrolledSports = <?php echo json_encode($enrolledSports ?? []); ?>;

        let bookings = <?php 
            // Transform bookings data for frontend
            $frontendBookings = [];
            if (isset($bookings) && is_array($bookings)) {
                foreach ($bookings as $booking) {
                    $frontendBookings[] = [
                        'id' => $booking['booking_id'],
                        'facility' => $booking['facility_name'],
                        'date' => $booking['date'],
                        'time' => $booking['start_time'] . ' - ' . $booking['end_time'],
                        'status' => $booking['display_status'],
                        'purpose' => $booking['purpose'],
                        'price' => $booking['price']
                    ];
                }
            }
            echo json_encode($frontendBookings);
        ?>;

        function init() {
            loadUserData();
            loadSports();
            loadBookings();
            document.getElementById('profile-upload').addEventListener('change', uploadProfileImage);
        }

        function loadUserData() {
            document.getElementById('userName').textContent = userData.name;
            document.getElementById('userEmail').textContent = userData.email;
            document.getElementById('accountType').textContent = userData.accountType;
            document.getElementById('userId').textContent = userData.id;
            document.getElementById('profilePicture').src = userData.profilePicture;
            
            if (userData.joinedDate) {
                const joinedDate = new Date(userData.joinedDate);
                document.getElementById('joinedDate').textContent = joinedDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            }
            
            if (userData.accountType !== 'STUDENT' && userData.accountType !== 'CAPTAIN') {
                document.getElementById('sportsSection').style.display = 'none';
            }
        }

        function loadSports() {
            if (userData.accountType !== 'STUDENT' && userData.accountType !== 'CAPTAIN') {
                return;
            }
            
            if (enrolledSports.length === 0) {
                const grid = document.getElementById('sportsGrid');
                grid.innerHTML = '<p style="text-align: center; color: #666; padding: 2rem;">You are not enrolled in any sports yet.</p>';
                return;
            }
            
            const grid = document.getElementById('sportsGrid');
            grid.innerHTML = enrolledSports.map(s => {
                // Generate sport images based on sport name
                const sportImages = {
                    'Cricket': 'https://images.unsplash.com/photo-1531415074968-036ba1b575da?w=400',
                    'Basketball': 'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=400',
                    'Swimming': 'https://images.unsplash.com/photo-1519315901367-f34ff9154487?w=400',
                    'Football': 'https://images.unsplash.com/photo-1579952363873-27f3bade9f55?w=400',
                    'Badminton': 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=400',
                    'Tennis': 'https://images.unsplash.com/photo-1554068865-24cecd4e34b8?w=400',
                    'Volleyball': 'https://images.unsplash.com/photo-1612872087720-bb876e2e67d1?w=400'
                };
                const image = sportImages[s.sport_name] || 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=400';
                const coachName = s.coach_name || 'TBA';
                
                return `
                    <div class="sport-card">
                        <div class="sport-image">
                            <img src="${image}" alt="${s.sport_name}">
                            <div class="sport-name">${s.sport_name}</div>
                        </div>
                        <div class="sport-details">
                            <p><i class="fas fa-user"></i><span><strong>Coach:</strong> ${coachName}</span></p>
                            <p><i class="fas fa-calendar"></i><span><strong>Joined:</strong> ${s.joined_date ? new Date(s.joined_date).toLocaleDateString('en-US', { month: 'short', year: 'numeric' }) : 'N/A'}</span></p>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function loadBookings() {
            const list = document.getElementById('bookingList');
            
            if (bookings.length === 0) {
                list.innerHTML = '<p style="text-align: center; color: #666; padding: 2rem;">You have no facility bookings yet.</p>';
                return;
            }
            
            list.innerHTML = bookings.map(b => {
                const statusClass = b.status.toLowerCase();
                const dateStr = new Date(b.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                return `
                    <div class="booking-item ${statusClass}">
                        <div class="booking-info">
                            <i class="fas fa-map-marker-alt booking-icon"></i>
                            <div class="booking-details">
                                <h3>${b.facility}</h3>
                                <div class="booking-meta">
                                    <span><i class="fas fa-calendar"></i><strong>Date:</strong> ${dateStr}</span>
                                    <span><i class="fas fa-clock"></i><strong>Time:</strong> ${b.time}</span>
                                </div>
                                <p class="booking-purpose"><strong>Purpose:</strong> ${b.purpose}</p>
                            </div>
                        </div>
                        <div class="booking-right">
                            <span class="booking-status ${statusClass}">${b.status}</span>
                            <span class="booking-price">Rs. ${b.price.toFixed(2)}</span>
                            ${b.status === 'PENDING' ? '<button class="btn-pay" onclick="payNow(\'' + b.id + '\')">Pay Now</button>' : ''}
                        </div>
                    </div>
                `;
            }).join('');
        }

        function sortBookings() {
            const sortBy = document.getElementById('sortBookings').value;
            if (sortBy === 'date-asc') bookings.sort((a, b) => new Date(a.date) - new Date(b.date));
            else if (sortBy === 'date-desc') bookings.sort((a, b) => new Date(b.date) - new Date(a.date));
            else if (sortBy === 'price-asc') bookings.sort((a, b) => a.price - b.price);
            else if (sortBy === 'price-desc') bookings.sort((a, b) => b.price - a.price);
            else if (sortBy === 'status') {
                const order = { 'PENDING': 1, 'PAID': 2, 'PAST': 3 };
                bookings.sort((a, b) => order[a.status] - order[b.status]);
            }
            loadBookings();
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
            window.location.href = '/uoc-sports/public/sign-in';
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