<?php


namespace carono\exchange1c\models;

use carono\exchange1c\interfaces\ProductInterface;

/**
 * Class TestingProductClass
 *
 * @package carono\exchange1c\models
 */
class TestingGroupClass extends TestingClass
{
    protected static $property = 'groupClass';

    public static function methodRules()
    {
        return [
            [['getIdFieldName1c'], 'return' => 'string'],
            [
                ['createTree1c'],
                'return' => false,
                'params' => ['cml.classifier.groups']
            ],
        ];
    }
}