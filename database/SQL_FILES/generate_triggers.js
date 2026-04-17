const fs = require('fs');
const sql = fs.readFileSync('Main.sql', 'utf8');

const tables = ['budget', 'sport_expenses', 'equipment_inventory', 'tournament'];
let out = '';

tables.forEach(t => {
    const rx = new RegExp('CREATE TABLE IF NOT EXISTS ?' + t + '?\\s*\\(([\\s\\S]*?)\\)(?:\\s*ENGINE|;)');
    const match = sql.match(rx);
    if (!match) return;
    
    // Parse columns
    const body = match[1].split('\n');
    const cols = [];
    body.forEach(line => {
        const lineTrim = line.trim();
        if (lineTrim.startsWith('')) {
            const colName = lineTrim.split('')[1];
            if (colName) cols.push(colName);
        }
    });

    if (cols.length === 0) return;
    const pk = cols[0];

    out += -- -------------------------------------------------------------------------\n;
    out += --  TABLE TRIGGERS\n;
    out += -- -------------------------------------------------------------------------\n\n;

    out += CREATE TRIGGER trg__insert\n;
    out += AFTER INSERT ON \${t}\\n;
    out += FOR EACH ROW\n;
    out += BEGIN\n;
    out +=     DECLARE v_audit_id INT;\n;
    out +=     INSERT INTO system_audit (table_name, record_id, action, changed_by)\n;
    out +=     VALUES ('', NEW., 'INSERT', @current_user_id);\n;
    out +=     SET v_audit_id = LAST_INSERT_ID();\n;
    out +=     INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES \n;
    
    const insertRows = cols.map(c =>     (v_audit_id, '', NULL, NEW.));
    out += insertRows.join(',\n') + ';\n';
    out += END \n\n;

    out += CREATE TRIGGER trg__update\n;
    out += AFTER UPDATE ON \${t}\\n;
    out += FOR EACH ROW\n;
    out += BEGIN\n;
    out +=     DECLARE v_audit_id INT;\n;
    
    const conditions = cols.map(c => NEW. <=> OLD.);
    out +=     IF NOT () THEN\n;
    out +=         INSERT INTO system_audit (table_name, record_id, action, changed_by)\n;
    out +=         VALUES ('', NEW., 'UPDATE', @current_user_id);\n;
    out +=         SET v_audit_id = LAST_INSERT_ID();\n;
    
    cols.forEach(c => {
        out +=         IF NOT (NEW. <=> OLD.) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, '', OLD., NEW.); END IF;\n;
    });
    
    out +=     END IF;\n;
    out += END \n\n;

    out += CREATE TRIGGER trg__delete\n;
    out += AFTER DELETE ON \${t}\\n;
    out += FOR EACH ROW\n;
    out += BEGIN\n;
    out +=     DECLARE v_audit_id INT;\n;
    out +=     INSERT INTO system_audit (table_name, record_id, action, changed_by)\n;
    out +=     VALUES ('', OLD., 'DELETE', @current_user_id);\n;
    out += END \n\n;
});

fs.writeFileSync('append_to_main2.sql', out);
