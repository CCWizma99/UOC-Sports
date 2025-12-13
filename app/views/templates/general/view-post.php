<?php
  $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($post['title']) ?> - Post View</title>
  <style>
    body {
      background: #f9fafb;
      color: #333;
      margin: 0;
      padding: 0;
    }

    #post-view {
      max-width: 800px;
      margin: 40px auto;
      background: #fff;
      padding: 24px;
      border-radius: 10px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    }

    #post-view h1 {
      font-size: 28px;
      margin-bottom: 12px;
      color: #222;
    }

    #post-view p {
      line-height: 1.6;
      margin-bottom: 16px;
    }

    #post-view small {
      color: #777;
    }

    .post-images {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 12px;
      margin: 20px 0;
    }

    .post-images .img-card {
      width: 100%;
      height: 0;
      padding-bottom: 66.666%;
      position: relative;
      overflow: hidden;
      border-radius: 10px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
      cursor: pointer;
      background: #f2f2f2;
    }

    .post-images .img-card img {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
      transition: transform .25s ease;
      display: block;
    }

    .post-images .img-card:hover img { transform: scale(1.05); }

    .pb-lightbox {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.8);
      z-index: 1200;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .pb-lightbox.active { display: flex; }

    .pb-lightbox .lb-inner {
      max-width: 1100px;
      width: 100%;
      max-height: 90vh;
      border-radius: 10px;
      overflow: hidden;
      background: #111;
      box-shadow: 0 10px 30px rgba(0,0,0,0.6);
      position: relative;
    }

    .pb-lightbox img {
      display: block;
      width: 100%;
      height: auto;
      max-height: 90vh;
      object-fit: contain;
      background: #111;
    }

    .pb-close {
      position: absolute;
      right: 18px;
      top: 18px;
      color: #fff;
      background: rgba(0,0,0,0.4);
      border-radius: 6px;
      padding: 6px 10px;
      font-size: 16px;
      cursor: pointer;
      border: none;
    }

    .pb-caption {
      padding: 10px 14px;
      color: #ddd;
      font-size: 14px;
      background: #0f0f0f;
      text-align: center;
    }

    /* ---------- COMMENTS ---------- */
    .comment-card {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-bottom: 10px;
      background: #fafafa;
      position: relative;
    }

    .delete-btn {
      background: #dc3545;
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 4px;
      font-size: 12px;
      position: absolute;
      top: 8px;
      right: 8px;
      cursor: pointer;
    }

    .delete-btn:hover { background: #b02a37; }

    textarea {
      width: 100%;
      border-radius: 6px;
      padding: 8px;
      border: 1px solid #ccc;
      font-family: inherit;
    }

    button {
      margin-top: 5px;
      padding: 8px 16px;
      border-radius: 6px;
      cursor: pointer;
      border: none;
      background: #007bff;
      color: #fff;
      font-weight: 600;
    }

    button:hover { background: #0056b3; }

    hr {
      border: none;
      border-top: 1px solid #ddd;
      margin: 24px 0;
    }
  </style>
</head>
<body>

<section id="post-view" style="margin-top: 120px;">
  <h1><?= htmlspecialchars($post['title']) ?></h1>
  <p><?= nl2br(htmlspecialchars($post['description'])) ?></p>
  <p><small>Posted on: <?= htmlspecialchars($post['date_posted']) ?></small></p>

  <?php if (!empty($post['images'])): ?>
    <div class="post-images">
      <?php foreach ($post['images'] as $image): ?>
        <div class="img-card" data-src="/uoc-sports/public/images/posts/<?= htmlspecialchars($image) ?>">
          <img src="/uoc-sports/public/<?= htmlspecialchars($image) ?>" alt="Post image">
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if (isset($post['commenting']) && strtoupper($post['commenting']) === 'YES'): ?>
  <hr>

  <h3>Comments</h3>
  <div id="comments">
      <?php if (!empty($comments)): ?>
          <?php foreach ($comments as $c): ?>
              <div class="comment-card" data-comment-id="<?= $c['comment_id'] ?>">
                  <strong><?= htmlspecialchars($c['fname'] . ' ' . $c['lname']) ?>:</strong>
                  <p><?= htmlspecialchars($c['content']) ?></p>
                  <?php if (isset($user_id) && $user_id == $c['comment_from']): ?>
                      <button class="delete-btn" data-id="<?= $c['comment_id'] ?>">Delete</button>
                  <?php endif; ?>
              </div>
          <?php endforeach; ?>
      <?php else: ?>
          <p>No comments yet.</p>
      <?php endif; ?>
  </div>

  <?php if (isset($_SESSION['user_id'])): ?>
      <hr>
      <h3>Add a comment</h3>
      <form id="comment-form">
          <textarea name="content" id="comment-content" rows="4" placeholder="Write your comment..." required></textarea>
          <input type="hidden" name="post_id" value="<?= htmlspecialchars($post['post_id']) ?>">
          <button type="submit">Post Comment</button>
      </form>
  <?php else: ?>
      <p><a href="/uoc-sports/public/sign-in">Log in to comment</a></p>
  <?php endif; ?>
  <?php endif; ?>
</section>

<!-- LIGHTBOX -->
<div id="pbLightbox" class="pb-lightbox" role="dialog" aria-hidden="true">
  <div class="lb-inner">
    <button id="pbClose" class="pb-close" aria-label="Close">✕</button>
    <img id="pbImage" src="" alt="">
    <div id="pbCaption" class="pb-caption"></div>
  </div>
</div>

<script>
(function(){
  // --- Lightbox setup ---
  const cards = document.querySelectorAll('.post-images .img-card');
  const lb = document.getElementById('pbLightbox');
  const lbImg = document.getElementById('pbImage');
  const lbCaption = document.getElementById('pbCaption');
  const lbClose = document.getElementById('pbClose');

  if (cards.length) {
    cards.forEach(card => {
      card.addEventListener('click', () => {
        const src = card.dataset.src || card.querySelector('img').src;
        const caption = card.querySelector('img').alt || '';
        lbImg.src = src;
        lbCaption.textContent = caption;
        lb.classList.add('active');
        document.body.style.overflow = 'hidden';
      });
    });
  }

  lbClose.addEventListener('click', closeLightbox);
  lb.addEventListener('click', (e) => { if (e.target === lb) closeLightbox(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeLightbox(); });

  function closeLightbox() {
    lb.classList.remove('active');
    lbImg.src = '';
    document.body.style.overflow = '';
  }

  // --- Add comment handler ---
  const commentForm = document.getElementById('comment-form');
  if (commentForm) {
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
          newComment.innerHTML = `
            <strong>${data.user_name}:</strong>
            <p>${content}</p>
            <button class="delete-btn" data-id="${data.comment_id}">Delete</button>
          `;
          commentsDiv.appendChild(newComment);
          commentForm.reset();
        } else alert(data.message);
      } catch (err) {
        alert('Error posting comment.');
      }
    });
  }

  // --- Delete comment handler ---
  document.addEventListener('click', async e => {
    if (e.target.classList.contains('delete-btn')) {
      const commentId = e.target.dataset.id;
      if (!confirm('Are you sure you want to delete this comment?')) return;

      try {
  const res = await fetch('/uoc-sports/public/post/delete-comment', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ comment_id: commentId })
  });
  const data = await res.json();
  if (data.status === 'success') {
    e.target.closest('.comment-card').remove();
  } else {
    alert(data.message || 'Failed to delete comment.');
  }
} catch (err) {
  alert('Error deleting comment.');
}

    }
  });
})();
</script>

</body>
</html>
