<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

echo Html::tag('div', 'В разработке', ['class' => 'alert alert-danger']);

echo Html::a('Импорт товаров', [
    '/1c_exchange.php',
    'mode' => 'import',
    'type' => 'catalog',
    'filename' => 'import.xml'
], ['class' => 'btn btn-primary', 'target' => '_blank']);
echo Html::a('Импорт предложений', [
    '/1c_exchange.php',
    'mode' => 'import',
    'type' => 'catalog',
    'filename' => 'offers.xml'
], ['class' => 'btn btn-primary', 'target' => '_blank']);

$form = ActiveForm::begin();
echo Html::submitButton('Start');
ActiveForm::end();
