-- Страница с новостями и счётчиком "Like" 

CREATE DATABASE IF NOT EXISTS news_portal_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE news_portal_db;

CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    summary VARCHAR(500) NOT NULL,
    content TEXT NOT NULL,
    published_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    likes_count INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS news_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    news_id INT NOT NULL,
    client_token VARCHAR(64) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_news_client (news_id, client_token),
    CONSTRAINT fk_news_likes_news FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO news (title, summary, content, published_at, likes_count) VALUES
(
    'Запуск обновлённой платформы веб-разработки',
    'Представлен новый стек инструментов для быстрого создания динамических веб-приложений.',
    'Современные веб-приложения сочетают серверную обработку данных (PHP, MySQL) с асинхронным обновлением интерфейса через AJAX. Это позволяет мгновенно обновлять счётчики и ленты новостей без полной перезагрузки страницы.',
    '2026-11-20 10:15:00',
    12
),
(
    'Адаптивный дизайн и мобильный веб',
    'Более половины пользователей открывают новостные порталы со смартфонов.',
    'При проектировании новостных страниц важно учитывать размер экрана, ориентацию устройства и достаточные отступы вокруг интерактивных кнопок, чтобы нажатие на кнопку «Нравится» было удобным на любом экране.',
    '2026-11-22 14:30:00',
    8
),
(
    'Облачные технологии в хостинге веб-приложений',
    'Переход на контейнеризацию и PaaS-платформы упрощает развёртывание PHP-проектов.',
    'Использование облачных платформ (IaaS, PaaS) позволяет масштабировать нагрузку при резком росте посещаемости новостных ресурсов и автоматически выполнять резервное копирование баз данных.',
    '2026-11-24 09:00:00',
    15
);
