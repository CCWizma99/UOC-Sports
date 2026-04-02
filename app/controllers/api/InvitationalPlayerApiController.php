<?php
class InvitationalPlayerApiController {
    /**
     * Search for invitational players by name or university
     */
    public function search() {
        header('Content-Type: application/json');
        try {
            $query = $_GET['q'] ?? '';
            if (strlen($query) < 2) {
                echo json_encode(['status' => 'success', 'data' => []]);
                return;
            }

            $db = Database::getConnection();
            $stmt = $db->prepare("
                SELECT inv_player_id, fname, lname, university, student_id 
                FROM invitational_players 
                WHERE CONCAT(fname, ' ', lname) LIKE :q OR university LIKE :q 
                LIMIT 10
            ");
            $stmt->execute(['q' => "%$query%"]);
            $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['status' => 'success', 'data' => $players]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Ensure an invitational player exists in the registry
     */
    public function ensure() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $fname = trim($input['fname'] ?? '');
            $lname = trim($input['lname'] ?? '');
            $university = trim($input['university'] ?? '');
            $studentId = trim($input['student_id'] ?? '');

            if (empty($fname) || empty($lname) || empty($university)) {
                echo json_encode(['status' => 'error', 'message' => 'First Name, Last Name, and University are required.']);
                return;
            }

            $db = Database::getConnection();
            
            // Check if exists
            $stmt = $db->prepare("SELECT inv_player_id FROM invitational_players WHERE fname = :fname AND lname = :lname AND university = :university");
            $stmt->execute([
                'fname' => $fname,
                'lname' => $lname,
                'university' => $university
            ]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                echo json_encode(['status' => 'success', 'data' => ['inv_player_id' => $existing['inv_player_id'], 'is_new' => false]]);
                return;
            }

            // Create new
            $stmt = $db->prepare("INSERT INTO invitational_players (fname, lname, university, student_id) VALUES (:fname, :lname, :university, :student_id)");
            $stmt->execute([
                'fname' => $fname,
                'lname' => $lname,
                'university' => $university,
                'student_id' => $studentId
            ]);
            $newId = $db->lastInsertId();

            echo json_encode(['status' => 'success', 'data' => ['inv_player_id' => $newId, 'is_new' => true]]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
