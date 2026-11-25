<?php
require_once 'config.php';

$sql = "
    SELECT n.id, n.title, n.summary, n.content, n.published_at, n.likes_count,
           CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END AS is_liked
    FROM news n
    LEFT JOIN news_likes nl
        ON nl.news_id = n.id AND nl.client_token = ?
    ORDER BY n.published_at DESC
";

$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, 's', $clientToken);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$newsList = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новостной портал — Лабораторная работа №2 (Вариант 4)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <h1>Новостной портал</h1>
    </div>
</header>

<main class="container">
    <div id="status-banner" class="status-banner" hidden></div>

    <?php if (empty($newsList)): ?>
        <p class="empty-msg">Новости не добавлены.</p>
    <?php else: ?>
        <?php foreach ($newsList as $item): ?>
            <article class="news-card" data-news-id="<?= (int)$item['id'] ?>">
                <div class="news-meta">
                    <time datetime="<?= htmlspecialchars($item['published_at']) ?>">
                        <?= htmlspecialchars(date('d.m.Y H:i', strtotime($item['published_at']))) ?>
                    </time>
                </div>
                <h2 class="news-title"><?= htmlspecialchars($item['title']) ?></h2>
                <p class="news-summary"><?= htmlspecialchars($item['summary']) ?></p>
                <p class="news-content"><?= nl2br(htmlspecialchars($item['content'])) ?></p>

                <div class="news-actions">
                    <button
                        type="button"
                        class="like-btn <?= $item['is_liked'] ? 'liked' : '' ?>"
                        data-news-id="<?= (int)$item['id'] ?>"
                        aria-pressed="<?= $item['is_liked'] ? 'true' : 'false' ?>"
                    >
                        <span class="like-icon"><?= $item['is_liked'] ? '♥' : '♡' ?></span>
                        <span class="like-label"><?= $item['is_liked'] ? 'Убрать Like' : 'Like' ?></span>
                        <span class="like-count" id="like-count-<?= (int)$item['id'] ?>">
                            <?= (int)$item['likes_count'] ?>
                        </span>
                    </button>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<script src="app.js"></script>
</body>
</html>
