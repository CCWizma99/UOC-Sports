`<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sports Manager - Student Achievements</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/sports-manager/sub-nav.css");
    @import url("/uoc-sports/public/css/general/footer.css");
    @import url("/uoc-sports/public/css/sports-manager/report.css");
    @import url("/uoc-sports/public/css/sports-manager/dynamic-background.css");
  </style>
  <script>
    // Pass PHP data to JavaScript
    const initialRankings = <?= json_encode($rankings ?? []) ?>;
    window.selectedSportName = '<?= htmlspecialchars($sportName ?? '') ?>';
  </script>
  <script src="/uoc-sports/public/js/sports-manager/dynamic-background.js"></script>
  <script src="/uoc-sports/public/js/sports-manager/team.js" defer></script>
</head>
<body>
<?php
    require "../app/views/templates/general/header.php";
    require "../app/views/sports-manager/header-subnav.php";
?>
<div class="page-container">

    <div class="container-header">
        <h2>Student Achievements</h2>
        <p>Manage student achievements and records</p>
    </div>

         
         
       <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search Student...">
  
    </div>

    <!-- Achievements Display Area (Top of Page) -->
    <div id="achievementsDisplay" style="display: none; margin: 1.5rem auto 0 auto; background: #f9f7ff; padding: 1.5rem; border-radius: 8px; border: 2px solid #5e2d91; max-width: 1000px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 id="achievementsStudentName" style="color: #5e2d91; margin: 0; font-size: 1.25rem;"></h3>
            <button onclick="closeAchievementsDisplay()" style="display: inline-block;
    text-align: center;
    color: var(--primary-purple);
    font-weight: 600;
    font-size: 0.8rem;
    padding: 0.625rem 1.5rem;
    background-color: var(--lightest-purple);
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid var(--primary-purple);
    box-shadow: 0 1px 2px var(--primary-purple);
    white-space: nowrap;
    margin-right: 15px;">
                Close
            </button>
        </div>
        
        <!-- Performance Analysis and Achievements Layout -->
        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
            <!-- Performance Analysis Chart (Left Side) -->
            <div style="flex: 1; min-width: 400px; background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h4 style="color: #2b0c4d; margin: 0 0 1rem 0; font-size: 1.1rem;">Performance Analysis</h4>
                <p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 1rem;">
                    Track student performance across competitions (Max possible: 12 points for 1st place + Best performer)
</br><p style="color: #6b7280; font-size: 0.7rem; margin-bottom: 1rem;"> 5 points for 1st place,
 3 points for 2nd place,
 2 points for 3rd place</p>
                
                <div style="position: relative; height: 350px;">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
            
            <!-- Achievements List (Right Side) -->
            <div style="flex: 0 0 350px; min-width: 300px;">
                <h4 style="color: #2b0c4d; margin: 0 0 1rem 0; font-size: 1.1rem;">All Achievements</h4>
                <div id="achievementsContent" style="max-height: 450px; overflow-y: auto; padding-right: 0.5rem;">
                    <!-- Achievements will be dynamically inserted here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Students Grid -->
    <div id="studentsContainer" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
        <!-- Student cards will be dynamically inserted here by JavaScript -->
    </div>
</div>

<!-- Achievement Modal -->
<div id="achievementModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 700px; width: 90%; max-height: 80vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        <!-- Modal Header -->
        <div style="padding: 1.5rem; border-bottom: 2px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #2b0c4d 0%, #6b1fa0 100%); color: white; border-radius: 12px 12px 0 0;">
            <div>
                <h2 id="modalStudentName" style="margin: 0 0 0.25rem 0; font-size: 1.5rem;">Student Name</h2>
                <p id="modalStudentId" style="margin: 0; opacity: 0.9; font-size: 0.9rem;">Student ID</p>
            </div>
            <button onclick="closeModal()" style="background: rgba(255,255,255,0.2); border: none; color: white; font-size: 1.5rem; cursor: pointer; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div style="padding: 2rem;">
            <h3 style="color: #2b0c4d; margin-bottom: 1rem; font-size: 1.3rem; display: flex; align-items: center; gap: 0.5rem;">
                Achievements
            </h3>
            <div id="achievementsList">
                <!-- Achievements will be dynamically inserted here -->
            </div>
        </div>
    </div>
</div>

</body>
</html>

<?php
    require "../app/views/templates/general/footer.php";
?>