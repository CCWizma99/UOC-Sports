<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News | UOC Sports E-Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/general/header.css);
        @import url(/uoc-sports/public/css/general/search-post.css);
        @import url(/uoc-sports/public/css/general/post-page-view.css);
        @import url(/uoc-sports/public/css/general/footer.css);

        .simple-bg {
            background-color: #f8f9fa;
        }
    </style>
    <link rel="stylesheet" href="/uoc-sports/public/css/general/news-tabs.css">
</head>
<body class="simple-bg">
    <?php require '../app/views/templates/general/header.php'; ?>

    <div class="news-tabs-container">
        <div class="tabs-nav">
            <div class="tab-link active" id="news-tab-btn" onclick="switchTab('news')">
                <i class="fa-solid fa-newspaper"></i> Latest News
            </div>
            <div class="tab-link" id="results-tab-btn" onclick="switchTab('results')">
                <i class="fa-solid fa-trophy"></i> Match Results
            </div>
        </div>

        <div id="news-tab-content" class="tab-content active">
            <?php
                require '../app/views/templates/general/search-post.php';
                require '../app/views/templates/general/post-page-view.php';
            ?>
        </div>

        <div id="results-tab-content" class="tab-content">
            <?php require '../app/views/templates/general/match-results-content.php'; ?>
        </div>
    </div>

    <?php require '../app/views/templates/general/footer.php'; ?>

    <script>
        function switchTab(tab) {
            // Update UI
            document.querySelectorAll('.tab-link').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            document.getElementById(tab + '-tab-btn').classList.add('active');
            document.getElementById(tab + '-tab-content').classList.add('active');

            // Initialize results if switching to results tab
            if (tab === 'results') {
                if (typeof initMatchResults === 'function') {
                    initMatchResults();
                }
            }

            // Update URL without reloading
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.pushState({}, '', url);
        }

        // Handle initial tab from URL
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            if (tab === 'results') {
                switchTab('results');
            } else {
                switchTab('news');
            }
        });

        // Set active nav
        var navItem = document.getElementById("nav-news");
        if (navItem) navItem.classList.add("active-portal");
    </script>
</body>
</html>