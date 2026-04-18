<?php
$pdo = new PDO('mysql:host=localhost;dbname=uoc-sports', 'root', '');

$tables = ['user', 'budget', 'sport_expenses', 'equipment_inventory', 'tournament'];

foreach ($tables as $table) {
    $stmt = $pdo->query("SHOW COLUMNS FROM `$table`");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Primary key usually first column for these tables
    $pk = $columns[0];

    echo "-- --------------------------------------------------------\n";
    echo "-- Triggers for `$table`\n";
    echo "-- --------------------------------------------------------\n";
    echo "DROP TRIGGER IF EXISTS trg_{$table}_insert;\n";
    echo "DROP TRIGGER IF EXISTS trg_{$table}_update;\n";
    echo "DROP TRIGGER IF EXISTS trg_{$table}_delete;\n\n";

    echo "DELIMITER \$\$\n\n";

    // INSERT Trigger
    echo "CREATE TRIGGER trg_{$table}_insert\n";
    echo "AFTER INSERT ON `$table`\n";
    echo "FOR EACH ROW\n";
    echo "BEGIN\n";
    echo "    DECLARE v_audit_id INT;\n";
    echo "    INSERT INTO system_audit (table_name, record_id, action, changed_by)\n";
    echo "    VALUES ('$table', NEW.$pk, 'INSERT', @current_user_id);\n";
    echo "    SET v_audit_id = LAST_INSERT_ID();\n";
    echo "    INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES \n";
    
    $insertRows = [];
    foreach ($columns as $col) {
        $insertRows[] = "    (v_audit_id, '$col', NULL, NEW.$col)";
    }
    echo implode(",\n", $insertRows) . ";\n";
    echo "END \$\$\n\n";

    // UPDATE Trigger
    echo "CREATE TRIGGER trg_{$table}_update\n";
    echo "AFTER UPDATE ON `$table`\n";
    echo "FOR EACH ROW\n";
    echo "BEGIN\n";
    echo "    DECLARE v_audit_id INT;\n";
    
    $conditions = [];
    foreach ($columns as $col) {
        $conditions[] = "NEW.$col <=> OLD.$col";
    }
    echo "    IF NOT (" . implode(" AND ", $conditions) . ") THEN\n";
    echo "        INSERT INTO system_audit (table_name, record_id, action, changed_by)\n";
    echo "        VALUES ('$table', NEW.$pk, 'UPDATE', @current_user_id);\n";
    echo "        SET v_audit_id = LAST_INSERT_ID();\n";
    
    foreach ($columns as $col) {
        echo "        IF NOT (NEW.$col <=> OLD.$col) THEN\n";
        echo "            INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, '$col', OLD.$col, NEW.$col);\n";
        echo "        END IF;\n";
    }
    echo "    END IF;\n";
    echo "END \$\$\n\n";

    // DELETE Trigger
    echo "CREATE TRIGGER trg_{$table}_delete\n";
    echo "AFTER DELETE ON `$table`\n";
    echo "FOR EACH ROW\n";
    echo "BEGIN\n";
    echo "    DECLARE v_audit_id INT;\n";
    echo "    INSERT INTO system_audit (table_name, record_id, action, changed_by)\n";
    echo "    VALUES ('$table', OLD.$pk, 'DELETE', @current_user_id);\n";
    echo "    SET v_audit_id = LAST_INSERT_ID();\n";
    echo "    INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES \n";
    
    $deleteRows = [];
    foreach ($columns as $col) {
        $deleteRows[] = "    (v_audit_id, '$col', OLD.$col, NULL)";
    }
    echo implode(",\n", $deleteRows) . ";\n";
    echo "END \$\$\n\n";
    
    echo "DELIMITER ;\n\n";
}
?>
