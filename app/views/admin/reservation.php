<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Details | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/reservation-details.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
    </style>
</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';
?>

<section id="reservation-details">
    <h2>Reservation Details</h2>
    
    <div id="loading-message">
        <p>Loading reservation details...</p>
    </div>

    <div id="error-message" style="display: none;">
        <p class="error-text"></p>
    </div>

    <div id="details-container" style="display: none;">
        <div class="details-card">
            <div class="card-header">
                <h3>Booking Information</h3>
                <span class="status-badge" id="status-badge"></span>
            </div>

            <div class="details-grid">
                <div class="detail-item">
                    <label>Booking ID:</label>
                    <span id="booking-id"></span>
                </div>
                <div class="detail-item">
                    <label>Date:</label>
                    <span id="date"></span>
                </div>
                <div class="detail-item">
                    <label>Time Slot:</label>
                    <span id="time-slot"></span>
                </div>
                <div class="detail-item">
                    <label>Facility:</label>
                    <span id="facility"></span>
                </div>
                <div class="detail-item">
                    <label>User Name:</label>
                    <span id="user-name"></span>
                </div>
                <div class="detail-item">
                    <label>User Email:</label>
                    <span id="user-email"></span>
                </div>
                <div class="detail-item">
                    <label>User Type:</label>
                    <span id="user-type"></span>
                </div>
                <div class="detail-item">
                    <label>Payment Status:</label>
                    <span id="payment-status"></span>
                </div>
                <div class="detail-item full-width">
                    <label>Purpose:</label>
                    <span id="purpose"></span>
                </div>
                <div class="detail-item full-width" id="rejection-reason-container" style="display: none;">
                    <label>Rejection Reason:</label>
                    <span id="rejection-reason" class="rejection-text"></span>
                </div>
            </div>

            <div class="action-buttons" id="action-buttons">
                <button class="btn-message" id="message-btn">
                    <i class="fa-solid fa-envelope"></i> Send Message to Customer
                </button>
            </div>
        </div>

        <!-- Weekly Reservations Section -->
        <div class="weekly-reservations">
            <h3>Reservations This Week</h3>
            <div id="weekly-loading">
                <p>Loading weekly reservations...</p>
            </div>
            <div id="weekly-table-container" style="display: none;">
                <table class="weekly-table">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Facility</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Payment</th>
                        </tr>
                    </thead>
                    <tbody id="weekly-tbody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Message Modal -->
<div id="message-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-envelope"></i> Send Message to Customer</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div class="recipient-info">
                <p><strong>To:</strong> <span id="recipient-name"></span> (<span id="recipient-email"></span>)</p>
                <p><strong>Regarding:</strong> Booking <span id="modal-booking-id"></span></p>
            </div>
            <label for="message-subject">Subject:</label>
            <input type="text" id="message-subject" placeholder="Enter message subject..." />
            <label for="message-body">Message:</label>
            <textarea id="message-body" rows="6" placeholder="Type your message to the customer..."></textarea>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" id="cancel-message">Cancel</button>
            <button class="btn-send" id="send-message"><i class="fa-solid fa-paper-plane"></i> Send Message</button>
        </div>
    </div>
</div>

<style>
.btn-message {
    background: linear-gradient(135deg, #5e2d91 0%, #7b3fa0 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-message:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(94, 45, 145, 0.3);
}

.recipient-info {
    background: #f5f5f5;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 16px;
}

.recipient-info p {
    margin: 4px 0;
    color: #555;
}

#message-subject {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 0.95rem;
    margin-bottom: 12px;
}

#message-body {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 0.95rem;
    resize: vertical;
    min-height: 120px;
}

.btn-send {
    background: linear-gradient(135deg, #5e2d91 0%, #7b3fa0 100%);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
}

.btn-send:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(94, 45, 145, 0.3);
}
</style>

<?php require '../app/views/templates/admin/footer.php'; ?>

<script>
const urlParams = new URLSearchParams(window.location.search);
const bookingId = urlParams.get('id');

if (!bookingId) {
    document.getElementById('loading-message').style.display = 'none';
    document.getElementById('error-message').style.display = 'block';
    document.querySelector('.error-text').textContent = 'No booking ID provided.';
} else {
    loadReservationDetails();
}

