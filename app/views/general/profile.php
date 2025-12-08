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
                    <img id="profilePicture" src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=400" alt="Profile" class="profile-picture">
                    <label for="profile-upload" class="change-picture-btn">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" id="profile-upload" accept="image/*">
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

    <script>
        const userData = {
            id: 'STU2024001',
            name: 'John Doe',
            email: 'john.doe@uoc.lk',
            accountType: 'STUDENT',
            profilePicture: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=400',
            joinedDate: '2024-01-15'
        };

        const enrolledSports = [
            { id: 1, name: 'Cricket', coach: 'Mr. Silva', schedule: 'Mon, Wed, Fri - 4:00 PM', image: 'https://images.unsplash.com/photo-1531415074968-036ba1b575da?w=400' },
            { id: 2, name: 'Basketball', coach: 'Ms. Fernando', schedule: 'Tue, Thu - 5:00 PM', image: 'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=400' },
            { id: 3, name: 'Swimming', coach: 'Mr. Perera', schedule: 'Sat - 8:00 AM', image: 'https://images.unsplash.com/photo-1519315901367-f34ff9154487?w=400' }
        ];

        let bookings = [
            { id: 1, facility: 'Tennis Court A', date: '2024-12-10', time: '2:00 PM - 4:00 PM', status: 'PENDING', purpose: 'Practice session', price: 1500 },
            { id: 2, facility: 'Cricket Oval', date: '2024-12-08', time: '10:00 AM - 12:00 PM', status: 'PAID', purpose: 'Team practice', price: 3000 },
            { id: 3, facility: 'Basketball Court', date: '2024-12-05', time: '4:00 PM - 6:00 PM', status: 'PAST', purpose: 'Tournament practice', price: 2000 },
            { id: 4, facility: 'Swimming Pool', date: '2024-12-12', time: '8:00 AM - 10:00 AM', status: 'PAID', purpose: 'Training session', price: 2500 }
        ];

        function init() {
            loadUserData();
            loadSports();
            loadBookings();
            document.getElementById('profile-upload').addEventListener('change', handleProfilePictureChange);
        }

        function loadUserData() {
            document.getElementById('userName').textContent = userData.name;
            document.getElementById('userEmail').textContent = userData.email;
            document.getElementById('accountType').textContent = userData.accountType;
            document.getElementById('userId').textContent = userData.id;
            document.getElementById('profilePicture').src = userData.profilePicture;
            document.getElementById('joinedDate').textContent = new Date(userData.joinedDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            if (userData.accountType !== 'STUDENT') document.getElementById('sportsSection').style.display = 'none';
        }

        function loadSports() {
            if (userData.accountType !== 'STUDENT') return;
            const grid = document.getElementById('sportsGrid');
            grid.innerHTML = enrolledSports.map(s => `
                <div class="sport-card">
                    <div class="sport-image">
                        <img src="${s.image}" alt="${s.name}">
                        <div class="sport-name">${s.name}</div>
                    </div>
                    <div class="sport-details">
                        <p><i class="fas fa-user"></i><span><strong>Coach:</strong> ${s.coach}</span></p>
                        <p><i class="fas fa-clock"></i><span><strong>Schedule:</strong> ${s.schedule}</span></p>
                    </div>
                </div>
            `).join('');
        }

        function loadBookings() {
            const list = document.getElementById('bookingList');
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
                            ${b.status === 'PENDING' ? '<button class="btn-pay" onclick="payNow(' + b.id + ')">Pay Now</button>' : ''}
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

        function handleProfilePictureChange(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = ev => document.getElementById('profilePicture').src = ev.target.result;
                reader.readAsDataURL(file);
            }
        }

        function handleLogout() { alert('Logging out...'); }
        function showDeleteModal() { document.getElementById('deleteModal').classList.add('show'); }
        function hideDeleteModal() { document.getElementById('deleteModal').classList.remove('show'); }
        function confirmDelete() { alert('Account deletion requested...'); hideDeleteModal(); }
        function payNow(id) { alert('Redirecting to payment for booking #' + id); }

        init();
    </script>

<?php
        require '../app/views/templates/general/footer.php';
    ?>
</body>
<script>
    var currentPage = document.getElementById("nav-pro");
    currentPage.classList.add("active") 
</script>
</html>