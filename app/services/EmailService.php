<?php

class EmailService {
    private $apiKey;
    private $senderEmail;
    private $senderName;

    public function __construct() {
        global $smtpKey, $senderEmail;
        $this->apiKey = $smtpKey;
        $this->senderEmail = $senderEmail ?: "noreply@uoc-sports.lk"; // Fallback
        $this->senderName = "UOC Sports System";
    }

    /**
     * Send email using Brevo API
     * 
     * @param string $toEmail Recipient email
     * @param string $toName Recipient name
     * @param string $subject Email subject
     * @param string $htmlContent HTML content of email
     * @return array Response with status and message
     */
    public function sendEmail($toEmail, $toName, $subject, $htmlContent) {
        if (empty($this->apiKey) || empty($this->senderEmail)) {
            return [
                'status' => 'error',
                'message' => 'Email configuration missing: API key or sender email not set.'
            ];
        }

        $payload = [
            "sender" => [
                "name" => $this->senderName,
                "email" => $this->senderEmail
            ],
            "to" => [
                [
                    "email" => $toEmail,
                    "name" => $toName
                ]
            ],
            "subject" => $subject,
            "htmlContent" => $htmlContent
        ];

        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
            'api-key: ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            return [
                'status' => 'error',
                'message' => "Email sending failed (cURL error: $error)"
            ];
        }

        $respData = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300 && isset($respData['messageId'])) {
            return [
                'status' => 'success',
                'message' => 'Email sent successfully.',
                'messageId' => $respData['messageId']
            ];
        } else {
            $errorMsg = $respData['message'] ?? 'Unknown Brevo error';
            return [
                'status' => 'error',
                'message' => "Email sending failed. Brevo error: $errorMsg"
            ];
        }
    }

    /**
     * Send tournament invitation email
     * 
     * @param string $email Recipient email
     * @param string $recipientName Recipient name (institution)
     * @param array $tournamentDetails Tournament information
     * @return array Response with status and message
     */
    public function sendTournamentInvitation($email, $recipientName, $tournamentDetails) {
        $tournamentName = htmlspecialchars($tournamentDetails['tournament_name'] ?? 'Tournament');
        $sportName = htmlspecialchars($tournamentDetails['sport_name'] ?? 'Sport');
        $startDate = date('F j, Y', strtotime($tournamentDetails['start_date'] ?? ''));
        $endDate = date('F j, Y', strtotime($tournamentDetails['end_date'] ?? ''));

        $subject = "Invitation to Participate in $tournamentName";
        
        $htmlContent = "
            <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
                        .header { background-color: #5e2d91; color: white; padding: 20px; text-align: center; }
                        .content { padding: 20px; }
                        .details { background-color: #f4f4f4; padding: 15px; margin: 20px 0; border-left: 4px solid #5e2d91; }
                        .details h3 { margin-top: 0; color: #5e2d91; }
                        .footer { background-color: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; color: #666; }
                        .btn { display: inline-block; padding: 12px 24px; background-color: #5e2d91; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
                    </style>
                </head>
                <body>
                    <div class='header'>
                        <h1>University of Colombo Sports</h1>
                        <p>Tournament Invitation</p>
                    </div>
                    <div class='content'>
                        <h2>Dear $recipientName,</h2>
                        <p>We are pleased to invite your institution to participate in the upcoming <strong>$tournamentName</strong>.</p>
                        
                        <div class='details'>
                            <h3>Tournament Details</h3>
                            <p><strong>Tournament Name:</strong> $tournamentName</p>
                            <p><strong>Sport:</strong> $sportName</p>
                            <p><strong>Start Date:</strong> $startDate</p>
                            <p><strong>End Date:</strong> $endDate</p>
                        </div>
                        
                        <p>This tournament promises to be an exciting event, bringing together talented athletes from various institutions. We would be honored to have your team participate.</p>
                        
                        <p>Please confirm your participation at your earliest convenience by replying to this email.</p>
                        
                        <p>We look forward to your positive response and to hosting your team at the University of Colombo.</p>
                        
                        <p>Best regards,<br>
                        <strong>University of Colombo Sports Department</strong></p>
                    </div>
                    <div class='footer'>
                        <p>This is an automated email from the UOC Sports Management System.</p>
                        <p>University of Colombo | Sports Department</p>
                    </div>
                </body>
            </html>
        ";

        return $this->sendEmail($email, $recipientName, $subject, $htmlContent);
    }

    /**
     * Send temporary password email (for user registration)
     * 
     * @param string $email User email
     * @param string $name User name
     * @param string $tempPassword Temporary password
     * @return array Response with status and message
     */
    public function sendTempPasswordEmail($email, $name, $tempPassword) {
        $subject = "Your Temporary Password - UOC Sports System";
        
        $htmlContent = "
            <html>
                <body style='font-family: Arial, sans-serif; color: #333;'>
                    <h3>Hello $name,</h3>
                    <p>Your temporary password is:</p>
                    <p style='font-size:18px; font-weight:bold; color:#007bff;'>$tempPassword</p>
                    <p>Please change it after your first login.</p>
                    <hr>
                    <small>This is an automated email from the UOC Sports System.</small>
                </body>
            </html>
        ";

        return $this->sendEmail($email, $name, $subject, $htmlContent);
    }

    /**
     * Send inquiry confirmation email
     * 
     * @param string $email User email
     * @param string $subject Inquiry subject
     * @param string $inquiryId Generated inquiry ID
     * @return array Response with status and message
     */
    public function sendInquiryConfirmation($email, $subject, $inquiryId) {
        $emailSubject = "Inquiry Received - UOC Sports Department";
        
        $htmlContent = "
            <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
                        .header { background-color: #5e2d91; color: white; padding: 20px; text-align: center; }
                        .content { padding: 20px; }
                        .inquiry-box { background-color: #f4f4f4; padding: 15px; margin: 20px 0; border-left: 4px solid #5e2d91; }
                        .inquiry-box h3 { margin-top: 0; color: #5e2d91; }
                        .footer { background-color: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; color: #666; }
                        .inquiry-id { font-weight: bold; color: #5e2d91; font-size: 16px; }
                    </style>
                </head>
                <body>
                    <div class='header'>
                        <h1>University of Colombo Sports</h1>
                        <p>Physical Education Department</p>
                    </div>
                    <div class='content'>
                        <h2>Thank you for contacting us!</h2>
                        <p>We have received your inquiry and our team will review it shortly.</p>
                        
                        <div class='inquiry-box'>
                            <h3>Inquiry Details</h3>
                            <p><strong>Reference ID:</strong> <span class='inquiry-id'>$inquiryId</span></p>
                            <p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
                            <p><strong>Date:</strong> " . date('F j, Y') . "</p>
                        </div>
                        
                        <p>Our team typically responds within 2-3 business days. You can use the reference ID above to track your inquiry.</p>
                        
                        <p>If you have any urgent matters, please contact us directly at:</p>
                        <ul>
                            <li><strong>Email:</strong> info@ped.cmb.ac.lk</li>
                            <li><strong>Phone:</strong> +94 112 502 405</li>
                        </ul>
                        
                        <p>Best regards,<br>
                        <strong>University of Colombo Sports Department</strong></p>
                    </div>
                    <div class='footer'>
                        <p>This is an automated confirmation email from the UOC Sports E-Portal.</p>
                        <p>University of Colombo | Physical Education Department</p>
                        <p>94, Cumaratunga Munidasa Mw, Colombo 03, Sri Lanka</p>
                    </div>
                </body>
            </html>
        ";

        return $this->sendEmail($email, 'Valued User', $emailSubject, $htmlContent);
    }

    /**
     * Send email to captain when they are granted permission to add match results
     *
     * @param string $captainEmail  Captain's email address
     * @param string $captainName   Captain's full name
     * @param array  $tournament    Tournament details (tournament_name, sport_name, start_date, end_date)
     * @return array Response with status and message
     */
    public function sendCaptainPermissionEmail($captainEmail, $captainName, $tournament) {
        $tournamentName = htmlspecialchars($tournament['tournament_name'] ?? 'Tournament');
        $sportName      = htmlspecialchars($tournament['sport_name'] ?? 'Sport');
        $startDate      = !empty($tournament['start_date']) ? date('F j, Y', strtotime($tournament['start_date'])) : 'N/A';
        $endDate        = !empty($tournament['end_date'])   ? date('F j, Y', strtotime($tournament['end_date']))   : 'N/A';
        $portalUrl      = 'http://localhost/uoc-sports/public/captain/add-result';

        $subject = "Action Required: You Can Now Add Match Results — $tournamentName";

        $htmlContent = "
            <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 0; }
                        .header { background: linear-gradient(135deg, #5e2d91, #8b5cf6); color: white; padding: 30px 20px; text-align: center; }
                        .header h1 { margin: 0; font-size: 24px; }
                        .header p  { margin: 5px 0 0; opacity: 0.85; font-size: 14px; }
                        .content { padding: 30px 20px; }
                        .permission-box {
                            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
                            border-left: 5px solid #16a34a;
                            border-radius: 8px;
                            padding: 20px;
                            margin: 20px 0;
                        }
                        .permission-box h3 { margin-top: 0; color: #15803d; font-size: 16px; }
                        .details-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                        .details-table td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; }
                        .details-table td:first-child { font-weight: bold; color: #5e2d91; width: 40%; }
                        .btn {
                            display: inline-block; padding: 14px 28px;
                            background: linear-gradient(135deg, #5e2d91, #8b5cf6);
                            color: white; text-decoration: none; border-radius: 8px;
                            font-weight: bold; margin: 20px 0; font-size: 15px;
                        }
                        .footer { background: #f9fafb; padding: 20px; text-align: center; font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb; }
                    </style>
                </head>
                <body>
                    <div class='header'>
                        <h1>🏆 Match Result Permission Granted</h1>
                        <p>University of Colombo Sports E-Portal</p>
                    </div>
                    <div class='content'>
                        <h2>Dear $captainName,</h2>
                        <div class='permission-box'>
                            <h3>✅ You have been granted permission!</h3>
                            <p>You can now add match results for the following tournament through the Captain portal.</p>
                        </div>

                        <table class='details-table'>
                            <tr><td>Tournament</td><td><strong>$tournamentName</strong></td></tr>
                            <tr><td>Sport</td><td>$sportName</td></tr>
                            <tr><td>Start Date</td><td>$startDate</td></tr>
                            <tr><td>End Date</td><td>$endDate</td></tr>
                        </table>

                        <p>Please log in to the Captain Portal and navigate to <strong>\"Add Match Result\"</strong> to enter the results at your earliest convenience.</p>

                        <p style='text-align:center;'>
                            <a href='$portalUrl' class='btn'>Go to Captain Portal →</a>
                        </p>

                        <p style='font-size:13px; color:#6b7280;'>If you have any questions, please contact the Sports Administration.</p>

                        <p>Best regards,<br><strong>University of Colombo Sports Administration</strong></p>
                    </div>
                    <div class='footer'>
                        <p>This is an automated notification from the UOC Sports E-Portal.</p>
                        <p>University of Colombo | Physical Education Department | Colombo 03, Sri Lanka</p>
                    </div>
                </body>
            </html>
        ";

        return $this->sendEmail($captainEmail, $captainName, $subject, $htmlContent);
    }

    /**
     * Send account status update email (Verification Approved/Rejected)
     */
    public function sendAccountStatusEmail($email, $name, $status, $reason = null) {
        $isApproved = ($status === 'VERIFIED');
        $subject = $isApproved ? "Account Verified - UOC Sports E-Portal" : "Account Verification Update - UOC Sports E-Portal";
        
        $headerColor = $isApproved ? "linear-gradient(135deg, #059669, #10b981)" : "linear-gradient(135deg, #dc2626, #f87171)";
        $statusIcon = $isApproved ? "✅" : "⚠️";
        $statusTitle = $isApproved ? "Account Verified Successfully" : "Account Verification Declined";
        
        $reasonHtml = '';
        if (!$isApproved && $reason) {
            $reasonHtml = "
                <div style='background: #fef2f2; border-left: 4px solid #dc2626; padding: 15px; margin: 20px 0;'>
                    <h4 style='margin: 0 0 5px; color: #dc2626;'>Reason for Rejection:</h4>
                    <p style='margin: 0;'>$reason</p>
                </div>
            ";
        }

        $htmlContent = "
            <html>
                <body style='font-family: Arial, sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 0;'>
                    <div style='background: $headerColor; color: white; padding: 30px 20px; text-align: center;'>
                        <h1>$statusIcon $statusTitle</h1>
                        <p>University of Colombo Sports Management</p>
                    </div>
                    <div style='padding: 30px 20px;'>
                        <h2>Hello $name,</h2>
                        <p>Your account verification process is complete.</p>
                        
                        " . ($isApproved ? 
                            "<p>Your identity has been verified by the Registrar. You now have full access to all features of the UOC Sports E-Portal.</p>
                             <div style='text-align:center; margin: 30px 0;'>
                                <a href='http://localhost/uoc-sports/public/sign-in' style='display: inline-block; padding: 14px 28px; background: #059669; color: white; text-decoration: none; border-radius: 8px; font-weight: bold;'>Login to Portal →</a>
                             </div>" : 
                            "<p>Unfortunately, your account verification request was declined at this time.</p> $reasonHtml <p>Please review the reason above and update your registration details accordingly.</p>"
                        ) . "

                        <p>If you have any questions, please reply to this email or visit the Physical Education Department.</p>
                        <p>Best regards,<br><strong>Registrar's Office</strong></p>
                    </div>
                </body>
            </html>
        ";

        return $this->sendEmail($email, $name, $subject, $htmlContent);
    }

    /**
     * Send password reset request email
     */
    public function sendPasswordResetEmail($email, $name, $token) {
        $subject = "Password Reset Request - UOC Sports E-Portal";
        $resetUrl = "http://localhost/uoc-sports/public/reset-password?token=" . $token;

        $htmlContent = "
            <html>
                <body style='font-family: Arial, sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 0;'>
                    <div style='background: linear-gradient(135deg, #1e293b, #334155); color: white; padding: 30px 20px; text-align: center;'>
                        <h1>🔑 Password Recovery</h1>
                    </div>
                    <div style='padding: 30px 20px;'>
                        <h2>Hello $name,</h2>
                        <p>We received a request to reset the password for your UOC Sports E-Portal account.</p>
                        <p>Click the button below to choose a new password. <strong>This link will expire in 1 hour.</strong></p>
                        
                        <div style='text-align:center; margin: 30px 0;'>
                            <a href='$resetUrl' style='display: inline-block; padding: 14px 28px; background: #3b82f6; color: white; text-decoration: none; border-radius: 8px; font-weight: bold;'>Reset My Password</a>
                        </div>
                        
                        <p>If you did not request a password reset, you can safely ignore this email. Your password will remain unchanged.</p>
                        
                        <p style='font-size: 13px; color: #666; border-top: 1px solid #eee; padding-top: 20px;'>
                            If you're having trouble clicking the button, copy and paste the URL below into your web browser:<br>
                            <a href='$resetUrl'>$resetUrl</a>
                        </p>
                    </div>
                </body>
            </html>
        ";

        return $this->sendEmail($email, $name, $subject, $htmlContent);
    }

    /**
     * Send a generic email for mass communication
     * Note: This is called sequentially for each recipient to allow personalization
     */
    public function sendMassEmail($email, $name, $subject, $message) {
        $htmlContent = "
            <html>
                <body style='font-family: Arial, sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 0;'>
                    <div style='background: #5e2d91; color: white; padding: 25px; text-align: center;'>
                        <h1>UOC Sports Announcement</h1>
                    </div>
                    <div style='padding: 30px 20px;'>
                        <h2>Hello $name,</h2>
                        <div style='white-space: pre-wrap;'>$message</div>
                        <p style='margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;'>
                            Best regards,<br>
                            <strong>University of Colombo Sports Management</strong>
                        </p>
                    </div>
                </body>
            </html>
        ";
        return $this->sendEmail($email, $name, $subject, $htmlContent);
    }

    /**
     * Send facility booking confirmation (Received but Pending Payment)
     */
    public function sendBookingConfirmationEmail($email, $name, $bookingData) {
        $subject = "Booking Received: " . $bookingData['booking_id'] . " - UOC Sports";
        $bookingId = $bookingData['booking_id'];
        $facilityName = $bookingData['facility_name'];
        $date = date('F j, Y', strtotime($bookingData['date']));
        $startTime = $bookingData['start_time'];
        $endTime = $bookingData['end_time'];

        $htmlContent = "
            <html>
                <body style='font-family: Arial, sans-serif; color: #333; line-height: 1.6;'>
                    <div style='background: #3b82f6; color: white; padding: 20px; text-align: center;'>
                        <h1>Booking Request Received</h1>
                    </div>
                    <div style='padding: 20px; border: 1px solid #eee;'>
                        <h2>Dear $name,</h2>
                        <p>We have received your booking request for <strong>$facilityName</strong>.</p>
                        
                        <div style='background: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                            <h3 style='margin: 0 0 10px; color: #1e40af;'>Booking Details</h3>
                            <p style='margin: 5px 0;'><strong>Booking ID:</strong> $bookingId</p>
                            <p style='margin: 5px 0;'><strong>Facility:</strong> $facilityName</p>
                            <p style='margin: 5px 0;'><strong>Date:</strong> $date</p>
                            <p style='margin: 5px 0;'><strong>Time:</strong> $startTime - $endTime</p>
                        </div>
                        
                        <p style='color: #dc2626; font-weight: bold;'>Action Required:</p>
                        <p>To confirm your booking, please upload your payment slip through the portal within 24 hours.</p>
                        
                        <div style='text-align: center; margin: 20px 0;'>
                            <a href='http://localhost/uoc-sports/public/student/bookings' style='background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Upload Payment Slip</a>
                        </div>
                    </div>
                </body>
            </html>
        ";
        return $this->sendEmail($email, $name, $subject, $htmlContent);
    }

    /**
     * Send payment status update (Verified/Approved)
     */
    public function sendPaymentUpdateEmail($email, $name, $status, $bookingId) {
        $isApproved = ($status === 'BOOKED'); // Final state is BOOKED after payment approval
        $subject = $isApproved ? "Booking Confirmed - $bookingId" : "Payment Update - $bookingId";
        
        $headerColor = $isApproved ? "#059669" : "#dc2626";
        $statusText = $isApproved ? "Payment Verified & Booking Confirmed" : "Payment Verification Failed";

        $htmlContent = "
            <html>
                <body style='font-family: Arial, sans-serif; color: #333; line-height: 1.6;'>
                    <div style='background: $headerColor; color: white; padding: 20px; text-align: center;'>
                        <h1>$statusText</h1>
                    </div>
                    <div style='padding: 20px;'>
                        <h2>Hello $name,</h2>
                        <p>This is an update regarding your booking <strong>$bookingId</strong>.</p>
                        
                        " . ($isApproved ? 
                            "<p style='color: #059669; font-size: 18px;'>Your payment has been successfully verified! Your slot is now officially reserved.</p>" : 
                            "<p style='color: #dc2626; font-size: 18px;'>There was an issue verifying your payment slip. Please check the portal for details or re-upload a clear image of the receipt.</p>"
                        ) . "
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='http://localhost/uoc-sports/public/student/bookings' style='background: #1e293b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;'>View My Bookings</a>
                        </div>
                    </div>
                </body>
            </html>
        ";
        return $this->sendEmail($email, $name, $subject, $htmlContent);
    }

    /**
     * Send equipment request status update (Approved/Rejected/Received)
     */
    public function sendEquipmentRequestStatusEmail($email, $name, $status, $requestData) {
        $isApproved = ($status === 'ACTIVE' || $status === 'APPROVED');
        $isPending = ($status === 'PENDING');
        
        $subject = $isPending ? "Equipment Request Received - UOC Sports" : "Equipment Request Update - UOC Sports";
        
        $headerColor = "#1e293b"; // Default
        if ($isApproved) $headerColor = "#059669";
        if ($status === 'REJECTED') $headerColor = "#dc2626";

        $statusText = "Request Pending";
        if ($isApproved) $statusText = "Request Approved";
        if ($status === 'REJECTED') $statusText = "Request Declined";

        $htmlContent = "
            <html>
                <body style='font-family: Arial, sans-serif; color: #333; line-height: 1.6;'>
                    <div style='background: $headerColor; color: white; padding: 20px; text-align: center;'>
                        <h1>$statusText</h1>
                    </div>
                    <div style='padding: 20px;'>
                        <h2>Hello $name,</h2>
                        <p>This is an update regarding your equipment request <strong>#" . $requestData['request_id'] . "</strong>.</p>
                        
                        <div style='background: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                            <h3 style='margin: 0 0 10px; color: #1e40af;'>Request Summary</h3>
                            <p style='margin: 5px 0;'><strong>Items:</strong> " . $requestData['equipment_name'] . "</p>
                            <p style='margin: 5px 0;'><strong>Date:</strong> " . date('F j, Y', strtotime($requestData['request_date'])) . "</p>
                            <p style='margin: 5px 0;'><strong>Time:</strong> " . $requestData['start_time'] . " - " . $requestData['end_time'] . "</p>
                        </div>
                        
                        " . ($isApproved ? 
                            "<p style='color: #059669; font-weight: bold;'>Your request has been approved! Please visit the equipment store at the scheduled time to collect the items. Remember to bring your University ID.</p>" : 
                            ($status === 'REJECTED' ? 
                                "<p style='color: #dc2626; font-weight: bold;'>Your request was declined. Please check the portal for details or contact the Equipment Manager.</p>" :
                                "<p>We have received your request and the Equipment Manager will review it shortly.</p>")
                        ) . "
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='http://localhost/uoc-sports/public/student/equipment-requests' style='background: #1e293b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;'>View My Requests</a>
                        </div>
                    </div>
                </body>
            </html>
        ";
        return $this->sendEmail($email, $name, $subject, $htmlContent);
    }

    /**
     * Notify Admin of a new expense submission and budget warnings
     */
    public function sendExpenseAddedEmail($adminEmail, $adminName, $expenseData, $remainingBalance = null) {
        $subject = "New Expense Submitted: " . $expenseData['expense_title'];
        
        $warningHtml = "";
        $headerColor = "#1e293b";
        $headerTitle = "New Expense Submission";
        
        if ($remainingBalance !== null) {
            if ($remainingBalance <= 0) {
                $subject = "URGENT: Budget Exceeded - " . $expenseData['sport'];
                $headerColor = "#dc2626"; // Red
                $headerTitle = "⚠️ Budget Exceeded Limit";
                $warningHtml = "<div style='background-color: #fee2e2; color: #dc2626; padding: 15px; border-left: 4px solid #dc2626; margin: 20px 0; font-weight: bold;'>WARNING: The remaining budget for " . $expenseData['sport'] . " has exceeded the allocation or hit zero. Current Remaining: LKR " . number_format($remainingBalance, 2) . "</div>";
            } elseif ($remainingBalance < 10000) {
                $subject = "WARNING: Low Budget - " . $expenseData['sport'];
                $headerColor = "#f59e0b"; // Yellow 
                $headerTitle = "⚠️ Low Budget Warning";
                $warningHtml = "<div style='background-color: #fef3c7; color: #d97706; padding: 15px; border-left: 4px solid #f59e0b; margin: 20px 0; font-weight: bold;'>WARNING: The remaining budget for " . $expenseData['sport'] . " is running low. Current Remaining: LKR " . number_format($remainingBalance, 2) . "</div>";
            }
        }

        $htmlContent = "
            <html>
                <body style='font-family: Arial, sans-serif; color: #333; line-height: 1.6;'>
                    <div style='background: $headerColor; color: white; padding: 20px; text-align: center;'>
                        <h1>$headerTitle</h1>
                    </div>
                    <div style='padding: 20px;'>
                        <h2>Hello $adminName,</h2>
                        <p>A new sport expense has been submitted for review.</p>
                        
                        $warningHtml
                        
                        <div style='background: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                            <h3 style='margin: 0 0 10px; color: #1e40af;'>Expense Details</h3>
                            <p style='margin: 5px 0;'><strong>Sport:</strong> " . $expenseData['sport'] . "</p>
                            <p style='margin: 5px 0;'><strong>Title:</strong> " . $expenseData['expense_title'] . "</p>
                            <p style='margin: 5px 0;'><strong>Amount:</strong> LKR " . number_format($expenseData['amount'], 2) . "</p>
                            <p style='margin: 5px 0;'><strong>Submitted By:</strong> " . $expenseData['submitted_by'] . "</p>
                            <p style='margin: 5px 0;'><strong>Date:</strong> " . date('F j, Y') . "</p>
                        </div>
                        
                        <p>Please log in to the Administrative Portal to review the details and verify the receipt.</p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='http://localhost/uoc-sports/public/admin/expenses' style='background: #1e293b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;'>Review Expenses</a>
                        </div>
                    </div>
                </body>
            </html>
        ";
        return $this->sendEmail($adminEmail, $adminName, $subject, $htmlContent);
    }

    /**
     * Send a portal message notification email.
     * Called automatically whenever a message is sent through the UOC Sports messaging system.
     *
     * @param string $recipientEmail  Registered email of the recipient
     * @param string $recipientName   Full name of the recipient
     * @param string $senderName      Full name of the sender
     * @param string $senderRole      Human-readable role of the sender (e.g., "Sports Manager")
     * @param string $subject         Message subject/title
     * @param string $messageText     The actual message content
     * @param string $inboxUrl        Deep-link to the recipient's inbox
     * @return array
     */
    public function sendMessageNotification(
        $recipientEmail,
        $recipientName,
        $senderName,
        $senderRole,
        $subject,
        $messageText,
        $inboxUrl = 'http://localhost/uoc-sports/public/'
    ) {
        $emailSubject = "New Message: " . htmlspecialchars($subject) . " — UOC Sports E-Portal";
        $safeMessage  = nl2br(htmlspecialchars($messageText));
        $safeSubject  = htmlspecialchars($subject);
        $safeSender   = htmlspecialchars($senderName);
        $safeRole     = htmlspecialchars($senderRole);
        $safeRecipient = htmlspecialchars($recipientName);
        $date         = date('F j, Y \a\t g:i A');

        $htmlContent = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        </head>
        <body style='margin:0; padding:0; font-family: Arial, Helvetica, sans-serif; background-color:#f4f6f9; color:#333;'>

            <!-- Wrapper -->
            <table width='100%' cellpadding='0' cellspacing='0' style='background:#f4f6f9; padding:30px 0;'>
                <tr>
                    <td align='center'>
                        <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08);'>

                            <!-- Header -->
                            <tr>
                                <td style='background: linear-gradient(135deg, #2b0c4d 0%, #6b3fa0 100%); padding:30px 40px; text-align:center;'>
                                    <h1 style='color:#ffffff; margin:0 0 6px; font-size:22px; font-weight:700; letter-spacing:-0.5px;'>
                                        ✉️ New Message
                                    </h1>
                                    <p style='color:rgba(255,255,255,0.8); margin:0; font-size:13px;'>
                                        University of Colombo Sports E-Portal
                                    </p>
                                </td>
                            </tr>

                            <!-- Body -->
                            <tr>
                                <td style='padding:36px 40px;'>
                                    <p style='font-size:15px; margin:0 0 20px;'>
                                        Dear <strong>$safeRecipient</strong>,
                                    </p>
                                    <p style='font-size:14px; color:#555; margin:0 0 24px; line-height:1.6;'>
                                        You have received a new message from <strong>$safeSender</strong>
                                        (<span style='color:#6b3fa0;'>$safeRole</span>) on the UOC Sports portal.
                                    </p>

                                    <!-- Message Card -->
                                    <table width='100%' cellpadding='0' cellspacing='0' style='border:1px solid #e8e0f5; border-radius:8px; overflow:hidden; margin-bottom:28px;'>
                                        <tr>
                                            <td style='background:#f5f0ff; padding:14px 20px; border-bottom:1px solid #e8e0f5;'>
                                                <p style='margin:0; font-size:13px; color:#888;'>SUBJECT</p>
                                                <p style='margin:4px 0 0; font-size:16px; font-weight:700; color:#2b0c4d;'>$safeSubject</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='padding:20px; font-size:14px; line-height:1.7; color:#333;'>
                                                $safeMessage
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='padding:12px 20px; background:#fafafa; border-top:1px solid #f0f0f0;'>
                                                <p style='margin:0; font-size:12px; color:#aaa;'>Sent on $date</p>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- CTA Button -->
                                    <table width='100%' cellpadding='0' cellspacing='0'>
                                        <tr>
                                            <td align='center'>
                                                <a href='$inboxUrl'
                                                   style='display:inline-block; padding:14px 32px;
                                                          background:linear-gradient(135deg,#6b3fa0,#8e5fb8);
                                                          color:#ffffff; text-decoration:none;
                                                          border-radius:8px; font-size:14px;
                                                          font-weight:700; letter-spacing:0.3px;'>
                                                    View &amp; Reply in Portal →
                                                </a>
                                            </td>
                                        </tr>
                                    </table>

                                    <p style='font-size:13px; color:#999; margin:28px 0 0; line-height:1.6;'>
                                        If you did not expect this message, please log in and review your inbox.
                                        You can always manage your notifications in your profile settings.
                                    </p>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td style='background:#f8f8f8; border-top:1px solid #eee; padding:20px 40px; text-align:center;'>
                                    <p style='margin:0; font-size:12px; color:#aaa; line-height:1.6;'>
                                        This is an automated notification from the UOC Sports E-Portal.<br>
                                        University of Colombo | Physical Education Department | Colombo 03, Sri Lanka
                                    </p>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>

        </body>
        </html>";

        return $this->sendEmail($recipientEmail, $recipientName, $emailSubject, $htmlContent);
    }
}




