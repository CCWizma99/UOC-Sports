<div id="add-event">
    <!-- Event Creation -->
    <section>
      <h2>Create an Sport Event</h2>
      <form id="eventForm">
        <input type="text" id="eventName" placeholder="Event Name" required>
        <input type="date" id="eventDate" required>
        <input type="text" id="eventSport" placeholder="Sport Type" required>
        <button type="submit">Create Event</button>
      </form>
    </section>

    <!-- Team Registration -->
    <section>
      <h2>Register a Team for the Event</h2>
      <form id="teamForm">
        <input type="text" id="teamName" placeholder="Team Name" required>
        <input type="text" id="coachName" placeholder="Coach Name" required>
        <input type="number" id="playersCount" placeholder="Number of Players" required min="1">
        <button type="submit">Register Team</button>
      </form>
    </section>

    <!-- Teams List -->
    <section>
      <h2>Teams List</h2>
      <ul id="teamsList"></ul>
    </section>
  </div>