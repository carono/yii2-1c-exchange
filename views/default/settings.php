<?php

use yii\widgets\DetailView;
use carono\exchange1c\widgets\Panel;

/**
 * @var \yii\web\View $this
 */
$this->title = 'Настройки модуля';
Panel::begin();
echo DetailView::widget([
    'model' => Yii::$app->controller->module,
    'attributes' => [
        'exchangeDocuments:boolean',
        'debug:boolean',
        'productClass',
        'documentClass',
        'groupClass',
        'partnerClass',
        'offerClass',
        'useZip:boolean',
        'tmpDir',
        'validateModelOnSave:boolean',
        'timeLimit'
    ]
]);
Panel::end();