<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile | UOC Sports E-Portal</title>

<style>
    @import url(/uoc-sports/public/css/global.css);
    @import url(/uoc-sports/public/css/general/header.css);
    @import url(/uoc-sports/public/css/general/intro.css);
    @import url(/uoc-sports/public/css/general/footer.css);

    body { font-family: Arial, sans-serif; margin: 0; background: #f6f6f6; }
    .profile-container { max-width: 600px; margin: 80px auto; background: white; border-radius: 10px; padding: 30px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    .profile-container h2 { text-align: center; margin-bottom: 20px; color: #5e2d91; }
    .profile-details p { margin: 8px 0; }
    .btn { display: inline-block; background: #5e2d91; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; margin-top: 1rem; }
    .btn:hover { background: #764ba2; }
    .logout-btn { background: crimson; }
    .popup-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
    .popup { background: white; padding: 20px 30px; border-radius: 10px; width: 320px; text-align: center; }
    .popup select { width: 100%; padding: 8px; margin: 10px 0; }
</style>
</head>
<body>

<?php include APP_ROOT.'/app/views/templates/general/header.php'; ?>

<div class="profile-container">
    <h2>Personal Details</h2>
    <div class="profile-details">
        <p><strong>User ID:</strong></p>
        <p><strong>Name:</strong></p>
        <p><strong>Email:</strong></p>
        <p><strong>Role:</strong></p>
    </div>

    <div style="margin-top:20px; text-align:center;">
        <a href="/uoc-sports/public/logout" class="btn logout-btn">Logout</a>
        <?php if ($isAdminViewingStudent): ?>
            <button class="btn" id="promoteBtn">Promote to Captain</button>
        <?php endif; ?>
    </div>
</div>

<script>
const promoteBtn = document.getElementById("promoteBtn");
const popupOverlay = document.getElementById("popupOverlay");
const closePopup = document.getElementById("closePopup");
const confirmBtn = document.getElementById("confirmBtn");

if (promoteBtn) promoteBtn.addEventListener("click", () => popupOverlay.style.display = "flex");
closePopup.addEventListener("click", () => popupOverlay.style.display = "none");
confirmBtn.addEventListener("click", () => {
    const sport = document.getElementById("sportSelect").value;
    if (!sport) { alert("Please select a sport first!"); return; }
    alert(`Student promoted to Captain of ${sport}!`);
    popupOverlay.style.display = "none";
});
</script>

</body>
</html>
