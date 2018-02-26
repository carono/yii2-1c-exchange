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

$items = Article::formMenuItems($article->id);
if ($items) {
    Panel::begin();
    echo Menu::widget(['items' => $items, 'options' => ['class' => 'article-menu']]);
    Panel::end();
}

Panel::begin(['id' => 'article-content']);
echo $article->content;
Panel::end();

$query = Article::find()
    ->orderBy(['{{%article}}.[[pos]]' => SORT_ASC])
    ->andWhere(['<>', '[[id]]', $article->id])
    ->andWhere(['>=', '[[pos]]', $article->pos]);
$next = $query->one();

$query = Article::find()
    ->orderBy(['{{%article}}.[[pos]]' => SORT_DESC])
    ->andWhere(['<>', '[[id]]', $article->id])
    ->andWhere(['<=', '[[pos]]', $article->pos]);
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

$this->registerJs("$('#article-content').find('table').addClass('table table-striped table-bordered');");
