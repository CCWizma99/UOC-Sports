<?php
// Toggle this to true if you want demo data instead of DB data
$override_db = true;

$reservations = [];

if ($override_db) {
    // Hardcoded demo data
    $reservations = [
        [
            'booking_id' => 1,
            'user_id' => 101,
            'facility_id' => 5,
            'date' => '2025-08-18',
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'purpose' => 'Practice',
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ],
        [
            'booking_id' => 2,
            'user_id' => 102,
            'facility_id' => 3,
            'date' => '2025-08-22',
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
            'purpose' => 'Match',
            'status' => 'pending',
            'payment_status' => 'unpaid'
        ]
    ];
} else {
    try {
        require '../../../../config/config.php';
        require '../../../../core/Database.php';

        $db = Database::getConnection();

        // SQL: Get reservations for this week and next week
        $query = "
            SELECT booking_id, user_id, facility_id, date, start_time, end_time, purpose, status, payment_status
            FROM `facility-booking`
            WHERE YEARWEEK(date, 1) = YEARWEEK(CURDATE(), 1)
               OR YEARWEEK(date, 1) = YEARWEEK(CURDATE(), 1) + 1
            ORDER BY date, start_time
        ";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        echo "<p>Error fetching reservations: " . htmlspecialchars($e->getMessage()) . "</p>";
        exit;
    }
}
?>
<section id="week-reservations">
    <h2>Reservations (This Week & Next Week)</h2>
    
    <?php if (!empty($reservations)) : ?>
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>User ID</th>
                    <th>Facility ID</th>
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
                        <td><?= htmlspecialchars($r["user_id"]) ?></td>
                        <td><?= htmlspecialchars($r["facility_id"]) ?></td>
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