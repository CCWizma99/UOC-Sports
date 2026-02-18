<?php
session_start();
require_once 'C:/xampp/htdocs/uoc-sports/config/config.php';
require_once 'C:/xampp/htdocs/uoc-sports/core/Database.php';

$pdo = Database::getConnection();

echo "<h2>Session Data</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Sports Table</h2>";
$stmt = $pdo->query("SELECT * FROM sport");
echo "<pre>";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
echo "</pre>";

echo "<h2>Practice Sessions</h2>";
$stmt = $pdo->query("SELECT * FROM practice_sessions");
echo "<pre>";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
echo "</pre>";

echo "<h2>Users Table (Coaches/Captains)</h2>";
$stmt = $pdo->query("SELECT user_id, fname, lname, type, sport_id FROM user WHERE type IN ('COACH', 'CAPTAIN')");
echo "<pre>";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
echo "</pre>";
