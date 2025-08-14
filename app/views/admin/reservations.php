<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/quick-bar.css);
        @import url(/uoc-sports/public/css/admin/user-stat.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
        #link-bar{
            width: calc(100vw - 488px);
        }
    </style>
</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';
require '../app/views/templates/admin/quick-bar.php';
require '../app/views/templates/admin/reservations-status.php';
require '../app/views/templates/admin/footer.php';
?>
</body>
<script>
    var currentPage = document.getElementById("sidebar-reservations");
    currentPage.classList.add("active") 
</script>
<script src="/uoc-sports/public/js/user-stat.js"></script>
</html>

