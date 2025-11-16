<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 7 - RabbitMQ Очередь сообщений</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .header h2 {
            font-size: 1.3rem;
            font-weight: 300;
            opacity: 0.9;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn:active {
            transform: translateY(0);
        }

        .log-container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            max-height: 400px;
            overflow-y: auto;
        }

        .log-entry {
            padding: 12px 15px;
            margin-bottom: 8px;
            background: white;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .log-time {
            font-weight: 600;
            color: #667eea;
            margin-bottom: 5px;
        }

        .log-message {
            color: #333;
        }

        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-online {
            background: #28a745;
        }

        .status-offline {
            background: #dc3545;
        }

        .links {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .link-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            text-decoration: none;
            color: #333;
            flex: 1;
            min-width: 200px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .link-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: #667eea;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .empty-state {
            text-align: center;
            color: #6c757d;
            padding: 40px;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Лабораторная работа №7</h1>
            <h2>Асинхронная обработка данных через RabbitMQ</h2>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                ✅ Сообщение успешно отправлено в очередь!
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                ❌ Ошибка при отправке сообщения
            </div>
        <?php endif; ?>

        <div class="card">
            <h3>📨 Отправка сообщения в очередь</h3>
            <form method="POST" action="send.php">
                <div class="form-group">
                    <label for="name">Имя студента:</label>
                    <input type="text" id="name" name="name" placeholder="Введите ваше имя" required>
                </div>
                <button type="submit" class="btn">Отправить в очередь RabbitMQ</button>
            </form>
        </div>

        <div class="card">
            <h3>
                <span class="status-indicator status-<?php echo file_exists('processed_rabbit.log') ? 'online' : 'offline'; ?>"></span>
                📊 Лог обработки сообщений
            </h3>
            <div class="log-container">
                <?php
                if (file_exists('processed_rabbit.log') && filesize('processed_rabbit.log') > 0) {
                    $lines = array_reverse(file('processed_rabbit.log'));
                    foreach ($lines as $line) {
                        $data = json_decode($line, true);
                        if ($data) {
                            echo '<div class="log-entry">';
                            echo '<div class="log-time">' . htmlspecialchars($data['timestamp']) . '</div>';
                            echo '<div class="log-message">👤 ' . htmlspecialchars($data['name']) . '</div>';
                            if (isset($data['ip']) && $data['ip'] !== 'unknown') {
                                echo '<div class="log-ip">🌐 IP: ' . htmlspecialchars($data['ip']) . '</div>';
                            }
                            echo '</div>';
                        }
                    }
                } else {
                    echo '<div class="empty-state">';
                    echo '📭 Очередь пуста<br><small>Отправленные сообщения появятся здесь после обработки воркером</small>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <div class="links">
            <a href="http://localhost:15672" target="_blank" class="link-card">
                🐰 Панель RabbitMQ<br>
                <small>guest / guest</small>
            </a>
            <a href="send.php?name=ТестовыйСтудент" class="link-card">
                🧪 Тестовый запрос
            </a>
            <a href="worker.php" target="_blank" class="link-card">
                👷 Запустить воркер
            </a>
        </div>
    </div>

    <script>
        // Авто-обновление лога каждые 5 секунд
        setTimeout(() => {
            location.reload();
        }, 5000);

        // Плавная прокрутка к новым сообщениям
        document.addEventListener('DOMContentLoaded', function() {
            const logContainer = document.querySelector('.log-container');
            if (logContainer.scrollHeight > logContainer.clientHeight) {
                logContainer.scrollTop = 0;
            }
        });
    </script>
</body>
</html>