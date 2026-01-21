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
  <script src="/uoc-sports/public/js/sports-manager/page.js" defer></script>  
</head> 
<body data-user-id="<?= htmlspecialchars($userId ?? '') ?>">
<?php
    require "../app/views/templates/general/header.php";
    require "../app/views/sports-manager/header-subnav.php";  
?>

<script>
// Pass backend conversations data to JavaScript
const backendConversations = <?= json_encode($conversations ?? []) ?>;
</script>

<div class="page-container">
  <!-- Header -->
    <div class="container-header">
        <h2>Messages</h2>
        <p>Manage important messages among the sport's team members.</p>
      </div>

  <?php if (isset($_SESSION['success_message'])): ?>
    <div style="padding: 0.75rem; background: #d1fae5; color: #065f46; border-radius: 8px; margin: 0 2rem 1rem;">
        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
    </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['error_message'])): ?>
    <div style="padding: 0.75rem; background: #fee2e2; color: #991b1b; border-radius: 8px; margin: 0 2rem 1rem;">
        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
    </div>
  <?php endif; ?>

  <!-- Send Message Form (Initially Hidden) -->
 
  <div class="form-container" id="messageFormSection" style="display: none;">
    <div class="page-header" style="margin-bottom: 1.5rem;">
      <h2 style="color: #5e2d91; margin: 0 0 0.5rem 0;">Send a Message</h2>
      <p style="color: rgba(0,0,0,0.6); margin: 0;">Compose a new message to team members</p>
    </div>
    <form class="form" id="messageForm" method="POST" action="/uoc-sports/public/sport-manager/messages/send" style="padding: 0;">
      <div class="form-grid">
      <div class="form-group">
        <label for="recipient">To</label>
        <select name="receiver_id" id="recipient" required>
          <option value="" disabled selected>Select Recipient</option>
          <option value="all">T S Silva - women's Cricket Captain</option>
          <option value="all">T S Silva - Men'sCricket Captain</option>
          <option value="coaches">A A Perera - Women's Vice Captain</option>
          <option value="coaches">D M Fernando - Men's Vice Captain</option>
          <option value="coaches">M K Silva - Cricket Coach</option>
          <option value="players">Equipment Manager</option>
          <?php if (!empty($recipients)): ?>
            <?php foreach ($recipients as $recipient): ?>
              <option value="<?= htmlspecialchars($recipient['user_id']) ?>">
                <?= htmlspecialchars($recipient['name']) ?> - <?= htmlspecialchars($recipient['role']) ?>
              </option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </div>


      <div class="form-group">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" placeholder="Add Title" required>
      </div>
      </div>

      <div class="form-group">
        <label for="message">Message</label>
        <textarea name="message" id="message" placeholder="Add Message ..." required></textarea>
      </div>

      <div class="form-actions">
        <button type="button" class="view-all-link" onclick="toggleMessageForm()" style="width: fit-content; cursor: pointer;">Cancel</button>
        <button type="submit" class="view-all-link">Send</button>
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
      <!-- Dummy Conversation 1 -->
      <div class="message-card" style="background: white; border: 2px solid #5e2d91; border-radius: 8px; padding: 1.5rem; transition: all 0.3s ease;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
          <div>
            <h3 style="margin: 0 0 0.25rem 0; color: #5e2d91; font-size: 1.1rem;">
              John Smith
            </h3>
            <p style="margin: 0; color: #6b7280; font-size: 0.875rem;">
              Coach
            </p>
          </div>
          <span style="color: #9ca3af; font-size: 0.75rem;">2 hours ago</span>
        </div>
        <div style="margin-bottom: 0.5rem;">
          <strong style="color: #374151; display: block; margin-bottom: 0.25rem;">Practice Session Update</strong>
          <p style="margin: 0; color: #374151; line-height: 1.5;">
            The practice session scheduled for tomorrow has been moved to 4:00 PM instead of 3:00 PM. Please inform all team members.
          </p>
        </div>
        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
          <button onclick="alert('Reply feature coming soon')" style="background: #2b0c4d; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.875rem; margin-right: 0.5rem;">
            Reply
          </button>
          <button onclick="alert('View full conversation')" style="background: white; color: #5e2d91; border: 1px solid #5e2d91; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.875rem;">
            View Thread
          </button>
        </div>
      </div>

      
      <!-- Dummy Conversation 3 -->
      <div class="message-card" style="background: white; border: 2px solid #5e2d91; border-radius: 8px; padding: 1.5rem; transition: all 0.3s ease;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
          <div>
            <h3 style="margin: 0 0 0.25rem 0; color: #5e2d91; font-size: 1.1rem;">
              Michael Chen
            </h3>
            <p style="margin: 0; color: #6b7280; font-size: 0.875rem;">
              Captain
            </p>
          </div>
          <span style="color: #9ca3af; font-size: 0.75rem;">1 day ago</span>
        </div>
        <div style="margin-bottom: 0.5rem;">
          <strong style="color: #374151; display: block; margin-bottom: 0.25rem;">Tournament Registration</strong>
          <p style="margin: 0; color: #374151; line-height: 1.5;">
            We need to finalize the team roster for the inter-university tournament. Please review the attached list and confirm by end of week.
          </p>
        </div>
        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
          <button onclick="alert('Reply feature coming soon')" style="background: #2b0c4d; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.875rem; margin-right: 0.5rem;">
            Reply
          </button>
          <button onclick="alert('View full conversation')" style="background: white; color: #5e2d91; border: 1px solid #5e2d91; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.875rem;">
            View Thread
          </button>
        </div>
      </div>

      <!-- Dummy Conversation 4 -->
      <div class="message-card" style="background: white; border: 2px solid #5e2d91; border-radius: 8px; padding: 1.5rem; transition: all 0.3s ease;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
          <div>
            <h3 style="margin: 0 0 0.25rem 0; color: #5e2d91; font-size: 1.1rem;">
              Emma Davis
            </h3>
            <p style="margin: 0; color: #6b7280; font-size: 0.875rem;">
              Team Member
            </p>
          </div>
          <span style="color: #9ca3af; font-size: 0.75rem;">2 days ago</span>
        </div>
        <div style="margin-bottom: 0.5rem;">
          <strong style="color: #374151; display: block; margin-bottom: 0.25rem;">Injury Report</strong>
          <p style="margin: 0; color: #374151; line-height: 1.5;">
            I injured my ankle during yesterday's practice. Medical report attached. I'll need to sit out for the next two weeks per doctor's recommendation.
          </p>
        </div>
        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
          <button onclick="alert('Reply feature coming soon')" style="background: #2b0c4d; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.875rem; margin-right: 0.5rem;">
            Reply
          </button>
          <button onclick="alert('View full conversation')" style="background: white; color: #5e2d91; border: 1px solid #5e2d91; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.875rem;">
            View Thread
          </button>
        </div>
      </div>
    </div>
  </div>
