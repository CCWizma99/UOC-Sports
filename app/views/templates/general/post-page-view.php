<section class="section-block">
    <div class="section-header">
        <h2>Latest News</h2>
        <div class="section-accent"></div>
    </div>
    <?php if (!empty($recentNews)): ?>
        <div class="grid">
            <?php foreach ($recentNews as $news): ?>
                <article class="card">
                    <div class="img-box">
                        <?php if (!empty($news['image_path'])): ?>
                            <img src="/uoc-sports/public/<?= htmlspecialchars($news['image_path']) ?>" alt="Post image">
                        <?php else: ?>
                            <img src="/uoc-sports/public/images/posts/no-image.png" alt="No image">
                        <?php endif; ?>
                        <div class="img-overlay"></div>
                    </div>
                    <div class="content">
                        <h3><?= htmlspecialchars($news['title']) ?></h3>
                        <p><?= htmlspecialchars(substr($news['description'], 0, 100)) ?>...</p>
                        <a href="/uoc-sports/public/post/<?= htmlspecialchars($news['post_id']) ?>" class="btn">
                            <span>Read More</span>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M6 3L11 8L6 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                <path d="M19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3Z" stroke="currentColor" stroke-width="2"/>
                <path d="M8 10H16M8 14H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <p>No news to show.</p>
        </div>
    <?php endif; ?>
</section>

<section class="section-block">
    <div class="section-header">
        <h2>Lost & Found</h2>
        <div class="section-accent"></div>
    </div>
    <?php if (!empty($recentLostFound)): ?>
        <div class="grid">
            <?php foreach ($recentLostFound as $case): ?>
                <article class="card">
                    <div class="img-box">
                        <?php if (!empty($case['image_name'])): ?>
                            <img src="/uoc-sports/app/internal/lostitem/<?= htmlspecialchars($case['image_name']) ?>" alt="Lost item">
                        <?php else: ?>
                            <div class="placeholder-img">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                    <path d="M12 8V12L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                        <div class="img-overlay"></div>
                    </div>
                    <div class="content">
                        <h3><?= htmlspecialchars($case['case_title']) ?></h3>
                        <p><?= htmlspecialchars(substr($case['description'], 0, 100)) ?>...</p>
                        <a href="/uoc-sports/public/#contact" class="btn">
                            <span>Contact for Details</span>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M6 3L11 8L6 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                <path d="M9.5 9.5C9.5 9.5 10.5 8 12 8C13.5 8 14.5 9 14.5 10C14.5 11.5 12 11.5 12 13.5M12 16H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <p>No lost & found reports yet.</p>
        </div>
    <?php endif; ?>
</section>