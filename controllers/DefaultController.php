<?php

namespace carono\exchange1c\controllers;

use carono\exchange1c\helpers\ByteHelper;
use carono\exchange1c\interfaces\ProductInterface;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\web\Controller;
use Zenwalker\CommerceML\CommerceML;

/**
 * Default controller for the `exchange` module
 */
class DefaultController extends Controller
{
    public $enableCsrfValidation = false;

    public function init()
    {
        if (!$this->module->productClass) {
            throw new Exception('1');
        }
        $c = new $this->module->productClass;

        if (!$c instanceof ProductInterface) {
            throw new Exception('2');
        }
        parent::init();
    }

    public function auth($login, $password)
    {
        /**
         * @var $class \yii\web\IdentityInterface
         */
        $class = Yii::$app->user->identityClass;
        $user = $class::findByUsername($login);
        if ($user->validatePassword($password)) {
            return $user;
        } else {
            return null;
        }
    }

    public function behaviors()
    {
        if (Yii::$app->user->isGuest) {
            return [
                'basicAuth' => [
                    'class' => \yii\filters\auth\HttpBasicAuth::className(),
                    'auth'  => [$this, 'auth']
                ]
            ];
        } else {
            return [];
        }
    }

    public function afterAction($action, $result)
    {
        Yii::$app->response->headers->set('uid', Yii::$app->user->getId());
        if (is_bool($result)) {
            return $result ? "success" : "failure";
        } elseif (is_array($result)) {
            $r = [];
            foreach ($result as $key => $value) {
                $r[] = is_int($key) ? $value : $key . '=' . $value;
            }
            return join("\n", $r);
        } else {
            return parent::afterAction($action, $result);
        }
    }

    public function actionCheckauth($type)
    {
        if (Yii::$app->user->isGuest) {
            return false;
        } else {
            return [
                "success",
                "PHPSESSID",
                Yii::$app->session->getId()
            ];
        }
    }

    public function actionInit()
    {
        @unlink(self::getTmpPath() . DIRECTORY_SEPARATOR . 'import.xml');
        @unlink(self::getTmpPath() . DIRECTORY_SEPARATOR . 'offers.xml');
        return [
            "zip"        => "no",
            "file_limit" => ByteHelper::maximum_upload_size(),
        ];
    }

    public function actionFile($type, $filename)
    {
        $body = Yii::$app->request->getRawBody();
        $filePath = self::getTmpPath() . DIRECTORY_SEPARATOR . $filename;
        self::setData($type . '_archive', $filePath);
        file_put_contents($filePath, $body, FILE_APPEND);
        return true;
    }

    public function actionImport($type, $filename)
    {
//        if (!self::getData($key = 'progress_' . $type . '_' . $filename)) {
//        $filePath = self::getData($type . '_archive');
//        @unlink($filePath);

        $filePath = self::getTmpPath() . DIRECTORY_SEPARATOR . $filename;
        $p = new CommerceML();

        if ($filename == 'offers.xml') {
            $p->addXmls(null, $filePath);
        } elseif ($filename == 'import.xml') {
            $p->addXmls($filePath, null);
            foreach ($p->getProducts() as $product) {
                $this->parseProduct($product);
            }
        } else {
            return false;
        }
        return true;
    }

    public function actionQuery($type)
    {
        return file_get_contents(Yii::$app->runtimePath . DIRECTORY_SEPARATOR . 'query.xml');
    }

    public function actionSuccess($type)
    {
        return true;
    }

    protected static function setData($name, $value)
    {
        return Yii::$app->session->set($name, $value);
    }

    protected static function getData($name, $default = null)
    {
        return Yii::$app->session->get($name, $default);
    }

    protected static function clearData()
    {
        return Yii::$app->session->closeSession();
    }

    protected static function getTmpPath()
    {
        $dir = Yii::$app->runtimePath . DIRECTORY_SEPARATOR . 'exchange1c';
        mkdir($dir);
        return $dir;
    }

    /**
     * @param \Zenwalker\CommerceML\Model\Product $product
     */
    protected function parseProduct($product)
    {
        /**
         * @var $class ProductInterface
         */
        $class = $this->getProductClass();
        $id = $class::getFields1c()['id'];
        if (!$model = $class::find()->andWhere([$id => $product->id])->one()) {
            $model = new $class;
        }
        foreach ($product as $property => $value) {
            $fields = $class::getFields1c();
            if (isset($fields[$property]) && $fields[$property]) {
                $model->{$fields[$property]} = $value;
            }
        }
        $model->save();
        print_r($product);
    }

    /**
     * @return ActiveRecord
     */
    protected function getProductClass()
    {
        return $this->module->productClass;
    }
}
