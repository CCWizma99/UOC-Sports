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
      <div class="stat-info">
        <div class="stat-label">Team Members</div>
        <div class="stat-subtitle">Active members this season</div>
      </div>
      <div class="stat-value"><?php echo isset($member_count) ? htmlspecialchars($member_count) : '0'; ?></div>
    </div>

    <div class="stat-card blue">
      <div class="stat-icon">🏋️</div>
      <div class="stat-info">
        <div class="stat-label">Practice Sessions</div>
        <div class="stat-subtitle">Scheduled this month</div>
      </div>
      <div class="stat-value"><?php echo isset($session_count) ? htmlspecialchars($session_count) : '0'; ?></div>
    </div>

    <div class="stat-card green">
      <div class="stat-icon">📊</div>
      <div class="stat-info">
        <div class="stat-label">Attendance Rate</div>
        <div class="stat-subtitle">Overall team average</div>
      </div>
      <div class="stat-value"><?php echo isset($attendance_rate) ? htmlspecialchars($attendance_rate) : '0'; ?>%</div>
    </div>

    <div class="stat-card orange">
      <div class="stat-icon">🎯</div>
      <div class="stat-info">
        <div class="stat-label">Upcoming Tournaments</div>
        <div class="stat-subtitle">Next 30 days</div>
      </div>
      <div class="stat-value"><?php echo count($upcoming_events); ?></div>
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
              <th>Time</th>
              <th>Venue</th>
              <th>Purpose</th>  
            </tr>
          </thead>
          <tbody id="practiceBody">
            <?php foreach ($practice_sessions as $session): ?>
            <tr>
              <td class="date-cell"><?php echo isset($session['session_date']) ? htmlspecialchars($session['session_date']) : 'N/A'; ?></td>
              <td class="time-cell"><?php echo isset($session['start_time']) ? htmlspecialchars($session['start_time']) : 'N/A'; ?></td>
              <td class="venue-cell"><?php echo isset($session['location']) ? htmlspecialchars($session['location']) : 'N/A'; ?></td>
              <td><?php echo isset($session['notes']) ? htmlspecialchars($session['notes']) : 'N/A'; ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar-section">
      <!-- Upcoming Events Table -->
      <div class="info-card">
        <div class="info-card-header">
          <h3>Upcoming Tournaments</h3>
        </div>
        <div class="info-card-content">
          <div class="table-wrapper" >
            <table class="events-table" >
              <thead>
                <tr >
                  <th >Start Date</th>
                  <th >End Date</th>
                  <th >Tournament</th>
                </tr>
              </thead>
              <tbody id="upcomingEvents">
                <?php foreach ($upcoming_events as $event): ?>
                <tr>
                  <td class="date-cell"><?php echo htmlspecialchars($event['start_date'])?></td>
                  <td class="venue-cell"><?php echo htmlspecialchars($event['end_date'])?></td>
                  <td><span ><?php echo htmlspecialchars($event['name'])?></span></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
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