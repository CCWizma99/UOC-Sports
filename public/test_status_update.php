<?php
// Test status update directly
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test Status Update</h2>";
echo "<pre>";

require_once '../config/config.php';
require_once '../core/Database.php';
require_once '../app/models/EquipmentBookigRequest.php';

try {
    $model = new EquipmentBookigRequest();
    
    // Get current status
    echo "Step 1: Get current request status\n";
    $request = $model->getRequestById('req_6937e152');
    if ($request) {
        echo "Current status: " . $request['status'] . "\n\n";
        
        // Try to update status
        echo "Step 2: Update status to PENDING\n";
        $result = $model->updateStatus('req_6937e152', 'PENDING');
        echo "Update result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n\n";
        
        // Verify the update
        echo "Step 3: Verify the update\n";
        $request = $model->getRequestById('req_6937e152');
        echo "New status: " . $request['status'] . "\n\n";
        
        // Change it back
        echo "Step 4: Change back to ACTIVE\n";
        $result = $model->updateStatus('req_6937e152', 'ACTIVE');
        echo "Update result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n\n";
        
        // Final verification
        echo "Step 5: Final verification\n";
        $request = $model->getRequestById('req_6937e152');
        echo "Final status: " . $request['status'] . "\n";
        
    } else {
        echo "Request not found!\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString();
}

echo "</pre>";

// Test the AJAX endpoint
echo "<hr>";
echo "<h3>Test AJAX Endpoint</h3>";
echo "<button onclick='testAjax()'>Test Status Update via AJAX</button>";
echo "<div id='result'></div>";
?>

<script>
function testAjax() {
    fetch('/uoc-sports/public/equipment-manager/update-booking-status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            request_id: 'req_6937e152', 
            status: 'PENDING' 
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('result').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        if (data.success) {
            alert('AJAX test successful!');
        } else {
            alert('AJAX test failed: ' + data.message);
        }
    })
    .catch(error => {
        document.getElementById('result').innerHTML = '<pre style="color:red;">Error: ' + error + '</pre>';
        alert('AJAX request failed');
    });
}
</script>
