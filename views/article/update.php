<?php

use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\redactor\widgets\Redactor;
use carono\exchange1c\models\Article;

/**
 * @var Article $article
 */
$articles = ArrayHelper::map(Article::find()->select(['id', 'name'])->asArray()->all(), 'id', 'name');
$form = ActiveForm::begin();
echo $form->field($article, 'name');
echo $form->field($article, 'parent_id')->dropDownList($articles, ['prompt' => '']);
echo $form->field($article, 'content')->widget(Redactor::className(), [
    'clientOptions' => [
        'lang' => 'ru',
        'plugins' => ['clips', 'fontcolor', 'imagemanager'],
    ],
]);
echo \yii\helpers\Html::submitButton();
ActiveForm::end();
