<?php
require_once '../../config/config.php';
require_once '../../core/Database.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['sport_id'])) {
        echo json_encode(['success' => false, 'message' => 'Sport ID is required']);
        exit;
    }

    $sportId = $_GET['sport_id'];
    $requestDate = $_GET['request_date'] ?? null;
    $startTime = $_GET['start_time'] ?? null;
    $endTime = $_GET['end_time'] ?? null;
    $currentRequestId = $_GET['current_request_id'] ?? null; // Exclude current request when editing
    
    $db = Database::getConnection();

    // Get equipment for the sport with availability
    $query = "SELECT 
                e.equipment_id,
                e.equipment_name,
                COALESCE(SUM(ei.usable), 0) as available_count
              FROM equipment e
              LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id
              WHERE e.sport_id = ?
              GROUP BY e.equipment_id, e.equipment_name
              HAVING available_count > 0
              ORDER BY e.equipment_name";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$sportId]);
    $equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // For each equipment, get pending and accepted request counts
    foreach ($equipment as &$equip) {
        $equipmentName = $equip['equipment_name'];
        $equip['available_count'] = (int)$equip['available_count'];
        
        // Count pending and accepted requests for this equipment
        $countQuery = "SELECT 
                        status,
                        COUNT(*) as count
                       FROM `equipment-requests`
                       WHERE sport_id = ?
                       AND status IN ('PENDING', 'ACCEPTED', 'ACTIVE')
                       AND (equipment_items LIKE ? OR category_name LIKE ?)";
        
        $params = [$sportId, "%\"$equipmentName\"%", "%$equipmentName%"];
        
        // Exclude current request if editing
        if ($currentRequestId) {
            $countQuery .= " AND request_id != ?";
            $params[] = $currentRequestId;
        }
        
        $countQuery .= " GROUP BY status";
        
        $countStmt = $db->prepare($countQuery);
        $countStmt->execute($params);
        $counts = $countStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $equip['pending_count'] = 0;
        $equip['accepted_count'] = 0;
        $equip['active_count'] = 0;
        
        foreach ($counts as $count) {
            if ($count['status'] === 'PENDING') {
                $equip['pending_count'] = $count['count'];
            } elseif ($count['status'] === 'ACCEPTED') {
                $equip['accepted_count'] = $count['count'];
            } elseif ($count['status'] === 'ACTIVE') {
                $equip['active_count'] = $count['count'];
            }
        }
        
        // Check for time overlaps if date and time provided
        $equip['overlapping_slots'] = [];
        $equip['slot_reserved_count'] = 0;
        $equip['slot_available_count'] = $equip['available_count'];
        if ($requestDate && $startTime && $endTime) {
            $overlapQuery = "SELECT 
                                request_id,
                                requester_name,
                                start_time,
                                end_time,
                                status,
                                equipment_items,
                                category_name
                             FROM `equipment-requests`
                             WHERE sport_id = ?
                             AND request_date = ?
                             AND status IN ('PENDING', 'ACCEPTED', 'ACTIVE')
                             AND (equipment_items LIKE ? OR category_name LIKE ?)
                             AND (
                                 (start_time < ? AND end_time > ?)
                                 OR (start_time >= ? AND start_time < ?)
                                 OR (end_time > ? AND end_time <= ?)
                             )";
            
            $overlapParams = [
                $sportId, 
                $requestDate, 
                "%\"$equipmentName\"%", 
                "%$equipmentName%",
                $endTime, $startTime,  // Existing request ends after new start OR starts before new end
                $startTime, $endTime,  // Existing request starts within new range
                $startTime, $endTime   // Existing request ends within new range
            ];
            
            // Exclude current request if editing
            if ($currentRequestId) {
                $overlapQuery .= " AND request_id != ?";
                $overlapParams[] = $currentRequestId;
            }
            
            $overlapQuery .= " ORDER BY start_time";
            
            $overlapStmt = $db->prepare($overlapQuery);
            $overlapStmt->execute($overlapParams);
            $overlaps = $overlapStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Parse quantity for each overlapping slot
            $slotReservedCount = 0;
            $hasPracticeOverlap = false;
            foreach ($overlaps as &$overlap) {
                $requestedQty = 1;
                if (!empty($overlap['equipment_items'])) {
                    $items = json_decode($overlap['equipment_items'], true);
                    if (is_array($items)) {
                        foreach ($items as $item) {
                            if (isset($item['equipment_name']) && $item['equipment_name'] === $equipmentName) {
                                $requestedQty = $item['quantity'] ?? 1;
                                break;
                            }
                        }
                    }
                }
                $overlap['requested_quantity'] = $requestedQty;
                $overlap['source_type'] = 'booking';
                $slotReservedCount += (int)$requestedQty;
            }
            
            // Also check for practice sessions with need_equipment='Yes'
            $practiceQuery = "SELECT 
                                id,
                                session_date,
                                start_time,
                                end_time,
                                status,
                                location,
                                notes
                             FROM practice_sessions
                             WHERE sport_id = ?
                             AND session_date = ?
                             AND need_equipment = 'Yes'
                             AND status IN ('PENDING', 'ACCEPTED', 'ACTIVE')
                             AND (
                                 (start_time < ? AND end_time > ?)
                                 OR (start_time >= ? AND start_time < ?)
                                 OR (end_time > ? AND end_time <= ?)
                             )
                             ORDER BY start_time";
            
            $practiceParams = [
                $sportId, 
                $requestDate,
                $endTime, $startTime,  // Existing session ends after new start OR starts before new end
                $startTime, $endTime,  // Existing session starts within new range
                $startTime, $endTime   // Existing session ends within new range
            ];
            
            $practiceStmt = $db->prepare($practiceQuery);
            $practiceStmt->execute($practiceParams);
            $practiceSessions = $practiceStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug logging
            error_log("Practice sessions found for sport $sportId on $requestDate: " . count($practiceSessions));
            
            // Calculate total usable equipment count for the sport (only if there are practice sessions)
            if (!empty($practiceSessions)) {
                $totalEquipmentQuery = "SELECT 
                                            COALESCE(SUM(ei.usable), 0) as total_equipment
                                        FROM equipment e
                                        LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id
                                        WHERE e.sport_id = ?";
                $totalStmt = $db->prepare($totalEquipmentQuery);
                $totalStmt->execute([$sportId]);
                $totalEquipmentCount = $totalStmt->fetchColumn();
                
                error_log("Total equipment count for sport $sportId: $totalEquipmentCount");
                
                // Add practice sessions to overlapping slots
                foreach ($practiceSessions as &$practice) {
                    $practice['requester_name'] = 'Practice Session';
                    $practice['requested_quantity'] = $totalEquipmentCount . ' items';
                    $practice['source_type'] = 'practice';
                    $practice['request_id'] = 'PS-' . $practice['id'];
                    $overlaps[] = $practice;
                    $hasPracticeOverlap = true;
                    error_log("Added practice session PS-" . $practice['id'] . " to overlaps");
                }
            }
            
            $equip['overlapping_slots'] = $overlaps;
            $equip['slot_reserved_count'] = $hasPracticeOverlap ? $equip['available_count'] : $slotReservedCount;
            $equip['slot_available_count'] = max(0, $equip['available_count'] - $equip['slot_reserved_count']);
        }
    }

    echo json_encode([
        'success' => true,
        'equipment' => $equipment
    ]);

} catch (Exception $e) {
    error_log("Error fetching equipment with requests: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching equipment data',
        'error' => $e->getMessage()
    ]);
}
