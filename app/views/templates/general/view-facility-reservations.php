<section class="my-reservations">
    <h2>My Reservations</h2>

    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if (!empty($reservations)): ?>
            <div class="reservation-list">
                <?php foreach ($reservations as $r): ?>
                    <?php $isUnpaid = strtolower($r['payment_status']) !== 'completed'; ?>
                    <div class="reservation-item <?= $isUnpaid ? 'unpaid' : '' ?>">
                        <div class="info">
                            <h3><?= htmlspecialchars($r['facility_name']) ?></h3>
                            <p>
                                <strong><?= htmlspecialchars($r['date']) ?></strong> |
                                <?= htmlspecialchars($r['start_time']) ?> - <?= htmlspecialchars($r['end_time']) ?> |
                                <span><?= htmlspecialchars($r['purpose']) ?></span>
                            </p>
                        </div>

                        <div class="status">
                            <span class="status-tag <?= $isUnpaid ? 'pending' : 'paid' ?>">
                                <?= htmlspecialchars($r['payment_status']) ?>
                            </span>
                            <div class="actions">
                                <?php if ($isUnpaid): ?>
                                    <button class="btn pay-btn">Pay</button>
                                <?php endif; ?>
                                <button class="btn cancel-btn">Cancel</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="empty-msg">You have no reservations yet.</p>
        <?php endif; ?>
    <?php else: ?>
        <p class="empty-msg">Please log in to view your reservations.</p>
    <?php endif; ?>
</section>
