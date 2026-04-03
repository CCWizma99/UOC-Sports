<?php
/**
 * Database Seed Data Loader
 * 
 * Use this script to load seed data into the UOC Sports database
 * Access via: /uoc-sports/public/load-seed-data.php
 * 
 * ⚠️  IMPORTANT: Remove or disable this file in production!
 */

// Only allow admin access
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['confirm_seed'])) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Load Seed Data</title>
        <style>
            body { font-family: Arial; margin: 40px; background: #f5f5f5; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
            h1 { color: #333; }
            .warning { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 4px; margin: 20px 0; color: #856404; }
            .info { background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 4px; margin: 20px 0; color: #004085; }
            button { background: #dc3545; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
            button:hover { background: #c82333; }
            .back-link { display: inline-block; margin-top: 20px; }
            a { color: #007bff; text-decoration: none; }
            a:hover { text-decoration: underline; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🌱 Seed Database with Test Data</h1>
            
            <div class="warning">
                <strong>⚠️  WARNING:</strong> This will DELETE all existing data and load test data.
                This is for development/testing only. Do NOT use in production!
            </div>
            
            <div class="info">
                <strong>ℹ️  What will be loaded:</strong>
                <ul>
                    <li>24 Users (Coaches, Managers, Captains, Students, Executives)</li>
                    <li>5 Sports with faculty assignments</li>
                    <li>10 Budget records with spending data</li>
                    <li>40 Sport expenses across all sports</li>
                    <li>5 Tournaments with 10 matches</li>
                    <li>10 Practice sessions with attendance</li>
                    <li>Equipment inventory and procurement records</li>
                    <li>Facility bookings and utility data</li>
                    <li>Posts, comments, inquiries, and achievements</li>
                </ul>
            </div>
            
            <form method="POST">
                <label style="display: flex; align-items: center; margin-bottom: 15px;">
                    <input type="checkbox" name="confirm_seed" value="1" required>
                    <span style="margin-left: 10px;">I understand this will DELETE all data and I want to proceed</span>
                </label>
                <button type="submit">Load Seed Data</button>
            </form>
            
            <div class="back-link">
                <a href="/uoc-sports/public/index.php">← Back to Dashboard</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// If we get here, they confirmed - load the data
require_once '../core/Database.php';

try {
    $db = Database::getConnection();
    
    // Read the seed data file
    $seedFile = __DIR__ . '/../database/seed_data.sql';
    
    if (!file_exists($seedFile)) {
        throw new Exception("Seed data file not found: $seedFile");
    }
    
    $sqlContent = file_get_contents($seedFile);
    
    // Split by semicolon and execute each statement
    $statements = array_filter(
        array_map('trim', explode(';', $sqlContent)),
        fn($s) => !empty($s) && !preg_match('/^--/', $s)
    );
    
    $count = 0;
    $errors = [];
    
    foreach ($statements as $statement) {
        try {
            $db->exec($statement);
            $count++;
        } catch (PDOException $e) {
            $errors[] = $e->getMessage();
        }
    }
    
    // Display results
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Seed Data Loaded</title>
        <style>
            body { font-family: Arial; margin: 40px; background: #f5f5f5; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
            h1 { color: #333; }
            .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 4px; margin: 20px 0; color: #155724; }
            .error-item { background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 4px; margin: 10px 0; color: #721c24; }
            .stats { background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 4px; margin: 20px 0; color: #004085; }
            .stats strong { display: block; margin: 5px 0; }
            .back-link { display: inline-block; margin-top: 20px; }
            a { color: #007bff; text-decoration: none; }
            a:hover { text-decoration: underline; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>✅ Seed Data Loaded Successfully!</h1>
            
            <div class="success">
                <strong>Success!</strong> Your database has been populated with test data.
            </div>
            
            <div class="stats">
                <strong>Execution Statistics:</strong>
                <strong>SQL Statements Executed: <?php echo $count; ?></strong>
                <strong>Errors: <?php echo count($errors); ?></strong>
            </div>
            
            <?php if (count($errors) > 0): ?>
                <div style="margin-top: 20px;">
                    <strong style="color: #721c24;">Errors encountered:</strong>
                    <?php foreach ($errors as $error): ?>
                        <div class="error-item"><?php echo htmlspecialchars($error); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div style="margin: 30px 0; padding: 15px; background: #f9f9f9; border-radius: 4px;">
                <strong>Test Account Credentials:</strong>
                <p style="margin: 5px 0;"><code>Email: nuwan.exec@uoc.lk | Password: password | Role: Executive | Faculty: Science</code></p>
                <p style="margin: 5px 0;"><code>Email: priya.exec@uoc.lk | Password: password | Role: Executive | Faculty: Arts</code></p>
            </div>
            
            <div class="back-link">
                <a href="/uoc-sports/public/index.php">→ Go to Dashboard</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    
} catch (Exception $e) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Error Loading Seed Data</title>
        <style>
            body { font-family: Arial; margin: 40px; background: #f5f5f5; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
            h1 { color: #333; }
            .error { background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 4px; margin: 20px 0; color: #721c24; }
            .back-link { display: inline-block; margin-top: 20px; }
            a { color: #007bff; text-decoration: none; }
            a:hover { text-decoration: underline; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>❌ Error Loading Seed Data</h1>
            
            <div class="error">
                <strong>Error:</strong> <?php echo htmlspecialchars($e->getMessage()); ?>
            </div>
            
            <div class="back-link">
                <a href="/uoc-sports/public/index.php">← Back to Dashboard</a>
            </div>
        </div>
    </body>
    </html>
    <?php
}
