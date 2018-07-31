<?php

use carono\exchange1c\models\Article;
use yii\widgets\Menu;
use carono\exchange1c\widgets\Panel;
use yii\helpers\Html;

/**
 * @var \carono\exchange1c\models\Article $article
 * @var \yii\web\View $this
 */

$this->title = $article->name;

if ($items = Article::formMenuItems()) {
    Panel::begin();
    echo Menu::widget([
        'items' => $items,
        'options' => ['class' => 'article-menu'],
    ]);
    Panel::end();
}

echo $this->render('partial/nav', ['article' => $article]);

Panel::begin(['id' => 'article-content']);
echo $article->content;
Panel::end();

echo $this->render('partial/nav', ['article' => $article]);

$this->registerJs("$('#article-content').find('table').addClass('table table-striped table-bordered');");
