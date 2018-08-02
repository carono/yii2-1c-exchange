<?php

use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\redactor\widgets\Redactor;
use carono\exchange1c\models\Article;
use carono\exchange1c\widgets\Panel;
use carono\exchange1c\helpers\ModuleHelper;
use yii\redactor\RedactorModule;
use yii\helpers\Html;

/**
 * @var Article $article
 * @var \yii\web\View $this
 */
$module = Yii::$app->controller->module->redactor;
if (!$module) {
    echo '<div class="alert alert-warning">Для редактирования, необходимо установить редактор <b>composer require yiidoc/yii2-redactor</b></div>';
}

$this->title = $article->isNewRecord ? 'Добавить статью' : 'Редактирование ' . $article->name;
$articles = ArrayHelper::map(Article::find()->select(['id', 'name'])->asArray()->all(), 'id', 'name');
Panel::begin();
$form = ActiveForm::begin();
echo $form->field($article, 'name');
echo $form->field($article, 'pos')->textInput(['type' => 'number']);
echo $form->field($article, 'parent_id')->dropDownList($articles, ['prompt' => '']);
if ($module) {
    echo $form->field($article, 'content')->widget(Redactor::class, [
        'moduleId' => $module->id,
        'clientOptions' => [
            'lang' => 'ru',
            'plugins' => ['clips', 'fontcolor', 'imagemanager', 'table']
        ],
    ]);
} else {
    echo $form->field($article, 'content')->textarea();
}

echo '<div class="btn-group pull-right">';
echo Html::submitButton($article->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => 'btn btn-primary']);
echo Html::a('Отмена', ['view', 'id' => $article->id], ['class' => 'btn btn-warning']);
echo '</div>';

ActiveForm::end();
Panel::end();