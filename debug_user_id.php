<?php
require_once 'config/config.php';
require_once 'core/Database.php';

try {
    $db = Database::getConnection();
    
    $idToCheck = '91';
    echo "Checking for user_id = '$idToCheck' in user table...\n";
    
    $stmt = $db->prepare("SELECT * FROM user WHERE user_id = ?");
    $stmt->execute([$idToCheck]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "User found:\n";
        print_r($user);
    } else {
        echo "User NOT found.\n";
        
        echo "\nListing first 10 users to see ID format:\n";
        $stmt = $db->query("SELECT user_id, fname, lname, type FROM user LIMIT 10");
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    
    echo "\nChecking event_result_permissions for any other rows:\n";
    $stmt = $db->query("SELECT * FROM event_result_permissions");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
