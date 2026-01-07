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
                'student_id' => $_POST['student_id'] ?? '',
                'faculty_id' => $_POST['faculty_id'] ?? ''
            ];

            // Handle file upload
            $idCardImage = null;
            if (isset($_FILES['id_card']) && $_FILES['id_card']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../app/student_id/';
                
                // Create directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Generate unique filename
                $ext = pathinfo($_FILES['id_card']['name'], PATHINFO_EXTENSION);
                $idCardImage = $data['student_id'] . '_' . time() . '.' . $ext;
                $targetPath = $uploadDir . $idCardImage;

                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!in_array($_FILES['id_card']['type'], $allowedTypes)) {
                    $_SESSION['message'] = 'Invalid file type. Please upload JPG or PNG image.';
                    $_SESSION['color'] = 'red';
                    header("Location: /uoc-sports/public/student-sign-up");
                    exit;
                }

                // Validate file size (5MB max)
                if ($_FILES['id_card']['size'] > 5 * 1024 * 1024) {
                    $_SESSION['message'] = 'File too large. Maximum size is 5MB.';
                    $_SESSION['color'] = 'red';
                    header("Location: /uoc-sports/public/student-sign-up");
                    exit;
                }

                // Move uploaded file
                if (!move_uploaded_file($_FILES['id_card']['tmp_name'], $targetPath)) {
                    $_SESSION['message'] = 'Failed to upload ID card image.';
                    $_SESSION['color'] = 'red';
                    header("Location: /uoc-sports/public/student-sign-up");
                    exit;
                }
            }

            // Create student user
            if ($user->createStudent($data)) {
                // Save ID card info to database if uploaded
                if ($idCardImage) {
                    $this->saveStudentIdCard($data['student_id'], $idCardImage);
                }

                $_SESSION['message'] = "Sign Up Successful! Please Sign In.";
                $_SESSION['redirectURL'] = "/uoc-sports/public/sign-in";
                $_SESSION['color'] = "green";
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

    private function saveStudentIdCard($studentId, $imageName) {
        $db = Database::getConnection();
        $sql = "INSERT INTO student_id_cards (student_id, image_name) VALUES (:student_id, :image_name)
                ON DUPLICATE KEY UPDATE image_name = :image_name2";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':student_id' => $studentId,
            ':image_name' => $imageName,
            ':image_name2' => $imageName
        ]);
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
                    case "REG":
                        $_SESSION['redirectURL'] = "/uoc-sports/public/registrar//";
                        break;
                    case "SPT":
                        $_SESSION['redirectURL'] = "/uoc-sports/public/sport-manager//";
                        break;
                    case "EQP":
                        $_SESSION['redirectURL'] = "/uoc-sports/public/equipment-manager//";
                        break;
                    case "STUDENT":
                        $_SESSION['redirectURL'] = "/uoc-sports/public/student";
                        break;
                    case "CAPTAIN":
                        $_SESSION['redirectURL'] = "/uoc-sports/public/captain//";
                        break;
                    case "COACH":
                        $_SESSION['redirectURL'] = "/uoc-sports/public/coach//";
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
        global $smtpKey, $senderEmail;
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
    
            // --- Brevo Config (from .env via config.php) ---
            $apiKey = $smtpKey;
            $senderAddr = $senderEmail;
    
            if (empty($apiKey) || empty($senderAddr)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'User added but email not sent: Brevo API key or sender email missing in .env file.',
                    'user_id' => $userId
                ]);
                return;
            }
    
            // --- Prepare email payload ---
            $payload = [
                "sender" => [
                    "name" => "UOC Sports System",
                    "email" => $senderAddr
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
