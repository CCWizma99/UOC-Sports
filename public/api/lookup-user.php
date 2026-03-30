<?php
require_once '../../config/config.php';
require_once '../../core/Database.php';

header('Content-Type: application/json');

try {
    $db = Database::getConnection();
    
    error_log("Lookup user API called with: " . print_r($_GET, true));
    
    // Check if looking up by ID or by name
    if (isset($_GET['user_id'])) {
        $userId = trim($_GET['user_id']);
        
        // Query to find user by user_id
        $query = "SELECT user_id, fname, lname, email 
                  FROM user 
                  WHERE user_id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo json_encode([
                'success' => true,
                'user' => [
                    'user_id' => $user['user_id'],
                    'full_name' => trim($user['fname'] . ' ' . $user['lname']),
                    'fname' => $user['fname'],
                    'lname' => $user['lname'],
                    'email' => $user['email']
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'User not found'
            ]);
        }
    } 
    elseif (isset($_GET['name'])) {
        $name = trim($_GET['name']);
        
        // Split name into parts
        $nameParts = explode(' ', $name, 2);
        
        if (count($nameParts) >= 2) {
            $firstName = trim($nameParts[0]);
            $lastName = trim($nameParts[1]);
            
            // Query to find user by first and last name
            $query = "SELECT user_id, fname, lname, email 
                      FROM user 
                      WHERE LOWER(fname) = LOWER(?) 
                      AND LOWER(lname) = LOWER(?)
                      LIMIT 1";
            
            $stmt = $db->prepare($query);
            $stmt->execute([$firstName, $lastName]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo json_encode([
                    'success' => true,
                    'user' => [
                        'user_id' => $user['user_id'],
                        'full_name' => trim($user['fname'] . ' ' . $user['lname']),
                        'fname' => $user['fname'],
                        'lname' => $user['lname'],
                        'email' => $user['email']
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Please enter both first and last name'
            ]);
        }
    } 
    else {
        echo json_encode([
            'success' => false,
            'message' => 'Please provide user_id or name parameter'
        ]);
    }

} catch (Exception $e) {
    error_log("Error looking up user: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error looking up user',
        'error' => $e->getMessage()
    ]);
}
