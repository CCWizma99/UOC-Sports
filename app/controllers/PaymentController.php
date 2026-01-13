<?php

class PaymentController {

    /**
     * Handle successful payment return from PayHere
     * User is redirected here after completing payment
     * 
     * NOTE: For localhost development, the IPN (notify_url) won't work because
     * PayHere servers can't reach localhost. So we update the status here as well.
     * In production with a public URL, both IPN and this method will update the status.
     */
    public function success() {
        // PayHere sends order_id in the return URL
        $order_id = $_GET['order_id'] ?? null;
        
        // Update payment status in database
        // This serves as a fallback for localhost where IPN can't reach
        if ($order_id) {
            $facilityModel = new Facility();
            $facilityModel->updatePaymentStatus($order_id, 'COMPLETE', 'RETURN-' . time());
        }
        
        view('general/payment-success', [
            'order_id' => $order_id,
            'message' => 'Your payment was successful! Your booking has been confirmed.'
        ]);
    }

    /**
     * Handle cancelled payment from PayHere
     * User is redirected here if they cancel the payment
     */
    public function cancel() {
        $order_id = $_GET['order_id'] ?? null;
        
        view('general/payment-cancel', [
            'order_id' => $order_id,
            'message' => 'Payment was cancelled. Your booking is not confirmed until payment is completed.'
        ]);
    }

    /**
     * Handle PayHere server-to-server notification (IPN)
     * This is called by PayHere servers, not the user's browser
     */
    public function notify() {
        // Get POST data from PayHere
        $merchant_id = $_POST['merchant_id'] ?? '';
        $order_id = $_POST['order_id'] ?? '';
        $payhere_amount = $_POST['payhere_amount'] ?? '';
        $payhere_currency = $_POST['payhere_currency'] ?? '';
        $status_code = $_POST['status_code'] ?? '';
        $md5sig = $_POST['md5sig'] ?? '';
        $payment_id = $_POST['payment_id'] ?? '';
        $status_message = $_POST['status_message'] ?? '';

        // Get merchant secret from config
        $merchant_secret = PAYHERE_MERCHANT_SECRET;

        // Generate local signature to verify the notification
        $local_md5sig = strtoupper(
            md5(
                $merchant_id . 
                $order_id . 
                $payhere_amount . 
                $payhere_currency . 
                $status_code . 
                strtoupper(md5($merchant_secret))
            )
        );

        // Verify the signature
        if ($local_md5sig === $md5sig && $status_code == 2) {
            // Payment successful - Update booking status in database
            $facilityModel = new Facility();
            $facilityModel->updatePaymentStatus($order_id, 'COMPLETE', $payment_id);
            
            // Log success
            error_log("PayHere Payment Success - Order: $order_id, Payment ID: $payment_id");
            
            http_response_code(200);
            echo "OK";
        } else if ($status_code == 0) {
            // Payment pending
            error_log("PayHere Payment Pending - Order: $order_id");
            http_response_code(200);
            echo "PENDING";
        } else if ($status_code == -1) {
            // Payment cancelled
            error_log("PayHere Payment Cancelled - Order: $order_id");
            http_response_code(200);
            echo "CANCELLED";
        } else if ($status_code == -2) {
            // Payment failed/chargedback
            error_log("PayHere Payment Failed - Order: $order_id");
            http_response_code(200);
            echo "FAILED";
        } else {
            // Invalid signature or unknown status
            error_log("PayHere Invalid Notification - Order: $order_id, Signature mismatch or unknown status");
            http_response_code(400);
            echo "INVALID";
        }
    }
}
