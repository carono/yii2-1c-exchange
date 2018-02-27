<?php


namespace carono\exchange1c\models;


use yii\base\Model;
use yii\base\Module;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

/**
 * Class Testing
 *
 * @package carono\exchange1c\models
 */
abstract class Testing extends Model
{
    public $name;
    public $method;
    public $comment;
    protected $_result;

    /**
     * @return \carono\exchange1c\ExchangeModule|Module
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
        $reflection = new \ReflectionClass(static::class);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_STATIC);
        $methods = ArrayHelper::map(array_filter($methods, function ($data) {
            return StringHelper::startsWith($data->name, 'test');
        }), 'name', 'name');
        $data = [];
        foreach ($methods as $method) {
            if ($test = call_user_func(static::class . "::$method")) {
                $data[] = $test;
            }
        }
        return $data;
    }

    /**
     * @return bool
     */
    public function testing()
    {
        return !$this->hasErrors();
    }

    /**
     * Testing constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }
}