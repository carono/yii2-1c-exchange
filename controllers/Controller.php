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
        parent::init();
        if ($dbPath = realpath(__DIR__ . '/../exchange.db')) {
            $config = [
                'class' => 'yii\db\Connection',
                'dsn' => 'sqlite:' . $dbPath,
                'username' => '',
                'password' => '',
                'charset' => 'utf8',
            ];
            \Yii::$app->set('exchangeDb', $config);
        }
    }

    public function render($view, $params = [])
    {
        if (!\Yii::$app->getView()->title) {
            \Yii::$app->getView()->title = $this->action->id;
        }
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
                    'class' => HttpBasicAuth::class,
                    'auth' => $auth
                ],
            ];
        }
        return $behaviors;
    }
}