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
            <h2>Sign Up<br>UOC Sports E-Portal</h2>
            <div class="double-input-div">
                <div class="input-div">
                    <label for="fname-inp">Enter Your First Name</label>
                    <input type="text" name="fname" id="fname-inp" title="Enter your First Name">
                    <div class="error">The name cannot be empty!</div>
                </div>
                <div class="input-div">
                    <label for="lname-inp">Enter Your Last Name</label>
                    <input type="text" name="lname" id="lname-inp" title="Enter your Last Name">
                    <div class="error">The name cannot be empty!</div>
                </div>
            </div>
            <div class="input-div">
                <label for="email-inp">Enter Your Email</label>
                <input type="email" name="email" id="email-inp" title="Enter your Email">
                <div class="error">The email is invalid!</div>
            </div>
            <div class="input-div password-wrapper">
                <label for="password-inp">Create a Password</label>
                <div class="password-field">
                    <input type="password" name="password" id="password-inp" title="Enter your Password">
                    <div class="error">The password is weak!</div>
                    <span class="toggle-password" data-target="password-inp"><i class="fa-solid fa-eye-slash"></i></span>
                </div>
            </div>
            <div class="input-div password-wrapper">
                <label for="confirm-password-inp">Confirm Password</label>
                <div class="password-field">
                    <input type="password" name="confirm-password" id="confirm-password-inp" title="Confirm your Password">
                    <div class="error">Passwords don't match!</div>
                    <span class="toggle-password" data-target="password-inp"><i class="fa-solid fa-eye-slash"></i></span>
                </div>
            </div>
            <a href="#" id="submit-btn" class="no-dec text-black">Sign Up</a>
            <div id="other-opt">
                <span>Already a member? </span><a href="./sign-in" class="no-dec">Sign In</a><br>
                Or <br>
                <span>Are you a student of UOC? </span><a class="no-dec" href="#">Sign In as a Student</a>
            </div>
        </form>
    </section>
    <?php
        require '../app/views/templates/general/floating-message.php';
    ?>
    <script src="/uoc-sports/public/js/sign-up-validator.js"></script>
    <script src="/uoc-sports/public/js/hide-password.js"></script>
</body>