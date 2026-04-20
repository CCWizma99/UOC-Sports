<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification | UOC Sports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />
    <style>
        @import url("/uoc-sports/public/css/global.css");
        @import url("/uoc-sports/public/css/sign-pages.css");
        
        #verification-container {
            max-width: 450px;
            width: 100%;
            padding: 2.5rem;
            background: white;
            border-radius: 2rem;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
            text-align: center;
        }

        .step-container { display: none; }
        .step-container.active { display: block; animation: fadeIn 0.5s ease; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background: #f3efff;
            color: #5e2d91;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
        }

        h2 { margin-bottom: 0.5rem; color: #111; }
        p { color: #666; margin-bottom: 2rem; font-size: 0.95rem; line-height: 1.5; }

        .form-group { text-align: left; margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151; }
        
        input[type="email"], input[type="text"] {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
            box-sizing: border-box;
        }

        input:focus {
            border-color: #5e2d91;
            box-shadow: 0 0 0 4px rgba(94, 45, 145, 0.1);
            outline: none;
        }

        .otp-input-wrapper {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .otp-digit {
            width: 45px;
            height: 55px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            background: #f9fafb;
        }

        .btn-verify {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #5e2d91 0%, #8b5cf6 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, filter 0.2s;
            box-shadow: 0 10px 20px rgba(94, 45, 145, 0.2);
        }

        .btn-verify:hover { transform: translateY(-2px); filter: brightness(1.1); }
        .btn-verify:disabled { background: #9ca3af; cursor: not-allowed; transform: none; }

        .timer-text {
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: #666;
        }

        #timer { font-weight: 700; color: #dc2626; }

        .error-msg {
            color: #dc2626;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: none;
        }

        .resend-link {
            color: #5e2d91;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }

        .resend-link.disabled { color: #9ca3af; cursor: not-allowed; }
    </style>
</head>
<body class="flex xy-center" style="background: #f5f3ff; min-height: 100vh;">
    
    <div id="verification-container">
        <!-- Step 1: Email Entry -->
        <div id="step-email" class="step-container active">
            <div class="icon-circle"><i class="fas fa-envelope-open-text"></i></div>
            <h2>Student Verification</h2>
            <p>Please enter your official University email address to receive a verification code.</p>
            
            <div class="form-group">
                <label for="email-input">University Email</label>
                <input type="email" id="email-input" placeholder="example@sci.cmb.ac.lk" required>
                <div id="email-error" class="error-msg">Please enter a valid university email (.cmb.ac.lk)</div>
            </div>

            <button id="btn-send-otp" class="btn-verify">Send Verification Code</button>
            
            <div style="margin-top: 1.5rem; font-size: 0.85rem; color: #666;">
                Back to <a href="/uoc-sports/public/student-sign-up" class="resend-link">Sign Up</a>
            </div>
        </div>

        <!-- Step 2: OTP Entry -->
        <div id="step-otp" class="step-container">
            <div class="icon-circle"><i class="fas fa-key"></i></div>
            <h2>Enter Code</h2>
            <p>We've sent a 6-digit verification code to <br><strong id="display-email"></strong></p>
            
            <div class="otp-input-wrapper">
                <input type="text" class="otp-digit" maxlength="1" pattern="\d*">
                <input type="text" class="otp-digit" maxlength="1" pattern="\d*">
                <input type="text" class="otp-digit" maxlength="1" pattern="\d*">
                <input type="text" class="otp-digit" maxlength="1" pattern="\d*">
                <input type="text" class="otp-digit" maxlength="1" pattern="\d*">
                <input type="text" class="otp-digit" maxlength="1" pattern="\d*">
            </div>
            <div id="otp-error" class="error-msg" style="text-align: center; margin-bottom: 1rem;">Invalid or expired code.</div>

            <button id="btn-verify-otp" class="btn-verify">Verify & Continue</button>
            
            <div class="timer-text">
                Code expires in: <span id="timer">02:00</span>
            </div>
            <div style="margin-top: 1rem; font-size: 0.85rem;">
                Didn't receive it? <span id="resend-btn" class="resend-link disabled">Resend in 60s</span>
            </div>
        </div>
    </div>

    <script src="/uoc-sports/public/js/general/floating-message.js"></script>
    <script>
        const stepEmail = document.getElementById('step-email');
        const stepOtp = document.getElementById('step-otp');
        const emailInput = document.getElementById('email-input');
        const emailError = document.getElementById('email-error');
        const btnSendOtp = document.getElementById('btn-send-otp');
        const btnVerifyOtp = document.getElementById('btn-verify-otp');
        const displayEmail = document.getElementById('display-email');
        const timerDisplay = document.getElementById('timer');
        const resendBtn = document.getElementById('resend-btn');
        const otpDigits = document.querySelectorAll('.otp-digit');

        let timerInterval;
        let resendCooldown = 60;
        let cooldownInterval;

        // Domain validation
        function isValidDomain(email) {
            return email.toLowerCase().endsWith('.cmb.ac.lk');
        }

        btnSendOtp.addEventListener('click', async () => {
            const email = emailInput.value.trim();
            if (!isValidDomain(email)) {
                emailError.style.display = 'block';
                return;
            }
            emailError.style.display = 'none';
            btnSendOtp.disabled = true;
            btnSendOtp.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

            try {
                const response = await fetch('/uoc-sports/public/student/send-otp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });
                const result = await response.json();

                if (result.status === 'success') {
                    displayEmail.textContent = email;
                    stepEmail.classList.remove('active');
                    stepOtp.classList.add('active');
                    startTimer(120); // 2 minutes
                    startResendCooldown();
                    UI.showToast('Verification code sent to ' + email, 'success');
                } else {
                    UI.showToast(result.message || 'Failed to send OTP. Please try again.', 'error');
                    btnSendOtp.disabled = false;
                    btnSendOtp.textContent = 'Send Verification Code';
                }
            } catch (err) {
                console.error(err);
                btnSendOtp.disabled = false;
                btnSendOtp.textContent = 'Send Verification Code';
            }
        });

        // OTP Input Handling
        otpDigits.forEach((digit, index) => {
            digit.addEventListener('input', (e) => {
                if (e.target.value.length > 0 && index < otpDigits.length - 1) {
                    otpDigits[index + 1].focus();
                }
            });

            digit.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                    otpDigits[index - 1].focus();
                }
            });
        });

        btnVerifyOtp.addEventListener('click', async () => {
            const otpCode = Array.from(otpDigits).map(d => d.value).join('');
            if (otpCode.length < 6) return;

            btnVerifyOtp.disabled = true;
            btnVerifyOtp.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';

            try {
                const response = await fetch('/uoc-sports/public/student/verify-otp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        email: emailInput.value.trim(),
                        otp: otpCode 
                    })
                });
                const result = await response.json();

                if (result.status === 'success') {
                    window.location.href = '/uoc-sports/public/student-sign-up?email=' + encodeURIComponent(emailInput.value.trim());
                } else {
                    document.getElementById('otp-error').style.display = 'block';
                    btnVerifyOtp.disabled = false;
                    btnVerifyOtp.textContent = 'Verify & Continue';
                }
            } catch (err) {
                console.error(err);
                btnVerifyOtp.disabled = false;
                btnVerifyOtp.textContent = 'Verify & Continue';
            }
        });

        function startTimer(duration) {
            let timer = duration;
            clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                let minutes = Math.floor(timer / 60);
                let seconds = timer % 60;
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;
                timerDisplay.textContent = minutes + ":" + seconds;

                if (--timer < 0) {
                    clearInterval(timerInterval);
                    timerDisplay.textContent = "Expired";
                    btnVerifyOtp.disabled = true;
                }
            }, 1000);
        }

        function startResendCooldown() {
            resendCooldown = 60;
            resendBtn.classList.add('disabled');
            clearInterval(cooldownInterval);
            cooldownInterval = setInterval(() => {
                resendCooldown--;
                if (resendCooldown <= 0) {
                    clearInterval(cooldownInterval);
                    resendBtn.textContent = 'Resend Code';
                    resendBtn.classList.remove('disabled');
                    resendBtn.style.cursor = 'pointer';
                } else {
                    resendBtn.textContent = `Resend in ${resendCooldown}s`;
                }
            }, 1000);
        }

        resendBtn.addEventListener('click', () => {
            if (!resendBtn.classList.contains('disabled')) {
                btnSendOtp.click();
            }
        });
    </script>
</body>
</html>
