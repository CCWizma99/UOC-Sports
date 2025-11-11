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
<div class="top-bar">
    <div id="mobile-menu" onclick="toggleMenu()">
        <i class="fa-solid fa-bars"></i>
    </div>

    <div class="flex logo">
        <a href="#">
            <img src="/uoc-sports/public/images/uoc-logo.png" alt="">
            <div>UOC Sports<br>E-Portal</div>
        </a>
    </div>

    <div class="log-div">
        <?php if ($user): ?>
            <a href="/uoc-sports/public/profile">
                Profile <i class="fa-solid fa-circle-user"></i>
            </a>
        <?php else: ?>
            <a href="/uoc-sports/public/sign-in">
                Log in <i class="fa-solid fa-right-to-bracket"></i>
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="overlay" id="overlay" onclick="toggleMenu()"></div>

<section id="header">
    <nav class="flex" id="mobile-nav">
        <a href="/uoc-sports/public/" id="nav-home">Home</a>
        <a href="/uoc-sports/public/news" id="nav-news">News</a>
        <a href="/uoc-sports/public/facility-reservation" id="nav-res">Facility Reservation</a>

        <?php
            if ($user) {
                switch ($user['type']) {
                    case 'STUDENT':
                        echo '<a href="/uoc-sports/public/student/" class="user-type">Student Portal</a>';
                        break;

                    case 'CAPTAIN':
                        echo '<a href="/uoc-sports/public/student/" class="user-type">Student Portal</a>';
                        echo '<a href="/uoc-sports/public/captain/" class="user-type">Captain</a>';
                        break;

                    case 'EQP':
                        echo '<a href="/uoc-sports/public/equipment-manager/" class="user-type">Eq. Manager</a>';
                        break;

                    case 'SPT':
                        echo '<a href="/uoc-sports/public/sport-manager/" class="user-type">Sp. Manager</a>';
                        break;

                    case 'REGISTRAR':
                        echo '<a href="/uoc-sports/public/student/" class="user-type">Registrar</a>';
                        break;

                    case 'INSTAFF':
                        echo '<a href="/uoc-sports/public/student/" class="user-type">Staff</a>';
                        break;
                }
            }
        ?>
    </nav>
</section>

<script>
    const menuBtn = document.getElementById('mobile-menu');
    const header = document.getElementById('header');
    const overlay = document.getElementById('overlay');

    function toggleMenu() {
        header.classList.toggle('expand');
        overlay.classList.toggle('show');
        menuBtn.classList.toggle('expand');
    }
</script>
