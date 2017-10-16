<?php

use yii\grid\GridView;
use carono\exchange1c\widgets\TestingGridView;
use yii\helpers\Html;
use yii\data\ArrayDataProvider;
use carono\exchange1c\widgets\Panel;

/**
 * @var \yii\web\View $this
 * @var \carono\exchange1c\models\TestingClass $testingClass
 * @var \carono\exchange1c\models\TestingClass $resultClass
 */
$this->title = 'Тестирование модуля';

$links = [
    'groupClass' => ['testing/index', 'class' => 'TestingGroupClass'],
    'productClass' => ['testing/index', 'class' => 'TestingProductClass'],
];
?>
    <div class="panel">
        <div class="btn-group panel-body">
            <?php
            foreach ($links as $name => $link) {
                echo Html::a($name, $link, ['class' => 'btn btn-primary']);
            }
            ?>
        </div>
    </div>

    <div class="alert alert-danger">
        Внимание! Будет происходить тестирование вашей реализации методов.<br>
        Настоятельно не рекомеднуется производить тестирование для боевой базе.
    </div>

<?php
if (!$testingClass) {
    return;
}


$transaction = Yii::$app->db->beginTransaction();

Panel::begin();
$testingProductClass = $testingClass::testImplementsClass();
if (!$testingProductClass->hasErrors()) {
    echo TestingGridView::widget([
        'caption' => $testingClass->caption,
        'dataProvider' => new ArrayDataProvider(['allModels' => $testingClass::findAll()]),
    ]);
} else {
    $a = Html::a('productClass', ['interface/check', 'variable' => 'groupClass']);
    echo Html::tag('div', 'Ошибки реализации интерфейсов ' . $a, ['class' => 'alert alert-danger']);
}
Panel::end();

if ($resultClass) {
    Panel::begin();
    $result = $resultClass->getResult();
    if (is_array($result)) {
        echo GridView::widget(['dataProvider' => new ArrayDataProvider(['allModels' => $result])]);
    } elseif (is_string($result)) {
        echo $result;
    } elseif (is_object($result)) {
        echo \yii\widgets\DetailView::widget(['model' => $result]);
    } else {
        var_dump($result);
    }
    Panel::end();
}

$transaction->rollBack();