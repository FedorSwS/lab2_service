<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class NewsController extends Controller
{
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $news = Yii::$app->db->createCommand('SELECT * FROM news ORDER BY published_at DESC')->queryAll();
        return ['page' => 'news/index', 'items' => $news];
    }

    public function actionView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $item = Yii::$app->db->createCommand('SELECT * FROM news WHERE id = :id', [':id' => (int)$id])->queryOne();
        return ['page' => 'news/view', 'item' => $item];
    }

    public function actionToggleLike($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $newsId = (int)$id;
        $token = Yii::$app->request->cookies->getValue('news_client_token', Yii::$app->request->userIP);

        $existing = Yii::$app->db->createCommand(
            'SELECT id FROM news_likes WHERE news_id = :nid AND client_token = :tok',
            [':nid' => $newsId, ':tok' => $token]
        )->queryScalar();

        if ($existing) {
            Yii::$app->db->createCommand()->delete('news_likes', ['id' => $existing])->execute();
            Yii::$app->db->createCommand('UPDATE news SET likes_count = GREATEST(likes_count - 1, 0) WHERE id = :id', [':id' => $newsId])->execute();
            $liked = false;
        } else {
            Yii::$app->db->createCommand()->insert('news_likes', ['news_id' => $newsId, 'client_token' => $token])->execute();
            Yii::$app->db->createCommand('UPDATE news SET likes_count = likes_count + 1 WHERE id = :id', [':id' => $newsId])->execute();
            $liked = true;
        }

        $count = (int)Yii::$app->db->createCommand('SELECT likes_count FROM news WHERE id = :id', [':id' => $newsId])->queryScalar();
        return ['success' => true, 'news_id' => $newsId, 'liked' => $liked, 'likes_count' => $count];
    }

    public function actionAbout()
    {
        return 'Новостной портал с AJAX-счётчиком Like (Yii2 Framework)';
    }

    public function actionAdmin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['status' => 'admin panel'];
    }
}