</div>


         <?php
    require "../app/views/templates/general/footer.php";
?>

<script>
function toggleMessageForm() {
    const formSection = document.getElementById('messageFormSection');
    if (formSection.style.display === 'none') {
        formSection.style.display = 'block';
        formSection.scrollIntoView({ behavior: 'smooth' });
    } else {
        formSection.style.display = 'none';
        document.getElementById('messageForm').reset();
    }
}

// Debug function to check form submission
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('messageForm');
    if (messageForm) {
        console.log('Message form found');
        messageForm.addEventListener('submit', function(e) {
            console.log('Form submitted');
            console.log('Receiver ID:', document.getElementById('recipient').value);
            console.log('Title:', document.getElementById('title').value);
            console.log('Message:', document.getElementById('message').value);
            // Let form submit naturally - don't prevent default
        });
    }
});

function viewConversation(partnerId) {
    // Redirect to conversation view or open modal
    window.location.href = '/uoc-sports/public/sport-manager/messages/conversation?partner_id=' + partnerId;
}

function deleteConversation(partnerId) {
    if (!confirm('Are you sure you want to delete this conversation?')) {
        return;
    }
    
    // In a real implementation, you'd call an API to delete all messages in conversation
    alert('Delete conversation functionality - implement as needed');
}

// Auto-hide success/error messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('[style*="background: #d1fae5"], [style*="background: #fee2e2"]');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
});

// Add hover effect to message cards
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.message-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
            this.style.borderColor = '#5e2d91';
        });
        card.addEventListener('mouseleave', function() {
            this.style.boxShadow = 'none';
            this.style.borderColor = '#e5e7eb';
        });
    });
});
</script>

</body>
</html>