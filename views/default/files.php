<?php
use yii\grid\GridView;
use \yii\grid\ActionColumn;
use yii\helpers\Html;

/**
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \yii\web\View $this
 */
$this->title = 'Список файлов';

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