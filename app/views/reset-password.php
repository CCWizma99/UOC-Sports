<head>
    <title>Reset Password | UOC Sports E-Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url("/uoc-sports/public/css/global.css");
        @import url("/uoc-sports/public/css/sign-pages.css");
        @import url("/uoc-sports/public/css/general/floating-message.css");
        
        .instruction-text {
            color: #666;
            font-size: 14px;
            margin: 15px 0 25px;
            line-height: 1.5;
        }
    </style>
</head>

<body class="flex xy-center">
    <section id="sign-form" class="">
        <form action="/uoc-sports/public/reset-password" method="post" id="reset-password-form">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            
            <img src="/uoc-sports/public/images/uoc-logo.png" alt="UOC Logo" class="logo">
            <h2>Set New Password</h2>
            
            <p class="instruction-text">
                Please enter a strong new password for your account.
            </p>
            
            <div class="input-div password-wrapper">
                <div class="password-field">
                    <input type="password" name="password" id="password-inp" placeholder="New Password" title="Enter new password" required>
                    <div class="error">Password cannot be empty!</div>
                    <span class="toggle-password" data-target="password-inp"><i class="fa-solid fa-eye-slash"></i></span>
                </div>
            </div>

            <div class="input-div password-wrapper">
                <div class="password-field">
                    <input type="password" name="confirm_password" id="confirm-password-inp" placeholder="Confirm New Password" title="Confirm new password" required>
                    <div class="error" id="confirm-error">Passwords do not match!</div>
                    <span class="toggle-password" data-target="confirm-password-inp"><i class="fa-solid fa-eye-slash"></i></span>
                </div>
            </div>
            
            <a href="#" id="submit-btn" class="no-dec text-black" style="margin-top: 10px;">Update Password</a>
            
            <div id="other-opt">
                <a href="/uoc-sports/public/sign-in" class="no-dec">Back to Sign In</a>
            </div>
        </form>
    </section>
    
    <?php
        require '../app/views/templates/general/floating-message.php';
    ?>

    <script src="/uoc-sports/public/js/hide-password.js"></script>
    <script>
        document.getElementById('submit-btn').addEventListener('click', function(e) {
            e.preventDefault();
            const pass = document.getElementById('password-inp').value;
            const confirm = document.getElementById('confirm-password-inp').value;
            const confirmError = document.getElementById('confirm-error');
            
            if (pass.trim() === '') {
                document.getElementById('password-inp').parentElement.querySelector('.error').style.display = 'block';
                return;
            }
            
            if (pass !== confirm) {
                confirmError.style.display = 'block';
                return;
            }
            
            document.getElementById('reset-password-form').submit();
        });
    </script>
</body>
