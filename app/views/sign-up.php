<head>
    <title>Sign Up | UOC Sports E-Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url("/uoc-sports/public/css/global.css");
        @import url("/uoc-sports/public/css/sign-pages.css");
        @import url("/uoc-sports/public/css/general/floating-message.css");
    </style>

</head>

<body class="flex xy-center">
    <section id="sign-form" class="">
        <form action="/uoc-sports/public/sign-up" method="post">
            <img src="/uoc-sports/public/images/uoc-logo.png" alt="UOC Logo" class="logo">
            <h2>Sign Up<br>UOC Sports E-Portal</h2>
            <div class="double-input-div">
                <div class="input-div">
                    <input type="text" name="fname" id="fname-inp" placeholder="First Name" title="Enter your First Name">
                    <div class="error">The name cannot be empty!</div>
                </div>
                <div class="input-div">
                    <input type="text" name="lname" id="lname-inp" placeholder="Last Name" title="Enter your Last Name">
                    <div class="error">The name cannot be empty!</div>
                </div>
            </div>
            <div class="input-div">
                <input type="email" name="email" id="email-inp" placeholder="Email" title="Enter your Email">
                <div class="error">The email is invalid!</div>
            </div>
            <div class="input-div password-wrapper">
                <div class="password-field">
                    <input type="password" name="password" id="password-inp" placeholder="Create a Password" title="Enter your Password">
                    <div class="error">The password is weak!</div>
                    <span class="toggle-password" data-target="password-inp"><i class="fa-solid fa-eye-slash"></i></span>
                </div>
            </div>
            <div class="input-div password-wrapper">
                <div class="password-field">
                    <input type="password" name="confirm-password" id="confirm-password-inp" placeholder="Confirm Password" title="Confirm your Password">
                    <div class="error">Passwords don't match!</div>
                    <span class="toggle-password" data-target="confirm-password-inp"><i class="fa-solid fa-eye-slash"></i></span>
                </div>
            </div>
            <a href="#" id="submit-btn" class="no-dec text-black">Sign Up</a>
            <div id="other-opt">
                <span>Already a member? </span><a href="./sign-in" class="no-dec">Sign In</a><br>
                Or <br>
                <span>Are you a student of UOC? </span><a class="no-dec" href="/uoc-sports/public/student-sign-up">Sign Up as a Student</a>
            </div>
        </form>
    </section>
    <?php
        require '../app/views/templates/general/floating-message.php';
    ?>
    <script src="/uoc-sports/public/js/sign-up-validator.js"></script>
    <script src="/uoc-sports/public/js/hide-password.js"></script>
</body>