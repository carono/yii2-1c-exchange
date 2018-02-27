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
        if ($id = ModuleHelper::getModuleNameByClass()) {
            if (Yii::$app->getModule($id)->bootstrapUrlRule) {
                Yii::$app->urlManager->enablePrettyUrl = true;
                Yii::$app->urlManager->addRules([new UrlRule]);
            }
        }
    }

    public function __construct()
    {
    }
}