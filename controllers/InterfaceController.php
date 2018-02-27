<?php


namespace carono\exchange1c\controllers;


use carono\exchange1c\helpers\ModuleHelper;
use carono\exchange1c\models\InterfaceTest;
use yii\helpers\Html;
use yii\web\HttpException;

class InterfaceController extends Controller
{
    /**
     * @param string $variable
     * @return string
     * @throws HttpException
     */
    public function actionCheck($variable)
    {
        $class = \Yii::$app->controller->module->{$variable};
        if (!$class) {
            throw new HttpException(401, "Значение '$variable' не установлено");
        }
        $interfaceTest = InterfaceTest::findByClass($class);
        $interfaceClass = ModuleHelper::getPhpDocInterfaceProperty($variable);
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
            'interfaceClass' => $interfaceClass
        ]);
    }
}