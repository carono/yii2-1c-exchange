<?php


namespace carono\exchange1c;


use yii\base\Event;

class ExchangeEvent extends Event
{
    public $model;
    public $ml;
    public $ids = [];
    public $filePath;
}