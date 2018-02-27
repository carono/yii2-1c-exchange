<?php

use yii\grid\GridView;
use \yii\grid\ActionColumn;
use yii\helpers\Html;
use carono\exchange1c\widgets\Panel;

/**
 * @var \yii\data\ArrayDataProvider $dataProvider
 * @var \yii\web\View $this
 */
$this->title = "Список временных файлов ({$this->context->module->tmpDir})";

Panel::begin();
echo Html::a('Очистить всё', ['default/clear-tmp'], [
    'class' => ['btn btn-warning'],
    'data-confirm' => 'Очистить все временные файлы?'
]);

echo "<hr>";

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'filename',
        'size:shortSize',
        [
            'class' => ActionColumn::class,
            'template' => '{download}',
            'buttons' => [
                'download' => function ($url, $data) {
                    $span = Html::tag('i', '', ['class' => 'glyphicon glyphicon-download-alt']);
                    return Html::a($span, ['default/download', 'file' => $data['filename']]);
                }
            ]
        ]
    ]
]);
Panel::end();