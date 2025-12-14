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
    @import url("/uoc-sports/public/css/sports-manager/sub-nav.css");
    @import url("/uoc-sports/public/css/general/footer.css");

    @import url("/uoc-sports/public/css/sports-manager/report.css");
  </style>
</head>
<body>
<?php
    require "../app/views/templates/general/header.php";
    require "../app/views/sports-manager/header-subnav.php";
?>

        
        <div class="form-container">

            
            <!-- Add practice Form -->
             <div class="page-header">
                <div>
                    <h2>Add Practice Session</h2>
                    <p>Schedule a new practice session</p>
                </div>
               
            </div>
            <form id="addPracticeForm" class="form" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="Sport name">Sport *</label>
                        <select id="sport" name="sport" required>
                            <option value="">Select Sport</option>
                            <option value="Athletics">Athletics</option>
                            <option value="Rugby">Rugby</option>
                            <option value="Tennis">Tennis</option>
                            <option value="Weightlifting">Weightlifting</option>
                            <option value="Basketball">Basketball</option>
                            <option value="Carrom">Carrom</option>
                            <option value="Scrabble">Scrabble</option>
                            <option value="Chess">Chess</option>
                            <option value="Football">Football</option>
                            <option value="Baseball">Baseball</option>
                            <option value="Rowing">Rowing</option>
                            <option value="Netball">Netball</option>
                            <option value="Teakwondo">Teakwondo</option>
                            <option value="Hockey">Hockey</option>
                            <option value="Elle">Elle</option>
                            <option value="Cricket">Cricket</option>
                            <option value="Kabaddi">Kabaddi</option>
                            <option value="Wrestling">Wrestling</option>
                            <option value="Badminton">Badminton</option>
                            <option value="Table Tennis">Table Tennis</option>
                            <option value="Volleyball">Volleyball</option>
                            <option value="Boxing">Boxing</option>
                            <option value="Karate">Karate</option>
                            <option value="Swimming">Swimming</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="practiceSessionDate">Practice Session Date *</label>
                        <input type="date" id="practiceSessionDate" name="date" required>
                    </div>

                    <div class="form-group">
                        <label for="startTime">Start Time</label>
                        <input type="time" id="startTime" name="stime" required>
                    </div>
                    <div class="form-group">
                        <label for="endTime">End Time</label>
                        <input type="time" id="endTime" name="etime" required>
                    </div>
                    
                <div class="form-row">
                    <div class="form-group">
                        <label for="expenseDescription"> Special Notes *</label>
                        <textarea id="expenseDescription" name="notes" rows="4" placeholder="Enter any special notes..."></textarea>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="location"> Location *</label>
                        <select id="location" name="location" required>
                            <option>Select the Location</option>
                            <option value="Indoor Court">Indoor Tennis Court</option>
                            <option value="Indoor court">Indoor Badminton Court</option>
                            <option value="Outdoor Court">Outdoor Basketball court</option>
                            <option value="Outdoor Field">Outdoor Baseball court</option>
                            <option value="Outdoor Field">Indoor volleyball court</option>
                            <option value="Outdoor Field">Outdoor Cricket Field</option>
                            <option value="Swimming Pool">Elle Field</option>
                            <option value="Carrom room">Carrom Room</option>

                        </select>
                    </div>
                </div>
                
                    
                </div>

                <div class="form-actions">
                    <button type="button" class="view-all-link" onclick="window.location.href='/project/uoc-sports/app/views/sports-manager/expenses.php'">
                       Cancel
                    </button>
                    <button type="submit" class="view-all-link">
                       Add Practice Session
                    </button>
                </div>
            </form>
        </div>

 <?php
    require "../app/views/templates/general/footer.php";
?>

</body>
</html>