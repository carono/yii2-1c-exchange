<?php


namespace carono\exchange1c\controllers;


use carono\exchange1c\models\InterfaceTest;
use yii\helpers\Html;
use yii\web\HttpException;

class InterfaceController extends Controller
{
    public function actionCheck($variable)
    {
        $class = \Yii::$app->controller->module->{$variable};

        if (!$class) {
            throw new HttpException("Значение '$variable' не установлено");
        }

        $interfaceTest = InterfaceTest::findByClass($class);

        $reflection = new \ReflectionClass(\Yii::$app->controller->module);
        $property = $reflection->getProperty($variable);

        $doc = $property->getDocComment();
        preg_match('#@var\s+(.+)#', $doc, $m);

        $interfaceClass = trim($m[1], " \t\n\r\0\x0B\\");

        if ($interfaceTest->load(\Yii::$app->request->post())) {
            if ($interfaceTest->save()) {
                $this->refresh();
            } else {
                \Yii::$app->session->setFlash('error', Html::errorSummary($interfaceTest));
            }
        }
        return $this->render('check', [
            'variable' => $variable,
            'class' => $class,
            'interfaceTest' => $interfaceTest,
            'property' => $property,
            'interfaceClass' => $interfaceClass
        ]);
    }
}