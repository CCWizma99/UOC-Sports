</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Prepare all students data for JS filtering
  const allStudents = [
    // Available members (not selected)
    ...[
      <?php foreach ($available_members as $student): ?>
      {
        id: <?php echo json_encode($student['user_id']); ?>,
        name: <?php echo json_encode($student['fname'] . ' ' . $student['lname']); ?>,
        idNumber: <?php echo json_encode($student['student_id']); ?>,
        faculty: <?php echo json_encode($student['faculty_name'] ?? ''); ?>,
        selected: false
      },
      <?php endforeach; ?>
    ],
    // Team members (selected)
    ...[
      <?php foreach ($team_members as $member): ?>
      {
        id: <?php echo json_encode($member['user_id']); ?>,
        name: <?php echo json_encode($member['fname'] . ' ' . $member['lname']); ?>,
        idNumber: <?php echo json_encode($member['student_id']); ?>,
        faculty: <?php echo json_encode($member['faculty_name'] ?? ''); ?>,
        selected: true
      },
      <?php endforeach; ?>
    ]
  ];

  let currentFilter = 'all';
  let searchTerm = '';

  function getFilteredStudents() {
    return allStudents.filter(student => {
      const matchesSearch =
        student.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        student.idNumber.toLowerCase().includes(searchTerm.toLowerCase());
      const matchesFilter =
        currentFilter === 'all' ||
        (currentFilter === 'selected' && student.selected) ||
        (currentFilter === 'unselected' && !student.selected);
      return matchesSearch && matchesFilter;
    });
  }

  function getAvailableMembers() {
    return getFilteredStudents().filter(s => !s.selected);
  }
  function getSelectedMembers() {
    return getFilteredStudents().filter(s => s.selected);
  }

  async function sendMemberAction(student, isSelected, btn) {
    const tid = <?php echo json_encode($selected_tournament_id); ?>;
    if (!tid) return;

    // Loading state
    const originalText = btn.textContent;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;
    btn.style.opacity = '0.7';

    const formData = new FormData();
    formData.append('tournament_id', tid);
    formData.append(isSelected ? 'remove_member_id' : 'add_member_id', student.id);

    try {
      const response = await fetch('', {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      });

      if (!response.ok) throw new Error('Network response was not ok');
      
      const result = await response.json();

      if (result.success) {
        // Update local state
        const studentIndex = allStudents.findIndex(s => s.id === student.id);
        if (studentIndex !== -1) {
          allStudents[studentIndex].selected = !isSelected;
          render();
        }
        UI.showToast(result.message || (isSelected ? 'Member removed' : 'Member added'), 'success');
      } else {
        UI.showToast(result.message || 'Something went wrong', 'error');
        btn.textContent = originalText;
        btn.disabled = false;
        btn.style.opacity = '1';
      }
    } catch (error) {
      console.error('Fetch error:', error);
      UI.showToast('Network error. Please try again.', 'error');
      btn.textContent = originalText;
      btn.disabled = false;
      btn.style.opacity = '1';
    }
  }

  function createRow(student, isSelected) {
    const row = document.createElement('tr');
    const tid = <?php echo json_encode($selected_tournament_id); ?>;
    const buttonClass = isSelected ? 'remove' : 'add';
    const buttonText = isSelected ? 'Remove' : (tid ? 'Add to Squad' : 'Add to Team');

    row.innerHTML = `
      <td class="student-name">${student.name}</td>
      <td class="student-id">${student.idNumber}</td>
      <td class="faculty">${student.faculty}</td>
      <td>
        <button class="action-btn ${buttonClass}" type="button">${buttonText}</button>
      </td>
    `;

    const btn = row.querySelector('.action-btn');
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        sendMemberAction(student, isSelected, btn);
    });

    return row;
  }

  function render() {
    const availableBody = document.getElementById('availableTableBody');
    const selectedBody = document.getElementById('selectedTableBody');
    // Render available members
    availableBody.innerHTML = '';
    const available = getAvailableMembers();
    if (available.length === 0) {
      availableBody.innerHTML = `
        <tr>
          <td colspan="4" style="text-align: center; padding: 40px 0;">
            <div class="empty-state">
              <div class="empty-state-icon">📭</div>
              <h3>No Members Available</h3>
              <p>All members are already in the team</p>
            </div>
          </td>
        </tr>
      `;
    } else {
      available.forEach(student => {
        availableBody.appendChild(createRow(student, false));
      });
    }
    // Render selected members
    selectedBody.innerHTML = '';
    const selected = getSelectedMembers();
    if (selected.length === 0) {
      selectedBody.innerHTML = `
        <tr>
          <td colspan="4" style="text-align: center; padding: 40px 0;">
            <div class="empty-state">
              <div class="empty-state-icon">👥</div>
              <h3>No Members Selected</h3>
              <p>Add members to build your team</p>
            </div>
          </td>
        </tr>
      `;
    } else {
      selected.forEach(student => {
        selectedBody.appendChild(createRow(student, true));
      });
    }
    updateCounts();
  }

  function updateCounts() {
    document.getElementById('availableCount').textContent = getAvailableMembers().length;
    document.getElementById('selectedCount').textContent = getSelectedMembers().length;
    document.getElementById('availableTotal').textContent = allStudents.filter(s => !s.selected).length;
    document.getElementById('selectedTotal').textContent = allStudents.filter(s => s.selected).length;
    document.getElementById('readyToAdd').textContent = getAvailableMembers().length;
    document.getElementById('availableSlots').textContent = 20 - allStudents.filter(s => s.selected).length;
  }

  document.getElementById('searchInput').addEventListener('input', (e) => {
    searchTerm = e.target.value;
    render();
  });
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      e.target.classList.add('active');
      currentFilter = e.target.dataset.filter;
      render();
    });
  });
  render();

  // Tournament switch logic
  const tourSelect = document.getElementById('managementAreaSelect');
  if (tourSelect) {
    tourSelect.addEventListener('change', function() {
      const val = this.value;
      const url = new URL(window.location.href);
      url.searchParams.set('tournament_id', val);
      window.location.href = url.toString();
    });
  }
});
</script>

