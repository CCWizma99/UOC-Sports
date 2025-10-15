<?php

class AuthController extends BaseController {
    public function showSignupForm($message = null) {
        view('sign-up', ['message' => $message]);
    }

    public function showSigninForm($message = null) {
        view('sign-in', ['message' => $message]);
    }

    public function showStudentSignupForm($message = null) {
        view('student-sign-up', ['message' => $message]);
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
                'student_id' => $POST['student_id'] ?? '',
                'faculty_id' => $POST['faculty_id'] ?? ''
            ];

            if ($user->createStudent($data)) {
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
                // Store user session
                $_SESSION['user_id'] = $foundUser['user_id'];
                $_SESSION['user_name'] = $foundUser['fname'];
                $_SESSION['color'] = "green";
                $_SESSION['message'] = "Welcome back, " . htmlspecialchars($foundUser['fname']) . "!";
    
                // Redirect based on type
                switch ($foundUser['type']) {
                    case "ADMIN":
                        $_SESSION['redirectURL'] = "/uoc-sports/public/admin-index";
                        break;
                    default:
                        $_SESSION['redirectURL'] = "/uoc-sports/public/";
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
        $smtpKey = "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";
        $mainEmail = "chamalchamuditha1231@gmail.com";
        header('Content-Type: application/json');
    
        try {
            // Get POST data
            $input = json_decode(file_get_contents('php://input'), true);
    
            $fname = trim($input['fname'] ?? '');
            $lname = trim($input['lname'] ?? '');
            $email = trim($input['email'] ?? '');
            $type = trim($input['type'] ?? '');
            $phone = trim($input['phone'] ?? '');
            $sport = $input['sport'] ?? null;
            $faculty = $input['faculty'] ?? null;
    
            // Validation
            if (empty($fname) || empty($lname) || empty($email) || empty($type)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'All required fields must be filled.'
                ]);
                return;
            }
    
            // Generate temporary password
            $tempPass = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 8);
    
            // Add user
            $userModel = new User();
            $shouldChange = 1;
            $userId = $userModel->addUser($fname, $lname, $email, $type, $phone, $sport, $faculty, $tempPass, $shouldChange);
    
            // --- Brevo Config ---
            $apiKey = $smtpKey;   // from config
            $senderEmail = $mainEmail; // from config
    
            if (empty($apiKey) || empty($senderEmail)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'User added but email not sent: Brevo API key or sender email missing.',
                    'user_id' => $userId
                ]);
                return;
            }
    
            // --- Prepare email payload ---
            $payload = [
                "sender" => [
                    "name" => "UOC Sports System",
                    "email" => $senderEmail
                ],
                "to" => [
                    [
                        "email" => $email,
                        "name" => "$fname $lname"
                    ]
                ],
                "subject" => "Your Temporary Password - UOC Sports System",
                "htmlContent" => "
                    <html>
                        <body style='font-family: Arial, sans-serif; color: #333;'>
                            <h3>Hello $fname,</h3>
                            <p>Your temporary password is:</p>
                            <p style='font-size:18px; font-weight:bold; color:#007bff;'>$tempPass</p>
                            <p>Please change it after your first login.</p>
                            <hr>
                            <small>This is an automated email from the UOC Sports System.</small>
                        </body>
                    </html>
                "
            ];
    
            // --- Send email via Brevo ---
            $ch = curl_init('https://api.brevo.com/v3/smtp/email');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json',
                'api-key: ' . $apiKey
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    
            $response = curl_exec($ch);
            $error = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
    
            if ($error) {
                echo json_encode([
                    'status' => 'error',
                    'message' => "User added but email not sent (cURL error: $error)",
                    'user_id' => $userId
                ]);
                return;
            }
    
            $respData = json_decode($response, true);
    
            // --- Handle response ---
            if ($httpCode >= 200 && $httpCode < 300 && isset($respData['messageId'])) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'User added and email sent successfully.',
                    'user_id' => $userId
                ]);
            } else {
                $errorMsg = $respData['message'] ?? 'Unknown Brevo error';
                echo json_encode([
                    'status' => 'error',
                    'message' => "User added but email not sent. Brevo error: $errorMsg",
                    'user_id' => $userId
                ]);
            }
    
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }    
}
