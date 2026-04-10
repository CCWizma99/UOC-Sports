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
        @import url(/uoc-sports/public/css/general/profile.css?v=1.8);
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
                    <!-- Tabs Navigation -->
                    <div class="profile-tabs">
                        <button class="tab-btn active" onclick="switchTab('bookings')">Booked Facilities</button>
                        <button class="tab-btn" onclick="switchTab('activity')">Activity History</button>
                        <button class="tab-btn" onclick="switchTab('performance')">Performance Summary</button>
                    </div>

                    <!-- Tab Contents -->
                    <div id="bookings-tab" class="tab-content active">
                         <div class="section-header-clean">
                            <h2>My Bookings</h2>
                             <div class="sort-container-clean">
                                <select id="sortBookings" onchange="sortBookings()">
                                    <option value="date-desc">Date (Latest First)</option>
                                    <option value="date-asc">Date (Earliest First)</option>
                                    <option value="status">Status</option>
                                </select>
                            </div>
                        </div>
                        <div class="booking-list" id="bookingList"></div>
                    </div>

                    <div id="activity-tab" class="tab-content" style="display: none;">
                        <div class="section-header-clean">
                            <h2>Activity Analysis</h2>
                        </div>
                        <div class="chart-container" style="position: relative; height:300px; width:100%">
                            <canvas id="activityChart"></canvas>
                        </div>
                        <div class="activity-timeline" style="margin-top: 2rem;">
                            <h3 style="font-size: 1.1rem; color: #444; margin-bottom: 1rem;">Recent Timeline</h3>
                            <!-- Placeholder Data -->
                            <div class="activity-item">
                                <div class="activity-icon"><i class="fas fa-running"></i></div>
                                <div class="activity-details">
                                    <h4>Completed a Swimming Session</h4>
                                    <span class="activity-time">2 days ago</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon"><i class="fas fa-check-circle"></i></div>
                                <div class="activity-details">
                                    <h4>Booking Confirmed: Badminton Court</h4>
                                    <span class="activity-time">5 days ago</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon"><i class="fas fa-trophy"></i></div>
                                <div class="activity-details">
                                    <h4>Won Inter-Faculty Cricket Match</h4>
                                    <span class="activity-time">1 week ago</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="performance-tab" class="tab-content" style="display: none;">
                        <div class="section-header-clean">
                            <h2>Performance Summary</h2>
                        </div>
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-icon"><i class="fas fa-fire"></i></div>
                                <div class="stat-info">
                                    <h3>12</h3>
                                    <p>Matches Played</p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon"><i class="fas fa-medal"></i></div>
                                <div class="stat-info">
                                    <h3>5</h3>
                                    <p>Man of the Match</p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon"><i class="fas fa-stopwatch"></i></div>
                                <div class="stat-info">
                                    <h3>45h</h3>
                                    <p>Training Hours</p>
                                </div>
                            </div>
                        </div>
                    </div>
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
                        'price' => $booking['price'],
                        'payment_slip' => $booking['payment_slip']
                    ];
                }
            }
            echo json_encode($frontendBookings);
        ?>;

        // Chart instance
        let activityChart = null;

        function init() {
            loadUserData();
            loadBookings();
            document.getElementById('profile-upload').addEventListener('change', uploadProfileImage);
            
            // Load chart data
            fetchActivityData();
        }

        async function fetchActivityData() {
            try {
                const response = await fetch('/uoc-sports/public/api/chart/activity-analysis');
                const data = await response.json();
                renderChart(data);
            } catch (error) {
                console.error('Error loading chart data:', error);
            }
        }

        function renderChart(data) {
            const ctx = document.getElementById('activityChart').getContext('2d');
            
            if (activityChart) {
                activityChart.destroy();
            }

            activityChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: data.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: 'Activity Trends (Last 6 Months)' }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
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

        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
                content.classList.remove('active');
            });
            
            // Deactivate all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab content
            const selectedTab = document.getElementById(tabName + '-tab');
            selectedTab.style.display = 'block';
            setTimeout(() => selectedTab.classList.add('active'), 10);
            
            // Activate selected button
            document.querySelector(`button[onclick="switchTab('${tabName}')"]`).classList.add('active');

            // Resize chart if activity tab is shown
            if (tabName === 'activity' && activityChart) {
                setTimeout(() => activityChart.resize(), 50);
            }
        }

        function loadBookings() {
            const list = document.getElementById('bookingList');
            
            if (bookings.length === 0) {
                list.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <p>No facilities booked yet.</p>
                        <button class="btn btn-primary btn-sm" onclick="window.location.href='/uoc-sports/public/bookings/new'">Book Now</button>
                    </div>`;
                return;
            }
            
            list.innerHTML = bookings.map(b => {
                const statusClass = b.status.toLowerCase();
                const dateObj = new Date(b.date);
                const day = dateObj.getDate();
                const month = dateObj.toLocaleDateString('en-US', { month: 'short' });
                const year = dateObj.getFullYear();
                
                return `
                    <div class="booking-item ${statusClass}">
                        <div class="booking-date-box">
                            <span class="date-month">${month}</span>
                            <span class="date-day">${day}</span>
                            <span class="date-year">${year}</span>
                        </div>
                        
                        <div class="booking-info-compact">
                            <div class="booking-header">
                                <h3>${b.facility}</h3>
                                <span class="booking-time"><i class="far fa-clock"></i> ${b.time}</span>
                            </div>
                            <div class="booking-sub">
                                <span class="purpose-badge"><i class="fas fa-bullseye"></i> ${b.purpose}</span>
                                <span class="price-tag">Rs. ${b.price.toFixed(2)}</span>
                            </div>
                        </div>

                        <div class="booking-actions-modern">
                            <span class="status-dot ${statusClass}" title="${b.status}"></span>
                            ${b.status === 'PENDING' && !b.payment_slip ? 
                                '<button class="btn-pay-icon" onclick="payNow(\'' + b.id + '\')" title="Pay Now"><i class="fas fa-credit-card"></i></button>' : 
                                ''}
                            ${b.payment_slip ? 
                                '<a href="/uoc-sports/app/internal/payment_slips/' + b.payment_slip + '" target="_blank" class="btn-view-slip" title="View Submitted Proof"><i class="fas fa-file-invoice"></i></a>' : 
                                ''}
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