<?php if (!$selected_tournament_id): ?>
<!-- Tournament Selection Overlay -->
<div id="tournamentOverlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 16px; width: 100%; max-width: 500px; padding: 32px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="background: #f3f0f7; width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: #5e2d91;">
                <i class="fas fa-trophy fa-2x"></i>
            </div>
            <h2 style="color: #1e293b; margin-bottom: 8px;">Select Tournament</h2>
            <p style="color: #64748b; font-size: 14px;">Select an active tournament to manage your squad member selection.</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 12px; max-height: 300px; overflow-y: auto; padding-right: 4px;">
            <?php if (empty($tournaments)): ?>
                <div style="text-align: center; padding: 20px; color: #94a3b8; font-style: italic;">
                    No active tournaments found for your sport.
                </div>
            <?php else: ?>
                <?php foreach ($tournaments as $t): ?>
                    <a href="?tournament_id=<?php echo $t['tournament_id']; ?>" 
                       style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; text-decoration: none; transition: all 0.2s; color: #334155; font-weight: 600;">
                        <span><?php echo htmlspecialchars($t['tournament_name']); ?></span>
                        <i class="fas fa-chevron-right" style="color: #94a3b8; font-size: 12px;"></i>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 24px; text-align: center;">
            <a href="/uoc-sports/public/captain" style="color: #64748b; font-size: 14px; text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>
