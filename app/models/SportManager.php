<?php
class SportManager {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getSports() {
        if (!isset($_SESSION['user_id'])) {
            return [];
        }
    
        $user_id = $_SESSION['user_id'];
    
        $sql = "
            SELECT sport_name, sport_id
            FROM sport
            WHERE manager_id = :uid
        ";
    
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':uid', $user_id, PDO::PARAM_STR);
        $stmt->execute();
    
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);    
        return $result;
    }
    
    
    
}
