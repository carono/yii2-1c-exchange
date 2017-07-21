<?php


namespace carono\exchange1c\helpers;


use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

class ClassHelper
{
    /**
     * @param $class
     * @return string[]
     */
    public static function getMethods($class)
    {
        $path = \Yii::getAlias('@' . str_replace('\\', '/', ltrim($class, '\\'))) . '.php';
        $content = file_get_contents($path);
        preg_match_all('#^[\s\w]+function\s+(.+)\s*\(#im', $content, $match);
        return ArrayHelper::getValue($match, 1, []);
    }

    /**
     * @param $interface
     * @return string[]
     */
    public static function getInterfaceMethods($interface)
    {
        $interface = new \ReflectionClass($interface);
        $result = [];
        foreach ($interface->getMethods(\ReflectionMethod::IS_ABSTRACT) as $method) {
            if (StringHelper::startsWith($method->class, 'carono\exchange1c\interfaces')) {
                $result[] = $method;
            }
        }
        return array_values(ArrayHelper::map($result, 'name', 'name'));
    }

    /**
     * @param $class
     * @param $interface
     * @return boolean[]
     */
    public static function getImplementedMethods($class, $interface)
    {
        $methods = $class ? ClassHelper::getMethods($class) : [];
        $interfaceMethods = self::getInterfaceMethods($interface);
        $result = [];
        foreach ($interfaceMethods as $interfaceMethod) {
            $result[$interfaceMethod] = array_search($interfaceMethod, $methods) !== false;
        }
        return $result;
    }
}