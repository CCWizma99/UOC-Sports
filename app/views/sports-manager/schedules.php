<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add a New Event</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/sports-manager/sub-nav.css");
    @import url("/uoc-sports/public/css/sports-manager/schedules.css");
    @import url("/uoc-sports/public/css/general/footer.css");
  </style>
</head>
<body>

<?php
require "../app/views/templates/general/header.php";
require "../app/views/sports-manager/header-subnav.php";
?>
<div class="container">
  <!-- Header -->
  <div class="page-header">
    <h1>Sports Event Management</h1>
  </div>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Form Section -->
    <div class="form-container">
      <h2>Add a New Practice Session</h2>
      
      <form class="form" id="eventForm">
        <div class="form-group">
          <label>Select Sport</label>
          <select name="sport" id="sportSelect" required>
            <option value="cricket">Cricket</option>
            <option value="athletics">Athletics</option>
          </select>
        </div>

        <div class="form-group">
          <label>Session Name</label>
          <input type="text" name="event_name" placeholder="Add Event/Competition Name" required>
        </div>

        <div class="form-group">
          <label>Location</label>
          <input type="text" name="location" placeholder="Add Location" required>
        </div>

        <div class="form-group">
          <label>Equipments</label>
          <div id="equipmentList" class="equipment-list"></div>
          <input type="hidden" name="equipments" id="selectedEquipments">
        </div>

        <div class="form-group">
          <label>Select Participants</label>
          <div class="dropdown">
            <input type="text" id="searchBox" class="search-box" placeholder="Search participants...">
            <div id="optionsContainer" class="options"></div>
          </div>
        </div>

        <div class="buttons">
          <button type="reset" class="reset-btn">Reset</button>
          <button type="submit" class="submit-btn">Submit</button>
        </div>
      </form>
    </div>

    <!-- Table Section (spans full width on larger screens) -->
    <div class="schedule-section">
      <h2>Added Schedules</h2>
      <div class="table-wrapper">
        <table class="schedule-table">
          <thead>
            <tr>
              <th>Sport</th>
              <th>Event Name</th>
              <th>Location</th>
              <th>Equipments</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="scheduleBody">
            <!-- Dynamic content will be inserted here -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
  const equipmentData = {
    cricket: ['Bat', 'Ball', 'Helmet', 'Pads', 'Gloves', 'Stumps'],
    athletics: ['Stopwatch', 'Relay Batons', 'Starting Blocks', 'High Jump Bar', 'Javelin']
  };

  const equipmentList = document.getElementById('equipmentList');
  const selectedEquipmentsInput = document.getElementById('selectedEquipments');
  const sportSelect = document.getElementById('sportSelect');

  function renderEquipments(sport) {
    equipmentList.innerHTML = '';
    selectedEquipmentsInput.value = '';
    if(equipmentData[sport]) {
      equipmentData[sport].forEach(item => {
        const div = document.createElement('div');
        div.className = 'equipment-item';
        div.textContent = item;

        div.addEventListener('click', () => {
          div.classList.toggle('selected');
          const selected = Array.from(equipmentList.querySelectorAll('.equipment-item.selected'))
                                .map(el => el.textContent);
          selectedEquipmentsInput.value = selected.join(',');
        });

        equipmentList.appendChild(div);
      });
    }
  }

  renderEquipments(sportSelect.value);
  sportSelect.addEventListener('change', () => {
    renderEquipments(sportSelect.value);
  });

  let schedules = [
    {sport: 'Cricket', event_name: 'Intercollege Tournament', location: 'Main Ground', equipments: 'Bat,Ball,Helmet'},
    {sport: 'Athletics', event_name: 'Annual Track Meet', location: 'Track Field', equipments: 'Stopwatch,Relay Batons'}
  ];

  function renderSchedules() {
    const tbody = document.getElementById('scheduleBody');
    tbody.innerHTML = '';
    schedules.forEach((s, idx) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${s.sport}</td>
        <td>${s.event_name}</td>
        <td>${s.location}</td>
        <td>${s.equipments}</td>
        <td>
          <div class="action-buttons">
            <button class="action-btn update-btn" onclick="updateSchedule(${idx})">Update</button>
            <button class="action-btn delete-btn" onclick="deleteSchedule(${idx})">Delete</button>
          </div>
        </td>
      `;
      tbody.appendChild(tr);
    });
  }

  function updateSchedule(idx) {
    const s = schedules[idx];
    alert(`Redirect to update page for: ${s.event_name}`);
  }

  function deleteSchedule(idx) {
    if(confirm('Are you sure you want to delete this schedule?')) {
      schedules.splice(idx, 1);
      renderSchedules();
    }
  }

  renderSchedules();

  // Form submission
  document.getElementById('eventForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const newSchedule = {
      sport: formData.get('sport'),
      event_name: formData.get('event_name'),
      location: formData.get('location'),
      equipments: formData.get('equipments')
    };
    schedules.push(newSchedule);
    renderSchedules();
    e.target.reset();
    renderEquipments(sportSelect.value);
  });
</script>

</body>
</html>
