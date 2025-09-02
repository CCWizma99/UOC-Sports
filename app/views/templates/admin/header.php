<style>
    @import url("/uoc-sports/public/css/general/floating-message.css");
</style>

<?php
// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check session
if (!isset($_SESSION['user_id'])) {
    // If session not set, check remember token
    if (isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        $hashed = hash('sha256', $token);

        $stmt = $db->prepare("SELECT user_id FROM remember_tokens WHERE token = ? AND expires_at > ?");
        $stmt->execute([$hashed, time()]);
        $row = $stmt->fetch();

        if ($row) {
            // Auto-login
            $_SESSION['user_id'] = $row['user_id'];

            // Fetch user details for session
            $stmtUser = $db->prepare("SELECT fname, type FROM users WHERE user_id = ?");
            $stmtUser->execute([$row['user_id']]);
            $user = $stmtUser->fetch();

            if ($user) {
                $_SESSION['user_name'] = $user['fname'];
                $_SESSION['color'] = "green";
            }
        }
    }
}

// If still no session, redirect to sign-in
if (!isset($_SESSION['user_id'])) {
    header("Location: /uoc-sports/public/sign-in");
    exit;
}
?>


    
    <div class="filler">
        <header class="flex y-center">
            <a href="/uoc-sports/public/admin-index" class="no-dec text-black" id="home-link">
                <h1>
                    UOC Sports<br>E-Portal
                </h1>
            </a>
            <div id="header-links" class="flex y-center">
                <a href="#"><i class="fa-solid fa-bullhorn"></i></a>
                <a href="#"><i class="fa-solid fa-bell"></i></a>
                <a href="#"><i class="fa-solid fa-user"></i></a>         
            </div>
        </header>
    </div>