<?php
require_once 'config/config.php';
require_once 'core/Database.php';

try {
    $db = Database::getConnection();
    
    // Check if table exists
    $stmt = $db->query("SHOW TABLES LIKE 'event_result_permissions'");
    if ($stmt->rowCount() === 0) {
        echo "Table 'event_result_permissions' does not exist.\n";
    } else {
        echo "Table 'event_result_permissions' exists.\n";
        
        // Check row count
        $stmt = $db->query("SELECT COUNT(*) FROM event_result_permissions");
        $count = $stmt->fetchColumn();
        echo "Total rows in 'event_result_permissions': $count\n";
        
        if ($count > 0) {
            echo "Direct select (first 5):\n";
            $stmt = $db->query("SELECT * FROM event_result_permissions LIMIT 5");
            print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
            
            echo "\nTesting the JOIN query used in the model:\n";
            $sql = "
                SELECT 
                    erp.id,
                    t.tournament_name,
                    s.sport_name,
                    u.fname AS captain_fname,
                    a.fname AS granted_by_fname
                FROM event_result_permissions erp
                JOIN tournament t ON erp.tournament_id = t.tournament_id
                JOIN sport s ON erp.sport_id = s.sport_id
                JOIN user u ON erp.captain_id = u.user_id
                JOIN user a ON erp.granted_by = a.user_id
            ";
            try {
                $stmt = $db->query($sql);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "Joined query returned " . count($rows) . " rows.\n";
                if (count($rows) === 0) {
                    echo "The JOIN query failed to return rows. Checking which join is the culprit...\n";
                    
                    // Progressive joins
                    $checks = [
                        "SELECT COUNT(*) FROM event_result_permissions erp JOIN tournament t ON erp.tournament_id = t.tournament_id" => "Join Tournament",
                        "SELECT COUNT(*) FROM event_result_permissions erp JOIN sport s ON erp.sport_id = s.sport_id" => "Join Sport",
                        "SELECT COUNT(*) FROM event_result_permissions erp JOIN user u ON erp.captain_id = u.user_id" => "Join Captain (User table)",
                        "SELECT COUNT(*) FROM event_result_permissions erp JOIN user a ON erp.granted_by = a.user_id" => "Join GrantedBy (User table)"
                    ];
                    
                    foreach ($checks as $query => $label) {
                        $c = $db->query($query)->fetchColumn();
                        echo "$label: $c matches found.\n";
                    }
                }
            } catch (Exception $e) {
                echo "JOIN query error: " . $e->getMessage() . "\n";
            }
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
