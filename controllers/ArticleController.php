<?php


namespace carono\exchange1c\controllers;


use carono\exchange1c\models\Article;
use yii\helpers\Html;

class ArticleController extends Controller
{
    public function actionCreate($parent = null)
    {
        $article = new Article();
        $article->pos = 10;
        $article->parent_id = $parent;
        if ($article->load(\Yii::$app->request->post())) {
            if ($article->save()) {
                return $this->redirect(['article/index']);
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
                return $this->redirect(['article/view', 'id' => $article->id]);
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
        return $this->redirect(['article/index']);
    }

    public function actionIndex()
    {
        $dataProvider = Article::find()->orderBy(['{{%article}}.[[pos]]' => SORT_ASC])->search();
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }
}