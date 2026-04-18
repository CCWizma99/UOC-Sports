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

  function createRow(student, isSelected) {
    const row = document.createElement('tr');
    const buttonClass = isSelected ? 'remove' : 'add';
    const buttonText = isSelected ? 'Remove' : 'Add';
    row.innerHTML = `
      <td class="student-name">${student.name}</td>
      <td class="student-id">${student.idNumber}</td>
      <td class="faculty">${student.faculty}</td>
      <td>
        <form method="post" action="">
          <input type="hidden" name="${isSelected ? 'remove_member_id' : 'add_member_id'}" value="${student.id}">
          <button class="action-btn ${buttonClass}" type="submit">${buttonText}</button>
        </form>
      </td>
    `;
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
    document.getElementById('availableSlots').textContent = 15 - allStudents.filter(s => s.selected).length;
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
});
</script>
<div class="container">
  <!-- Header -->
  <div class="page-header">
    <h1>Add Members</h1>
    <div class="team-name">UOC Volleyball Team</div>
  </div>

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
          Available Members
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
          Current Team Members
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
          <span class="stat-label">Available Slots</span>
          <span class="stat-value" id="availableSlots"><?php echo isset($available_slots) ? htmlspecialchars($available_slots) : '15'; ?></span>
        </div>
      </div>
    </div>
  </div>
</div>

