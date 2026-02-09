<!DOCTYPE html>
<html>
<head>
    <title>Test Update Endpoint</title>
</head>
<body>
    <h2>Test Status Update Endpoint</h2>
    
    <button onclick="testUpdate()">Click to Test Update</button>
    <div id="result" style="margin-top: 20px; padding: 10px; background: #f0f0f0;"></div>

    <script>
    function testUpdate() {
        console.log('Testing update endpoint...');
        document.getElementById('result').innerHTML = 'Sending request...';
        
        fetch('/uoc-sports/public/equipment-manager/update-booking-status', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                request_id: 'req_6937e152', 
                status: 'PENDING' 
            })
        })
        .then(response => {
            console.log('Response received:', response);
            console.log('Status:', response.status);
            console.log('Status Text:', response.statusText);
            return response.text();
        })
        .then(text => {
            console.log('Response text:', text);
            document.getElementById('result').innerHTML = '<strong>Response:</strong><br><pre>' + text + '</pre>';
            
            // Try to parse as JSON
            try {
                const data = JSON.parse(text);
                console.log('Parsed JSON:', data);
            } catch(e) {
                console.error('Could not parse as JSON:', e);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('result').innerHTML = '<strong>Error:</strong><br>' + error.message;
        });
    }
    </script>
</body>
</html>
