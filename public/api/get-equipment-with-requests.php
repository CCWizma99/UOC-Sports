<?php
require_once '../../config/config.php';
require_once '../../core/Database.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['sport_id'])) {
        echo json_encode(['success' => false, 'message' => 'Sport ID is required']);
        exit;
    }

    $sportId = strtoupper(trim($_GET['sport_id']));
    $requestDate = $_GET['request_date'] ?? null;
    $startTime = $_GET['start_time'] ?? null;
    $endTime = $_GET['end_time'] ?? null;
    $currentRequestId = $_GET['current_request_id'] ?? null; // Exclude current request when editing
    
    $db = Database::getConnection();

    // Resolve requested quantity for one equipment from either JSON equipment_items
    // or legacy category_name format like "Tennis Racket (x2)".
    $extractRequestedQty = static function (array $row, string $equipmentName): int {
        $target = strtolower(trim($equipmentName));

        if (!empty($row['equipment_items'])) {
            $items = json_decode($row['equipment_items'], true);
            if (is_array($items)) {
                foreach ($items as $item) {
                    $itemName = strtolower(trim((string)($item['equipment_name'] ?? '')));
                    if ($itemName === $target) {
                        $qty = (int)($item['quantity'] ?? 1);
                        return $qty > 0 ? $qty : 1;
                    }
                }
            }
        }

        $category = strtolower(trim((string)($row['category_name'] ?? '')));
        if ($category !== '' && strpos($category, $target) !== false) {
            if (preg_match('/\(\s*x\s*(\d+)\s*\)/i', (string)$row['category_name'], $m)) {
                $qty = (int)$m[1];
                return $qty > 0 ? $qty : 1;
            }
            return 1;
        }

        return 0;
    };

    // Get equipment for the sport with availability
    $query = "SELECT 
                e.equipment_id,
                e.equipment_name,
                COALESCE(SUM(ei.usable), 0) as available_count
              FROM equipment e
              LEFT JOIN equipment_inventory ei ON e.equipment_id = ei.equipment_id
              WHERE UPPER(e.sport_id) = ?
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
        
        $equip['pending_count'] = 0;
        $equip['accepted_count'] = 0;
        $equip['active_count'] = 0;
        $equip['pending_qty'] = 0;
        $equip['accepted_qty'] = 0;
        $equip['active_qty'] = 0;
        $equip['request_details'] = [];

        // Only show request conflict summaries when full slot is selected.
        if (!empty($requestDate) && !empty($startTime) && !empty($endTime)) {
            $countQuery = "SELECT 
                            request_id,
                            requester_name,
                            request_date,
                            start_time,
                            end_time,
                            status,
                            equipment_items,
                            category_name
                           FROM `equipment-requests`
                           WHERE UPPER(sport_id) = ?
                           AND request_date = ?
                           AND status IN ('PENDING', 'ACCEPTED', 'ACTIVE')
                           AND (
                               (equipment_items IS NOT NULL AND equipment_items LIKE ?)
                               OR (category_name IS NOT NULL AND category_name LIKE ?)
                           )
                           AND (
                               (start_time < ? AND end_time > ?)
                               OR (start_time >= ? AND start_time < ?)
                               OR (end_time > ? AND end_time <= ?)
                           )";

            $params = [
                $sportId,
                $requestDate,
                "%\"$equipmentName\"%",
                "%$equipmentName%",
                $endTime,
                $startTime,
                $startTime,
                $endTime,
                $startTime,
                $endTime
            ];

            // Exclude current request if editing
            if ($currentRequestId) {
                $countQuery .= " AND request_id != ?";
                $params[] = $currentRequestId;
            }

            $countQuery .= " ORDER BY start_time";

            $countStmt = $db->prepare($countQuery);
            $countStmt->execute($params);
            $counts = $countStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($counts as $count) {
                $qty = $extractRequestedQty($count, $equipmentName);
                if ($qty <= 0) {
                    continue;
                }

                if ($count['status'] === 'PENDING') {
                    $equip['pending_count'] += 1;
                    $equip['pending_qty'] += $qty;
                } elseif ($count['status'] === 'ACCEPTED') {
                    $equip['accepted_count'] += 1;
                    $equip['accepted_qty'] += $qty;
                } elseif ($count['status'] === 'ACTIVE') {
                    $equip['active_count'] += 1;
                    $equip['active_qty'] += $qty;
                }

                $equip['request_details'][] = [
                    'request_id' => $count['request_id'] ?? '',
                    'requester_name' => $count['requester_name'] ?? 'Unknown',
                    'status' => $count['status'] ?? '',
                    'requested_quantity' => $qty,
                    'request_date' => $count['request_date'] ?? '',
                    'start_time' => $count['start_time'] ?? '',
                    'end_time' => $count['end_time'] ?? '',
                ];
            }
        }
        
        // Check for time overlaps if date and time provided
        $equip['overlapping_slots'] = [];
        $equip['slot_reserved_count'] = 0;
        $equip['slot_available_count'] = $equip['available_count'];
        if ($requestDate && $startTime && $endTime) {
            try {
                $overlapQuery = "SELECT 
                                request_id,
                                requester_name,
                                start_time,
                                end_time,
                                status,
                                equipment_items,
                                category_name
                             FROM `equipment-requests`
                             WHERE UPPER(sport_id) = ?
                             AND request_date = ?
                             AND status IN ('PENDING', 'ACCEPTED', 'ACTIVE')
                             AND (
                                 (equipment_items IS NOT NULL AND equipment_items LIKE ?)
                                 OR (category_name IS NOT NULL AND category_name LIKE ?)
                             )
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
                $relevantOverlaps = [];
                foreach ($overlaps as $overlap) {
                    $requestedQty = $extractRequestedQty($overlap, $equipmentName);
                    if ($requestedQty <= 0) {
                        continue;
                    }
                    $overlap['requested_quantity'] = $requestedQty;
                    $overlap['source_type'] = 'booking';
                    $slotReservedCount += (int)$requestedQty;
                    $relevantOverlaps[] = $overlap;
                }
                $overlaps = $relevantOverlaps;
            
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
                             WHERE UPPER(sport_id) = ?
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
            
            // If any overlapping practice session needs equipment, block this item for the slot.
                if (!empty($practiceSessions)) {
                    foreach ($practiceSessions as &$practice) {
                        $practice['requester_name'] = 'Practice Session';
                        $practice['requested_quantity'] = (int)$equip['available_count'];
                        $practice['source_type'] = 'practice';
                        $practice['request_id'] = 'PS-' . $practice['id'];
                        $overlaps[] = $practice;
                        error_log("Added practice session PS-" . $practice['id'] . " to overlaps");
                    }

                    // Reserve all available quantity for this slot to disable selection on the form.
                    $slotReservedCount = (int)$equip['available_count'];
                }
            
                $equip['overlapping_slots'] = $overlaps;
                $equip['slot_reserved_count'] = $slotReservedCount;
                $equip['slot_available_count'] = max(0, $equip['available_count'] - $equip['slot_reserved_count']);
            } catch (Throwable $overlapError) {
                error_log('Overlap evaluation failed for sport ' . $sportId . ': ' . $overlapError->getMessage());
                $equip['overlapping_slots'] = [];
                $equip['slot_reserved_count'] = 0;
                $equip['slot_available_count'] = $equip['available_count'];
            }
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
