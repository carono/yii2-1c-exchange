<?php


namespace carono\exchange1c\controllers;


use carono\exchange1c\models\InterfaceTest;
use yii\helpers\Html;

class InterfaceController extends Controller
{
    public function actionCheck($variable)
    {
        $class = \Yii::$app->controller->module->{$variable};
        $interfaceTest = InterfaceTest::findByClass($class);
        if ($interfaceTest->load(\Yii::$app->request->post())) {
            if ($interfaceTest->save()) {
                $this->refresh();
            } else {
                \Yii::$app->session->setFlash('error', Html::errorSummary($interfaceTest));
            }
        }
        return $this->render('check', ['variable' => $variable, 'class' => $class, 'interfaceTest' => $interfaceTest]);
    }
}