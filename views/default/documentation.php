<?php
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Html;

/**
 * @var \yii\data\ArrayDataProvider $dataProvider
 */


echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'filename',
        'size:shortSize',
        [
            'class' => ActionColumn::className(),
            'template' => '{view}',
            'buttons' => [
                'view' => function ($url, $data) {
                    $span = Html::tag('i', '', ['class' => 'glyphicon glyphicon-eye-open']);
                    return Html::a($span, ['file/doc', 'file' => $data['filename']], ['target' => '_blank']);
                }
            ]
        ]
    ]
]);