<?php
use yii\widgets\ActiveForm;
use yii\redactor\widgets\Redactor;

/**
 * @var \carono\exchange1c\models\Article $article
 */
$form = ActiveForm::begin();
echo $form->field($article, 'name');
echo $form->field($article, 'content')->widget(Redactor::className(), [
    'clientOptions' => [
        'lang' => 'ru',
        'plugins' => ['clips', 'fontcolor', 'imagemanager']
    ]
]);
echo \yii\helpers\Html::submitButton();
ActiveForm::end();
