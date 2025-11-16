<?php
require 'QueueManager.php';

// Получаем имя из GET или POST запроса
$name = $_POST['name'] ?? $_GET['name'] ?? '';

if (empty($name)) {
    header('Location: index.php?error=1');
    exit;
}

try {
    $q = new QueueManager();
    $data = [
        'name' => $name,
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];
    
    $q->publish($data);
    
    // Логируем отправку
    file_put_contents('sent_messages.log', json_encode($data) . PHP_EOL, FILE_APPEND);
    
    header('Location: index.php?success=1');
    exit;

} catch (Exception $e) {
    error_log("Queue error: " . $e->getMessage());
    header('Location: index.php?error=1');
    exit;
}