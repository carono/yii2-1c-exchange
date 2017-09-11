<?php

use carono\exchange1c\models\Article;
use yii\widgets\Menu;
use carono\exchange1c\widgets\Panel;

/**
 * @var \carono\exchange1c\models\Article $article
 * @var \yii\web\View $this
 */

$this->title = $article->name;

$items = Article::formMenuItems($article->id);
if ($items) {
    Panel::begin();
    echo Menu::widget(['items' => $items, 'options' => ['class' => 'article-menu']]);
    Panel::end();
}

Panel::begin();
echo $article->content;
Panel::end();
