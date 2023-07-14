<?php

namespace carono\exchange1c\queue;

use yii\queue\RetryableJobInterface;

abstract class ParseQueue implements \yii\queue\JobInterface, RetryableJobInterface
{
    public $timeout = 3600;
    public $xml = [];
    public $importXml;
    public $offerXml;
    public $ordersXml;


    public function getTtr()
    {
        return $this->timeout;
    }

    public function canRetry($attempt, $error)
    {
        return false;
    }
}