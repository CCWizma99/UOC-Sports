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
        <a href="/uoc-sports/public/" class="active">Home</a>
        <a href="/uoc-sports/public/news">News</a>
        <a href="/uoc-sports/public/facility-reservation">Facility Reservation</a>
        <a href="#">Contact Us</a>
    </nav>
</section>