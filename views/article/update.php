<?php

use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\redactor\widgets\Redactor;
use carono\exchange1c\models\Article;
use carono\exchange1c\widgets\Panel;

/**
 * @var Article $article
 * @var \yii\web\View $this
 */

$this->title = $article->isNewRecord ? 'Добавить статью' : 'Редактирование ' . $article->name;

$articles = ArrayHelper::map(Article::find()->select(['id', 'name'])->asArray()->all(), 'id', 'name');
Panel::begin();
$form = ActiveForm::begin();
echo $form->field($article, 'name');
echo $form->field($article, 'pos')->textInput(['type' => 'number']);
echo $form->field($article, 'parent_id')->dropDownList($articles, ['prompt' => '']);
echo $form->field($article, 'content')->widget(Redactor::className(), [
    'clientOptions' => [
        'lang' => 'ru',
        'plugins' => ['clips', 'fontcolor', 'imagemanager'],
    ],
]);
echo \yii\helpers\Html::submitButton($article->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => 'btn btn-primary']);
ActiveForm::end();
Panel::end();