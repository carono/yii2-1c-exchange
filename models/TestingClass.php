<?php


namespace carono\exchange1c\models;


use carono\exchange1c\helpers\ClassHelper;
use carono\exchange1c\helpers\ModuleHelper;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Zenwalker\CommerceML\CommerceML;

/**
 * Class TestingClass
 *
 * @package carono\exchange1c\models
 * @property mixed $result
 */
abstract class TestingClass extends Testing
{
    public $caption;
    protected static $property;
    protected static $required = false;
    public $expect;

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

    /**
     * @param $result
     * @return mixed
     */
    protected function saveResult($result)
    {
        if (\Yii::$app->cache) {
            \Yii::$app->cache->set([$this->method, self::getPropertyClass()], $result);
        }
        return $result;
    }

    /**
     * @return mixed|null
     */
    protected function getSavedResult()
    {
        if (\Yii::$app->cache) {
            return \Yii::$app->cache->get([$this->method, self::getPropertyClass()]);
        }
        return null;
    }

    /**
     * @param $params
     * @return array
     */
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
     * @param $method
     * @param $params
     * @return mixed|null
     */
    protected static function getMethodResult($method, $params)
    {
        $class = self::getPropertyClass();
        $methodResult = null;
        if (method_exists($class, $method)) {
            $reflectionMethod = new \ReflectionMethod($class, $method);
            $params = self::getParams($params);
            try {
                if ($reflectionMethod->isStatic()) {
                    $methodResult = call_user_func_array("$class::$method", $params);
                } elseif (!$context = static::getContext()) {
                    return null;
                } else {
                    $methodResult = call_user_func_array([$context, $method], $params);
                }
            } catch (\Exception $e) {
                return null;
            }
        }
        return $methodResult;
    }

    /**
     * @param Testing $test
     * @param $method
     * @param $rule
     */
    private static function validateMethodRule($test, $method, $rule)
    {

    }

    public static function findAll()
    {
        $result = parent::findAll();
        foreach (static::methodRules() as $method => $rule) {
            $test = new static();
            $test->name = "Результат '$method'";
            $test->method = $method;
            $test->expect = ArrayHelper::getValue($rule, 'return', false) ?: 'VOID';
            $result[] = $test;
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
        $test = new static();
        $test->name = ModuleHelper::getModuleNameByClass() . "->{$property}";
        if (!self::module()->{$property}) {
            $test->result = false;
            $test->comment = "Необходимо прописать '$property' в модуле '" . ModuleHelper::getModuleNameByClass() . "'";
        }
        return $test;
    }

    public function hasResult()
    {
        return $this->_result;
    }

    public function getResult($force = false)
    {
        if (!$force && ($cache = self::getSavedResult())) {
            return $cache;
        }
        try {
            return $this->saveResult($this->prepareResult());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function prepareResult()
    {
        if ($method = $this->method) {
            $methodName = "getResult" . ucfirst($method);
            if (method_exists($this, $methodName)) {
                $result = call_user_func([$this, $methodName]);
                return $this->_result = $result;
            } else {
                $params = ArrayHelper::getValue(self::methodRules(), $methodName . '.params', []);
                return $this->_result = self::getMethodResult($method, $params);
            }
        } else {
            return null;
        }
    }

    public static function testImplementsClass()
    {
        $property = static::$property;
        $test = new static();
        $test->name = "Реализация интерфейсов $property (" . self::module()->{$property} . ")";
        $implements = ClassHelper::getImplementedMethods(self::module()->{$property}, ModuleHelper::getPhpDocInterfaceProperty($property));
        $implements = array_filter($implements, function ($data) {
            return !$data;
        });
        if ($implements) {
            $comment = [];
            foreach ($implements as $class => $value) {
                $comment[] = $class;
                $test->addError('', 'Need implement: ' . $class);
            }
            $test->comment = "Не реализованы:<br>" . join("<br>", $comment);
        }
        return $test;
    }

    public static function getMethodRule($method)
    {
        return ArrayHelper::getValue(static::methodRules(), $method, []);
    }

    public function isAutoTest()
    {
        return ArrayHelper::getValue(self::getMethodRule($this->method), 'auto') == true;
    }

    public function validateMethod()
    {
//        $class = self::getPropertyClass();
//        if (method_exists($class, $method)) {
//            $reflectionMethod = new \ReflectionMethod($class, $method);
//            if ($params = ArrayHelper::getValue($rule, 'params', [])) {
//                $params = self::getParams($params);
//            }
//            try {
//                if ($reflectionMethod->isStatic()) {
//                    $methodResult = call_user_func_array("$class::$method", $params);
//                } elseif (!$context = static::getContext()) {
//                    $test->result = false;
//                    $test->comment = 'Не найдена модель для проверки';
//                    return;
//                } else {
//                    $methodResult = call_user_func_array([$context, $method], $params);
//                }
//            } catch (\Exception $e) {
//                $test->result = false;
//                $test->comment = $e->getMessage();
//                return;
//            }
//            switch (ArrayHelper::getValue($rule, 'return')) {
//                case "array":
//                    if (!is_array($methodResult)) {
//                        $test->result = false;
//                        $test->comment = 'Значение должно быть массивом';
//                    }
//                    break;
//                case "string":
//                    if (!is_string($methodResult)) {
//                        $test->result = false;
//                        $test->comment = 'Значение должно быть строкой';
//                    }
//                    break;
//                case "interface":
//                    if ($methodResult) {
//                        if (is_object($methodResult)) {
//                            $reflection = new \ReflectionClass(get_class($methodResult));
//                            if (!$reflection->implementsInterface(ltrim($rule['value'], '\\'))) {
//                                $test->result = false;
//                                $test->comment = "Результат должен имплементировать {$rule['value']}";
//                            }
//                        } else {
//                            $test->result = false;
//                            $test->comment = "Результат должен быль объектом и имплементировать {$rule['value']}";
//                        }
//                    } else {
//                        $test->result = null;
//                        $test->comment = 'Нет результата';
//                    }
//                    break;
//                case false:
//                    $test->result = null;
//                    $test->comment = 'VOID';
//                    break;
//                default:
//                    $test->comment = 'FAIL';
//                    $test->result = false;
//            }
//        } else {
//            $test->result = false;
//            $test->comment = 'Метод не реализован';
//        }
    }

    public function testing()
    {
        if (!$rule = self::getMethodRule($this->method)) {
            return parent::testing();
        }
        if ($this->isAutoTest()) {
            if ($this->validateMethod()) {
                return $this->result ?: true;
            } else {
                return false;
            }
        } else {
            return null;
        }
    }
}