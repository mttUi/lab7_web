<!DOCTYPE html>
<html>
<head>
    <title>Lab 7 - RabbitMQ</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .log { margin-top: 30px; padding: 15px; background: #f5f5f5; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Лабораторная работа №7</h1>
    <h2>Отправка сообщений в RabbitMQ</h2>
    
    <form method="POST" action="send.php">
        <div class="form-group">
            <label for="name">Имя студента:</label>
            <input type="text" id="name" name="name" placeholder="Введите ваше имя" required>
        </div>
        <button type="submit">Отправить в очередь</button>
    </form>

    <div class="log">
        <h3>Лог обработки:</h3>
        <?php
        if (file_exists('processed_rabbit.log')) {
            $lines = array_reverse(file('processed_rabbit.log'));
            foreach ($lines as $line) {
                $data = json_decode($line, true);
                if ($data) {
                    echo "<p><strong>" . htmlspecialchars($data['timestamp']) . "</strong> - " . htmlspecialchars($data['name']) . "</p>";
                }
            }
        } else {
            echo "<p>Пока нет обработанных сообщений</p>";
        }
        ?>
    </div>

    <div style="margin-top: 20px;">
        <h3>Ссылки:</h3>
        <ul>
            <li><a href="http://localhost:15672" target="_blank">Панель управления RabbitMQ (guest/guest)</a></li>
            <li><a href="send.php?name=TestUser" target="_blank">Тестовый запрос</a></li>
        </ul>
    </div>
</body>
</html>