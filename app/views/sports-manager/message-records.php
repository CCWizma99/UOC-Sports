
 <h2>Messages</h2>

 
  <div class="chat-box" onclick="toggleMessages(this)">
    <div class="chat-user">K S Silva</div>
    <div class="chat-preview">Yes, I’ll be there by 10!</div>
    <div class="chat-messages">
      <div class="message sent">Hey Saman!</div>
      <div class="message received">Hey, are you coming tomorrow?</div>
      <div class="message sent">Yes, I’ll be there by 10!</div>
    </div>
  </div>


  <div class="chat-box" onclick="toggleMessages(this)">
    <div class="chat-user">N S Perera</div>
    <div class="chat-preview">Hey Nimal, What is the time of the new practice session?</div>
    <div class="chat-messages">
      <div class="message received">Hi! I scheduled a new practice session.</div>
      <div class="message sent">Hey Nimal, What is the time of the new practice session?</div>
      
      
    </div>
  </div>

</div>

<script>
function toggleMessages(box) {
  box.classList.toggle("active");
}
</script>