<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Send a Message | UOC Sports E-Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  	<style>
		@import url("/uoc-sports/public/css/global.css");
		@import url("/uoc-sports/public/css/general/header.css");
		@import url("/uoc-sports/public/css/sports-manager/messages.css");
		@import url("/uoc-sports/public/css/sports-manager/sub-nav.css");
		@import url("/uoc-sports/public/css/general/footer.css");
    @import url("/uoc-sports/public/css/sports-manager/report.css");
	</style>
  <script src="/uoc-sports/public/js/sports-manager/page.js"></script>  
</head> 
<body>
<?php
    require "../app/views/templates/general/header.php";
    require "../app/views/sports-manager/header-subnav.php";  
?>

<div class="page-container">
  <!-- Header -->
    <div class="page-header">
        <h2>Messages</h2>
        <p>Manage important messages among the sport's team members.</p>
      </div>

  

  <!-- Send Message Form (Initially Hidden) -->
 
  <div class="form-container" id="messageFormSection" style="display: none;">
    <div class="page-header" style="margin-bottom: 1.5rem;">
      <h2 style="color: #7530E1; margin: 0 0 0.5rem 0;">Send a Message</h2>
      <p style="color: rgba(0,0,0,0.6); margin: 0;">Compose a new message to team members</p>
    </div>
    <form class="form" id="messageForm" style="padding: 0;">
      <div class="form-grid">
      <div class="form-group">
        <label for="recipient">To</label>
        <select name="To" id="recipient" required>
          <option value="" disabled selected>Select Recipient</option>
          <option value="captain_1">Nadith Nemal - Captain</option>
          <option value="coach_1">N S Perera - Coach</option>
          <option value="equipment_1">K S Silva - Equipment Manager</option>
        </select>
      </div>


      <div class="form-group">
        <label for="title">Title</label>
        <input type="text" name="Title" id="title" placeholder="Add Title" required>
      </div>
      </div>

      <div class="form-group">
        <label for="message">Message</label>
        <textarea name="Message" id="message" placeholder="Add Message ..." required></textarea>
      </div>

      <div class="form-actions"">
        <button type="button" class="view-all-link" onclick="toggleMessageForm()" style="width: fit-content;">Cancel</button>
        <button type="submit" class="view-all-link" style="width: fit-content;">Send</button>
      </div>
    </form>
  </div>

  <!-- Messages Grid -->
  <div class="messages-section" style="margin: 0 2rem;">
    <div class="messages-header">
      <h2>Conversations</h2>
      <!-- Toggle Button -->
  <div>
    <button class="view-all-link" onclick="toggleMessageForm()">
     New Message
    </button>
  </div>
    </div>

    <div class="messages-container" id="messagesContainer" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
      <!-- Chat cards will be dynamically inserted here -->
    </div>
  </div>
</div>


         <?php
    require "../app/views/templates/general/footer.php";
?>

</body>
</html>