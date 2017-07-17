<?php


namespace carono\exchange1c;


use carono\exchange1c\helpers\ModuleHelper;
use yii\base\Application;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     *
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        if (ModuleHelper::getModuleNameByClass('carono\exchange1c\ExchangeModule')) {
            \Yii::$app->urlManager->addRules(['class' => 'carono\exchange1c\UrlRule']);
        }
    }
}