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
                                <span class="summary-value">BK-2024-001</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Facility Name:</span>
                                <span class="summary-value">Tennis Court A</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Date:</span>
                                <span class="summary-value">2024-01-15</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Slot:</span>
                                <span class="summary-value">08:00 AM - 10:00 AM</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Purpose:</span>
                                <span class="summary-value">Practice Session</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Name:</span>
                                <span class="summary-value">John Doe</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Amount:</span>
                                <span class="summary-value amount">Rs. 2,500.00</span>
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
                        <h2 class="section-title">Payment Proof Form</h2>
                        <form id="paymentForm">
                            <div class="form-group">
                                <label>Payment Method <span class="required">*</span></label>
                                <div class="radio-group">
                                    <div class="radio-option">
                                        <input type="radio" id="bankDeposit" name="paymentMethod" value="Bank Deposit" required>
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

                            <button type="submit" class="btn-submit">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>