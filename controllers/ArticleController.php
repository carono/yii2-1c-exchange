<?php


namespace carono\exchange1c\controllers;


use carono\exchange1c\models\Article;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Html;

/**
 * Class ArticleController
 *
 * @package carono\exchange1c\controllers
 */
class ArticleController extends Controller
{
    public function actionCreate($parent = null)
    {
        $article = new Article();
        $article->pos = Article::find()->andWhere(['[[parent_id]]' => $parent])->max('pos') + 10;
        $article->parent_id = $parent;
        if ($article->load(\Yii::$app->request->post())) {
            if ($article->save()) {
                return $this->redirect(['article/index']);
            }

            \Yii::$app->session->setFlash('error', Html::errorSummary($article));
        }
        return $this->render('update', ['article' => $article]);
    }

    public function actionUpdate($id)
    {
        $article = Article::findOne($id, true);
        if ($article->load(\Yii::$app->request->post())) {
            if ($article->save()) {
                return $this->redirect(['article/view', 'id' => $article->id]);
            }

            \Yii::$app->session->setFlash('error', Html::errorSummary($article));
        }
        return $this->render('update', ['article' => $article]);
    }

    public function actionView($id)
    {
        Yii::$app->getView()->registerJs("$('pre').each(function(i, block) { hljs.highlightBlock(block); });");
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

    /**
     * Удаляем все реальные файлы, которые не используются в статьях
     */
    public function actionDeleteUnusedImages()
    {
        $content = Article::find()->select(['content' => 'group_concat([[content]])'])->scalar();
        $dir = Yii::getAlias(Yii::$app->controller->module->redactor->uploadDir);
        $files = Article::extractFilesFromString($content);
        $realFiles = FileHelper::findFiles($dir);
        array_walk($realFiles, function (&$item) use ($dir) {
            $item = str_replace('\\', '/', substr($item, strlen($dir)));
        });
        foreach (array_diff($realFiles, $files) as $file) {
            unlink($dir . '/' . $file);
        };
        return $this->redirect(['article/index']);
    }

    public function actionCreateReadme()
    {
        $badges = [];
        $lines = [];
        $titles = Article::formTitleItems();

        $badges[] = '[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/carono/yii2-1c-exchange/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/carono/yii2-1c-exchange/?branch=master)';
        $badges[] = '[![Latest Stable Version](https://poser.pugx.org/carono/yii2-1c-exchange/v/stable)](https://packagist.org/packages/carono/yii2-1c-exchange)';
        $badges[] = '[![Total Downloads](https://poser.pugx.org/carono/yii2-1c-exchange/downloads)](https://packagist.org/packages/carono/yii2-1c-exchange)';
        $badges[] = '[![License](https://poser.pugx.org/carono/yii2-1c-exchange/license)](https://packagist.org/packages/carono/yii2-1c-exchange)';
        $badges[] = "\n";
        $titles[] = "\n";
        foreach (Article::formContentItems() as $item) {
            $titles[] = $item;
        }
        $titles[] = "\n";
        $content = implode("\n", array_merge($badges, $titles, $lines));
        file_put_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'README.md', trim($content));
    }
}