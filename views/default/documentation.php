<?php

use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use carono\exchange1c\widgets\Panel;

/**
 * @var \yii\data\ArrayDataProvider $dataProvider
 * @var \yii\web\View $this
 */

$this->title = 'Спецификация по стандарту CommerceML';
Panel::begin();
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'filename',
        'size:shortSize',
        [
            'class' => ActionColumn::class,
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
Panel::end();