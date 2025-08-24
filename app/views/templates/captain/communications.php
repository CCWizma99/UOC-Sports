
  <div class="communication-container">
    <h2>Communicate with Coach and Sports Manager</h2>
    <p class="subtitle">Send messages to your coach and sports manager, and view previous communications.</p>

    <!-- Form Section -->
    <div class="form-section">
      <label for="recipient">Recipient</label>
      <select id="recipient">
        <option value="">-- Select Recipient --</option>
        <option value="Coach Kasun">Coach Kasun</option>
        <option value="Coach Ratnayaka">Coach Ratnayaka</option>
        <option value="Sports Manager">Sports Manager</option>
      </select>

      <label for="title">Title</label>
      <input type="text" id="title" placeholder="Enter Subject">

      <label for="message">Message</label>
      <textarea id="message" placeholder="Type your message here......."></textarea>

      <div class="buttons">
        <button id="sendBtn">Send</button>
        <button id="clearBtn" class="clear">Clear</button>
      </div>
      <p id="statusMsg" class="status"></p>
    </div>

    <!-- Previous Messages -->
    <div class="messages-section">
      <h3>Previous Messages</h3>
      <ul id="messagesList">
        <li>
          <span class="sender">Coach Kasun</span>
          <span class="text">Check the schedule..</span>
          <span class="date">27 Jul</span>
          <span class="delete" title="Delete">&times;</span>
        </li>
        <li>
          <span class="sender">Coach Ratnayaka</span>
          <span class="text">Cancel the schedule..</span>
          <span class="date">27 Jul</span>
          <span class="delete" title="Delete">&times;</span>
        </li>
      </ul>
    </div>
  </div>



