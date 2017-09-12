<?php


namespace carono\exchange1c\models;


use carono\exchange1c\helpers\ClassHelper;
use carono\exchange1c\helpers\ModuleHelper;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Zenwalker\CommerceML\CommerceML;

class TestingClass extends Testing
{
    protected static $property;
    protected static $required = false;

    /**
     * [['name'], 'return' => 'array|string|interface']
     * [['name'], 'return' => 'interface', 'value'=>'']
     *
     *
     * @return array
     */
    protected static function methodRules()
    {
        return [];
    }

    protected static function getParams($params)
    {
        $values = [];
        foreach ($params as $param) {
            $value = null;
            $cml = new CommerceML();
            $cml->loadImportXml(\Yii::getAlias('@vendor/carono/yii2-1c-exchange/files/xml/import.xml'));
            $cml->loadOffersXml(\Yii::getAlias('@vendor/carono/yii2-1c-exchange/files/xml/offers.xml'));
            if ($param instanceof \Closure) {
                $values[] = call_user_func($param);
            } elseif (StringHelper::startsWith($param, 'cml.')) {
                $value = ArrayHelper::getValue($cml, substr($param, 4));
            } else {
                $value = $param;
            }
            $values[] = $value;
        }
        return $values;
    }

    /**
     * @return ActiveRecord
     */
    protected static function getPropertyClass()
    {
        return self::module()->{static::$property};
    }

    /**
     * @param $test
     * @param $method
     * @param $rule
     */
    private static function validateMethodRule($test, $method, $rule)
    {
        $class = self::getPropertyClass();
        if (method_exists($class, $method)) {
            $reflectionMethod = new \ReflectionMethod($class, $method);
            if ($params = ArrayHelper::getValue($rule, 'params', [])) {
                $params = self::getParams($params);
            }
            try {
                if ($reflectionMethod->isStatic()) {
                    $methodResult = call_user_func_array("$class::$method", $params);
                } elseif (!$context = static::getContext()) {
                    $test->result = false;
                    $test->comment = 'Не найдена модель для проверки';
                    return;
                } else {
                    $methodResult = call_user_func_array([$context, $method], $params);
                }
            } catch (\Exception $e) {
                $test->result = false;
                $test->comment = $e->getMessage();
                return;
            }
            switch (ArrayHelper::getValue($rule, 'return')) {
                case "array":
                    if (!is_array($methodResult)) {
                        $test->result = false;
                        $test->comment = 'Значение должно быть массивом';
                    }
                    break;
                case "string":
                    if (!is_string($methodResult)) {
                        $test->result = false;
                        $test->comment = 'Значение должно быть строкой';
                    }
                    break;
                case "interface":
                    if ($methodResult) {
                        if (is_object($methodResult)) {
                            $reflection = new \ReflectionClass(get_class($methodResult));
                            if (!$reflection->implementsInterface(ltrim($rule['value'], '\\'))) {
                                $test->result = false;
                                $test->comment = "Результат должен имплементировать {$rule['value']}";
                            }
                        } else {
                            $test->result = false;
                            $test->comment = "Результат должен быль объектом и имплементировать {$rule['value']}";
                        }
                    } else {
                        $test->result = null;
                        $test->comment = 'Нет результата';
                    }
                    break;
                case false:
                    $test->result = null;
                    $test->comment = 'VOID';
                    break;
                default:
                    $test->comment = 'FAIL';
                    $test->result = false;
            }
        } else {
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
                $test->addError('', 'Need implement: ' . $class);
            }
            $test->comment = "Не реализованы:<br>" . join("<br>", $comment);
        }
        return $test;
    }
}