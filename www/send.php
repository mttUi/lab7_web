<?php
require 'QueueManager.php';

// Получаем имя из GET или POST запроса
$name = $_POST['name'] ?? $_GET['name'] ?? 'Без имени';

try {
    $q = new QueueManager();
    $data = [
        'name' => $name,
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];
    
    $q->publish($data);
    
    if (php_sapi_name() === 'cli') {
        echo "✅ Сообщение отправлено в очередь: " . json_encode($data) . "\n";
    } else {
        header('Location: index.php?success=1');
        exit;
    }
} catch (Exception $e) {
    if (php_sapi_name() === 'cli') {
        echo "❌ Ошибка: " . $e->getMessage() . "\n";
    } else {
        header('Location: index.php?error=1');
        exit;
    }
}