<?php
use yii\widgets\DetailView;

/**
 * @var \yii\web\View $this
 */
$this->title = 'Настройки модуля';

echo DetailView::widget([
    'model' => Yii::$app->controller->module,
    'attributes' => [
        'exchangeDocuments:boolean',
        'debug:boolean',
        'productClass',
        'documentClass',
        'useZip:boolean',
        'tmpDir',
        'validateModelOnSave:boolean',
        'timeLimit'
    ]
]);