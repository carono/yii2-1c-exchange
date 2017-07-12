<?php


namespace carono\exchange1c\helpers;


use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

class ClassHelper
{
    /**
     * @param $class
     * @return mixed
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
     * @return \ReflectionMethod[] array
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
        return $result;
    }
}