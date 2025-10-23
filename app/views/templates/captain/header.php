<section id="header">
    <div class="top-bar flex">
        <a href="#" class="logo">
            <img src="/uoc-sports/public/images/uoc-logo.png" alt="">
            <div>UOC Sports<br>E-Portal</div>
        </a>
        <div class="mid-div">

        </div>
        <div class="log-div">
        <?php
            if (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];

                require_once APP_ROOT.'/core/Database.php';

                $db = Database::getConnection();

                // Prepare statement
                $stmt = $db->prepare("SELECT type FROM user WHERE user_id = :user_id");

                // Bind parameter (this part was missing)
                $stmt->execute(['user_id' => $user_id]);

                // Fetch the row properly
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && $user['type'] === 'STUDENT') {
                    echo '<a href="/uoc-sports/public/student/" id="user_type">Student</a>';
                }
                else if ($user && $user['type'] === 'CAPTAIN') {
                    echo '<a href="/uoc-sports/public/captain/" id="user_type">Captain</a>';
                }
                else if($user && $user['type'] === 'EQP') {
                    echo '<a href="/uoc-sports/public/equipment-manager/" id="user_type">Eq. Manager</a>';
                }
                else if($user && $user['type'] === 'SPT') {
                    echo '<a href="/uoc-sports/public/sport-manager/" id="user_type">Sp. Manager</a>';
                }
                else if($user && $user['type'] === 'REGISTRAR') {
                    echo '<a href="/uoc-sports/public/student/" id="user_type">Registrar</a>';
                }
                else if($user && $user['type'] === 'INSTAFF') {
                    echo '<a href="/uoc-sports/public/student/" id="user_type">Staff</a>';
                }

                echo '<a href="/uoc-sports/public/profile">
                        Profile <i class="fa-solid fa-circle-user"></i>
                    </a>';
            } else {
                echo '<a href="/uoc-sports/public/sign-in">
                        Log in <i class="fa-solid fa-right-to-bracket"></i>
                    </a>';
            }
        ?>
        
        </div>
    </div>
    <nav class="flex">
        <a href="/uoc-sports/public/captain//" class="active">Home</a>
        <a href="/uoc-sports/public/captain/add-members">Team</a>
        <a href="/uoc-sports/public/captain/schedule-practice">Schedule</a>
        <a href="/uoc-sports/public/captain/mark-attendance">Attendance</a>
        <a href="/uoc-sports/public/captain/communication">Communication</a>
    </nav>
</section>