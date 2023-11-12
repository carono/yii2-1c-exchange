<?php

namespace carono\exchange1c\queue;

use carono\exchange1c\controllers\ApiController;
use carono\exchange1c\ExchangeModule;
use carono\exchange1c\helpers\ModuleHelper;
use SimpleXMLElement;
use Yii;
use yii\queue\JobInterface;
use Zenwalker\CommerceML\CommerceML;
use Zenwalker\CommerceML\Model\Offer;
use Zenwalker\CommerceML\Model\Product;

class ProductParseQueue extends ParseQueue
{
    public function execute($queue)
    {
        /**
         * @var ExchangeModule $module
         */
        $module = ModuleHelper::getModuleByClass();
        $controller = new ApiController('api', $module);
        $commerceMl = new CommerceML();
        $commerceMl->addXmls($this->importXml, $this->offerXml, $this->ordersXml);
        $productClass = $module->productClass;

        $controller->reloadClassifier($commerceMl);
        foreach ($this->xml as $xml) {
            $product = new Product($commerceMl, new SimpleXMLElement($xml));
            if (!$model = $productClass::createModel1c($product)) {
                $message = "Модель продукта не найдена, проверьте реализацию $productClass::createModel1c";
                Yii::warning($message, 'exchange1c');
                continue;
            }
            $controller->parseProduct($model, $product);
            unset($model, $product);
            gc_collect_cycles();
        }
    }
}