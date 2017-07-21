<?php


namespace carono\exchange1c\controllers;


use carono\exchange1c\models\InterfaceTest;
use yii\helpers\Html;

class TestingController extends Controller
{
    public function actionIndex()
    {
        $interfaceTest = new InterfaceTest();
        if ($interfaceTest->load(\Yii::$app->request->post())) {
            if ($interfaceTest->save()) {
                $this->refresh();
            } else {
                \Yii::$app->session->setFlash('error', Html::errorSummary($interfaceTest));
            }
        }
        return $this->render('index');
    }
}