<?php
require 'QueueManager.php';

echo "👷 Рабочий запущен (RabbitMQ)...\n";
echo "⏳ Ожидание сообщений...\n\n";

try {
    $q = new QueueManager();

    $q->consume(function($data) {
        echo "📥 Получено сообщение: " . json_encode($data) . "\n";
        
        // Имитация обработки
        echo "⏳ Обработка...";
        sleep(2);
        
        // Записываем в лог
        file_put_contents('processed_rabbit.log', json_encode($data) . PHP_EOL, FILE_APPEND);
        echo "✅ Обработано\n\n";
    });

} catch (Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
    sleep(5);
}