<?php endif; ?>
<div class="container">
  <!-- Header -->
  <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px;">
    <div>
      <h1 style="margin: 0; color: #1e293b;">Squad Management</h1>
      <div class="team-name" style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">
        <i class="fas fa-shield-alt"></i> <?php echo htmlspecialchars($sport_name); ?>
      </div>
    </div>
    <div style="text-align: right;">
        <label style="display: block; font-size: 10px; text-transform: uppercase; color: #94a3b8; font-weight: 700; margin-bottom: 4px;">Switch Tournament</label>
        <select id="managementAreaSelect" style="padding: 8px 12px; border-radius: 8px; border: 2px solid #5e2d91; background: white; color: #5e2d91; font-weight: 600; font-size: 13px; cursor: pointer; outline: none;">
            <option value="" disabled <?php echo !$is_tournament_mode ? 'selected' : ''; ?>>Select a Tournament...</option>
            <?php foreach ($tournaments as $t): ?>
                <option value="<?php echo $t['tournament_id']; ?>" <?php echo ($selected_tournament_id == $t['tournament_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($t['tournament_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
  </div>

  <?php if ($is_tournament_mode): ?>
    <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; color: #1e40af; font-size: 13px; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-info-circle fa-lg"></i>
        <span>Managing Squad for <strong><?php echo htmlspecialchars($current_tournament_name); ?></strong>. You can add any student enrolled in this sport to the squad.</span>
    </div>
  <?php endif; ?>

  <!-- Filter Section -->
  <div class="filter-section">
    <div class="search-group">
      <label>Search Members</label>
      <input type="text" id="searchInput" class="search-input" placeholder="Search by name or ID...">
    </div>
    <div class="search-group">
      <label>Filter by Status</label>
      <div class="filter-buttons">
        <button class="filter-btn active" data-filter="all">All Members</button>
        <button class="filter-btn" data-filter="selected">Selected</button>
        <button class="filter-btn" data-filter="unselected">Not Selected</button>
      </div>
    </div>
  </div>

  <!-- Content Grid -->
  <div class="content-grid">
    <!-- Available Members Table -->
    <div class="table-section">
      <div class="table-header-bar">
        <h2>
          <?php echo $is_tournament_mode ? 'All Enrolled Members' : 'Eligible Students'; ?>
          <span class="member-count" id="availableCount"><?php echo isset($available_total) ? htmlspecialchars($available_total) : '0'; ?></span>
        </h2>
      </div>
      <div class="table-wrapper">
        <table class="members-table">
          <thead>
            <tr>
              <th>Student Name</th>
              <th>ID Number</th>
              <th>Faculty</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="availableTableBody">
            <?php if (!empty($available_members)): ?>
              <?php foreach ($available_members as $student): ?>
                <tr>
                  <td class="student-name"><?php echo htmlspecialchars($student['fname'] . ' ' . $student['lname']); ?></td>
                  <td class="student-id"><?php echo htmlspecialchars($student['student_id']); ?></td>
                  <td class="faculty"><?php echo htmlspecialchars($student['faculty_name'] ?? ''); ?></td>
                  <td>
                    <form method="post" action="">
                      <input type="hidden" name="add_member_id" value="<?php echo htmlspecialchars($student['user_id']); ?>">
                      <button class="action-btn add" type="submit">Add</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" style="text-align: center; padding: 40px 0;">
                  <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <h3>No Members Available</h3>
                    <p>All members are already in the team</p>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="stats-bar">
        <div class="stat">
          <span class="stat-label">Total Available</span>
          <span class="stat-value" id="availableTotal"><?php echo isset($available_total) ? htmlspecialchars($available_total) : '0'; ?></span>
        </div>
        <div class="stat">
          <span class="stat-label">Ready to Add</span>
          <span class="stat-value" id="readyToAdd"><?php echo isset($available_total) ? htmlspecialchars($available_total) : '0'; ?></span>
        </div>
      </div>
    </div>

    <!-- Current Team Members Table -->
    <div class="table-section">
      <div class="table-header-bar">
        <h2>
          <?php echo $is_tournament_mode ? 'Selected Tournament Squad' : 'Current Team Members'; ?>
          <span class="member-count" id="selectedCount"><?php echo isset($selected_total) ? htmlspecialchars($selected_total) : '0'; ?></span>
        </h2>
      </div>
      <div class="table-wrapper">
        <table class="members-table">
          <thead>
            <tr>
              <th>Student Name</th>
              <th>ID Number</th>
              <th>Faculty</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="selectedTableBody">
            <?php if (!empty($team_members)): ?>
              <?php foreach ($team_members as $member): ?>
                <tr>
                  <td class="student-name"><?php echo htmlspecialchars($member['fname'] . ' ' . $member['lname']); ?></td>
                  <td class="student-id"><?php echo htmlspecialchars($member['student_id']); ?></td>
                  <td class="faculty"><?php echo htmlspecialchars($member['faculty_name'] ?? ''); ?></td>
                  <td>
                    <form method="post" action="">
                      <input type="hidden" name="remove_member_id" value="<?php echo htmlspecialchars($member['user_id']); ?>">
                      <button class="action-btn remove" type="submit">Remove</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" style="text-align: center; padding: 40px 0;">
                  <div class="empty-state">
                    <div class="empty-state-icon">👥</div>
                    <h3>No Members Selected</h3>
                    <p>Add members to build your team</p>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="stats-bar">
        <div class="stat">
          <span class="stat-label">Total Members</span>
          <span class="stat-value" id="selectedTotal"><?php echo isset($selected_total) ? htmlspecialchars($selected_total) : '0'; ?></span>
        </div>
        <div class="stat">
          <span class="stat-label">Capacity</span>
          <span class="stat-value" id="availableSlots">20</span>
        </div>
      </div>
    </div>
  </div>
</div>

