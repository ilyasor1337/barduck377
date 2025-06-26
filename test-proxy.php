<?php
header('Content-Type: application/json');

// Простой тест
echo json_encode([
    'status' => 'success',
    'message' => 'PHP прокси работает',
    'time' => date('Y-m-d H:i:s'),
    'server' => $_SERVER['SERVER_NAME']
]);
?> 