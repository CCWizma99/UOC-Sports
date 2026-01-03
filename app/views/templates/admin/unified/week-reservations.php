<?php
// Expect $reservations data to be passed from the controller
// Default to empty array if not set
$reservations = $reservations ?? [];
?>
<section id="week-reservations">
    <h2>Reservations (This Week & Next Week)</h2>
    
    <?php if (!empty($reservations)) : ?>
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>User Name</th>
                    <th>Facility Name</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Purpose</th>
                    <th>Status</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r["booking_id"]) ?></td>
                        <td><?= htmlspecialchars($r["user_name"]) ?></td>
                        <td><?= htmlspecialchars($r["facility_name"]) ?></td>
                        <td><?= htmlspecialchars($r["date"]) ?></td>
                        <td><?= htmlspecialchars($r["start_time"]) ?></td>
                        <td><?= htmlspecialchars($r["end_time"]) ?></td>
                        <td><?= htmlspecialchars($r["purpose"]) ?></td>
                        <td><?= htmlspecialchars($r["status"]) ?></td>
                        <td><?= htmlspecialchars($r["payment_status"]) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-data">No reservations found for this week or next week.</p>
    <?php endif; ?>
</section>