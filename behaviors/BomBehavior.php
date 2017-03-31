<?php

namespace carono\exchange1c\behaviors;

use yii\base\ActionFilter;

class BomBehavior extends ActionFilter
{
    public function beforeAction($action)
    {
        echo chr(0xEF) . chr(0xBB) . chr(0xBF);
        return parent::beforeAction($action);
    }
}