<?php


namespace carono\exchange1c;


class UrlRule extends \yii\web\UrlRule
{
    public $route = 'exchange/default/<mode>';
    public $pattern = '1c_exchange.php';

    public function parseRequest($manager, $request)
    {
        $this->defaults = ['mode' => \Yii::$app->request->get('mode', 'index')];
        return parent::parseRequest($manager, $request);
    }
}