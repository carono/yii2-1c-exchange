<?php


namespace carono\exchange1c\models;

use carono\exchange1c\interfaces\GroupInterface;
use Zenwalker\CommerceML\Model\Group;

/**
 * Class TestingProductClass
 *
 * @package carono\exchange1c\models
 */
class TestingGroupClass extends TestingClass
{
    public $caption = 'Тестирование интерфейса группы';
    protected static $property = 'groupClass';

    /**
     * @return array
     */
    public function getResultCreateTree1c()
    {
        /**
         * @var Group $group
         * @var GroupInterface $class
         */
        self::getMethodResult('createTree1c', ['cml.classifier.groups']);
        $params = self::getParams(['cml.classifier.groups']);
        $class = self::getPropertyClass();
        return $this->createTree($params[0], $class);
    }

    /**
     * @param Group[] $groups
     * @param GroupInterface $class
     * @return array
     */
    private function createTree($groups, $class)
    {
        $result = [];
        $id = $class::getIdFieldName1c();
        foreach ($groups as $group) {
            if ($model = $class::find()->andWhere([$id => $group->id])->one()) {
                $result[] = $model;
            }
            if ($children = $group->getChildren()) {
                $result += $this->createTree($children, $class);
            }
        }
        return $result;
    }

    public static function methodRules()
    {
        return [
            'getIdFieldName1c' => [
                'return' => 'string',
                'auto' => true
            ],
            'createTree1c' => [
                'return' => false,
                'params' => ['cml.classifier.groups']
            ],
        ];
    }
}