function loadReservationDetails() {
    fetch(`/uoc-sports/public/api/reservation-details.php?id=${bookingId}`)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }

            // Hide loading, show details
            document.getElementById('loading-message').style.display = 'none';
            document.getElementById('details-container').style.display = 'block';

            // Populate details
            document.getElementById('booking-id').textContent = data.booking_id;
            document.getElementById('date').textContent = data.date;
            document.getElementById('time-slot').textContent = data.time_range;
            document.getElementById('facility').textContent = data.facility_name;
            document.getElementById('user-name').textContent = data.user_name;
            document.getElementById('user-email').textContent = data.user_email;
            document.getElementById('user-type').textContent = data.user_type;
            document.getElementById('payment-status').textContent = data.payment_status;
            document.getElementById('purpose').textContent = data.purpose;

            // Status badge
            const statusBadge = document.getElementById('status-badge');
            statusBadge.textContent = data.status;
            statusBadge.className = 'status-badge status-' + data.status.toLowerCase();

            // Show rejection reason if rejected
            if (data.status === 'REJECTED' && data.rejection_reason) {
                document.getElementById('rejection-reason-container').style.display = 'flex';
                document.getElementById('rejection-reason').textContent = data.rejection_reason;
            }

            // Store data for message modal
            currentReservationData = data;

            // Always show action buttons (message button available for all statuses)
            document.getElementById('action-buttons').style.display = 'flex';

            // Load weekly reservations
            loadWeeklyReservations(data.date);
        })
        .catch(err => {
            document.getElementById('loading-message').style.display = 'none';
            document.getElementById('error-message').style.display = 'block';
            document.querySelector('.error-text').textContent = err.message;
        });
}

function loadWeeklyReservations(date) {
    fetch(`/uoc-sports/public/api/week-reservations-api.php?date=${date}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('weekly-loading').style.display = 'none';
            document.getElementById('weekly-table-container').style.display = 'block';

            const tbody = document.getElementById('weekly-tbody');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">No reservations found for this week.</td></tr>';
                return;
            }

            data.forEach(r => {
                const row = document.createElement('tr');
                if (r.booking_id === bookingId) {
                    row.classList.add('current-reservation');
                }

                row.innerHTML = `
                    <td>${r.booking_id}</td>
                    <td>${r.date}</td>
                    <td>${r.start_time}</td>
                    <td>${r.facility_name}</td>
                    <td>${r.user_name}</td>
                    <td><span class="status-badge status-${r.status.toLowerCase()}">${r.status}</span></td>
                    <td>${r.payment_status}</td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(err => {
            console.error('Error loading weekly reservations:', err);
        });
}

// Store reservation data for message modal
let currentReservationData = null;

// Message button - open modal
document.getElementById('message-btn').addEventListener('click', () => {
    if (currentReservationData) {
        document.getElementById('recipient-name').textContent = currentReservationData.user_name;
        document.getElementById('recipient-email').textContent = currentReservationData.user_email;
        document.getElementById('modal-booking-id').textContent = currentReservationData.booking_id;
        document.getElementById('message-subject').value = `Regarding your facility booking ${currentReservationData.booking_id}`;
    }
    document.getElementById('message-modal').style.display = 'flex';
});

// Close modal
document.querySelector('.close-modal').addEventListener('click', () => {
    document.getElementById('message-modal').style.display = 'none';
});

document.getElementById('cancel-message').addEventListener('click', () => {
    document.getElementById('message-modal').style.display = 'none';
});

// Send message (placeholder - just shows confirmation for now)
document.getElementById('send-message').addEventListener('click', () => {
    const subject = document.getElementById('message-subject').value.trim();
    const message = document.getElementById('message-body').value.trim();

    if (!subject) {
        alert('Please enter a subject.');
        return;
    }

    if (!message) {
        alert('Please enter a message.');
        return;
    }

    // For now, just show confirmation - will be connected to DB later
    alert(`Message queued for sending!\n\nTo: ${currentReservationData.user_email}\nSubject: ${subject}\n\nNote: Message storage will be implemented with upcoming DB changes.`);
    
    document.getElementById('message-modal').style.display = 'none';
    document.getElementById('message-subject').value = '';
    document.getElementById('message-body').value = '';
});

// Set active sidebar item
var currentPage = document.getElementById("sidebar-reservations");
currentPage.classList.add("active");
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</body>
</html>
