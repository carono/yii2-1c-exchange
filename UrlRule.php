<?php


namespace carono\exchange1c;


class UrlRule extends \yii\web\UrlRule
{
    public $route = 'exchange/api/<mode>';
    public $pattern = '1c_exchange.php';

    public function init()
    {
        $this->route = 'exchange/api/<mode>';
        if (!isset(\Yii::$app->modules['exchange'])) {
            foreach (\Yii::$app->modules as $name => $module) {
                $class = '';
                if ((is_array($module))) {
                    $class = ltrim($module['class'], '\\');
                } elseif (is_object($module)) {
                    $class = get_class($module);
                }
                if ($class == 'carono\exchange1c\ExchangeModule') {
                    $this->route = "$name/api/<mode>";
                    break;
                }
            }
        }
        parent::init();
    }

    public function parseRequest($manager, $request)
    {
        $this->defaults = ['mode' => \Yii::$app->request->get('mode', 'index')];
        return parent::parseRequest($manager, $request);
    }
}