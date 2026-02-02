<?php
declare(strict_types=1);

class Database {
    private static ?PDO $instance = null;
    
    public static function getInstance(): PDO {
        if (!self::$instance) {
            try {
                self::$instance = new PDO(
                    'mysql:host=localhost;dbname=sr99_salon;charset=utf8mb4',
                    'your_db_user',
                    'your_db_password',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $e) {
                die('Database connection failed: ' . $e->getMessage());
            }
        }
        return self::$instance;
    }
    
    private function __construct() {}
    private function __clone() {}
}