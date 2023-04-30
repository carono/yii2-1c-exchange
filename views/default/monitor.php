<?php
/**
 * @var ActiveDataProvider $dataProvider
 * @var View $this
 */

use carono\exchange1c\widgets\Panel;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

$this->title = 'Монитор запросов';

if (!Yii::$app->controller->module->debug) {
    echo Html::tag('div', 'Требуется включить debug режим в модуле', ['class' => 'alert alert-danger']);
    return;
}
Panel::begin();
echo Html::a('Очистить всё', ['default/clear-monitor'], [
    'class' => ['btn btn-warning'],
    'data-confirm' => 'Очистить все временные файлы?'
]);

echo "<hr>";

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'url',
        'route',
        'get',
        'post',
        'user_id',
        'http_code',
        'response',
        'created_at:datetime'
    ]
]);
Panel::end();