<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled | UOC Sports E-Portal</title>
    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/general/header.css);
        @import url(/uoc-sports/public/css/general/footer.css);

        .result-container {
            max-width: 600px;
            margin: 80px auto;
            padding: 40px;
            text-align: center;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .cancel-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 40px;
            color: white;
        }
        h1 { color: #f59e0b; margin-bottom: 16px; }
        p { color: #64748b; margin-bottom: 24px; line-height: 1.6; }
        .order-id { 
            background: #f1f5f9; 
            padding: 12px 20px; 
            border-radius: 8px; 
            font-family: monospace;
            color: #334155;
            margin-bottom: 24px;
            display: inline-block;
        }
        .btn-group { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-block;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #5e2d91, #7c3aed);
            color: white;
        }
        .btn-secondary {
            background: #f1f5f9;
            color: #334155;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <?php require '../app/views/templates/general/header.php'; ?>
    
    <div class="result-container">
        <div class="cancel-icon">✕</div>
        <h1>Payment Cancelled</h1>
        <p><?php echo htmlspecialchars($message); ?></p>
        <?php if ($order_id): ?>
            <div class="order-id">Booking ID: <?php echo htmlspecialchars($order_id); ?></div>
        <?php endif; ?>
        <br><br>
        <div class="btn-group">
            <a href="/uoc-sports/public/payment?booking_id=<?php echo htmlspecialchars($order_id); ?>" class="btn btn-primary">Try Again</a>
            <a href="/uoc-sports/public/facility-reservation" class="btn btn-secondary">View Bookings</a>
        </div>
    </div>

    <?php require '../app/views/templates/general/footer.php'; ?>
</body>
</html>
