<?php
// app/models/InjuryReport.php
require_once __DIR__ . '/../../core/Database.php';

class InjuryReport {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    private function generateReportId() {
        return 'IRP' . strtoupper(bin2hex(random_bytes(4)));
    }

    /**
     * Save an injury report
     * @param array $data - keys: user_id, coach_id, practice_id, date, description, need_substitude, substitude_id
     * @return array
     */
    public function saveReport($data) {
        try {
            $reportId = $this->generateReportId();
            $stmt = $this->db->prepare("INSERT INTO injury_report (report_id, user_id, coach_id, practice_id, `date`, description, need_substitude, substitude_id) VALUES (:report_id, :user_id, :coach_id, :practice_id, :date, :description, :need_substitude, :substitude_id)");

            $stmt->execute([
                'report_id' => $reportId,
                'user_id' => $data['user_id'] ?? '',
                'coach_id' => $data['coach_id'] ?? '',
                'practice_id' => $data['practice_id'] ?? '',
                'date' => $data['date'] ?? date('Y-m-d'),
                'description' => $data['description'] ?? '',
                'need_substitude' => $data['need_substitude'] ?? 'NO',
                'substitude_id' => !empty($data['substitude_id']) ? $data['substitude_id'] : null
            ]);

            return ['status' => 'success', 'message' => 'Injury report saved', 'report_id' => $reportId];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Get injury reports for a sport
     * @param string $sportId
     * @return array
     */
    public function getReportsBySport($sportId) {
        $stmt = $this->db->prepare(
            "SELECT ir.report_id, ir.user_id, ir.coach_id, ir.practice_id, ir.date, ir.description, ir.need_substitude, ir.substitude_id, u.fname, u.lname, ps.facility, ps.session_date, ps.start_time
             FROM injury_report ir
             LEFT JOIN user u ON ir.user_id = u.user_id
             LEFT JOIN practice_sessions ps ON ir.practice_id = ps.id
             WHERE ps.sport_id = :sport_id AND ir.status = 'ACTIVE'
             ORDER BY ir.date DESC"
        );
        $stmt->execute(['sport_id' => $sportId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteReport($reportId) {
        $stmt = $this->db->prepare("UPDATE injury_report SET status = 'DELETED' WHERE report_id = :id");
        return $stmt->execute(['id' => $reportId]);
    }

    public function updateReport($reportId, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE injury_report 
                SET user_id = :user_id, 
                    practice_id = :practice_id, 
                    date = :date, 
                    description = :description, 
                    need_substitude = :need_substitude, 
                    substitude_id = :substitude_id
                WHERE report_id = :report_id
            ");

            return $stmt->execute([
                'user_id' => $data['user_id'],
                'practice_id' => $data['practice_id'],
                'date' => $data['date'],
                'description' => $data['description'],
                'need_substitude' => $data['need_substitude'],
                'substitude_id' => !empty($data['substitude_id']) ? $data['substitude_id'] : null,
                'report_id' => $reportId
            ]);
        } catch (Exception $e) {
            return false;
        }
    }
}
