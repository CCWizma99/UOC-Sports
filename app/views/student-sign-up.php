<head>
    <title>Student Sign Up | UOC Sports E-Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url("/uoc-sports/public/css/global.css");
        @import url("/uoc-sports/public/css/sign-pages.css");
        @import url("/uoc-sports/public/css/general/floating-message.css");
    </style>
</head>

<body class="flex xy-center">
    <section id="sign-form">
        <form action="/uoc-sports/public/sign-up-student" method="post" enctype="multipart/form-data">
            <img src="/uoc-sports/public/images/uoc-logo.png" alt="UOC Logo" class="logo">
            <h2>Student Sign Up<br>UOC Sports E-Portal</h2>

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
                <input type="text" name="student_id" id="student-id-inp" placeholder="Student ID Number" title="Enter your Student ID">
                <div class="error">Invalid Student ID!</div>
            </div>

            <div class="input-div">
                <select name="faculty_id" id="faculty-inp" title="Select your Faculty">
                    <option value="none">-- Select Faculty --</option>
                </select>
                <div class="error">Faculty cannot be empty!</div>
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

            <div class="input-div file-upload-div">
                <label for="id-card-inp" class="file-label">
                    <i class="fa-solid fa-id-card"></i>
                    <span id="file-name">Upload Student ID Card Image</span>
                </label>
                <input type="file" name="id_card" id="id-card-inp" accept="image/jpeg,image/png,image/jpg" style="display:none">
                <div class="error">Please upload your student ID card image!</div>
            </div>

            <a href="#" id="submit-btn" class="no-dec text-black">Sign Up</a>

            <div id="other-opt">
                <span>Already a member? </span><a href="./sign-in" class="no-dec">Sign In</a><br>
                Or <br>
                <span>Want to sign up as a regular user? </span><a class="no-dec" href="/uoc-sports/public/sign-up">Sign Up as User</a>
            </div>
        </form>
    </section>

    <?php
        require '../app/views/templates/general/floating-message.php';
    ?>
    <script src="/uoc-sports/public/js/sign-up-validator.js"></script>
    <script src="/uoc-sports/public/js/hide-password.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            const facultySelect = document.getElementById('faculty-inp');

            if (facultySelect) {
                try {
                    const res = await fetch('/uoc-sports/public/get-faculties');
                    const data = await res.json();

                    if (data.status === 'success' && Array.isArray(data.faculties)) {
                        data.faculties.forEach(f => {
                            const option = document.createElement('option');
                            option.value = f.faculty_id;
                            option.textContent = f.faculty_name;
                            facultySelect.appendChild(option);
                        });
                    } else {
                        console.error('Failed to load faculties.');
                    }
                } catch (err) {
                    console.error('Error fetching faculties:', err);
                }
            }

            // File upload handling
            const fileInput = document.getElementById('id-card-inp');
            const fileLabel = document.querySelector('.file-label');
            const fileNameSpan = document.getElementById('file-name');

            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        const maxSize = 5 * 1024 * 1024; // 5MB

                        if (file.size > maxSize) {
                            alert('File size must be less than 5MB');
                            this.value = '';
                            fileNameSpan.textContent = 'Upload Student ID Card Image';
                            fileLabel.classList.remove('has-file');
                            return;
                        }

                        fileNameSpan.textContent = file.name;
                        fileLabel.classList.add('has-file');
                        
                        // Hide error if file selected
                        const errorDiv = this.parentElement.querySelector('.error');
                        if (errorDiv) errorDiv.style.display = 'none';
                    } else {
                        fileNameSpan.textContent = 'Upload Student ID Card Image';
                        fileLabel.classList.remove('has-file');
                    }
                });
            }
        });
    </script>
</body>
