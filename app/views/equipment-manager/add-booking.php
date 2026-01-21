<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Equipment Report</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/equipment-manager/sub-nav.css");
    @import url("/uoc-sports/public/css/general/footer.css");

    @import url("/uoc-sports/public/css/equipment-manager/report.css");
    @import url("/uoc-sports/public/css/equipment-manager/page.css");
  </style>
</head>
<body>
<?php
    require "../app/views/templates/general/header.php";
    require "../app/views/equipment-manager/header-subnav.php";
?>

<div class="main-wrapper">        
    <div class="page-form-container">

            
            <!-- Add practice Form -->
             <div class="page-header">
                <div>
                    <h2>Reserve Equipment</h2>
                    <p>Manage equipment reservations</p>
                </div>
               
            </div>
            <form id="addPracticeForm" class="form" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="user_id">User ID *</label>
                        <input type="text" id="user_id" name="userId" placeholder="Enter user ID" required>    
                    </div>  

                    <div class="form-group">
                        <label for="sport">Sport *</label>
                        <select id="sport" name="sport" required>
                            <option value="">Select Sport</option>
                            <option value="ATH">Athletics</option>
                            <option value="RUG">Rugby</option>
                            <option value="TEN">Tennis</option>
                            <option value="WEI">Weightlifting</option>
                            <option value="BAS">Basketball</option>
                            <option value="CAR">Carrom</option>
                            <option value="SCR">Scrabble</option>
                            <option value="CHE">Chess</option>
                            <option value="FOO">Football</option>
                            <option value="BSB">Baseball</option>
                            <option value="ROW">Rowing</option>
                            <option value="NET">Netball</option>
                            <option value="TEA">Teakwondo</option>
                            <option value="HOC">Hockey</option>
                            <option value="ELL">Elle</option>
                            <option value="CRI">Cricket</option>
                            <option value="KAB">Kabaddi</option>
                            <option value="WRE">Wrestling</option>
                            <option value="BAD">Badminton</option>
                            <option value="TBT">Table Tennis</option>
                            <option value="VOL">Volleyball</option>
                            <option value="BOX">Boxing</option>
                            <option value="KAR">Karate</option>
                            <option value="SWI">Swimming</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="equipment">Equipment Type *</label>
                        <select id="equipment" name="equipmentId" required disabled>
                            <option value="">Select Sport First</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quantity">Quantity </label>
                        <input type="number" id="quantity" name="quantity" min="1" placeholder="Enter quantity" >
                    </div>

                    <div class="form-group">
                        <label for="reservation_date">Reservation Date *</label>
                        <input type="date" id="reservation_date" name="reservationDate" required>
                    </div>

                    <div class="form-group">
                        <label for="start_time">Reservation Start Time *</label>
                        <input type="time" id="start_time" name="startTime" required>
                    </div>

                    <div class="form-group">
                        <label for="end_time">Reservation End Time *</label>
                        <input type="time" id="end_time" name="endTime" required>      
                    </div>
                    
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-add" onclick="window.location.href='/uoc-sports/public/equipment-manager/practiceschedule/'" >
                       Cancel
                    </button>
                    <button type="submit" class="btn-add" >
                       Add Booking
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
// Equipment data organized by sport
const equipmentBySport = {
    'FOO': [
        { id: 'EQ007', name: 'Football' }
    ],
    'BAS': [
        { id: 'EQ014', name: 'Basketball' },
        { id: 'EQ015', name: 'Basketball Hoop' }
    ],
    'CRI': [
        { id: 'EQ022', name: 'Cricket Bat' },
        { id: 'EQ023', name: 'Cricket Ball' }
    ],
    'TEN': [
        { id: 'EQ001', name: 'Tennis Racket' },
        { id: 'EQ002', name: 'Tennis Ball' }
    ],
    'BAD': [
        { id: 'EQ003', name: 'Badminton Racket' },
        { id: 'EQ004', name: 'Shuttlecock' }
    ],
    'TBT': [
        { id: 'EQ005', name: 'Table Tennis Bat' },
        { id: 'EQ006', name: 'Table Tennis Ball' }
    ],
    'VOL': [
        { id: 'EQ008', name: 'Volleyball' },
        { id: 'EQ009', name: 'Volleyball Net' }
    ],
    'NET': [
        { id: 'EQ010', name: 'Netball' },
        { id: 'EQ011', name: 'Netball Post' }
    ],
    'RUG': [
        { id: 'EQ012', name: 'Rugby Ball' }
    ],
    'HOC': [
        { id: 'EQ016', name: 'Hockey Stick' },
        { id: 'EQ017', name: 'Hockey Ball' }
    ],
    'ATH': [
        { id: 'EQ018', name: 'Javelin' },
        { id: 'EQ019', name: 'Shot Put' },
        { id: 'EQ020', name: 'Discus' }
    ],
    'SWI': [
        { id: 'EQ021', name: 'Swimming Goggles' }
    ]
};

document.getElementById('sport').addEventListener('change', function() {
    const sportId = this.value;
    const equipmentSelect = document.getElementById('equipment');
    
    // Clear existing options
    equipmentSelect.innerHTML = '<option value="">Select Equipment</option>';
    
    if (sportId && equipmentBySport[sportId]) {
        // Enable the dropdown
        equipmentSelect.disabled = false;
        
        // Populate with equipment for selected sport
        equipmentBySport[sportId].forEach(equipment => {
            const option = document.createElement('option');
            option.value = equipment.id;
            option.textContent = equipment.name;
            equipmentSelect.appendChild(option);
        });
    } else {
        // Disable and reset if no sport or no equipment available
        equipmentSelect.disabled = true;
        equipmentSelect.innerHTML = '<option value="">Select Sport First</option>';
    }
});
</script>

<?php
    require "../app/views/templates/general/footer.php";
?>
</body>
</html>