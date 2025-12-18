<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UOC Teams | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/team-stat.css);
        @import url(/uoc-sports/public/css/admin/search-team.css);
        @import url(/uoc-sports/public/css/admin/team-details.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
    </style>
</head>
<body style="margin-top: 140px;">
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';

// Show team statistics if no sport is selected
if (!isset($_GET['sport_id'])) {
    require '../app/views/templates/admin/team-stat.php';
}

require '../app/views/templates/admin/search-team.php';

// Show team details if sport_id is provided
require '../app/views/templates/admin/team-details.php';

require '../app/views/templates/admin/footer.php';
?>
</body>
<script>
    var currentPage = document.getElementById("sidebar-teams");
    currentPage.classList.add("active") 
</script>
</html>

