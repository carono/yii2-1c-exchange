<?php

namespace carono\exchange1c\models;

use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * @property ActiveRecord model
 */
class InterfaceTest extends Model
{
    public $class;
    public $id;

    public function rules()
    {
        return [
            [['class', 'id'], 'safe']
        ];
    }

    public function save()
    {
        if (!$this->getModel()) {
            $this->addError('id', 'Model not found');
            return false;
        } else {
            return \Yii::$app->cache->set([self::class, $this->class], $this, 300);
        }
    }

    public function getModel()
    {
        try {
            return call_user_func("{$this->class}::findOne", $this->id);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param $class
     * @return InterfaceTest|mixed
     */
    public static function findByClass($class)
    {
        if (!$model = \Yii::$app->cache->get([self::class, $class])) {
            return new self(['class' => $class]);
        } else {
            return $model;
        }
    }
}