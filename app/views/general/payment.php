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
        @import url(/uoc-sports/public/css/ui-notifications.css);

        
    </style>
</head>
<body class="">
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

        function switchTab(tab) {
            // Update Tab Buttons
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            // Update Tab Content
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('.btn-submit');
            const originalBtnText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';
            
            const formData = new FormData(form);
            formData.append('booking_id', '<?php echo $booking['booking_id']; ?>');
            
            fetch('/uoc-sports/public/api/facility/submit-payment-slip', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    UI.showToast(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = '/uoc-sports/public/profile';
                    }, 2000);
                } else {
                    UI.showToast(data.message, 'error');
                    // Reset button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                UI.showToast('An error occurred while submitting. Please try again.', 'error');
                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    </script>
    <script src="/uoc-sports/public/js/ui-notifications.js"></script>
</html>