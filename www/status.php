<?php
require 'QueueManager.php';

header('Content-Type: application/json');

try {
    $status = [
        'rabbitmq' => false,
        'worker' => false,
        'queue' => 0,
        'processed_messages' => 0
    ];

    // Проверяем подключение к RabbitMQ
    $q = new QueueManager();
    $status['rabbitmq'] = true;
    
    // Проверяем лог-файл
    if (file_exists('processed_rabbit.log')) {
        $lines = file('processed_rabbit.log');
        $status['processed_messages'] = count($lines);
    }

    // Проверяем запущен ли воркер
    exec('ps aux | grep "php worker.php" | grep -v grep', $output);
    $status['worker'] = count($output) > 0;

    echo json_encode($status, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'rabbitmq' => false,
        'worker' => false
    ], JSON_PRETTY_PRINT);
}