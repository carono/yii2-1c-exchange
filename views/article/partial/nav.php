<?php

use carono\exchange1c\models\Article;
use carono\exchange1c\widgets\Panel;
use yii\helpers\Html;

/**
 * @var Article $article
 */
$query = Article::find()
    ->orderBy(['{{%article}}.[[pos]]' => SORT_ASC])
    ->andWhere(['parent_id' => $article->parent_id])
    ->andWhere(['<>', '[[id]]', $article->id])
    ->andWhere(['>=', '[[pos]]', $article->pos])
    ->orWhere(['parent_id' => $article->id]);
$next = $query->one();

$query = Article::find()
    ->orderBy(['{{%article}}.[[pos]]' => SORT_DESC])
    ->andWhere(['parent_id' => $article->parent_id])
    ->andWhere(['<>', '[[id]]', $article->id])
    ->andWhere(['<=', '[[pos]]', $article->pos])
    ->orWhere(['id' => $article->parent_id]);
$prev = $query->one();


Panel::begin();
if ($prev) {
    $options = ['class' => 'btn btn-primary'];
    echo Html::a('Читать ранее: ' . $prev->name, ['article/view', 'id' => $prev->id], $options);
}
if ($next) {
    $options = ['class' => 'btn btn-primary pull-right'];
    echo Html::a('Читать далее: ' . $next->name, ['article/view', 'id' => $next->id], $options);
}
Panel::end();