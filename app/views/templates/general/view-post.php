<section id="post-view">
    <h1><?= htmlspecialchars($post['title']) ?></h1>
    <p><?= nl2br(htmlspecialchars($post['description'])) ?></p>
    <p><small>Posted on: <?= htmlspecialchars($post['date_posted']) ?></small></p>

    <hr>

    <h3>Comments</h3>
    <div id="comments">
        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $c): ?>
                <div class="comment-card">
                    <strong><?= htmlspecialchars($c['fname'] . ' ' . $c['lname']) ?>:</strong>
                    <p><?= htmlspecialchars($c['content']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No comments yet.</p>
        <?php endif; ?>
    </div>

    <?php if (isset($user_id)): ?>
        <hr>
        <h3>Add a comment</h3>
        <form id="comment-form">
            <textarea name="content" id="comment-content" rows="4" placeholder="Write your comment..." required></textarea>
            <input type="hidden" name="post_id" value="<?= htmlspecialchars($post['post_id']) ?>">
            <button type="submit">Post Comment</button>
        </form>

        <script>
        const commentForm = document.getElementById('comment-form');
        commentForm.addEventListener('submit', async e => {
            e.preventDefault();
            const content = document.getElementById('comment-content').value.trim();
            if (!content) return;

            const postId = commentForm.querySelector('[name="post_id"]').value;

            try {
                const res = await fetch('/uoc-sports/public/post/add-comment', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ post_id: postId, content })
                });
                const data = await res.json();
                if (data.status === 'success') {
                    const commentsDiv = document.getElementById('comments');
                    const newComment = document.createElement('div');
                    newComment.classList.add('comment-card');
                    newComment.innerHTML = `<strong>${data.user_name}:</strong> <p>${content}</p>`;
                    commentsDiv.appendChild(newComment);
                    commentForm.reset();
                } else {
                    alert(data.message);
                }
            } catch (err) {
                alert('Error posting comment.');
            }
        });
        </script>

    <?php else: ?>
        <p><a href="/uoc-sports/public/sign-in">Log in to comment</a></p>
    <?php endif; ?>
</section>

<style>
#post-view { max-width: 700px; margin: auto; padding: 20px; }
#post-view h1 { font-size: 28px; margin-bottom: 10px; }
#post-view p { line-height: 1.5; }
.comment-card { padding: 10px; border: 1px solid #ccc; border-radius: 6px; margin-bottom: 10px; }
textarea { width: 100%; border-radius: 6px; padding: 8px; }
button { margin-top: 5px; padding: 6px 12px; border-radius: 6px; cursor: pointer; }
</style>
