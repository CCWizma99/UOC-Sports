<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support | UOC Sports E-Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/general/header.css);
        @import url(/uoc-sports/public/css/general/footer.css);

        .support-wrapper {
            background: linear-gradient(135deg, #faf9fc 0%, #f3f1f7 100%);
            min-height: 100vh;
            padding-top: 2rem;
        }
    </style>
</head>
<body class="support-wrapper">
    <?php
        require '../app/views/templates/general/header.php';
        require '../app/views/templates/general/support-page.php';
        require '../app/views/templates/general/footer.php';
    ?>
</body>
</html>
