<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile | UOC Sports E-Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/general/header.css);
        @import url(/uoc-sports/public/css/student/student-profile.css);
        @import url(/uoc-sports/public/css/general/footer.css);

    </style>
</head>
<body class="mesh-sporty">
    <?php
        require '../app/views/templates/general/header.php';
        require '../app/views/templates/student/view-profile.php';
        require '../app/views/templates/general/footer.php';
    ?>
</body>

<script src="/uoc-sports/public/js/student/student-profile.js"></script>

<script>
    var currentPage = document.getElementById("user_type");
    currentPage.classList.add("active") 
</script>
</html>