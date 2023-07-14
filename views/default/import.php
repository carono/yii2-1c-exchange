<?php

use carono\exchange1c\models\forms\ImportForm;
use carono\exchange1c\widgets\Panel;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * @var ImportForm $model
 * @var View $this
 */
$this->title = 'Импорт';

echo Html::tag('div', 'В разработке', ['class' => 'alert alert-danger']);
echo Html::tag('div', 'Выберите zip архив выгрузки и нажмите импорт', ['class' => 'alert alert-info']);

Panel::begin();

$form = ActiveForm::begin();
echo $form->field($model, 'file')->fileInput()->label(false);
echo Html::submitButton('Импорт', ['class' => 'btn btn-warning']);
ActiveForm::end();


Panel::end();