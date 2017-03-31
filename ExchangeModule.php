<?php

namespace carono\exchange1c;

/**
 * exchange module definition class
 */
class ExchangeModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'carono\exchange1c\controllers';
    public $productClass;
    public $documentClass;
    public $debug = false;
    public $useZip = true;
    public $tmpDir = '@runtime/1c_exchange';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
