<?php

class Database {
    private static $pdo = null;

    public static function getConnection() {
        if (self::$pdo === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            try {
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Inject the current user into MySQL session for audit tracking triggers
                if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['user_id'])) {
                    $stmt = self::$pdo->prepare("SET @current_user_id = :user_id");
                    $stmt->execute(['user_id' => $_SESSION['user_id']]);
                } else {
                    self::$pdo->exec("SET @current_user_id = 'SYSTEM'");
                }
                
            } catch (PDOException $e) {
                die('DB Connection failed: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
