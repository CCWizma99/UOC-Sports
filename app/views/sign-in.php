<head>
    <title>Sign In | UOC Sports E-Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url("/uoc-sports/public/css/global.css");
        @import url("/uoc-sports/public/css/sign-pages.css");
        @import url("/uoc-sports/public/css/general/floating-message.css");
    </style>
</head>

<body class="flex xy-center">
    <section id="sign-form" class="">
        <form action="/uoc-sports/public/sign-in" method="post">
            <h2>Sign In<br>UOC Sports E-Portal</h2>
            
            <div class="input-div">
                <label for="email-inp">Enter Your Email</label>
                <input type="email" name="email" id="email-inp" title="Enter your Email">
                <div class="error">The email is invalid!</div>
            </div>
            
            <div class="input-div password-wrapper">
                <label for="password-inp">Enter Your Password</label>
                <div class="password-field">
                    <input type="password" name="password" id="password-inp" title="Enter your Password">
                    <div class="error">Password cannot be empty!</div>
                    <span class="toggle-password" data-target="password-inp"><i class="fa-solid fa-eye-slash"></i></span>
                </div>
            </div>

            <div class="remember">
                <div>
                    Remember Me
                </div>
                <div>
                    <input type="checkbox" name="remember" id="">
                </div>
            </div>
            
            <a href="#" id="submit-btn" class="no-dec text-black">Sign In</a>
            
            <div id="other-opt">
                <span>Don't have an account? </span><a href="/uoc-sports/public/sign-up" class="no-dec">Sign Up</a><br>
                Or <br>
                <span>Are you a student of UOC? </span><a class="no-dec" href="/uoc-sports/public/student-sign-up">Sign Up as a Student</a>
            </div>
        </form>
    </section>
    
    <?php
        require '../app/views/templates/general/floating-message.php';
    ?>

    <script src="/uoc-sports/public/js/sign-in-validator.js"></script>
    <script src="/uoc-sports/public/js/hide-password.js"></script>
</body>
