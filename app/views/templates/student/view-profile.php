<div class="container">
        <div class="header">
            <h1>Student Profile</h1>
        </div>

        <div class="content-wrapper">
            <div class="profile-layout">
                <!-- Left Sidebar - Profile Card -->
                <div>
                    <div class="card profile-card">
                        <div class="profile-image-container">
                            <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=400&h=400&fit=crop" alt="Profile Photo" class="profile-image" id="profileImage">
                            <button class="change-photo-btn" onclick="document.getElementById('photoInput').click()">📷</button>
                            <input type="file" id="photoInput" accept="image/*" style="display: none;">
                        </div>

                        <div class="profile-name" id="profileName">Kavindu Perera</div>
                        <div class="profile-role">Internal Student</div>

                        <div class="profile-stats">
                            <div class="stat-item">
                                <div class="stat-number">3</div>
                                <div class="stat-label">Teams</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">12</div>
                                <div class="stat-label">Events</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">5</div>
                                <div class="stat-label">Bookings</div>
                            </div>
                        </div>

                        <div class="profile-info-list">
                            <div class="info-item">
                                <div class="info-icon">🎓</div>
                                <div class="info-content">
                                    <div class="info-label">Student ID</div>
                                    <div class="info-value" id="studentId">IS/2023/001</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon">🏛️</div>
                                <div class="info-content">
                                    <div class="info-label">Faculty</div>
                                    <div class="info-value" id="faculty">Computing</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon">📧</div>
                                <div class="info-content">
                                    <div class="info-label">Email</div>
                                    <div class="info-value" id="email">kavindu@sci.cmb.ac.lk</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon">📱</div>
                                <div class="info-content">
                                    <div class="info-label">Phone</div>
                                    <div class="info-value" id="phone">+94 77 123 4567</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon">📅</div>
                                <div class="info-content">
                                    <div class="info-label">Member Since</div>
                                    <div class="info-value">January 2023</div>
                                </div>
                            </div>
                        </div>

                        <button class="edit-profile-btn" onclick="openEditModal()">✏️ Edit Profile</button>
                    </div>
                </div>

                <!-- Right Side - Main Content -->
                <div class="main-content">
                    <!-- Navigation Tabs -->
                    <div class="card">
                        <div class="tab-navigation">
                            <button class="tab-btn active" onclick="switchTab('teams')">My Teams</button>
                            <button class="tab-btn" onclick="switchTab('equipment')">Equipment</button>
                            <button class="tab-btn" onclick="switchTab('schedule')">Schedule</button>
                            <button class="tab-btn" onclick="switchTab('performance')">Performance</button>
                        </div>

                        <!-- Teams Tab -->
                        <div class="tab-content active" id="teamsTab">
                            <div class="section-header">
                                <h2>My Teams</h2>
                            </div>
                            <div class="teams-grid">
                                <div class="team-card">
                                    <div class="team-icon">⚽</div>
                                    <div class="team-name">Football Team</div>
                                    <div class="team-role">Forward</div>
                                    <span class="team-status">Active Member</span>
                                </div>
                                <div class="team-card">
                                    <div class="team-icon">🏀</div>
                                    <div class="team-name">Basketball Team</div>
                                    <div class="team-role">Point Guard</div>
                                    <span class="team-status">Active Member</span>
                                </div>
                                <div class="team-card">
                                    <div class="team-icon">🏐</div>
                                    <div class="team-name">Volleyball Team</div>
                                    <div class="team-role">Setter</div>
                                    <span class="team-status">Active Member</span>
                                </div>
                            </div>
                        </div>

                        <!-- Equipment Tab -->
                        <div class="tab-content" id="equipmentTab">
                            <div class="section-header">
                                <h2>Equipment Reservations</h2>
                            </div>
                            <div class="reservation-list">
                                <div class="reservation-item">
                                    <div class="reservation-info">
                                        <div class="equipment-icon">⚽</div>
                                        <div class="reservation-details">
                                            <h3>Football (Size 5)</h3>
                                            <div class="reservation-meta">
                                                📅 Oct 20, 2025 | ⏰ 14:00 - 16:00
                                            </div>
                                        </div>
                                    </div>
                                    <span class="status-badge approved">Approved</span>
                                </div>
                                <div class="reservation-item">
                                    <div class="reservation-info">
                                        <div class="equipment-icon">🏀</div>
                                        <div class="reservation-details">
                                            <h3>Basketball</h3>
                                            <div class="reservation-meta">
                                                📅 Oct 22, 2025 | ⏰ 16:00 - 18:00
                                            </div>
                                        </div>
                                    </div>
                                    <span class="status-badge pending">Pending</span>
                                </div>
                                <div class="reservation-item">
                                    <div class="reservation-info">
                                        <div class="equipment-icon">🎾</div>
                                        <div class="reservation-details">
                                            <h3>Tennis Racket</h3>
                                            <div class="reservation-meta">
                                                📅 Oct 15, 2025 | ⏰ 08:00 - 10:00
                                            </div>
                                        </div>
                                    </div>
                                    <span class="status-badge returned">Returned</span>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule Tab -->
                        <div class="tab-content" id="scheduleTab">
                            <div class="section-header">
                                <h2>Upcoming Practice Schedule</h2>
                            </div>
                            <div class="schedule-list">
                                <div class="schedule-item">
                                    <div class="schedule-date">
                                        <div class="schedule-day">20</div>
                                        <div class="schedule-month">OCT</div>
                                    </div>
                                    <div class="schedule-details">
                                        <h3>Football Team Practice</h3>
                                        <p>📍 Main Ground | 👥 Football Team</p>
                                        <span class="schedule-time">⏰ 14:00 - 16:00</span>
                                    </div>
                                </div>
                                <div class="schedule-item">
                                    <div class="schedule-date">
                                        <div class="schedule-day">22</div>
                                        <div class="schedule-month">OCT</div>
                                    </div>
                                    <div class="schedule-details">
                                        <h3>Basketball Practice Session</h3>
                                        <p>📍 Indoor Stadium | 👥 Basketball Team</p>
                                        <span class="schedule-time">⏰ 16:00 - 18:00</span>
                                    </div>
                                </div>
                                <div class="schedule-item">
                                    <div class="schedule-date">
                                        <div class="schedule-day">25</div>
                                        <div class="schedule-month">OCT</div>
                                    </div>
                                    <div class="schedule-details">
                                        <h3>Volleyball Training</h3>
                                        <p>📍 Indoor Gym | 👥 Volleyball Team</p>
                                        <span class="schedule-time">⏰ 15:00 - 17:00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Tab -->
                        <div class="tab-content" id="performanceTab">
                            <div class="section-header">
                                <h2>Performance & Achievements</h2>
                            </div>
                            
                            <div class="performance-grid">
                                <div class="performance-card">
                                    <div class="performance-value">92%</div>
                                    <div class="performance-label">Attendance Rate</div>
                                </div>
                                <div class="performance-card">
                                    <div class="performance-value">8</div>
                                    <div class="performance-label">Matches Played</div>
                                </div>
                                <div class="performance-card">
                                    <div class="performance-value">5</div>
                                    <div class="performance-label">Goals Scored</div>
                                </div>
                                <div class="performance-card">
                                    <div class="performance-value">3</div>
                                    <div class="performance-label">Trophies Won</div>
                                </div>
                            </div>

                            <h3 style="margin-bottom: 18px; color: #2c3e50; font-size: 18px;">🏆 Recent Achievements</h3>
                            <div class="achievements-list">
                                <div class="achievement-item">
                                    <div class="achievement-icon">🥇</div>
                                    <div class="achievement-details">
                                        <h4>Inter-University Football Championship - Winner</h4>
                                        <p>March 2025 | Leading goal scorer with 12 goals</p>
                                    </div>
                                </div>
                                <div class="achievement-item">
                                    <div class="achievement-icon">🥈</div>
                                    <div class="achievement-details">
                                        <h4>Faculty Basketball Tournament - Runner Up</h4>
                                        <p>February 2025 | Team Captain</p>
                                    </div>
                                </div>
                                <div class="achievement-item">
                                    <div class="achievement-icon">⭐</div>
                                    <div class="achievement-details">
                                        <h4>Best Newcomer Award 2023</h4>
                                        <p>December 2023 | Outstanding performance in Freshers' Tournament</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Profile</h2>
                <button class="close-modal" onclick="closeEditModal()">×</button>
            </div>
            <form id="editProfileForm" onsubmit="saveProfile(event)">
                <div class="form-group">
                    <label for="editName">Full Name</label>
                    <input type="text" id="editName" value="Kavindu Perera" required>
                </div>
                <div class="form-group">
                    <label for="editEmail">Email</label>
                    <input type="email" id="editEmail" value="kavindu@sci.cmb.ac.lk" required>
                </div>
                <div class="form-group">
                    <label for="editPhone">Phone Number</label>
                    <input type="tel" id="editPhone" value="+94 77 123 4567" required>
                </div>
                <div class="form-group">
                    <label for="editBio">Bio (Optional)</label>
                    <textarea id="editBio" placeholder="Tell us about yourself...">Passionate athlete and team player. Love playing football and basketball.</textarea>
                </div>
                <button type="submit" class="btn-save">Save Changes</button>
            </form>
        </div>
    </div>

    