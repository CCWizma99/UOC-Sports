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
                        .badge { display: inline-block; background: #5e2d91; color: white; padding: 3px 10px; border-radius: 20px; font-size: 12px; }
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
                            <tr><td>Sport</td><td><span class='badge'>$sportName</span></td></tr>
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
}

