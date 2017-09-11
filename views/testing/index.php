<?php

use yii\grid\GridView;
use carono\exchange1c\models\TestingProductClass;
use carono\exchange1c\models\TestingDocumentClass;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use carono\exchange1c\models\InterfaceTest;
use yii\data\ArrayDataProvider;
use carono\exchange1c\widgets\Panel;

/**
 * @var \yii\web\View $this
 * @var \carono\exchange1c\models\InterfaceTest $interfaceTest
 */
$this->title = 'Тестирование модуля';


Panel::begin();
echo $this->render('../partial/find-model', ['variable' => 'productClass']);
echo GridView::widget([
    'caption' => 'Тестирование интерфейса продукта',
    'dataProvider' => new ArrayDataProvider(['allModels' => TestingProductClass::findAll()]),
    'rowOptions' => function ($data) {
        if ($data->result === true) {
            return ['class' => 'success'];
        } elseif ($data->result === false) {
            return ['class' => 'danger'];
        } else {
            return ['class' => 'warning'];
        }
    },
    'columns' => [
        'name',
        'comment:raw'
    ]
]);
Panel::end();

Panel::begin();
echo $this->render('../partial/find-model', ['variable' => 'documentClass']);
echo GridView::widget([
    'caption' => 'Тестирование интерфейса документа',
    'dataProvider' => new ArrayDataProvider(['allModels' => TestingDocumentClass::findAll()]),
    'rowOptions' => function ($data) {
        if (!$data->result) {
            return ['class' => 'danger'];
        } else {
            return ['class' => 'success'];
        }
    },
    'columns' => [
        'name',
        'comment:raw'
    ]
]);

Panel::end();

