<?php


namespace carono\exchange1c\models;


use carono\exchange1c\helpers\ClassHelper;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @property integer status_id
 * @property string status_name
 */
class InterfaceModel extends Model
{
    const STATUS_UNKNOWN = -1;
    const STATUS_METHOD_NOT_FOUND = 1;
    public static $statuses = [
        self::STATUS_METHOD_NOT_FOUND => 'Метод не найден'
    ];
    public $function;
    public $interface;
    public $class;

    public function getStatus_id()
    {
        if (!$this->functionExists()) {
            return self::STATUS_METHOD_NOT_FOUND;
        }
        return self::STATUS_UNKNOWN;
    }

    public function getStatus_name()
    {
        return ArrayHelper::getValue(self::$statuses, $this->status_id, $this->status_id);
    }

    public function functionExists()
    {
        return array_search($this->function, ClassHelper::getMethods($this->class)) !== false;
    }

    public function getDescription()
    {
        $method = new \ReflectionMethod($this->interface, $this->function);
        return nl2br(preg_replace('#\*/|/\*|\*#', '', $method->getDocComment()));
    }
}