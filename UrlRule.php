<?php


namespace carono\exchange1c;


use carono\exchange1c\helpers\ModuleHelper;

class UrlRule extends \yii\web\UrlRule
{
    public $route = 'exchange/api/<mode>';
    public $pattern = '1c_exchange.php';

    public function init()
    {
        $this->route = ModuleHelper::getModuleNameByClass('carono\exchange1c\ExchangeModule', 'exchange') . '/api/<mode>';
        parent::init();
    }

    public function parseRequest($manager, $request)
    {
        $this->defaults = ['mode' => \Yii::$app->request->get('mode', 'error')];
        return parent::parseRequest($manager, $request);
    }
}