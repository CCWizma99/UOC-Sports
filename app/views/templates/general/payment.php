<div class="container">
        <div class="header">
            <h1>Complete Your Booking Payment</h1>
            <p>Please follow the instructions below and upload your payment proof. Your booking will be confirmed after admin verification.</p>
        </div>

        <!-- Top Sections: Booking Summary (Left) & Payment Instructions (Right) -->
        <div class="top-sections">
            <!-- Booking Summary Section -->
            <div class="section">
                <div class="card">
                    <h2 class="section-title">Booking Summary</h2>
                        <div class="booking-summary">
                            <div class="summary-row">
                                <span class="summary-label">Booking ID:</span>
                                <span class="summary-value"><?php echo $booking['booking_id']; ?></span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Facility Name:</span>
                                <span class="summary-value"><?php echo $booking['facility_name']; ?></span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Date:</span>
                                <span class="summary-value"><?php echo $booking['date']; ?></span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Slot:</span>
                                <span class="summary-value"><?php echo $booking['time_range']; ?></span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Purpose:</span>
                                <span class="summary-value"><?php echo htmlspecialchars($booking['purpose']); ?></span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Name:</span>
                                <span class="summary-value"><?php echo $booking['user_name']; ?></span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Amount:</span>
                                <span class="summary-value amount">Rs. <?php echo number_format($booking['amount'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Instructions Section -->
                <div class="section">
                    <div class="card">
                        <h2 class="section-title">Payment Instructions</h2>
                        <div class="payment-instructions">
                            <h3>Bank / Account Details</h3>
                            <div class="bank-details">
                                <p><strong>Bank Branch:</strong> Peoples Bank Thimbirigasyaya</p>
                                <p><strong>A/C No.:</strong> 607179200008</p>
                                <p><strong>A/C Name:</strong> Sports Promotion Fund of the University of Colombo</p>
                            </div>

                            <div class="steps">
                                <h3>Payment Process</h3>
                                <ol>
                                    <li>Make payment using bank deposit or online transfer.</li>
                                    
                                    <li>Take a clear photo or PDF of the payment slip.</li>
                                    <li>Upload the slip below and submit.</li>
                                </ol>
                            </div>

                            <div class="notice">
                                <span class="notice-icon">⚠️</span>
                                <span class="notice-text">Payments are subject to admin verification. Bookings are confirmed only after approval.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Section: Payment Proof Form (Centered) -->
            <div class="bottom-section">
                <div class="section">
                    <div class="card">
                        <h2 class="section-title">Choose Payment Method</h2>
                        
                        <!-- Tabs -->
                        <div class="payment-tabs">
                            <button class="tab-btn active" onclick="switchTab('online')">Pay Online (PayHere)</button>
                            <button class="tab-btn" onclick="switchTab('slip')">Upload Payment Slip</button>
                        </div>

                        <!-- Tab 1: PayHere Form -->
                        <div id="tab-online" class="tab-content active">
                            <div class="info-box">
                                <p>You will be redirected to the PayHere payment gateway for secure payment.</p>
                            </div>
                            
                            <form method="post" action="<?php echo $payhere['url']; ?>">
                                <input type="hidden" name="merchant_id" value="<?php echo $payhere['merchant_id']; ?>">
                                <input type="hidden" name="return_url" value="http://localhost/uoc-sports/public/payment/success">
                                <input type="hidden" name="cancel_url" value="http://localhost/uoc-sports/public/payment/cancel">
                                <input type="hidden" name="notify_url" value="http://localhost/uoc-sports/public/payment/notify">  
                                <input type="hidden" name="order_id" value="<?php echo $booking['booking_id']; ?>">
                                <input type="hidden" name="items" value="<?php echo $booking['facility_name']; ?> Reservation">
                                <input type="hidden" name="currency" value="LKR">
                                <input type="hidden" name="amount" value="<?php echo $payhere['amount_formatted']; ?>">  
                                <input type="hidden" name="first_name" value="<?php echo explode(' ', $booking['user_name'])[0]; ?>">
                                <input type="hidden" name="last_name" value="<?php echo explode(' ', $booking['user_name'])[1] ?? ''; ?>">
                                <input type="hidden" name="email" value="<?php echo $booking['user_email'] ?? 'test@example.com'; ?>">
                                <input type="hidden" name="phone" value="<?php echo $booking['contact_no'] ?? '0771234567'; ?>">
                                <input type="hidden" name="address" value="No.1, University of Colombo">
                                <input type="hidden" name="city" value="Colombo">
                                <input type="hidden" name="country" value="Sri Lanka">
                                <input type="hidden" name="hash" value="<?php echo $payhere['hash']; ?>">

                                <button type="submit" class="btn-submit">Pay with PayHere</button>
                            </form>
                        </div>

                        <!-- Tab 2: Upload Slip Form -->
                        <div id="tab-slip" class="tab-content">
                            <form id="paymentForm">
                                <div class="form-group">
                                    <label>Payment Method <span class="required">*</span></label>
                                    <div class="radio-group">
                                        <div class="radio-option">
                                            <input type="radio" id="bankDeposit" name="paymentMethod" value="Bank Deposit" required checked>
                                            <label for="bankDeposit">Bank Deposit</label>
                                        </div>
                                        <div class="radio-option">
                                            <input type="radio" id="onlineTransfer" name="paymentMethod" value="Online Transfer">
                                            <label for="onlineTransfer">Online Transfer</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="referenceNumber">Reference Number <span class="required">*</span></label>
                                    <input type="text" id="referenceNumber" name="referenceNumber" required placeholder="Enter transaction reference number">
                                </div>

                                <div class="form-group">
                                    <label for="paymentSlip">Upload Payment Slip <span class="required">*</span></label>
                                    <div class="file-upload">
                                        <input type="file" id="paymentSlip" name="paymentSlip" accept=".jpg,.jpeg,.png,.pdf" required>
                                        <label for="paymentSlip" class="file-upload-label">
                                            <span>📎 Click to upload payment slip (JPG, PNG, or PDF)</span>
                                        </label>
                                        <div class="file-name" id="fileName"></div>
                                    </div>
                                </div>

                                <button type="submit" class="btn-submit">Submit Payment Proof</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>