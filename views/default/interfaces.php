<?php

use yii\helpers\StringHelper;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use carono\exchange1c\widgets\Panel;

/**
 * @var \yii\web\View $this
 * @var \carono\exchange1c\ExchangeModule $module
 */
$this->title = 'Список интерфейсов';

$module = Yii::$app->controller->module;

$reflection = new ReflectionClass($module);
$properties = array_filter($reflection->getProperties(), function ($data) {
    return StringHelper::endsWith($data->name, 'Class');
});

Panel::begin();
echo GridView::widget([
    'dataProvider' => new \yii\data\ArrayDataProvider(['allModels' => $properties]),
    'columns' => [
        'name',
        [
            'attribute' => 'class',
            'value' => function ($data) use ($module) {
                return $module->{$data->name};
            }
        ],
        [
            'class' => ActionColumn::class,
            'template' => '{testing}',
            'buttons' => [
                'testing' => function ($url, $data) use ($module) {
                    if ($module->{$data->name}) {
                        $span = Html::tag('i', '', ['class' => 'glyphicon glyphicon-cog']);
                        return Html::a($span, ['interface/check', 'variable' => $data->name]);
                    } else {
                        return null;
                    }
                }
            ]
        ]
    ]
]);
Panel::end();