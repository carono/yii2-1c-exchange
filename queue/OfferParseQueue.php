<?php

namespace carono\exchange1c\queue;

use carono\exchange1c\controllers\ApiController;
use carono\exchange1c\ExchangeModule;
use carono\exchange1c\helpers\ModuleHelper;
use SimpleXMLElement;
use Yii;
use yii\helpers\Console;
use yii\queue\RetryableJobInterface;
use Zenwalker\CommerceML\CommerceML;
use Zenwalker\CommerceML\Model\Offer;

class OfferParseQueue extends ParseQueue
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

        foreach ($this->xml as $xml) {
            $offer = new Offer($commerceMl, new SimpleXMLElement($xml));
            $product_id = $offer->getClearId();
            if ($product = $controller->findProductModelById($product_id)) {
                $model = $product->getOffer1c($offer);
                $controller->parseProductOffer($model, $offer);
            } else {
                Yii::warning("Продукт $product_id не найден в базе", 'exchange1c');
            }
        }
    }

}