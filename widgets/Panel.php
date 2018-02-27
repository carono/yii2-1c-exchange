<?php

namespace carono\exchange1c\widgets;

use yii\base\Widget;

class Panel extends Widget
{
    public function init()
    {
        ob_start();
        ob_implicit_flush(0);
    }

    public function run()
    {
        echo \Yii::$app->getView()->render('@vendor/carono/yii2-1c-exchange/views/widgets/panel', ['content' => ob_get_clean()], $this);
    }
}