<div class="container">
  <!-- Header -->
  <div class="page-header">
    <h1>Welcome Back, <?php echo isset($username) ? htmlspecialchars($username) : 'Captain'; ?></h1>
    <p>Manage your team and track sports activities</p>

    <!-- Sport card (shows captain's sport) -->
    <div class="sport-card" style="margin-top:10px; padding:8px 12px; background:#fff; border-radius:6px; display:inline-block; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
      <strong>Sport:</strong>
      <span style="margin-left:8px;"><?php echo isset($sport_name) && $sport_name ? htmlspecialchars($sport_name) : 'Not assigned'; ?></span>
    </div>
  </div>

  <!-- Stats Grid -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon">👥</div>
      <div class="stat-label">Team Members</div>
      <div class="stat-value">25</div>
      <div class="stat-subtitle">Active members this season</div>
    </div>

    <div class="stat-card blue">
      <div class="stat-icon">🏋️</div>
      <div class="stat-label">Practice Sessions</div>
      <div class="stat-value">3</div>
      <div class="stat-subtitle">Scheduled this month</div>
    </div>

    <div class="stat-card green">
      <div class="stat-icon">📊</div>
      <div class="stat-label">Attendance Rate</div>
      <div class="stat-value">85%</div>
      <div class="stat-subtitle">Last 4 sessions average</div>
    </div>

    <div class="stat-card orange">
      <div class="stat-icon">🎯</div>
      <div class="stat-label">Upcoming Events</div>
      <div class="stat-value">2</div>
      <div class="stat-subtitle">Next 30 days</div>
    </div>
  </div>

  <!-- Main Content Grid -->
  <div class="content-grid">
    <!-- Practice Sessions Table -->
    <div class="table-section">
      <div class="table-header">
        <h2>Monthly Practices</h2>
      </div>
      <div class="table-wrapper">
        <table class="practice-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Venue</th>
              <th>Purpose</th>
            </tr>
          </thead>
          <tbody id="practiceBody">
            <tr>
              <td class="date-cell">20 Aug 2025</td>
              <td class="venue-cell">Main Gym</td>
              <td><span class="purpose-badge">Team Practice</span></td>
            </tr>
            <tr>
              <td class="date-cell">22 Aug 2025</td>
              <td class="venue-cell">Outdoor Field</td>
              <td><span class="purpose-badge">Fitness Training</span></td>
            </tr>
            <tr>
              <td class="date-cell">25 Aug 2025</td>
              <td class="venue-cell">Main Gym</td>
              <td><span class="purpose-badge">Strategy Meeting</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar-section">
      <!-- Team Info Card -->
      <div class="info-card">
        <div class="info-card-header">
          <h3>Team Overview</h3>
        </div>
        <div class="info-card-content">
          <div class="info-item">
            <span class="info-label">Total Players</span>
            <span class="info-value">25</span>
          </div>
          <div class="info-item">
            <span class="info-label">Coaches</span>
            <span class="info-value">2</span>
          </div>
          <div class="info-item">
            <span class="info-label">Matches Won</span>
            <span class="info-value highlight">12</span>
          </div>
          <div class="info-item">
            <span class="info-label">This Season</span>
            <span class="info-value">2025</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Add interactivity to quick action buttons
  document.querySelectorAll('.action-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const action = this.textContent.trim();
      console.log('Action clicked:', action);
      // Add your navigation logic here
    });
  });

  // Highlight active page
  const currentPage = document.getElementById('sub-dashboard');
  if (currentPage) currentPage.classList.add('active');
</script>