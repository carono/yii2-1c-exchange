<?php

use yii\grid\GridView;
use carono\exchange1c\models\TestingProductClass;
use carono\exchange1c\widgets\TestingGridView;
use yii\helpers\Html;
use yii\data\ArrayDataProvider;
use carono\exchange1c\widgets\Panel;
use carono\exchange1c\models\TestingGroupClass;

/**
 * @var \yii\web\View $this
 * @var \carono\exchange1c\models\InterfaceTest $interfaceTest
 */
$this->title = 'Тестирование модуля';

$transaction = Yii::$app->db->beginTransaction();

Panel::begin();
$testingProductClass = TestingGroupClass::testImplementsClass();
if (!$testingProductClass->hasErrors()) {
    echo TestingGridView::widget([
        'caption' => 'Тестирование интерфейса группы',
        'dataProvider' => new ArrayDataProvider(['allModels' => TestingGroupClass::findAll()]),

    ]);
} else {
    $a = Html::a('productClass', ['interface/check', 'variable' => 'groupClass']);
    echo Html::tag('div', 'Ошибки реализации интерфейсов ' . $a, ['class' => 'alert alert-danger']);
}
Panel::end();

Panel::begin();
$testingProductClass = TestingProductClass::testImplementsClass();
if (!$testingProductClass->hasErrors()) {
    echo TestingGridView::widget([
        'caption' => 'Тестирование интерфейса продукта',
        'dataProvider' => new ArrayDataProvider(['allModels' => TestingProductClass::findAll()]),
    ]);
} else {
    $a = Html::a('productClass', ['interface/check', 'variable' => 'productClass']);
    echo Html::tag('div', 'Ошибки реализации интерфейсов ' . $a, ['class' => 'alert alert-danger']);
}
Panel::end();

//Panel::begin();
//echo $this->render('../partial/find-model', ['variable' => 'documentClass']);
//echo GridView::widget([
//    'caption' => 'Тестирование интерфейса документа',
//    'dataProvider' => new ArrayDataProvider(['allModels' => TestingDocumentClass::findAll()]),
//    'rowOptions' => function ($data) {
//        if (!$data->result) {
//            return ['class' => 'danger'];
//        } else {
//            return ['class' => 'success'];
//        }
//    },
//    'columns' => [
//        'name',
//        'comment:raw'
//    ]
//]);
//Panel::end();

$transaction->rollBack();