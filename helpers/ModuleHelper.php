<?php


namespace carono\exchange1c\helpers;


class ModuleHelper
{
    public static function getModuleNameByClass($class, $default = null)
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
}