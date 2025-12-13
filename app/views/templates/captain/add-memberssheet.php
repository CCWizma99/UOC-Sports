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
          <span class="member-count" id="availableCount">0</span>
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
            <!-- Dynamically populated -->
          </tbody>
        </table>
      </div>
      <div class="stats-bar">
        <div class="stat">
          <span class="stat-label">Total Available</span>
          <span class="stat-value" id="availableTotal">0</span>
        </div>
        <div class="stat">
          <span class="stat-label">Ready to Add</span>
          <span class="stat-value" id="readyToAdd">0</span>
        </div>
      </div>
    </div>

    <!-- Current Team Members Table -->
    <div class="table-section">
      <div class="table-header-bar">
        <h2>
          Current Team Members
          <span class="member-count" id="selectedCount">0</span>
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
            <!-- Dynamically populated -->
          </tbody>
        </table>
      </div>
      <div class="stats-bar">
        <div class="stat">
          <span class="stat-label">Total Members</span>
          <span class="stat-value" id="selectedTotal">0</span>
        </div>
        <div class="stat">
          <span class="stat-label">Available Slots</span>
          <span class="stat-value" id="availableSlots">15</span>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Sample data - all available students
  const allStudents = [
    { id: 1, name: 'J. Balakrishnan', idNumber: '2023/IS/012', faculty: 'UCSC', selected: true },
    { id: 2, name: 'Jayaweera M. A. J. C. S.', idNumber: '2023/IS/043', faculty: 'UCSC', selected: false },
    { id: 3, name: 'Rajapaksha K. A. G. S. M.', idNumber: '2023/IS/079', faculty: 'UCSC', selected: true },
    { id: 4, name: 'Hettiarachchi H. H. K. C. C.', idNumber: '2023/IS/034', faculty: 'UCSC', selected: false },
    { id: 5, name: 'Silva D. K. R.', idNumber: '2023/IS/056', faculty: 'UCSC', selected: false },
    { id: 6, name: 'Perera A. M. S.', idNumber: '2023/IS/067', faculty: 'UCSC', selected: true },
    { id: 7, name: 'Karunarathna P. L. C.', idNumber: '2023/IS/089', faculty: 'UCSC', selected: false },
    { id: 8, name: 'Fernando W. P. T.', idNumber: '2023/IS/045', faculty: 'UCSC', selected: false },
    { id: 9, name: 'Dharmaratne M. K. S.', idNumber: '2023/IS/078', faculty: 'UCSC', selected: true },
    { id: 10, name: 'Wijesinghe R. N.', idNumber: '2023/IS/095', faculty: 'UCSC', selected: false }
  ];

  let currentFilter = 'all';
  let searchTerm = '';

  // Get filtered students based on status and search
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

  // Get available members (not selected)
  function getAvailableMembers() {
    return getFilteredStudents().filter(s => !s.selected);
  }

  // Get selected members
  function getSelectedMembers() {
    return getFilteredStudents().filter(s => s.selected);
  }

  // Create table row
  function createRow(student, isSelected) {
    const row = document.createElement('tr');
    const buttonClass = isSelected ? 'remove' : 'add';
    const buttonText = isSelected ? 'Remove' : 'Add';
    
    row.innerHTML = `
      <td class="student-name">${student.name}</td>
      <td class="student-id">${student.idNumber}</td>
      <td class="faculty">${student.faculty}</td>
      <td>
        <button class="action-btn ${buttonClass}" onclick="toggleMember(${student.id})">
          ${buttonText}
        </button>
      </td>
    `;
    return row;
  }

  // Toggle member selection
  function toggleMember(studentId) {
    const student = allStudents.find(s => s.id === studentId);
    if (student) {
      student.selected = !student.selected;
      render();
    }
  }

  // Render tables
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

    // Update counts
    updateCounts();
  }

  // Update statistics
  function updateCounts() {
    document.getElementById('availableCount').textContent = getAvailableMembers().length;
    document.getElementById('selectedCount').textContent = getSelectedMembers().length;
    document.getElementById('availableTotal').textContent = allStudents.filter(s => !s.selected).length;
    document.getElementById('selectedTotal').textContent = allStudents.filter(s => s.selected).length;
    document.getElementById('readyToAdd').textContent = getAvailableMembers().length;
    document.getElementById('availableSlots').textContent = 15 - allStudents.filter(s => s.selected).length;
  }

  // Search input handler
  document.getElementById('searchInput').addEventListener('input', (e) => {
    searchTerm = e.target.value;
    render();
  });

  // Filter button handlers
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      e.target.classList.add('active');
      currentFilter = e.target.dataset.filter;
      render();
    });
  });

  // Initial render
  render();
</script>