<?php


namespace carono\exchange1c\models;


use carono\exchange1c\helpers\ClassHelper;
use carono\exchange1c\helpers\ModuleHelper;

class TestingClass extends Testing
{
    protected static $property;

    public static function testPropertyIsSet()
    {
        $property = static::$property;
        $test = new self();
        $test->name = ModuleHelper::getModuleNameByClass() . "->{$property}";
        if (!self::module()->{$property}) {
            $test->result = false;
            $test->comment = "Необходимо прописать '$property' в модуле '" . ModuleHelper::getModuleNameByClass() . "'";
        }
        return $test;
    }

    public static function testImplementsClass()
    {
        $property = static::$property;
        $test = new self();
        $test->name = "Реализация интерфейсов $property (" . self::module()->{$property} . ")";
        $implements = ClassHelper::getImplementedMethods(self::module()->{$property}, ModuleHelper::getPhpDocInterfaceProperty($property));
        $implements = array_filter($implements, function ($data) {
            return !$data;
        });
        if ($implements) {
            $test->result = false;
            $comment = [];
            foreach ($implements as $class => $value) {
                $comment[] = $class;
            }
            $test->comment = "Не реализованы:<br>" . join("<br>", $comment);
        }
        return $test;
    }

    public static function testGetFields1c()
    {
        $class = self::module()->{static::$property};
        if (method_exists($class, 'getFields1c')) {
            $test = new self();
            $test->name = "Результат 'getFields1c'";
            if (!$test->result = is_array(call_user_func("$class::getFields1c"))) {
                $test->comment = 'значение должно быть массивом';
            }
            return $test;
        }
        return null;
    }
}