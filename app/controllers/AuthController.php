<?php

class AuthController {
    public function showSignupForm() {
        view('sign-up');
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
                echo "Signup successful!";
            } else {
                echo "Something went wrong.";
            }
        } else {
            echo "Invalid request";
        }
    }
}
