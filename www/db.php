<?php
// Класс для работы с базой данных (может быть использован в будущем)
class Database {
    private $pdo;
    
    public function __construct() {
        // Настройки подключения к БД
        $host = 'localhost';
        $dbname = 'lab7';
        $username = 'root';
        $password = 'password';
        
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Ошибка подключения: " . $e->getMessage());
        }
    }
    
    public function logMessage($data) {
        $stmt = $this->pdo->prepare("INSERT INTO processed_messages (name, timestamp, ip) VALUES (?, ?, ?)");
        $stmt->execute([$data['name'], $data['timestamp'], $data['ip']]);
    }
}