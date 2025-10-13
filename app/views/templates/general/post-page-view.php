<section class="section-block">
    <h2>Latest News</h2>
    <?php if (!empty($recentNews)): ?>
        <div class="grid">
            <?php foreach ($recentNews as $news): ?>
                <div class="card">
                    <div class="img-box">
                        <?php if (!empty($news['image_path'])): ?>
                            <img src="<?= htmlspecialchars($news['image_path']) ?>" alt="Post image">
                        <?php else: ?>
                            <img src="/uoc-sports/public/images/posts/no-image.png" alt="No image">
                        <?php endif; ?>
                    </div>
                    <div class="content">
                        <h3><?= htmlspecialchars($news['title']) ?></h3>
                        <p><?= htmlspecialchars(substr($news['description'], 0, 100)) ?>...</p>
                        <a href="/uoc-sports/public/post/<?= htmlspecialchars($news['post_id']) ?>" class="btn">Show More</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="empty">No news to show.</p>
    <?php endif; ?>
</section>

<section class="section-block">
    <h2>Lost & Found</h2>
    <?php if (!empty($recentLostFound)): ?>
        <div class="grid">
            <?php foreach ($recentLostFound as $case): ?>
                <div class="card">
                    <div class="img-box">
                        <?php if (!empty($case['image_name'])): ?>
                            <img src="/uoc-sports/public/uploads/lost-found/<?= htmlspecialchars($case['image_name']) ?>" alt="Lost item">
                        <?php endif; ?>
                    </div>
                    <div class="content">
                        <h3><?= htmlspecialchars($case['case_title']) ?></h3>
                        <p><?= htmlspecialchars(substr($case['description'], 0, 100)) ?>...</p>
                        <a href="/uoc-sports/public/lost-found/<?= htmlspecialchars($case['case_id']) ?>" class="btn">Show More</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="empty">No lost & found reports yet.</p>
    <?php endif; ?>
</section>