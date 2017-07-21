<?php


namespace carono\exchange1c\models;


use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

class Testing extends Model
{
    public $name;
    public $result = true;
    public $comment = 'OK';

    /**
     * @return \carono\exchange1c\ExchangeModule
     */
    public static function module()
    {
        return \Yii::$app->controller->module;
    }

    /**
     * @return array
     */
    public static function findAll()
    {
        $reflection = new \ReflectionClass(self::className());
        $methods = $reflection->getMethods(\ReflectionMethod::IS_STATIC);
        $methods = ArrayHelper::map(array_filter($methods, function ($data) {
            return StringHelper::startsWith($data->name, 'test');
        }), 'name', 'name');

        $data = [];
        foreach ($methods as $method) {
            if ($test = call_user_func(static::className() . "::$method")) {
                $data[] = $test;
            }
        }
        return $data;
    }
}