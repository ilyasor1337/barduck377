<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Логирование для отладки
error_log("API Proxy called: " . $_SERVER['REQUEST_URI']);

// Получаем метод запроса
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// Убираем /api-proxy.php из пути
$path = str_replace('/api-proxy.php', '', $path);

// URL Node.js сервера
$nodeUrl = 'http://127.0.0.1:3000' . $path;

error_log("Proxying to: " . $nodeUrl);

// Получаем данные запроса
$input = file_get_contents('php://input');

// Настраиваем cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $nodeUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

// Добавляем заголовки
$headers = ['Content-Type: application/json'];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Добавляем данные для POST/PUT запросов
if ($method === 'POST' || $method === 'PUT') {
    curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
}

// Выполняем запрос
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

error_log("cURL response code: " . $httpCode);
if ($error) {
    error_log("cURL error: " . $error);
}

// Если есть ошибка cURL, возвращаем ошибку
if ($error) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Ошибка подключения к серверу',
        'details' => $error
    ]);
    exit;
}

// Устанавливаем HTTP код ответа
http_response_code($httpCode);

// Возвращаем ответ
if ($response === false) {
    echo json_encode(['error' => 'Нет ответа от сервера']);
} else {
    echo $response;
}
?> 