<?php

class AuthController extends BaseController {
    public function showSignupForm($message = null) {
        view('sign-up', ['message' => $message]);
    }

    public function showSigninForm($message = null) {
        view('sign-in', ['message' => $message]);
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
    
                // ✅ Remember Me Feature
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
}
