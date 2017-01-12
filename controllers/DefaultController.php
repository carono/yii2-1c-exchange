<?php

namespace carono\exchange1c\controllers;

use carono\exchange1c\helpers\ByteHelper;
use carono\exchange1c\interfaces\ProductInterface;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\web\Controller;
use Zenwalker\CommerceML\CommerceML;
use Zenwalker\CommerceML\Model\Category;
use Zenwalker\CommerceML\Model\Property;

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
        $import = self::getTmpPath() . DIRECTORY_SEPARATOR . 'import.xml';
        $offers = self::getTmpPath() . DIRECTORY_SEPARATOR . 'offers.xml';
        $p = new CommerceML();

//        if ($filename == 'offers.xml') {
//            $p->addXmls(null, $filePath);
//            foreach ($p->getProducts() as $product) {
//                if (!$model = $this->findModel($product)) {
//                    $model = $this->createModel();
//                    $model->save(false);
//                }
//                $this->parseProductCost($model, $product);
//            }
//        } elseif ($filename == 'import.xml') {
        $p->addXmls($import, $offers);
        foreach ($p->getProducts() as $product) {
            if (!$model = $this->findModel($product)) {
                $model = $this->createModel();
                $model->save(false);
            }
            $this->parseProduct($model, $product);
        }
//        } else {
//            return false;
//        }
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
        Yii::$app->session->set($name, $value);
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
     * @param                                     $model ProductInterface
     * @param \Zenwalker\CommerceML\Model\Product $product
     */
    protected function parseProduct($model, $product)
    {
        foreach ($product as $property => $value) {
            $fields = $model::getFields1c();
            switch ($property) {
                case "properties":
                    $this->parseProductProperty($model, $value);
                    break;
                case "categories":
                    $this->parseCategories($model, $value);
                    break;
                case "requisites":
                    $this->parseRequisites($model, $value);
                    break;
                default:
                    if (isset($fields[$property]) && $fields[$property]) {
                        $model->{$fields[$property]} = $value;
                    }
            }
        }

        $this->parseCost($model, $product->price);

        $model->save();
    }

    /**
     * @param \Zenwalker\CommerceML\Model\Product $product
     *
     * @return ActiveRecord|null
     */
    protected function findModel($product)
    {
        /**
         * @var $class ProductInterface
         */
        $class = $this->getProductClass();
        $id = $class::getFields1c()['id'];
        return $class::find()->andWhere([$id => $product->id])->one();
    }

    protected function createModel()
    {
        /**
         * @var $class ProductInterface
         */
        $class = $this->getProductClass();
        $model = new $class;
        return $model;
    }

    /**
     * @param ProductInterface                    $model
     * @param \Zenwalker\CommerceML\Model\Price[] $prices
     */
    protected function parseCost($model, $prices)
    {
        foreach ($prices as $price) {
            $model->setPrice1c(
                $price->cost, is_object($price->type) ? $price->type->type : $price->type, $price->currency
            );
        }
    }

    /**
     * @param ProductInterface $model
     * @param Property[]       $properties
     */
    protected function parseProductProperty($model, $properties)
    {
        foreach ($properties as $property) {
            $model->setProperty1c($property->id, $property->name, $property->values);
        }
    }

    /**
     * @param ProductInterface $model
     * @param Category[]       $categories
     */
    protected function parseCategories($model, $categories)
    {
        foreach ($categories as $category) {
            $model->setCategory1c($category->id, $category->name, $category->parent, $category->owner);
        }
    }

    /**
     * @param ProductInterface $model
     * @param array            $requisites
     */
    protected function parseRequisites($model, $requisites)
    {
        foreach ($requisites as $name => $value) {
            $model->setRequisite1c($name, $value);
        }
    }

    /**
     * @return ActiveRecord
     */
    protected function getProductClass()
    {
        return $this->module->productClass;
    }
}
