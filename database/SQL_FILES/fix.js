const fs = require('fs');
let sql = fs.readFileSync('Main.sql', 'utf8');

// The file currently has:
// DELIMITER ;
// DROP TRIGGER IF EXISTS trg_budget_insert;
// DROP ...;
// CREATE TRIGGER trg_budget_insert

// We want to wrap each block of CREATE TRIGGER ... END  with DELIMITER  ... DELIMITER ;
// Actually, it's easiest just to replace \nCREATE TRIGGER with \nDELIMITER \nCREATE TRIGGER 
// and \nEND \n with \nEND \nDELIMITER ;\n
// Wait, the file already has some DELIMITER commands.
// Let's just fix the section for BUDGET, SPORT_EXPENSES, EQUIPMENT_INVENTORY, and TOURNAMENT.

let fixedSql = sql.replace(/DELIMITER ;\s*-- -------------------------------------------------------------------------\s*-- BUDGET TABLE TRIGGERS/g, '-- -------------------------------------------------------------------------\n-- BUDGET TABLE TRIGGERS');

fixedSql = fixedSql.replace(/CREATE TRIGGER trg_budget_insert/g, 'DELIMITER \nCREATE TRIGGER trg_budget_insert');
fixedSql = fixedSql.replace(/CREATE TRIGGER trg_sport_expenses_insert/g, 'DELIMITER \nCREATE TRIGGER trg_sport_expenses_insert');
fixedSql = fixedSql.replace(/CREATE TRIGGER trg_equipment_inventory_insert/g, 'DELIMITER \nCREATE TRIGGER trg_equipment_inventory_insert');
fixedSql = fixedSql.replace(/CREATE TRIGGER trg_tournament_insert/g, 'DELIMITER \nCREATE TRIGGER trg_tournament_insert');

// Change END  followed by DROP TRIGGER to END \nDELIMITER ;\n
fixedSql = fixedSql.replace(/END \$\$\s*-- -------------------------------------------------------------------------\s*-- SPORT_EXPENSES/g, 'END \nDELIMITER ;\n-- -------------------------------------------------------------------------\n-- SPORT_EXPENSES');
fixedSql = fixedSql.replace(/END \$\$\s*-- -------------------------------------------------------------------------\s*-- EQUIPMENT_INVENTORY/g, 'END \nDELIMITER ;\n-- -------------------------------------------------------------------------\n-- EQUIPMENT_INVENTORY');
fixedSql = fixedSql.replace(/END \$\$\s*-- -------------------------------------------------------------------------\s*-- TOURNAMENT/g, 'END \nDELIMITER ;\n-- -------------------------------------------------------------------------\n-- TOURNAMENT');

// Add DELIMITER ; at the very end of the file
if (!fixedSql.trim().endsWith('DELIMITER ;')) {
    fixedSql = fixedSql.replace(/END \$\$\s*$/g, 'END \nDELIMITER ;\n');
}

fs.writeFileSync('Main.sql', fixedSql);
