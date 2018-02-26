<?php


namespace carono\exchange1c\helpers;


class ModuleHelper
{
    /**
     * @param string $class
     * @param string|null $default
     * @return int|null|string
     */
    public static function getModuleNameByClass($class = 'carono\exchange1c\ExchangeModule', $default = null)
    {
        foreach (\Yii::$app->modules as $name => $module) {
            $result = '';
            if ((is_array($module))) {
                $result = ltrim($module['class'], '\\');
            } elseif (is_object($module)) {
                $result = get_class($module);
            }
            if ($result == ltrim($class, '\\')) {
                return $name;
            }
        }
        return $default;
    }

    /**
     * @param $variable
     * @param string $class
     * @return string|null
     */
    public static function getPhpDocInterfaceProperty($variable, $class = 'carono\exchange1c\ExchangeModule')
    {
        $reflection = new \ReflectionClass($class);
        $property = $reflection->getProperty($variable);
        if (preg_match('#@var\s+([\w\\\]+)#iu', $property->getDocComment(), $match)) {
            return $match[1];
        } else {
            return null;
        }
    }
}