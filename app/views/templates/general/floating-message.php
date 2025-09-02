<div id="floating-message" class="floating-message">
    <div class="message-box">
        <p id="message-text" class="<?=$_SESSION['color'] ?>"></p>
        <button id="message-ok-btn" class="<?=$_SESSION['color'] ?>">OK</button>
    </div>
</div>
<script>

document.addEventListener('DOMContentLoaded', () => {
    const floatingMessage = document.getElementById('floating-message');
    const messageText = document.getElementById('message-text');
    const okBtn = document.getElementById('message-ok-btn');

    function showMessage(text, redirectUrl = null) {
        messageText.innerText = text;
        floatingMessage.style.display = 'flex';

        okBtn.onclick = () => {
            floatingMessage.style.display = 'none';
            if (redirectUrl && redirectUrl.trim() !== '') {
                window.location.href = redirectUrl;
            }
        };
    }

    window.showFloatingMessage = showMessage;
});

</script>

<?php if (!empty($_SESSION['message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showFloatingMessage(
                <?php echo json_encode($_SESSION['message']); ?>,
                <?php echo json_encode($_SESSION['redirectURL'] ?? ''); ?>
            );
        });
    </script>
    <?php
    unset($_SESSION['message'], $_SESSION['redirectUrl']);
    ?>
<?php endif; ?>


