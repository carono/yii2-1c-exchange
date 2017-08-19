<?php


namespace carono\exchange1c\models;


use carono\exchange1c\helpers\ClassHelper;
use carono\exchange1c\helpers\ModuleHelper;

class TestingClass extends Testing
{
    protected static $property;
    protected static $required = false;

    protected static function methodRules()
    {
        return [
            [['getFields1c'], 'return' => 'array']
        ];
    }

    private static function validateMethodRule($test, $method, $rule)
    {
        $class = self::module()->{static::$property};
        if (method_exists($class, $method)) {
            $reflectionMethod = new \ReflectionMethod($class, $method);
            if ($reflectionMethod->isStatic()) {
                $methodResult = call_user_func("$class::$method");
            } elseif (!$context = self::getContext()) {
                $test->result = false;
                $test->comment = 'Не найдена модель для проверки';
                return;
            } else {
                $methodResult = call_user_func([$context, $method]);
            }
            switch ($rule['return']) {
                case "array":
                    if (!is_array($methodResult)) {
                        $test->result = false;
                        $test->comment = 'значение должно быть массивом';
                    }
                    break;
                case "interface":
                    if ($methodResult) {
                        $reflection = new \ReflectionClass(get_class($methodResult));
                        if (!$reflection->implementsInterface(ltrim($rule['value'], '\\'))) {
                            $test->result = false;
                            $test->comment = "результат должен имплементировать {$rule['value']}";
                        }
                    } else {
                        $test->result = null;
                        $test->comment = 'Нет результата';
                    }
                    break;
                default:
                    $test->comment = 'FAIL';
                    $test->result = false;
            }
        } elseif ($rule['required']) {
            $test->result = false;
            $test->comment = 'Метод не реализован';
        }
    }

    public static function findAll()
    {
        $result = parent::findAll();
        foreach (static::methodRules() as $rule) {
            foreach ($rule[0] as $method) {
                $test = new self();
                $test->name = "Результат '$method'";
                self::validateMethodRule($test, $method, $rule);
                $result[] = $test;
            }
        }

        return $result;
    }

    protected static function getContext()
    {
        $class = \Yii::$app->controller->module->{static::$property};
        return InterfaceTest::findByClass($class)->getModel();
    }

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
}