<?php


namespace carono\exchange1c\models;

class TestingDocumentClass extends TestingClass
{
    protected static $property = 'documentClass';

    protected static function methodRules()
    {
        return array_merge(parent::methodRules(), [
            [
                ['getPartner1c'],
                'needContext' => true,
                'return' => 'interface',
                'value' => 'carono\exchange1c\interfaces\PartnerInterface'
            ],
            [['findDocuments1c'], 'return' => 'array'],
        ]);
    }


//    public static function testFindDocuments1c()
//    {
//        $class = self::module()->{static::$property};
//        if (method_exists($class, 'findDocuments1c')) {
//            $test = new self();
//            $test->name = "Результат 'findDocuments1c'";
//            if (!$test->result = is_array(call_user_func("$class::findDocuments1c"))) {
//                $test->comment = 'значение должно быть массивом';
//            }
//            return $test;
//        }
//        return null;
//    }

    public static function testGetPartner1c()
    {
        $content = self::getContext();
//        var_dump($content);
//        exit;
    }
}