<?php


namespace carono\exchange1c\controllers;


use carono\exchange1c\ExchangeModule;
use yii\filters\auth\HttpBasicAuth;

/**
 * Class Controller
 *
 * @property ExchangeModule module
 * @package carono\exchange1c\controllers
 */
abstract class Controller extends \yii\web\Controller
{
    public $layout = '@vendor/carono/yii2-1c-exchange/views/layouts/main';

    public function behaviours()
    {
        if (\Yii::$app->user->isGuest) {
            if ($this->module->auth) {
                $auth = $this->module->auth;
            } else {
                $auth = [$this, 'auth'];
            }
            return [
                'basicAuth' => [
                    'class' => HttpBasicAuth::className(),
                    'auth' => $auth,
                    'except' => ['index'],
                ],
            ];
        }
        return [];
    }
}