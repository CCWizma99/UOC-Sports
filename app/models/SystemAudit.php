<?php

require_once __DIR__ . '/../../core/Model.php';

class SystemAudit extends Model {
    
    public function getLogsWithDetails($limit = 50, $offset = 0, $filters = []) {
        $whereClause = "";
        $params = [];
        
        $conditions = [];
        if (!empty($filters['table_name'])) {
            $conditions[] = "a.table_name = :table_name";
            $params[':table_name'] = $filters['table_name'];
        }
        if (!empty($filters['action'])) {
            $conditions[] = "a.action = :action";
            $params[':action'] = $filters['action'];
        }
        if (!empty($filters['date'])) {
            $conditions[] = "DATE(a.changed_at) = :date";
            $params[':date'] = $filters['date'];
        }
        
        if (!empty($conditions)) {
            $whereClause = "WHERE " . implode(" AND ", $conditions);
        }
        
        $query = "
            SELECT a.id, a.table_name, a.record_id, a.action, a.changed_by, a.changed_at,
                   d.column_name, d.old_value, d.new_value
            FROM system_audit a
            LEFT JOIN system_audit_detail d ON a.id = d.audit_id
            $whereClause
            ORDER BY a.changed_at DESC, a.id DESC, d.id ASC
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $this->db->prepare($query);
        
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Group details by audit record
        $logs = [];
        foreach ($results as $row) {
            $id = $row['id'];
            if (!isset($logs[$id])) {
                $logs[$id] = [
                    'id' => $row['id'],
                    'table_name' => $row['table_name'],
                    'record_id' => $row['record_id'],
                    'action' => $row['action'],
                    'changed_by' => $row['changed_by'],
                    'changed_at' => $row['changed_at'],
                    'details' => []
                ];
            }
            if ($row['column_name']) {
                $logs[$id]['details'][] = [
                    'column_name' => $row['column_name'],
                    'old_value' => $row['old_value'],
                    'new_value' => $row['new_value']
                ];
            }
        }
        
        return array_values($logs);
    }
    
    public function getTotalLogsCount($filters = []) {
        $whereClause = "";
        $params = [];
        
        $conditions = [];
        if (!empty($filters['table_name'])) {
            $conditions[] = "table_name = :table_name";
            $params[':table_name'] = $filters['table_name'];
        }
        if (!empty($filters['action'])) {
            $conditions[] = "action = :action";
            $params[':action'] = $filters['action'];
        }
        if (!empty($filters['date'])) {
            $conditions[] = "DATE(changed_at) = :date";
            $params[':date'] = $filters['date'];
        }
        
        if (!empty($conditions)) {
            $whereClause = "WHERE " . implode(" AND ", $conditions);
        }
        
        $query = "SELECT COUNT(*) as total FROM system_audit $whereClause";
        $stmt = $this->db->prepare($query);
        
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
}
