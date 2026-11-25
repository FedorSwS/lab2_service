<?php
// Серверный обработчик AJAX-запросов для увеличения/уменьшения счётчика Like
header('Content-Type: application/json; charset=utf-8');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Метод не поддерживается. Используйте POST.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!is_array($data)) {
    $data = $_POST;
}

$newsId = isset($data['news_id']) ? (int)$data['news_id'] : 0;
if ($newsId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Некорректный идентификатор новости.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Проверяем существование новости
$stmtCheckNews = mysqli_prepare($link, 'SELECT likes_count FROM news WHERE id = ?');
mysqli_stmt_bind_param($stmtCheckNews, 'i', $newsId);
mysqli_stmt_execute($stmtCheckNews);
$resNews = mysqli_stmt_get_result($stmtCheckNews);
$newsRow = mysqli_fetch_assoc($resNews);
mysqli_stmt_close($stmtCheckNews);

if (!$newsRow) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Новость не найдена.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Проверяем, ставил ли текущий клиент лайк этой новости
$stmtCheckLike = mysqli_prepare($link, 'SELECT id FROM news_likes WHERE news_id = ? AND client_token = ?');
mysqli_stmt_bind_param($stmtCheckLike, 'is', $newsId, $clientToken);
mysqli_stmt_execute($stmtCheckLike);
$resLike = mysqli_stmt_get_result($stmtCheckLike);
$existingLike = mysqli_fetch_assoc($resLike);
mysqli_stmt_close($stmtCheckLike);

mysqli_begin_transaction($link);

try {
    if ($existingLike) {
        // Уменьшение счётчика (снятие лайка)
        $stmtDel = mysqli_prepare($link, 'DELETE FROM news_likes WHERE id = ?');
        mysqli_stmt_bind_param($stmtDel, 'i', $existingLike['id']);
        mysqli_stmt_execute($stmtDel);
        mysqli_stmt_close($stmtDel);

        $stmtUpd = mysqli_prepare($link, 'UPDATE news SET likes_count = GREATEST(likes_count - 1, 0) WHERE id = ?');
        mysqli_stmt_bind_param($stmtUpd, 'i', $newsId);
        mysqli_stmt_execute($stmtUpd);
        mysqli_stmt_close($stmtUpd);

        $liked = false;
    } else {
        // Увеличение счётчика (добавление лайка)
        $stmtIns = mysqli_prepare($link, 'INSERT INTO news_likes (news_id, client_token) VALUES (?, ?)');
        mysqli_stmt_bind_param($stmtIns, 'is', $newsId, $clientToken);
        mysqli_stmt_execute($stmtIns);
        mysqli_stmt_close($stmtIns);

        $stmtUpd = mysqli_prepare($link, 'UPDATE news SET likes_count = likes_count + 1 WHERE id = ?');
        mysqli_stmt_bind_param($stmtUpd, 'i', $newsId);
        mysqli_stmt_execute($stmtUpd);
        mysqli_stmt_close($stmtUpd);

        $liked = true;
    }

    // Получаем актуальное значение счётчика
    $stmtCount = mysqli_prepare($link, 'SELECT likes_count FROM news WHERE id = ?');
    mysqli_stmt_bind_param($stmtCount, 'i', $newsId);
    mysqli_stmt_execute($stmtCount);
    $resCount = mysqli_stmt_get_result($stmtCount);
    $rowCount = mysqli_fetch_assoc($resCount);
    mysqli_stmt_close($stmtCount);

    mysqli_commit($link);

    echo json_encode([
        'success' => true,
        'news_id' => $newsId,
        'likes_count' => (int)$rowCount['likes_count'],
        'liked' => $liked
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    mysqli_rollback($link);
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Ошибка обновления базы данных.'], JSON_UNESCAPED_UNICODE);
}

mysqli_close($link);
?>
