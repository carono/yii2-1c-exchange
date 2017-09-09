<?php

use carono\exchange1c\models\Article;
use yii\widgets\Menu;

/**
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \yii\web\View $this
 */
$this->title = 'Документация по работе модуля';
$items = Article::formMenuItems();
echo Menu::widget(['items' => $items, 'options' => ['class' => 'article-menu']]);
