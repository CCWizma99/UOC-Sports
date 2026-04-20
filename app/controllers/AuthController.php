<?php
require_once __DIR__ . '/../services/EmailService.php';

class AuthController extends BaseController {
    public function showSignupForm($message = null) {
        view('sign-up', ['message' => $message]);
    }

    public function showSigninForm($message = null) {
        view('sign-in', ['message' => $message]);
    }

    public function showStudentSignupForm($message = null) {
        $email = $_GET['email'] ?? null;
        
        // If no email or not verified, redirect to verify
        if (!$email || !isset($_SESSION['verified_student_email']) || $_SESSION['verified_student_email'] !== $email) {
            header("Location: /uoc-sports/public/student/verify-email");
            exit;
        }

        view('student-sign-up', ['message' => $message, 'email' => $email]);
    }

    public function showStudentEmailVerify() {
        view('student-email-verify');
    }

    public function handleSendOTP() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $email = trim($data['email'] ?? '');

        if (!$email || !str_ends_with(strtolower($email), '.cmb.ac.lk')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid university email domain.']);
            return;
        }

        require_once __DIR__ . '/../models/EmailVerification.php';
        $verifyModel = new EmailVerification();
        $otp = $verifyModel->generateOTP($email);

        if ($otp) {
            $emailService = new EmailService();
            $result = $emailService->sendVerificationOTP($email, $otp);
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to generate verification code.']);
        }
    }

    public function handleVerifyOTP() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $email = trim($data['email'] ?? '');
        $otp = trim($data['otp'] ?? '');

        require_once __DIR__ . '/../models/EmailVerification.php';
        $verifyModel = new EmailVerification();

        if ($verifyModel->verifyOTP($email, $otp)) {
            $_SESSION['verified_student_email'] = $email;
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid or expired code.']);
        }
    }

    public function handleSignup() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new User();

            $data = [
                'fname' => $_POST['fname'] ?? '',
                'lname' => $_POST['lname'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? ''
            ];

            if ($user->create($data)) {
                $emailService = new EmailService();
                $emailService->sendWelcomeEmail($data['email'], $data['fname']);

                $_SESSION['message'] = "Sign Up Successful! Please Sign In.";
                $_SESSION['redirectURL'] = "/uoc-sports/public/sign-in";
                $_SESSION['color'] = "green";
            } else {
                $_SESSION['message'] = 'Something went wrong. Try again.';
            }
        } else {
            $_SESSION['message'] = 'Invalid request.';
        }

        header("Location: /uoc-sports/public/sign-up");
        exit;
    }

    public function handleStudentSignup() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new User();

            $data = [
                'fname' => $_POST['fname'] ?? '',
                'lname' => $_POST['lname'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'student_id' => $_POST['student_id'] ?? '',
                'faculty_id' => $_POST['faculty_id'] ?? ''
            ];

            // CRITICAL: Verify email in session matches submitted email
            if (!isset($_SESSION['verified_student_email']) || $_SESSION['verified_student_email'] !== $data['email']) {
                $_SESSION['message'] = 'Session expired or email mismatch. Please verify your email again.';
                $_SESSION['color'] = 'red';
                header("Location: /uoc-sports/public/student/verify-email");
                exit;
            }

            // Validate Student ID format based on Faculty
            if (!$this->validateStudentId($data['faculty_id'], $data['student_id'])) {
                $_SESSION['message'] = 'Invalid Student ID format for the selected faculty.';
                $_SESSION['color'] = 'red';
                header("Location: /uoc-sports/public/student-sign-up");
                exit;
            }

            // Create student user
            if ($user->createStudent($data)) {

                $emailService = new EmailService();
                $emailService->sendWelcomeEmail($data['email'], $data['fname']);

                $_SESSION['message'] = "Sign Up Successful! Please Sign In.";
                $_SESSION['redirectURL'] = "/uoc-sports/public/sign-in";
                $_SESSION['color'] = "green";
                // Cleanup verification session
                unset($_SESSION['verified_student_email']);
                
                header("Location: /uoc-sports/public/sign-in");
                exit;
            } else {
                $_SESSION['message'] = 'Something went wrong. Try again.';
                $_SESSION['color'] = 'red';
            }
        } else {
            $_SESSION['message'] = 'Invalid request.';
        }

        header("Location: /uoc-sports/public/student-sign-up");
        exit;
    }

    private function validateStudentId($facultyId, $studentId) {
        $patterns = [
            '1' => '/^\d{4}\s*\/\s*(CS|IS)\s*\/\s*\d{3,4}$/i', // UCSC
            '2' => '/^\d{4}\s*\/\s*S\s*\/\s*\d{4}$/i', // Science
            '3' => '/^\d{4}\s*\/\s*A\s*\/\s*\d{4}$/i', // Arts
            '4' => '/^\d{4}\s*\/\s*E\s*\/\s*\d{4}$/i', // Education
            '5' => '/^\d{4}\s*\/\s*IM\s*\/\s*\d{4}$/i', // Indigenous Medicine
            '6' => '/^\d{4}\s*\/\s*L\s*\/\s*\d{4}$/i', // Law
            '7' => '/^\d{4}\s*\/\s*BA\s*\/\s*\d{4}$/i', // Management & Finance
            '8' => '/^\d{4}\s*\/\s*M\s*\/\s*\d{4}$/i', // Medicine
            '9' => '/^\d{4}\s*\/\s*N\s*\/\s*\d{4}$/i', // Nursing
            '10' => '/^\d{4}\s*\/\s*T\s*\/\s*\d{4}$/i' // Technology
        ];

        if (!isset($patterns[$facultyId])) {
            return true; 
        }

        return preg_match($patterns[$facultyId], $studentId);
    }

    public function handleSignin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']); // Checkbox
    
            $user = new User();
            $foundUser = $user->findByEmail($email); // Make sure you have this method
    
            if ($foundUser && password_verify($password, $foundUser['password'])) {
                // Update last login time
                $user->updateLastLogin($foundUser['user_id']);
                
                // Store user session
                $_SESSION['user_id'] = $foundUser['user_id'];
                $_SESSION['user_name'] = $foundUser['fname'];
                $_SESSION['user_type'] = $foundUser['type'];
                $_SESSION['sport_id'] = $foundUser['sport_id'] ?? null;
                $_SESSION['faculty_id'] = $foundUser['faculty_id'] ?? null;
                $_SESSION['color'] = "green";
                $_SESSION['message'] = "Welcome back, " . htmlspecialchars($foundUser['fname']) . "!";
    
                // Redirect based on type
                switch ($foundUser['type']) {
                    case "ADMIN":
                        $_SESSION['redirectURL'] = "/uoc-sports/public/admin-index";
                        break;
                    case "EXECUTIVE":
                        $_SESSION['redirectURL'] = "/uoc-sports/public/executive-dashboard";
                        break;
                    case "REG":
                        $_SESSION['redirectURL'] = "/uoc-sports/public/registrar";
                        break;
                    case "SPT":
                        $_SESSION['redirectURL'] = "/uoc-sports/public/sport-manager";
                        break;
                    case "EQP":
                        $_SESSION['redirectURL'] = "/uoc-sports/public/equipment-manager";
                        break;
                    case "STUDENT":
                        $_SESSION['redirectURL'] = "/uoc-sports/public/student";
                        break;
                    case "CAPTAIN":
                        $_SESSION['redirectURL'] = "/uoc-sports/public/captain";
                        break;
                    case "COACH":
                        $_SESSION['redirectURL'] = "/uoc-sports/public/coach";
                        break;
                    default:
                        $_SESSION['redirectURL'] = "/uoc-sports/public/facility-reservation";
                }
    
                if ($remember) {
                    $token = bin2hex(random_bytes(32)); // Secure random token
                    $expiry = time() + (86400 * 30); // 30 days
    
                    // Set a secure cookie
                    setcookie("remember_token", $token, [
                        'expires' => $expiry,
                        'path' => '/',
                        'secure' => true,  // Use HTTPS
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]);
    
                    // Save token in DB (user_id + token + expiry)
                    $user->storeRememberToken($foundUser['user_id'], $token, $expiry);
                }
    
            } else {
                $_SESSION['message'] = "Invalid email or password.";
                $_SESSION['redirectURL'] = "/uoc-sports/public/sign-in";
                $_SESSION['color'] = "red";
            }
        } else {
            $_SESSION['message'] = "Invalid request.";
            $_SESSION['redirectURL'] = "/uoc-sports/public/sign-in";
            $_SESSION['color'] = "red";
        }
    
        header("Location: " . $_SESSION['redirectURL']);
        exit;
    }  
    
    public function addUser() {
        header('Content-Type: application/json');
    
        try {
            $input = json_decode(file_get_contents('php://input'), true);
    
            $fname = trim($input['fname'] ?? '');
            $lname = trim($input['lname'] ?? '');
            $email = trim($input['email'] ?? '');
            $type = trim($input['type'] ?? '');
            $phone = trim($input['phone'] ?? '');
            $sport = $input['sport'] ?? null;
            $faculty = $input['faculty'] ?? null;
    
            if (empty($fname) || empty($lname) || empty($email) || empty($type)) {
                echo json_encode(['status' => 'error', 'message' => 'All required fields must be filled.']);
                return;
            }
    
            $tempPass = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 8);
            $userModel = new User();
            $userId = $userModel->addUser($fname, $lname, $email, $type, $phone, $sport, $faculty, $tempPass, 1);
    
            $emailService = new EmailService();
            $emailResult = $emailService->sendTempPasswordEmail($email, "$fname $lname", $tempPass);
    
            if ($emailResult['status'] === 'success') {
                echo json_encode(['status' => 'success', 'message' => 'User added and email sent successfully.', 'user_id' => $userId]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'User added but email failed: ' . $emailResult['message'], 'user_id' => $userId]);
            }
    
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function showForgotPassword() {
        view('forgot-password');
    }

    public function handleForgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /uoc-sports/public/forgot-password");
            exit;
        }

        $email = $_POST['email'] ?? '';
        if (empty($email)) {
            $_SESSION['message'] = "Please enter your email address.";
            $_SESSION['color'] = "red";
            header("Location: /uoc-sports/public/forgot-password");
            exit;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            if ($userModel->createPasswordReset($email, $token)) {
                $emailService = new EmailService();
                $emailService->sendPasswordResetEmail($email, $user['fname'], $token);
            }
        }

        // Always show success message for security (don't reveal if user exists)
        $_SESSION['message'] = "If an account exists for $email, you will receive a password reset link shortly.";
        $_SESSION['color'] = "green";
        header("Location: /uoc-sports/public/forgot-password");
        exit;
    }

    public function showResetPassword() {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            $_SESSION['message'] = "Invalid or expired reset link.";
            $_SESSION['color'] = "red";
            header("Location: /uoc-sports/public/sign-in");
            exit;
        }

        $userModel = new User();
        $resetRequest = $userModel->validatePasswordReset($token);

        if (!$resetRequest) {
            $_SESSION['message'] = "Invalid or expired reset link.";
            $_SESSION['color'] = "red";
            header("Location: /uoc-sports/public/sign-in");
            exit;
        }

        view('reset-password', ['token' => $token]);
    }

    public function handleResetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /uoc-sports/public/sign-in");
            exit;
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token) || empty($password)) {
            $_SESSION['message'] = "All fields are required.";
            $_SESSION['color'] = "red";
            header("Location: /uoc-sports/public/reset-password?token=$token");
            exit;
        }

        if ($password !== $confirmPassword) {
            $_SESSION['message'] = "Passwords do not match.";
            $_SESSION['color'] = "red";
            header("Location: /uoc-sports/public/reset-password?token=$token");
            exit;
        }

        $userModel = new User();
        $resetRequest = $userModel->validatePasswordReset($token);

        if (!$resetRequest) {
            $_SESSION['message'] = "Invalid or expired reset link.";
            $_SESSION['color'] = "red";
            header("Location: /uoc-sports/public/sign-in");
            exit;
        }

        if ($userModel->resetPassword($resetRequest['user_id'], $password)) {
            $_SESSION['message'] = "Password reset successful! You can now sign in.";
            $_SESSION['color'] = "green";
            header("Location: /uoc-sports/public/sign-in");
        } else {
            $_SESSION['message'] = "Failed to reset password. Please try again.";
            $_SESSION['color'] = "red";
            header("Location: /uoc-sports/public/reset-password?token=$token");
        }
        exit;
    }

    public function handleLogout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Clear all session variables
        $_SESSION = [];
        
        // Delete the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy the session
        session_destroy();
        
        // Clear remember me cookie if exists
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        // Redirect to sign-in
        header("Location: /uoc-sports/public/sign-in");
        exit;
    }
}
