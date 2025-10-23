<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add a New Event</title>
  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/sports-manager/form.css");
    @import url("/uoc-sports/public/css/sports-manager/sub-nav.css");
    @import url("/uoc-sports/public/css/general/footer.css");

    .form-container {
      max-width: 650px;
      margin: 30px auto;
      padding: 20px;
      border-radius: 8px;
      background: #fff;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .form-container h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .form-container label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }

    .form-container input,
    .form-container textarea,
    .form-container select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
    }

    .buttons {
      margin-top: 20px;
      display: flex;
      justify-content: space-between;
    }

    .submit-btn {
      background-color: #4CAF50;
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 4px;
      cursor: pointer;
    }

    .reset-btn {
      background-color: #f44336;
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 4px;
      cursor: pointer;
    }

    /* Equipment styling */
    .equipment-list {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 5px;
    }

    .equipment-item {
      background: #e0e0e0;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 14px;
      cursor: pointer;
      user-select: none;
      transition: 0.2s;
    }

    .equipment-item.selected {
      background-color: #4CAF50;
      color: #fff;
    }

    /* Schedule Table */
    .schedule-table {
      width: 100%;
      max-width: 800px;
      margin: 30px auto;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      border-radius: 8px;
      overflow: hidden;
    }

    .schedule-table th, .schedule-table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    .schedule-table th {
      background-color: #f5f5f5;
    }

    .schedule-table tr:hover {
      background-color: #f1f1f1;
    }

    .action-btn {
      padding: 5px 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-right: 5px;
      font-size: 12px;
    }

    .update-btn {
      background-color: #4CAF50;
      color: #fff;
    }

    .delete-btn {
      background-color: #f44336;
      color: #fff;
    }
  </style>
</head>
<body>

<?php
require "../app/views/templates/general/header.php";
require "../app/views/sports-manager/header-subnav.php";
?>

<div class="form-container">
  <form class="form" action="" method="post">
    <h2>Add a New Practice Session</h2>

    <label>Select Sport</label>
    <select name="sport" id="sportSelect" required>
      <option value="cricket">Cricket</option>
      <option value="athletics">Athletics</option>
    </select>

    <label>Session Name</label>
    <input type="text" name="event_name" placeholder="Add Event/Competition Name" required>

    <label>Location</label>
    <input type="text" name="location" placeholder="Add Location" required>

    <label>Equipments</label>
    <div id="equipmentList" class="equipment-list"></div>
    <input type="hidden" name="equipments" id="selectedEquipments">

    <label>Select Participants</label>
    <div class="dropdown">
      <input type="text" id="searchBox" class="search-box" placeholder="Search participants...">
      <div id="optionsContainer" class="options"></div>
    </div>

    <div class="buttons">
      <button type="reset" class="reset-btn">Reset</button>
      <button type="submit" class="submit-btn">Submit</button>
    </div>
  </form>
</div>

<!-- SCHEDULES TABLE -->
<div class="form-container">
  <h2>Added Schedules</h2>
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

<?php
require "../app/views/templates/general/footer.php";
?>

<script>
var currentPage = document.getElementById("sub-schedules");
if(currentPage) currentPage.classList.add("active");

// Simulated equipments for each sport
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

// Initialize equipments on page load
renderEquipments(sportSelect.value);
sportSelect.addEventListener('change', () => {
  renderEquipments(sportSelect.value);
});

// --- SIMULATED SCHEDULES DATA ---
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
        <button class="action-btn update-btn" onclick="updateSchedule(${idx})">Update</button>
        <button class="action-btn delete-btn" onclick="deleteSchedule(${idx})">Delete</button>
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
    schedules.splice(idx,1);
    renderSchedules();
  }
}

renderSchedules();
</script>
</body>
</html>
