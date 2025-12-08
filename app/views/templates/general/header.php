<?php 
    // Always define user
    $user = null;

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        require_once APP_ROOT.'/core/Database.php';
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT type FROM user WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
?>
<header>
    <nav>
        <a href="/uoc-sports/public/" class="logo">
        <img src="/uoc-sports/public/images/uoc-logo.png" alt="">
        <span>UOC Sports E-Portal</span>
        </a>
        <i class="fa-solid fa-bars" id="menu-btn" onclick="toggleMenu()"></i>
        <div class="nav-links" id="nav-links">

            <a href="/uoc-sports/public/news">News</a>
            <a href="/uoc-sports/public/#contact">Contact Us</a>

            <a href="/uoc-sports/public/facility-reservation" id="nav-res" class="btn-secondary">
                Facility Reservation
            </a>

            <?php
                if ($user) {
                    // User-specific links
                    switch ($user['type']) {
                        case 'STUDENT':
                            echo '<a href="/uoc-sports/public/student/" id="user_type" class="user-type btn-primary">Student Portal</a>';
                            break;

                        case 'CAPTAIN':
                            echo '<a href="/uoc-sports/public/student/" id="user_type" class="user-type btn-primary">Student Portal</a>';
                            echo '<a href="/uoc-sports/public/captain/" id="user_type" class="user-type btn-primary">Captain</a>';
                            break;

                        case 'EQP':
                            echo '<a href="/uoc-sports/public/equipment-manager/" id="user_type" class="user-type btn-primary">Eq. Manager</a>';
                            break;

                        case 'SPT':
                            echo '<a href="/uoc-sports/public/sport-manager/" id="user_type" class="user-type btn-primary">Sp. Manager</a>';
                            break;

                        case 'REGISTRAR':
                            echo '<a href="/uoc-sports/public/student/" id="user_type" class="user-type btn-primary">Registrar</a>';
                            break;

                        case 'INSTAFF':
                            echo '<a href="/uoc-sports/public/student/" id="user_type" class="user-type btn-primary">Staff</a>';
                            break;
                    }

                    // Profile button
                    echo '
                        <a href="/uoc-sports/public/profile" class="btn-primary" id="nav-pro">
                            Profile <i class="fa-solid fa-circle-user"></i>
                        </a>
                    ';

                } else {
                    // Sign in button
                    echo '<a href="/uoc-sports/public/sign-in" class="btn-primary">Sign In</a>';
                }
            ?>

        </div>
    </nav>
</header>
<script>
    function toggleMenu() {
        const nav = document.getElementById('nav-links');
        nav.classList.toggle('show');
    }
</script>
