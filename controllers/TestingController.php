<?php


namespace carono\exchange1c\controllers;


use carono\exchange1c\models\TestingClass;

class TestingController extends Controller
{
    /**
     * @param null $class
     * @param null $result
     * @return string
     * @throws \Exception
     */
    public function actionIndex($class = null, $result = null)
    {
        /**
         * @var TestingClass $testingClass
         * @var TestingClass $resultClass
         */
        $testingClass = null;
        $resultClass = null;
        if ($class) {
            $className = 'carono\exchange1c\models\\' . $class;
            if (class_exists($className)) {
                $testingClass = new $className();
            } else {
                throw new \Exception("Class $className not found");
            }

            if ($testingClass::testImplementsClass()->hasErrors()) {
                throw new \Exception('Ошибки реализации интерфейсов.');
            }

            if ($result) {
                $resultClass = new $className(['method' => $result]);
            }
        }
        return $this->render('index', ['testingClass' => $testingClass, 'resultClass' => $resultClass]);
    }
}