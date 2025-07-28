<!DOCTYPE html>
<html lang="en">
<head>
	<!--Google Configuration-->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-PMM2LGQ54K"></script>
	<script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-PMM2LGQ54K');
    </script>
	<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1718552974441890"
     crossorigin="anonymous"></script>
	<meta name="google-adsense-account" content="ca-pub-1718552974441890">
    <!--Google Configuration End-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up | WizGen Technologies</title>

    <link rel="stylesheet" href="./styles/wg_ease_styler.css">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="./styles/sign_pages.css">
    
    <style>
        #email_error , #username_error{
            color: #f00;
            display:none;
        }
        #username_error.error{
            display:block;
        }
        #email_error.error{
            display: block;
        }

    </style>

</head>
<body>
    <section id="form_errors">
        <div id="email_valid" class="">
            Please use  a valid email address !
        </div>
        <div id="email_exist" class="">
            Sorry, the email you added has an account signed !
        </div>
        <div id="username_chars" class="">
            Username can only contain alpanumericals and underscore ( a-z (lowercase only) | 0-9 | _ )
        </div>
        <div id="username_count" class="">
            Username must have minimum 6 chars. and maximum 18 chars. !
        </div>
        <div id="username_exist" class="">
            Sorry, the username you added has an account signed !
        </div>
        <div id="password_pattern" class="">
            Password must have minimum 8 chars with at least;<br>
             - One Uppercase AND
             <br>
             - One Lowercase AND
             <br>
             - One Special character AND
             <br>
             - One Number !
        </div>
        <div id="password_match" class="">
            Passwords are not matching correctly !
        </div>
    </section>
    <section id="signer" class="auto">
        <img id="wg_logo" src="./base_images/wizgen_white_logo.webp" alt="WizGen Technologies Logo">
        <span id="sp_sign_up">Sign up</span>
        <span id="sp_tech_com">on our <br> Tech Community</span>

        <form action="./sign_processor" id="sign_form" class="flexer wraper ver-c hor-c" method="post">
            <div class="input_div">
                <input type="email" id="email" name="email" class="form-control" required>
                <label for="email">Email</label>
                <p id="email_error" class="">Email already exists</p>
            </div>
            <div class="input_div">
                <input type="text" id="username" name="username" class="form-control" required>
                <label for="username">Username</label>
                <p id="username_error" class="">Username already exists</p>
            </div>
            <div class="input_div">
                <input type="password" id="password" name="password" class="form-control" required>
                <label for="password">Password</label>
            </div>
            <div class="input_div">
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                <label for="confirm_password">Confirm password</label>
            </div>
            <div class="">
                <button type="submit" id="sign_btn" class="">Sign up</button>
            </div>
        </form>
        
        

        <div id="or_sign">
            OR
        </div>
        <a href="#" class="or_sign_link">
            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 48 48">
                <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path>
            </svg>
            <span>Continue with Google</span>
        </a>
        <a href="#" class="or_sign_link">
            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 64 64">
                <linearGradient id="KpzH_ttTMIjq8dhx1zD2pa_52539_gr1" x1="30.999" x2="30.999" y1="16" y2="55.342" gradientUnits="userSpaceOnUse" spreadMethod="reflect"><stop offset="0" stop-color="#6dc7ff"></stop><stop offset="1" stop-color="#e6abff"></stop></linearGradient><path fill="url(#KpzH_ttTMIjq8dhx1zD2pa_52539_gr1)" d="M25.008,56.007c-0.003-0.368-0.006-1.962-0.009-3.454l-0.003-1.55 c-6.729,0.915-8.358-3.78-8.376-3.83c-0.934-2.368-2.211-3.045-2.266-3.073l-0.124-0.072c-0.463-0.316-1.691-1.157-1.342-2.263 c0.315-0.997,1.536-1.1,2.091-1.082c3.074,0.215,4.63,2.978,4.694,3.095c1.569,2.689,3.964,2.411,5.509,1.844 c0.144-0.688,0.367-1.32,0.659-1.878C20.885,42.865,15.27,40.229,15.27,30.64c0-2.633,0.82-4.96,2.441-6.929 c-0.362-1.206-0.774-3.666,0.446-6.765l0.174-0.442l0.452-0.144c0.416-0.137,2.688-0.624,7.359,2.433 c1.928-0.494,3.969-0.749,6.074-0.759c2.115,0.01,4.158,0.265,6.09,0.759c4.667-3.058,6.934-2.565,7.351-2.433l0.451,0.145 l0.174,0.44c1.225,3.098,0.813,5.559,0.451,6.766c1.618,1.963,2.438,4.291,2.438,6.929c0,9.591-5.621,12.219-10.588,13.087 c0.563,1.065,0.868,2.402,0.868,3.878c0,1.683-0.007,7.204-0.015,8.402l-2-0.014c0.008-1.196,0.015-6.708,0.015-8.389 c0-2.442-0.943-3.522-1.35-3.874l-1.73-1.497l2.274-0.253c5.205-0.578,10.525-2.379,10.525-11.341c0-2.33-0.777-4.361-2.31-6.036 l-0.43-0.469l0.242-0.587c0.166-0.401,0.894-2.442-0.043-5.291c-0.758,0.045-2.568,0.402-5.584,2.447l-0.384,0.259l-0.445-0.123 c-1.863-0.518-3.938-0.796-6.001-0.806c-2.052,0.01-4.124,0.288-5.984,0.806l-0.445,0.123l-0.383-0.259 c-3.019-2.044-4.833-2.404-5.594-2.449c-0.935,2.851-0.206,4.892-0.04,5.293l0.242,0.587l-0.429,0.469 c-1.536,1.681-2.314,3.712-2.314,6.036c0,8.958,5.31,10.77,10.504,11.361l2.252,0.256l-1.708,1.49 c-0.372,0.325-1.03,1.112-1.254,2.727l-0.075,0.549l-0.506,0.227c-1.321,0.592-5.839,2.162-8.548-2.485 c-0.015-0.025-0.544-0.945-1.502-1.557c0.646,0.639,1.433,1.673,2.068,3.287c0.066,0.19,1.357,3.622,7.28,2.339l1.206-0.262 l0.012,3.978c0.003,1.487,0.006,3.076,0.009,3.444L25.008,56.007z"></path><linearGradient id="KpzH_ttTMIjq8dhx1zD2pb_52539_gr2" x1="32" x2="32" y1="5" y2="59.167" gradientUnits="userSpaceOnUse" spreadMethod="reflect"><stop offset="0" stop-color="#1a6dff"></stop><stop offset="1" stop-color="#c822ff"></stop></linearGradient><path fill="url(#KpzH_ttTMIjq8dhx1zD2pb_52539_gr2)" d="M32,58C17.663,58,6,46.337,6,32S17.663,6,32,6s26,11.663,26,26S46.337,58,32,58z M32,8 C18.767,8,8,18.767,8,32s10.767,24,24,24s24-10.767,24-24S45.233,8,32,8z"></path>
            </svg>
            <span>Continue with GitHub</span>    
        </a>
    </section>
    <div id="sign_up_div" class="flexer ver-c hor-c">
        <span>Already a member?</span><a href="./log-in">Log in</a>
    </div>

    <footer style='position: relative; margin-top: 40px;'>
		<div id="links">
			<a href="./about_us" class="">About us</a>
			<a href="./privacy_policy" class="">Privacy policy and Terms of use</a>
			<a href="./contact_us" class="">Contact us</a>
		</div>
		<div id="copyrights" class="flexer ver-c hor-c">
			&copy; 2024 WizGen Tech. All Rights Reserved.
		</div>
	 </footer>

    <script>

        //Variables
        var form = document.getElementById('sign_form');
        var email = document.getElementById('email');
        var username = document.getElementById('username');
        var password = document.getElementById('password');
        var confirm_password = document.getElementById('confirm_password');
        var sign_btn = document.getElementById('sign_btn');

        var email_validated = false;
        var username_validated_1 = false;
        var username_validated_2 = false;
        var password_valid = false;
        var confirm_pass_valid_1 = false;
        var confirm_pass_valid_2 = false;


        //Focus control
        function clickedInput(input_field){
            if (input_field.value.length != 0){
                input_field.parentNode.querySelector('label').classList.add('clicked');
            }
        }
        //+
        // Form Validation

        function validateEmail(email) {
            const re = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            return re.test(email);
        }
        email.addEventListener('input' , function(event){            
            if (validateEmail(email.value)){
                document.getElementById('email_valid').classList.remove('not-valid');
                email_validated = true;
            }
            else{
                document.getElementById('email_valid').classList.add('not-valid');
                email_validated = false;
            }
            clickedInput(email)
        });
        email.addEventListener('input' , function(event){
            if (email_validated === true){
                email.parentNode.classList.add('validated');
            }
            else{
                email.parentNode.classList.remove('validated');
            }
        });



        username.addEventListener('input' , function(event){            
            if ( username.value.length >= 6 && username.value.length <= 18 ){
                document.getElementById('username_count').classList.remove('not-valid');
                username_validated_1 = true;
            }
            else{
                document.getElementById('username_count').classList.add('not-valid');
                username_validated_1 = false;
            }
            clickedInput(username)
        });
        function invalidUsername(str) {
            const re = /[^a-z0-9_]/g;
            return re.test(str);
        }
        username.addEventListener('input' , function(event){            
            if (!invalidUsername(username.value) && username.value.length != 0){
                document.getElementById('username_chars').classList.remove('not-valid');
                username_validated_2 = true;
            }
            else{
                document.getElementById('username_chars').classList.add('not-valid');
                username_validated_2 = false;
            }

            clickedInput(username);
        });
        username.addEventListener('input' , function(event){
            if(username_validated_1 === true && username_validated_2 === true){
                username.parentNode.classList.add('validated');
            }
            else{
                username.parentNode.classList.remove('validated');
            }
        });




        function validatePassword(password) {
            const uppercaseRegex = /[A-Z]/;
            const lowercaseRegex = /[a-z]/;
            const specialCharRegex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;
            const numberRegex = /[0-9]/;
        
            return (
                uppercaseRegex.test(password) &&
                lowercaseRegex.test(password) &&
                specialCharRegex.test(password) &&
                numberRegex.test(password)
            );
        };
        password.addEventListener('input' , function(event){            
            if (validatePassword(password.value) && password.value.length >= 8){
                document.getElementById('password_pattern').classList.remove('not-valid');
                password_valid = true;
            }
            else{
                document.getElementById('password_pattern').classList.add('not-valid');
                password_valid = false;
            }
            clickedInput(password)
        });
        password.addEventListener('input' , function(event){
            if (password_valid === true){
                password.parentNode.classList.add('validated');
            }
            else{
                password.parentNode.classList.remove('validated');
            }
        });




        confirm_password.addEventListener('input' , function(event){            
            if (password.value === confirm_password.value && confirm_password.value.length != 0){
                document.getElementById('password_match').classList.remove('not-valid');
                confirm_pass_valid_1 = true;
                confirm_pass_valid_2 = true;
            }
            else{
                document.getElementById('password_match').classList.add('not-valid');
                confirm_pass_valid_1 = false;
            }
            clickedInput(confirm_password)
        });
        password.addEventListener('input' , function(event){            
            if (password.value === confirm_password.value && confirm_password.value.length != 0){
                document.getElementById('password_match').classList.remove('not-valid');
                confirm_pass_valid_2 = true;
                confirm_pass_valid_1 = true;
            }
            else{
                document.getElementById('password_match').classList.add('not-valid');
                confirm_pass_valid_2 = false;
            }
            clickedInput(confirm_password)
        });
        confirm_password.addEventListener('input' , function(event){
            if (confirm_pass_valid_1 === true && confirm_pass_valid_2 === true){
                confirm_password.parentNode.classList.add('validated');
            }
            else{
                confirm_password.parentNode.classList.remove('validated');
            }
        });
        password.addEventListener('input' , function(event){
            if (confirm_pass_valid_1 === true && confirm_pass_valid_2 === true){
                confirm_password.parentNode.classList.add('validated');
            }
            else{
                confirm_password.parentNode.classList.remove('validated');
            }
        });

        var inputFields = document.querySelectorAll('div.input_div');
        
        inputFields.forEach(function(input) {
            input.addEventListener('input', function(event) {
                var allValid = true;
                inputFields.forEach(function(input) {
                    if (!input.classList.contains('validated')) {
                        allValid = false;
                    }
                });
                if (allValid) {
                    sign_btn.classList.add('ready_submit');
                } else {
                    sign_btn.classList.remove('ready_submit');
                }
            });
        });

        form.addEventListener('submit', function(event) {
            var allValid = true;
            inputFields.forEach(function(input) {
                if (!input.classList.contains('validated')) {
                    allValid = false;
                }
            });
            if (!allValid) {
                event.preventDefault(); 
                inputFields.forEach(function(input){
                    if (!input.classList.contains('validated')){
                        input.classList.add('crazy_submitter');
                    }
                    else{
                        input.classList.remove('crazy_submitter');
                    }
                });
            }
        });


        email.addEventListener('blur', function () {
            var email_val = email.value;
            fetch('email_check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'email=' + email_val
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'exists'){
                    document.getElementById('email_exist').classList.add('not-valid');
                    email.parentNode.classList.remove('validated');
                } else {
                    document.getElementById('email_exist').classList.remove('not-valid');
                }
            })
            .catch(error => console.error('Error:', error));
        });
        
        username.addEventListener('blur', function () {
            var username_val = username.value;
            fetch('check_username', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'user_name=' + username_val
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'exists'){
                    document.getElementById('username_exist').classList.add('not-valid');
                    username.parentNode.classList.remove('validated');
                } else {
                    document.getElementById('username_exist').classList.remove('not-valid');
                }
            })
            .catch(error => console.error('Error:', error));
        });
        
        
        
    </script>
</body>
</html>