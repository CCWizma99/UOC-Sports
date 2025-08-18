<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/admin-calendar.css);
        @import url(/uoc-sports/public/css/admin/budget.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/quick-bar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);

        #link-bar{
            width: calc(100vw - 488px);
        }
    </style>

</head>
<body>
<?php 
$title = "Home";
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/admin-calendar.php';
require 'templates/admin/budget.php';
require '../app/views/templates/admin/sidebar.php';
require '../app/views/templates/admin/quick-bar.php';
require '../app/views/templates/admin/footer.php';
?>
</body>
</html>

