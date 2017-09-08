<?php

use yii\helpers\Html;
use carono\exchange1c\widgets\Panel;

/**
 * @var \carono\exchange1c\models\Article $article
 * @var \yii\web\View $this
 */
$this->title = $article->name;
Html::a('редактировать', ['default/update-article', 'id' => $article->id], ['class' => 'btn btn-success']);

Panel::begin();
echo $article->content;
Panel::end();

