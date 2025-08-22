<?php
class SportTeam {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection(); // Assuming you have a Database class like PDO
    }

    public function search($query) {
        $sql = "SELECT * FROM sport
                WHERE sport_id LIKE :query OR sport_name LIKE :query";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['query' => "%$query%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

