<?php


namespace carono\exchange1c\controllers;


use carono\exchange1c\models\Article;
use yii\helpers\Html;

class ArticleController extends Controller
{
    public function actionCreate()
    {
        $article = new Article();
        if ($article->load(\Yii::$app->request->post())) {
            if ($article->save()) {
                return $this->redirect(['default/start']);
            } else {
                \Yii::$app->session->setFlash('error', Html::errorSummary($article));
            }
        }
        return $this->render('update', ['article' => $article]);
    }

    public function actionUpdate($id)
    {
        $article = Article::findOne($id, true);
        if ($article->load(\Yii::$app->request->post())) {
            if ($article->save()) {
                return $this->redirect(['default/start']);
            } else {
                \Yii::$app->session->setFlash('error', Html::errorSummary($article));
            }
        }
        return $this->render('update', ['article' => $article]);
    }

    public function actionView($id)
    {
        $article = Article::findOne($id, true);
        return $this->render('view', ['article' => $article]);
    }

    public function actionDelete($id)
    {
        Article::findOne($id, true)->delete();
        return $this->redirect(['default/start']);
    }

    public function actionIndex()
    {
        $dataProvider = Article::find()->search();
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }
}