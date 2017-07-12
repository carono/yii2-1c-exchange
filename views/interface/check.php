<?php
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var string $variable
 */

$class = \Yii::$app->controller->module->{$variable};

$classes = [
    'productClass' => ['interface' => 'carono\exchange1c\interfaces\ProductInterface'],
    'documentClass' => ['interface' => 'carono\exchange1c\interfaces\DocumentInterface'],
];
$interfaceClass = $classes[$variable]['interface'];

$this->title = "Класс $class";

$interface = new ReflectionClass($interfaceClass);

$data = [];
foreach ($interface->getMethods(ReflectionMethod::IS_ABSTRACT) as $method) {
    if ($method->class == $interface->name) {
        $data[] = [
            'function' => $method->name,
            'description' => $method->getDocComment()
        ];
    }
}

echo GridView::widget([
    'dataProvider' => new ArrayDataProvider(['allModels' => $data]),
    'columns' => [
        'function',
        [
            'attribute' => 'description',
            'format' => 'raw',
            'value' => function ($data) {
                $description = trim($data['description'], '/*');
                return $data['description'] ? Html::tag('pre', $description) : '';
            }
        ]
    ]
]);