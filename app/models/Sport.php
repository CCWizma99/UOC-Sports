<?php
class Sport {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection(); // Your PDO connection
    }

    /**
     * Get all tournaments
     */
    public function getTournaments() {
        $stmt = $this->db->query("SELECT tournament_id, tournament_name FROM tournament WHERE status = 'INCOMPLETE' ORDER BY tournament_name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all sports
     */
    public function getSports() {
        $stmt = $this->db->query("SELECT sport_id, sport_name FROM sport ORDER BY sport_name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all students (to pick winners)
     */
    public function getStudents() {
        $stmt = $this->db->query("SELECT user_id, CONCAT(fname,' ',lname) AS name FROM user WHERE type='STUDENT' ORDER BY fname");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get dynamic fields for a sport
     */
    public function getSportFields($sportId) {
        $stmt = $this->db->prepare("SELECT * FROM sport_result_field WHERE sport_id = :sport_id ORDER BY field_id");
        $stmt->execute(['sport_id' => $sportId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get sport by captain user id
     */
    public function getSportByCaptain($captainId) {
        $stmt = $this->db->prepare("SELECT sport_id, sport_name FROM sport WHERE captain_id = :captain_id LIMIT 1");
        $stmt->execute(['captain_id' => $captainId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Add a match result
     */
    public function addResult($tournamentId, $sportId, $matchName, $matchDate, $winnerId, $fieldValues) {
        $matchId = uniqid("match_", true);

        // Insert match
        $stmt = $this->db->prepare("
            INSERT INTO tournament_match (match_id, tournament_id, match_name, match_date, sport_id, winner_id)
            VALUES (:match_id, :tournament_id, :match_name, :match_date, :sport_id, :winner_id)
        ");
        $stmt->execute([
            'match_id' => $matchId,
            'tournament_id' => $tournamentId,
            'match_name' => $matchName,
            'match_date' => $matchDate,
            'sport_id' => $sportId,
            'winner_id' => $winnerId
        ]);

        // Insert field values
        if(!empty($fieldValues)) {
            $stmt2 = $this->db->prepare("
                INSERT INTO tournament_result_field_value (match_id, field_id, value)
                VALUES (:match_id, :field_id, :value)
            ");
            foreach($fieldValues as $fieldId => $val) {
                $stmt2->execute([
                    'match_id' => $matchId,
                    'field_id' => $fieldId,
                    'value' => $val
                ]);
            }
        }

        return $matchId;
    }

    /**
     * Get sport with staff details (coach, captain, manager)
     * @param string $sportId
     * @return array|null
     */
    public function getSportWithStaff($sportId) {
        $sql = "
            SELECT 
                s.sport_id,
                s.sport_name,
                s.coach_id,
                s.captain_id,
                s.manager_id,
                coach.fname AS coach_fname,
                coach.lname AS coach_lname,
                coach.email AS coach_email,
                coach.contact_no AS coach_contact,
                captain.fname AS captain_fname,
                captain.lname AS captain_lname,
                captain.email AS captain_email,
                captain.contact_no AS captain_contact,
                manager.fname AS manager_fname,
                manager.lname AS manager_lname,
                manager.email AS manager_email,
                manager.contact_no AS manager_contact
            FROM sport s
            LEFT JOIN user coach ON s.coach_id = coach.user_id
            LEFT JOIN user captain ON s.captain_id = captain.user_id
            LEFT JOIN user manager ON s.manager_id = manager.user_id
            WHERE s.sport_id = :sport_id
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['sport_id' => $sportId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
