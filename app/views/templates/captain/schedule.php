
    <div class="container">
        <h1>Schedule Practice</h1>
        <p class="subheading">Create new practice sessions</p>

        <h2>New Practice Session</h2>
        <form action="submit_practice.php" method="post">
            <!-- Facility Dropdown -->
            <label for="facility">Select Facility</label>
            <select id="facility" name="facility" required>
                <option value="">-- Select Facility --</option>
                <option value="indoor">Indoor</option>
                <option value="basketball court">Basketball Court</option>
                <option value="volleyball court">Volleyball Court</option>
                <option value="ground">Ground</option>
            </select>

            <!-- Date & Time -->
            <div class="datetime">
                <div>
                    <label for="date">Select Date</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <div>
                    <label for="time">Select Time</label>
                    <input type="time" id="time" name="time" required>
                </div>
            </div>

            <!-- Description -->
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" placeholder="Enter details..."></textarea>

            <!-- Submit Button -->
            <button type="submit">Schedule Practice</button>
        </form>
    </div>

