
    <div class="container">
        <h1>Schedule Practice</h1>
        <p class="subheading">Create new practice sessions</p>

        <h2>New Practice Session</h2>
        <form action="/uoc-sports/public/captain/schedule-practice" method="post" id="scheduleForm">
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
            <button type="submit" name="create" id="createBtn">Schedule Practice</button>
        </form>
      <br> <br> <br> 

        <!-- Display Table -->
    <h2>Scheduled Practices</h2>
    <table class="schedule-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Facility</th>
                <th>Date</th>
                <th>Time</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($schedules)): ?>
                <?php foreach ($schedules as $row): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['facility']) ?></td>
                        <td><?= htmlspecialchars($row['session_date']) ?></td>
                        <td><?= htmlspecialchars($row['session_time']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td>
                            <button class="edit-btn" 
                                    data-id="<?= $row['id'] ?>"
                                    data-facility="<?= htmlspecialchars($row['facility'], ENT_QUOTES) ?>"
                                    data-date="<?= $row['session_date'] ?>"
                                    data-time="<?= $row['session_time'] ?>"
                                    data-description="<?= htmlspecialchars($row['description'], ENT_QUOTES) ?>">&#9998;</button>         
                            <a href="?delete=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Delete this record?');">&times;</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="no-records">No schedules found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>




