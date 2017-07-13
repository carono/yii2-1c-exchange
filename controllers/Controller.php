<?php


namespace carono\exchange1c\controllers;


use carono\exchange1c\components\Breadcrumbs;
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

    public function init()
    {
        $db = [
            'class' => 'yii\db\Connection',
            'dsn' => 'sqlite:' . realpath(__DIR__ . '/../exchange.db'),
            'username' => '',
            'password' => '',
            'charset' => 'utf8',
        ];
        $components = \Yii::$app->getComponents();
        $components['exchangeDb'] = $db;
        \Yii::$app->setComponents($components);
    }

    public function render($view, $params = [])
    {
        Breadcrumbs::formCrumbs($this->action, $params);
        return parent::render($view, $params);
    }

    public function behaviors()
    {
        $behaviors = [];
        if (\Yii::$app->user->isGuest) {
            if ($this->module->auth) {
                $auth = $this->module->auth;
            } else {
                $auth = [$this->module, 'auth'];
            }
            $behaviors = [
                'basicAuth' => [
                    'class' => HttpBasicAuth::className(),
                    'auth' => $auth,
                ],
            ];
        }
        return $behaviors;
    }
}