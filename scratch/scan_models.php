<?php
$modelsDir = 'c:/wamp64/www/uoc-sports/app/models';
$tables = [];

$files = scandir($modelsDir);
foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    
    $content = file_get_contents($modelsDir . '/' . $file);
    
    // Match common SQL patterns to find table names
    // FROM table, JOIN table, INTO table, UPDATE table
    $patterns = [
        '/FROM\s+`?([\w-]+)`?/i',
        '/JOIN\s+`?([\w-]+)`?/i',
        '/INSERT\s+INTO\s+`?([\w-]+)`?/i',
        '/UPDATE\s+`?([\w-]+)`?/i',
        '/DELETE\s+FROM\s+`?([\w-]+)`?/i'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[1] as $table) {
                $tables[strtolower($table)][] = $file;
            }
        }
    }
}

ksort($tables);
foreach ($tables as $table => $models) {
    echo "$table: " . implode(', ', array_unique($models)) . "\n";
}
