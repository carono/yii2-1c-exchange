<?php

use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use carono\exchange1c\widgets\Panel;

/**
 * @var \yii\web\View $this
 * @var \carono\exchange1c\ExchangeModule $module
 */

$module = Yii::$app->controller->module;

$data = [
    ['name' => 'productClass', 'value' => $module->productClass],
    ['name' => 'documentClass', 'value' => $module->documentClass],
    ['name' => 'groupClass', 'value' => $module->groupClass],
    ['name' => 'partnerClass', 'value' => $module->partnerClass],
];
Panel::begin();
echo GridView::widget([
    'dataProvider' => new \yii\data\ArrayDataProvider(['allModels' => $data]),
    'columns' => [
        'name',
        'value',
        [
            'class' => ActionColumn::className(),
            'template' => '{download}',
            'buttons' => [
                'download' => function ($url, $data) {
                    $span = Html::tag('i', '', ['class' => 'glyphicon glyphicon-cog']);
                    return Html::a($span, ['interface/check', 'variable' => $data['name']]);
                }
            ]
        ]
    ]
]);
Panel::end();