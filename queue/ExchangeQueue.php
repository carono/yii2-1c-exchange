<?php

namespace carono\exchange1c\queue;

use carono\exchange1c\controllers\ApiController;
use carono\exchange1c\ExchangeModule;
use carono\exchange1c\helpers\ModuleHelper;
use yii\helpers\Console;
use yii\queue\RetryableJobInterface;

class ExchangeQueue implements \yii\queue\JobInterface, RetryableJobInterface
{
    public $timeout = 3600;

    public function execute($queue)
    {
        /**
         * @var ExchangeModule $module
         */
        $module = ModuleHelper::getModuleByClass();
        $ctrl = new ApiController('api', $module);
//        Yii::$app->request->setRawBody($content);
//        $ctrl->actionFile(null, 'import.zip');

        foreach (glob(realpath($module->getTmpDir()) . '/*.xml') as $file) {
            Console::output($file);
            $ctrl->actionImport('catalog', basename($file));
        }
    }

    public function getTtr()
    {
        return $this->timeout;
    }

    public function canRetry($attempt, $error)
    {
        return false;
    }
}