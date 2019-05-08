<?php


namespace carono\exchange1c;


use carono\exchange1c\helpers\ModuleHelper;
use yii\base\Application;
use yii\base\BootstrapInterface;
use Yii;

class Bootstrap implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     *
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        /**
         * @var ExchangeModule $module
         */
        if ($id = ModuleHelper::getModuleNameByClass()) {
            $module = Yii::$app->getModule($id);
            if ($module && $module->bootstrapUrlRule) {
                Yii::$app->urlManager->enablePrettyUrl = true;
                Yii::$app->urlManager->addRules([new UrlRule], $module->appendRule);
            }
        }
    }

    public function __construct()
    {
    }
}