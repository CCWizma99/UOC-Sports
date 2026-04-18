<head>
    <title>Forgot Password | UOC Sports E-Portal</title>
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
        
        #forgot-form {
            max-width: 450px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body class="flex xy-center">
    <section id="sign-form" class="">
        <form action="/uoc-sports/public/forgot-password" method="post" id="forgot-password-form">
            <img src="/uoc-sports/public/images/uoc-logo.png" alt="UOC Logo" class="logo">
            <h2>Reset Password</h2>
            
            <p class="instruction-text">
                Enter your email address and we'll send you a link to reset your password.
            </p>
            
            <div class="input-div">
                <input type="email" name="email" id="email-inp" placeholder="Email" title="Enter your Email" required>
                <div class="error">Please enter a valid email!</div>
            </div>
            
            <a href="#" id="submit-btn" class="no-dec text-black" style="margin-top: 10px;">Send Reset Link</a>
            
            <div id="other-opt">
                <a href="/uoc-sports/public/sign-in" class="no-dec"><i class="fa-solid fa-arrow-left"></i> Back to Sign In</a>
            </div>
        </form>
    </section>
    
    <?php
        require '../app/views/templates/general/floating-message.php';
    ?>

    <script>
        document.getElementById('submit-btn').addEventListener('click', function(e) {
            e.preventDefault();
            const emailInp = document.getElementById('email-inp');
            if (emailInp.value.trim() === '' || !emailInp.value.includes('@')) {
                emailInp.parentElement.querySelector('.error').style.display = 'block';
                return;
            }
            document.getElementById('forgot-password-form').submit();
        });
    </script>
</body>
