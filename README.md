# Лабораторная работа №2 (Вариант 4): Страница новостей со счётчиком Like (PHP + MySQL + AJAX + Laravel/Yii2)

## Быстрый запуск PHP-приложения (Задание 1)
1. Установите **XAMPP** (или OpenServer) с PHP 8+ и MySQL/MariaDB.
2. Запустите **Apache** и **MySQL** в панели управления XAMPP.
3. Откройте `http://localhost/phpmyadmin`, перейдите во вкладку **Импорт** и загрузите `php_app/database.sql`.
4. Скопируйте папку `php_app` в `C:\xampp\htdocs\news_lab2`.
5. Проверьте параметры доступа в `config.php` (`$db_user`, `$db_pass`).
6. Откройте в браузере: `http://localhost/news_lab2/index.php`.

## Маршруты Laravel (Задание 2)
Скопируйте `laravel_routes/routes/web.php` и `laravel_routes/app/Http/Controllers/NewsController.php` в созданный проект Laravel (`composer create-project laravel/laravel news-app`) и запустите `php artisan serve`.

## Маршруты Yii2 (Задание 3)
Перенесите настройки `urlManager` из `yii2_routes/config/web.php` и контроллер `yii2_routes/controllers/NewsController.php` в проект Yii2 (`composer create-project --prefer-dist yiisoft/yii2-app-basic news-yii2`) и запустите `php yii serve`.
