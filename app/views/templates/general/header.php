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

        $stmt = $db->prepare("SELECT type, fname, lname FROM user WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check for pending facility bookings with incomplete payment
        // Only show this on the facility reservation page
        $isFacilityReservationPage = strpos($currentPage, '/facility-reservation') !== false;
        
        if ($isFacilityReservationPage && !$isPaymentPage) {
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
            <?php
                $isFacilityPage = strpos($currentPage, '/facility-reservation') !== false;
                $facilityActiveClass = $isFacilityPage ? ' active-portal' : '';
            ?>
            
            <?php $userType = $user['type'] ?? null; ?>
            <?php if ($userType !== 'SPT' && $userType !== 'EQP' && $userType !== 'REG' && $userType !== 'COACH'): ?>
            <a href="/uoc-sports/public/facility-reservation" id="nav-res" class="btn-primary<?php echo $facilityActiveClass; ?>">
                Facility Reservation
            </a>
            <?php endif; ?>

            <?php
                $isResultsPage = strpos($currentPage, '/results') !== false;
                $resultsActiveClass = $isResultsPage ? ' active-portal' : '';
            ?>
            <a href="/uoc-sports/public/results" id="nav-results" class="btn-primary<?php echo $resultsActiveClass; ?>">
                Match Results
            </a>

            <?php
                if ($user) {
                    // Detect current portal page for active-portal styling
                    $isCaptainPage = strpos($currentPage, '/captain') !== false;
                    $isStudentPage = strpos($currentPage, '/student') !== false;
                    $isEquipmentPage = strpos($currentPage, '/equipment-manager') !== false;
                    $isCoachPage = strpos($currentPage, '/coach') !== false;
                    $isSportManagerPage = strpos($currentPage, '/sport-manager') !== false;
                    $isRegistrarPage = strpos($currentPage, '/registrar') !== false;
                    $isAdminPage = strpos($currentPage, '/admin') !== false;

                    // User-specific links
                    switch ($user['type']) {
                        case 'STUDENT':
                            $activeClass = $isStudentPage ? ' active-portal' : '';
                            echo '<a href="/uoc-sports/public/student/" id="nav-student-portal" class="user-type btn-primary'.$activeClass.'">Student Portal</a>';
                            break;

                        case 'CAPTAIN':
                            $studentActive = $isStudentPage ? ' active-portal' : '';
                            $captainActive = $isCaptainPage ? ' active-portal' : '';
                            echo '<a href="/uoc-sports/public/student/" id="nav-student-portal" class="user-type btn-primary'.$studentActive.'">Student Portal</a>';
                            echo '<a href="/uoc-sports/public/captain/" id="nav-captain-portal" class="user-type btn-primary'.$captainActive.'">Captain</a>';
                            break;

                        case 'EQP':
                            $activeClass = $isEquipmentPage ? ' active-portal' : '';
                            echo '<a href="/uoc-sports/public/equipment-manager/" id="nav-eqp-portal" class="user-type btn-primary'.$activeClass.'">Eq. Manager</a>';
                            break;

                        case 'COACH':
                            $activeClass = $isCoachPage ? ' active-portal' : '';
                            echo '<a href="/uoc-sports/public/coach/" id="nav-coach-portal" class="user-type btn-primary'.$activeClass.'">Coach</a>';
                            break;

                        case 'SPT':
                            $activeClass = $isSportManagerPage ? ' active-portal' : '';
                            echo '<a href="/uoc-sports/public/sport-manager/" id="nav-spt-portal" class="user-type btn-primary'.$activeClass.'">Sp. Manager</a>';
                            break;

                        case 'REG':
                            $activeClass = $isRegistrarPage ? ' active-portal' : '';
                            echo '<a href="/uoc-sports/public/registrar//" id="nav-reg-portal" class="user-type btn-primary'.$activeClass.'">Registrar</a>';
                            break;

                        case 'INSTAFF':
                            $activeClass = $isStudentPage ? ' active-portal' : '';
                            echo '<a href="/uoc-sports/public/student/" id="nav-staff-portal" class="user-type btn-primary'.$activeClass.'">Staff</a>';
                            break;

                        case 'ADMIN':
                            $activeClass = $isAdminPage ? ' active-portal' : '';
                            echo '<a href="/uoc-sports/public/admin-index" id="nav-admin-portal" class="user-type btn-primary'.$activeClass.'">Admin Dashboard</a>';
                            break;
                    }

                    // Profile button
                    $isProfilePage = strpos($currentPage, '/profile') !== false;
                    $profileActiveClass = $isProfilePage ? ' active-portal' : '';
                    echo '
                        <a href="/uoc-sports/public/profile" class="profile-link" id="nav-pro">
                            <div class="profile-info">
                                <span class="profile-name">' . htmlspecialchars(strtoupper(substr($user['fname'], 0, 1)) . '. ' . $user['lname']) . '</span>
                            </div>
                            <i class="fa-solid fa-circle-user"></i>
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

<?php require_once APP_ROOT . '/app/views/templates/general/secondary-header.php'; ?>

<?php if (false && $pendingBooking): ?>
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

<?php if (isset($_SESSION['message'])): ?>
<div id="toast-overlay" class="toast-overlay">
    <div id="toast-notification" class="toast-notification <?php echo $_SESSION['color'] ?? 'green'; ?>">
        <div class="toast-icon">
            <?php if (($_SESSION['color'] ?? 'green') === 'red'): ?>
                <i class="fas fa-exclamation-circle"></i>
            <?php else: ?>
                <i class="fas fa-check-circle"></i>
            <?php endif; ?>
        </div>
        <div class="toast-content">
            <p class="toast-message"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
        </div>
        <button class="toast-close" onclick="closeToast()"><i class="fas fa-times"></i></button>
    </div>
</div>

<style>
.toast-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(8px);
    z-index: 10001;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease-out forwards;
}

.toast-notification {
    background: white;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    border-radius: 20px;
    padding: 32px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 20px;
    min-width: 400px;
    max-width: 500px;
    transform: scale(0.9);
    animation: popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    border-top: 8px solid;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes popIn {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}

.toast-notification.green { border-top-color: #2e7d32; }
.toast-notification.red { border-top-color: #d32f2f; }

.toast-icon { 
    font-size: 48px; 
    margin-bottom: 10px;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.toast-notification.green .toast-icon { color: #2e7d32; background: #e8f5e9; }
.toast-notification.red .toast-icon { color: #d32f2f; background: #ffebee; }

.toast-content { width: 100%; }
.toast-title { margin: 0 0 12px; font-size: 24px; font-weight: 800; color: #333; }
.toast-message { margin: 0; font-size: 18px; color: #666; line-height: 1.5; }

.toast-close {
    position: absolute;
    top: 20px;
    right: 20px;
    background: none;
    border: none;
    color: #999;
    cursor: pointer;
    font-size: 20px;
    padding: 5px;
    transition: color 0.2s;
}
.toast-close:hover { color: #333; }
</style>

<script>
function closeToast() {
    const overlay = document.getElementById('toast-overlay');
    if (overlay) {
        overlay.style.animation = 'fadeOut 0.3s forwards';
        setTimeout(() => overlay.remove(), 300);
    }
}

// Auto hide after 3 seconds
setTimeout(closeToast, 3000);
</script>

<?php 
    // Clear message after displaying
    unset($_SESSION['message']);
    unset($_SESSION['color']);
?>
<?php endif; ?>

