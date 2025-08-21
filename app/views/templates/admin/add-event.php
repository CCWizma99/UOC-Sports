<div id="add-event">
    <!-- Event Creation -->
    <section>
      <h2>Create an Sport Event</h2>
      <form id="eventForm">
        <input type="text" id="eventName" placeholder="Event Name" required>
        <input type="date" id="eventDate" required>
        <input type="text" id="eventVenue" placeholder="Event Venue" required>
        <input type="text" id="eventSport" placeholder="Sport Type" required>
        <textarea name="notice" id="eventNotice" placeholder="Event Description"></textarea>
        <button type="submit">Create Event</button>
      </form>
    </section>

    <section>
      <h2>Send Invitations for Teams to participate the Event</h2>
      <div class="recipient-row flex">
        <span class="number xy-center">01</span>
        <div class="recipient">
          <span class="name">University of Sri Jayawardhanapura</span>
          <span class="email">sports@usj.ac.lk</span>
        </div>
        <button class="send-mail-btn">Send Invitation</button>
      </div>
      <div class="recipient-row flex">
        <span class="number xy-center">02</span>
        <div class="recipient">
          <span class="name">University of Kelaniya</span>
          <span class="email">sports@usj.ac.lk</span>
        </div>
        <button class="send-mail-btn">Send Invitation</button>
      </div>
      <div class="recipient-row flex">
        <span class="number xy-center">03</span>
        <div class="recipient">
          <span class="name">University of Moratuwa</span>
          <span class="email">sports@usj.ac.lk</span>
        </div>
        <button class="send-mail-btn">Send Invitation</button>
      </div>
      <div class="recipient-row flex">
        <span class="number xy-center">04</span>
        <div class="recipient">
          <span class="name">University of Ruhuna</span>
          <span class="email">sports@usj.ac.lk</span>
        </div>
        <button class="send-mail-btn">Send Invitation</button>
      </div>
      <div class="custom-recipient">
        <h4>Invite a Custom Recipient</h4>
        <input type="text" id="custom-institution" placeholder="Enter Institution Name">
        <input type="text" id="custom-email" placeholder="Enter Email Address">
        <button type="submit">Send Invitation</button>
      </div>
    </section>
  </div>