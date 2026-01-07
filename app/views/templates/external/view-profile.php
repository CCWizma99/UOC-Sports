<div class="header">
        <h1>External Member Profile</h1>
    </div>

    <div class="container">
        <div class="content-wrapper">
            <!-- Sidebar -->
            <aside class="sidebar">
                <div class="profile-section">
                    <div class="profile-image-wrapper">
                        <div class="profile-image">👤</div>
                        <div class="profile-badge">🌐</div>
                    </div>
                    <h2 class="profile-name">Name</h2>
                    <p class="profile-type">External Member</p>
                </div>

                <div class="stats-row">
                    <div class="stat-box">
                        <span class="stat-number">8</span>
                        <span class="stat-label">Bookings</span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-number">24</span>
                        <span class="stat-label">Posts</span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-number">47</span>
                        <span class="stat-label">Comments</span>
                    </div>
                </div>

                <div class="divider"></div>

                <div class="info-list">
                    <div class="info-row">
                        <div class="info-icon">🎓</div>
                        <div class="info-details">
                            <span class="info-label">Member ID</span>
                            <div class="info-value">EXT/2023/042</div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-icon">🏢</div>
                        <div class="info-details">
                            <span class="info-label">Organization</span>
                            <div class="info-value">Tech Solutions Ltd.</div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-icon">📧</div>
                        <div class="info-details">
                            <span class="info-label">Email</span>
                            <div class="info-value">sarah.johnson@techsol.com</div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-icon">📱</div>
                        <div class="info-details">
                            <span class="info-label">Phone</span>
                            <div class="info-value">+94 71 234 5678</div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-icon">📅</div>
                        <div class="info-details">
                            <span class="info-label">Member Since</span>
                            <div class="info-value">March 2023</div>
                        </div>
                    </div>
                </div>

                <button class="edit-profile-btn" onclick="editProfile()">
                    ✏️ Edit Profile
                </button>
            </aside>

            <!-- Main Content -->
            <main class="main-content">
                <div class="tabs-container">
                    <div class="tabs">
                        <div class="tab active" onclick="switchTab('bookings')">My Bookings</div>
                        <div class="tab" onclick="switchTab('newsfeed')">Newsfeed</div>
                        <div class="tab" onclick="switchTab('payments')">Payments</div>
                    </div>
                </div>

                <!-- Bookings Tab -->
                <div class="tab-content active" id="bookingsContent">
                    <h2 class="content-header">My Bookings</h2>
                    <div class="bookings-grid">
                        <div class="booking-card">
                            <div class="booking-icon icon-conference">🏢</div>
                            <div class="facility-title">Indoor Gym </div>
                            
                            
                            <div class="booking-details">
                                <div class="detail-row">📅 October 25, 2025</div>
                                <div class="detail-row">⏰ 09:00 - 11:00</div>
                                <div class="detail-row">💰 Rs. 5,000</div>
                            </div>
                        </div>

                        <div class="booking-card">
                            <div class="booking-icon icon-sports">⚽</div>
                            <div class="facility-title">Sports Ground</div>
                           
                            
                            <div class="booking-details">
                                <div class="detail-row">📅 October 28, 2025</div>
                                <div class="detail-row">⏰ 14:00 - 16:00</div>
                                <div class="detail-row">💰 Rs. 15,000</div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Newsfeed Tab -->
                <div class="tab-content" id="newsfeedContent">
                    <h2 class="content-header">University Newsfeed</h2>
                    <div class="posts-container" id="postsContainer"></div>
                </div>

                <!-- Payments Tab -->
                <div class="tab-content" id="paymentsContent">
                    <h2 class="content-header">Payment History</h2>
                    <p style="color: #6b7280; padding: 20px 0;">Payment history will be displayed here.</p>
                </div>
            </main>
        </div>
    </div>
