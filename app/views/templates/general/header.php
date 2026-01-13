<?php 
    // Always define user
    $user = null;
    $pendingBooking = null;

    // Check if we're on the payment page - don't show modal there
    $currentPage = $_SERVER['REQUEST_URI'] ?? '';
    $isPaymentPage = strpos($currentPage, '/payment') !== false;

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        require_once APP_ROOT.'/core/Database.php';
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT type FROM user WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check for pending facility bookings with incomplete payment
        // Skip this check if already on the payment page
        if (!$isPaymentPage) {
            $pendingStmt = $db->prepare("
                SELECT fb.booking_id, fb.date, fb.slot, fr.facility_name
                FROM `facility-booking` fb
                INNER JOIN facility_rates fr ON fb.facility_id = fr.id
                WHERE fb.user_id = :user_id 
                AND fb.payment_status = 'INCOMPLETE'
                AND fb.status = 'BOOKED'
                ORDER BY fb.date ASC
                LIMIT 1
            ");
            $pendingStmt->execute(['user_id' => $user_id]);
            $pendingBooking = $pendingStmt->fetch(PDO::FETCH_ASSOC);
        }
    }
?>
<header>
    <nav>
        <a href="/uoc-sports/public/" class="logo">
        <img src="/uoc-sports/public/images/uoc-logo.png" alt="">
        <span>UOC Sports E-Portal</span>
        </a>
        <i class="fa-solid fa-bars" id="menu-btn" onclick="toggleMenu()"></i>
        <div class="nav-links" id="nav-links">

            <a href="/uoc-sports/public/news">News</a>
            <a href="/uoc-sports/public/#contact">Contact Us</a>

            <a href="/uoc-sports/public/facility-reservation" id="nav-res" class="btn-secondary">
                Facility Reservation
            </a>

            <?php
                if ($user) {
                    // User-specific links
                    switch ($user['type']) {
                        case 'STUDENT':
                            echo '<a href="/uoc-sports/public/student/" id="user_type" class="user-type btn-primary">Student Portal</a>';
                            break;

                        case 'CAPTAIN':
                            echo '<a href="/uoc-sports/public/student/" id="user_type" class="user-type btn-primary">Student Portal</a>';
                            echo '<a href="/uoc-sports/public/captain/" id="user_type" class="user-type btn-primary">Captain</a>';
                            break;

                        case 'EQP':
                            echo '<a href="/uoc-sports/public/equipment-manager/" id="user_type" class="user-type btn-primary">Eq. Manager</a>';
                            break;

                        case 'COACH':
                            echo '<a href="/uoc-sports/public/coach/" id="user_type" class="user-type btn-primary">Coach</a>';
                            break;

                        case 'SPT':
                            echo '<a href="/uoc-sports/public/sport-manager/" id="user_type" class="user-type btn-primary">Sp. Manager</a>';
                            break;

                        case 'REG':
                            echo '<a href="/uoc-sports/public/registrar//" id="user_type" class="user-type btn-primary">Registrar</a>';
                            break;

                        case 'INSTAFF':
                            echo '<a href="/uoc-sports/public/student/" id="user_type" class="user-type btn-primary">Staff</a>';
                            break;

                        case 'ADMIN':
                            echo '<a href="/uoc-sports/public/admin-index" id="user_type" class="user-type btn-primary">Admin Dashboard</a>';
                            break;
                    }

                    // Profile button
                    echo '
                        <a href="/uoc-sports/public/profile" class="btn-primary" id="nav-pro">
                            Profile <i class="fa-solid fa-circle-user"></i>
                        </a>
                    ';

                } else {
                    // Sign in button
                    echo '<a href="/uoc-sports/public/sign-in" class="btn-primary">Sign In</a>';
                }
            ?>

        </div>
    </nav>
</header>

<?php if ($pendingBooking): ?>
<!-- Pending Payment Reminder Modal -->
<div id="pendingPaymentModal" class="pending-payment-modal">
    <div class="pending-payment-content">
        <div class="pending-payment-icon">
            <i class="fa-solid fa-exclamation-triangle"></i>
        </div>
        <h3>Incomplete Booking</h3>
        <p>You have an unfinished facility reservation that requires payment:</p>
        <div class="pending-booking-details">
            <p><strong>Facility:</strong> <?php echo htmlspecialchars($pendingBooking['facility_name']); ?></p>
            <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($pendingBooking['date'])); ?></p>
            <p><strong>Slot:</strong> <?php echo $pendingBooking['slot']; ?></p>
        </div>
        <p class="warning-text">Your booking is not confirmed until payment is completed.</p>
        <div class="pending-payment-actions">
            <button class="btn-pay-now" onclick="proceedToPayment('<?php echo $pendingBooking['booking_id']; ?>')">
                <i class="fa-solid fa-credit-card"></i> Complete Payment
            </button>
            <button class="btn-dismiss" onclick="dismissPendingModal()">Remind Me Later</button>
        </div>
    </div>
</div>

<style>
.pending-payment-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.pending-payment-content {
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    border-radius: 16px;
    padding: 32px;
    max-width: 420px;
    width: 90%;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.pending-payment-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.pending-payment-icon i {
    font-size: 32px;
    color: white;
}

.pending-payment-content h3 {
    color: #333;
    margin-bottom: 12px;
    font-size: 1.5rem;
}

.pending-payment-content p {
    color: #666;
    margin-bottom: 16px;
    line-height: 1.5;
}

.pending-booking-details {
    background: #f0f4f8;
    border-radius: 10px;
    padding: 16px;
    margin: 16px 0;
    text-align: left;
}

.pending-booking-details p {
    margin: 8px 0;
    color: #444;
}

.pending-booking-details strong {
    color: #5e2d91;
}

.warning-text {
    color: #e65100 !important;
    font-weight: 500;
    font-size: 0.9rem;
}

.pending-payment-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 20px;
}

.btn-pay-now {
    background: linear-gradient(135deg, #5e2d91 0%, #7b3fa0 100%);
    color: white;
    border: none;
    padding: 14px 28px;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-pay-now:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(94, 45, 145, 0.3);
}

.btn-dismiss {
    background: transparent;
    color: #888;
    border: 1px solid #ddd;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-dismiss:hover {
    background: #f5f5f5;
    color: #666;
}
</style>

<script>
function proceedToPayment(bookingId) {
    window.location.href = '/uoc-sports/public/payment?booking_id=' + bookingId;
}

function dismissPendingModal() {
    const modal = document.getElementById('pendingPaymentModal');
    if (modal) {
        modal.style.animation = 'fadeOut 0.3s ease forwards';
        setTimeout(() => modal.remove(), 300);
    }
}

// Add fadeOut animation
const style = document.createElement('style');
style.textContent = '@keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }';
document.head.appendChild(style);
</script>
<?php endif; ?>

<script>
    function toggleMenu() {
        const nav = document.getElementById('nav-links');
        nav.classList.toggle('show');
    }
</script>

