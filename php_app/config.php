<?php
// Конфигурация подключения к базе данных MySQL (MariaDB)
// Используется расширение mysqli согласно методическим указаниям

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';          // В XAMPP по умолчанию пароль пустой; в OpenServer — '' или 'root'
$db_name = 'news_portal_db';

$link = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$link) {
    http_response_code(500);
    die('Ошибка подключения к базе данных. Код ошибки: ' . mysqli_connect_error());
}

// Явная установка кодировки UTF-8 для корректной работы с кириллицей
mysqli_set_charset($link, 'utf8mb4');

// Идентификатор клиента в cookie для отслеживания поставленных лайков (увеличение/уменьшение)
if (!isset($_COOKIE['news_client_token']) || !preg_match('/^[a-f0-9]{32}$/', $_COOKIE['news_client_token'])) {
    $clientToken = bin2hex(random_bytes(16));
    setcookie('news_client_token', $clientToken, time() + 60 * 60 * 24 * 365, '/');
} else {
    $clientToken = $_COOKIE['news_client_token'];
}
?>
