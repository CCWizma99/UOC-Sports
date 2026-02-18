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
    @import url("/uoc-sports/public/css/sports-manager/add-participants.css");
  </style>
  <script src="/uoc-sports/public/js/sports-manager/add-participants.js" defer></script>
</head>
<body>
<?php
    require "../app/views/templates/general/header.php";
    require "../app/views/sports-manager/header-subnav.php";
?>

        
        <div class="form-container">

            
            <!-- Add Participants Form -->
             <div class="page-header">
                <div>
                    <h2>Add Participants</h2>
                    <p>Add participants to the competition by uploading a file or selecting from students</p>
                </div>
            </div>

            <!-- Selection Tabs -->
            <div class="selection-tabs">
                <button class="tab-button active" onclick="switchTab('upload')">Upload File</button>
                <button class="tab-button" onclick="switchTab('select')">Select Students</button>
            </div>

            <form id="addParticipantsForm" class="form" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="sport">Sport *</label>
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
                        <label for="competitionName">Competition Name *</label>
                        <input type="text" id="competitionName" name="competitionName" placeholder="Enter competition name" required>
                    </div>
                </div>

                <!-- Upload File Tab -->
                <div id="uploadTab" class="tab-content active">
                    <div class="form-group full-width">
                        <label for="participantsFile">Upload Participant List</label>
                        <div class="file-upload-area" id="fileUploadArea">
                            <div class="upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="upload-text">
                                <h3>Drag & Drop or Click to Upload</h3>
                                <p>Supported formats: JPG, PNG, PDF (Max 5MB)</p>
                            </div>
                            <input type="file" id="participantsFile" name="participantsFile" class="file-input-hidden" accept="image/jpeg,image/jpg,image/png,application/pdf">
                        </div>
                        <div class="file-info" id="fileInfo">
                            <div class="file-details">
                                <i class="fas fa-file file-icon"></i>
                                <span class="file-name" id="fileName"></span>
                            </div>
                            <button type="button" class="remove-file-btn" id="removeFileBtn">Remove</button>
                        </div>
                    </div>
                </div>

                <!-- Select Students Tab -->
                <div id="selectTab" class="tab-content">
                    <div class="form-group full-width">
                        <label for="participants">Select Participants *</label>
                        <div class="multi-select-container">
                            <div class="multi-select-display" id="multiSelectDisplay">
                                <div class="selected-items" id="selectedItems">
                                    <span class="placeholder-text" id="placeholderText">Click to select students...</span>
                                </div>
                                <span class="dropdown-arrow">▼</span>
                            </div>
                            <div class="multi-select-dropdown" id="multiSelectDropdown">
                                <div class="search-box">
                                    <input type="text" id="studentSearch" placeholder="Search by name or student ID...">
                                </div>
                                <div class="student-list" id="studentList">
                                    <!-- Students will be populated by JavaScript -->
                                </div>
                            </div>
                            <input type="hidden" name="selectedStudents" id="selectedStudents">
                        </div>
                    </div>
                </div>
                        
                <div class="form-actions">
                    <button type="button" class="view-all-link" onclick="window.location.href='/uoc-sports/public/sport-manager/competitions/'">
                       Cancel
                    </button>
                    <button type="submit" class="view-all-link">
                       Add Participants
                    </button>
                </div>

            </form>
        </div>

        <?php
    require "../app/views/templates/general/footer.php";
?>
</body>
</html>
