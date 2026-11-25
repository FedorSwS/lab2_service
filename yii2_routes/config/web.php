<?php
// Фрагмент конфигурации компонентов приложения Yii2 (config/web.php)
// Настройка красивых URL (маршрутизации) для новостного портала (Вариант 4)

return [
    'id' => 'news-portal-yii2',
    'basePath' => dirname(__DIR__),
    'defaultRoute' => 'news/index',
    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                '' => 'news/index',
                'news' => 'news/index',
                'news/<id:\d+>' => 'news/view',
                'POST news/<id:\d+>/like' => 'news/toggle-like',
                'about' => 'news/about',
                'admin/news' => 'news/admin',
            ],
        ],
    ],
];
