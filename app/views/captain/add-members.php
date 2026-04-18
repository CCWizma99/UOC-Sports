<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Members | UOC Sports E-Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/general/header.css);
        @import url(/uoc-sports/public/css/captain/add-members.css);
        @import url(/uoc-sports/public/css/general/footer.css);

        
        .active{
            border: none !important;
            font-weight: 300 !important;
        }
    </style>
</head>
<body class="">
    <?php
        require '../app/views/templates/general/header.php';
        require '../app/views/templates/captain/add-memberssheet.php';
        require '../app/views/templates/general/footer.php';
    ?>
</body>
</html>