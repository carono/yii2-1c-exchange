<?php


namespace carono\exchange1c\interfaces;


use yii\db\ActiveRecordInterface;

interface DocumentInterface extends ActiveRecordInterface
{
    public static function getFields1c();

    public static function findOrders1c();

    public function getProducts1c();

    public function getRequisites1c();
}