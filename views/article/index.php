<?php

use carono\exchange1c\models\Article;
use yii\widgets\Menu;
use carono\exchange1c\widgets\Panel;

/**
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \yii\web\View $this
 */
$this->title = 'Документация по работе модуля';
$items = Article::formMenuItems();
Panel::begin();
echo Menu::widget([
    'items' => $items,
    'options' => ['class' => 'article-menu']
]);
Panel::end();
