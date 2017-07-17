<?php
use yii\grid\GridView;
use \yii\grid\ActionColumn;
use yii\helpers\Html;

/**
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \yii\web\View $this
 */
$this->title = "Список временных файлов ({$this->context->module->tmpDir})";

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
            'class' => ActionColumn::className(),
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