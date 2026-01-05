<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment | UOC Sports E-Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/general/header.css);
        @import url(/uoc-sports/public/css/general/payment.css);
        @import url(/uoc-sports/public/css/general/footer.css);

        .mesh-sporty {
            background: 
                linear-gradient(rgba(94, 45, 145, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(94, 45, 145, 0.05) 1px, transparent 1px),
                linear-gradient(135deg, #faf9fc 0%, #f3f1f7 100%);
            background-size: 40px 40px, 40px 40px, 100% 100%;
        }
    </style>
</head>
<body class="mesh-sporty">
    <?php
        require '../app/views/templates/general/header.php';
        require '../app/views/templates/general/payment.php';
        require '../app/views/templates/general/footer.php';
    ?>
</body>
<script>
       const fileInput = document.getElementById('paymentSlip');
        const fileNameDisplay = document.getElementById('fileName');
        const form = document.getElementById('paymentForm');

        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                fileNameDisplay.textContent = '✓ ' + e.target.files[0].name;
            } else {
                fileNameDisplay.textContent = '';
            }
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Here you would send the form data to your PHP backend
            const formData = new FormData(form);
            
            // Simulate form submission
            alert('Payment proof submitted successfully! You will receive a confirmation email once verified by admin.');
            
            // In production, connect to PHP backend:
            // fetch('submit_payment.php', {
            //     method: 'POST',
            //     body: formData
            // })
            // .then(response => response.json())
            // .then(data => {
            //     if(data.success) {
            //         alert('Payment proof submitted successfully!');
            //         window.location.href = 'confirmation.php';
            //     }
            // });
        });
</script>
</html>