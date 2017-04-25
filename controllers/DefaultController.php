<?php

namespace carono\exchange1c\controllers;

use carono\exchange1c\behaviors\BomBehavior;
use carono\exchange1c\ExchangeEvent;
use carono\exchange1c\ExchangeModule;
use carono\exchange1c\helpers\ByteHelper;
use carono\exchange1c\interfaces\DocumentInterface;
use carono\exchange1c\interfaces\ProductInterface;
use Yii;
use yii\base\Event;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\filters\auth\HttpBasicAuth;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\Controller;
use Zenwalker\CommerceML\CommerceML;
use Zenwalker\CommerceML\Model\Category;
use Zenwalker\CommerceML\Model\Property;

/**
 * Default controller for the `exchange` module
 * @property ExchangeModule $module
 */
class DefaultController extends Controller
{
    public $enableCsrfValidation = false;
    const EVENT_BEFORE_UPDATE = 'beforeUpdate';
    const EVENT_AFTER_UPDATE = 'afterUpdate';
    const EVENT_BEFORE_SYNC = 'beforeSync';
    const EVENT_AFTER_SYNC = 'afterSync';
    private $_ids;

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

    public function actionIndex()
    {
        return '';
    }

    public function auth($login, $password)
    {
        /**
         * @var $class \yii\web\IdentityInterface
         */
        $class = Yii::$app->user->identityClass;
        $user = $class::findByUsername($login);
        if ($user && $user->validatePassword($password)) {
            return $user;
        } else {
            return null;
        }
    }

    public function behaviors()
    {
        $behaviors = [
            'bom' => [
                'class' => BomBehavior::className(),
                'only'  => ['query']
            ]
        ];
        if (Yii::$app->user->isGuest) {
            if ($this->module->auth) {
                $auth = $this->module->auth;
            } else {
                $auth = [$this, 'auth'];
            }
            return ArrayHelper::merge(
                $behaviors, [
                    'basicAuth' => [
                        'class'  => HttpBasicAuth::className(),
                        'auth'   => $auth,
                        'except' => ['index']
                    ]
                ]
            );
        }
        return $behaviors;
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

    protected function getFileLimit()
    {
        $limit = ByteHelper::maximum_upload_size();
        if (!($limit % 2)) {
            $limit--;
        }
        return $limit;
    }

    public function actionInit()
    {
        @unlink(self::getTmpDir() . DIRECTORY_SEPARATOR . 'import.xml');
        @unlink(self::getTmpDir() . DIRECTORY_SEPARATOR . 'offers.xml');
        return [
            "zip"        => class_exists('ZipArchive') && $this->module->useZip ? "yes" : "no",
            "file_limit" => $this->getFileLimit()
        ];
    }

    public function actionFile($type, $filename)
    {
        $body = Yii::$app->request->getRawBody();
        $filePath = self::getTmpDir() . DIRECTORY_SEPARATOR . $filename;
        if (!self::getData('archive') && pathinfo($filePath, PATHINFO_EXTENSION) == 'zip') {
            self::setData('archive', $filePath);
        }
        file_put_contents($filePath, $body, FILE_APPEND);
        if ((int)Yii::$app->request->headers->get('Content-Length') != $this->getFileLimit()) {
            $this->afterFinishUploadFile($filePath);
        }
        return true;
    }

    /**
     * @param $filePath
     */
    public function afterFinishUploadFile($filePath)
    {
        //
    }

    public function beforeSync()
    {
        $event = new ExchangeEvent();
        $this->module->trigger(self::EVENT_BEFORE_SYNC, $event);
    }


    public function afterSync()
    {
        $event = new ExchangeEvent();
        $event->ids = $this->_ids;
        $this->module->trigger(self::EVENT_AFTER_SYNC, $event);
    }

    public function parsing($import, $offers)
    {
        $this->beforeSync();
        $this->_ids = [];
        $commerce = new CommerceML();
        $commerce->addXmls($import, $offers);
        foreach ($commerce->getProducts() as $product) {
            if (!$model = $this->findModel($product)) {
                $model = $this->createModel();
                $model->save(false);
            }
            $this->parseProduct($model, $product);
            $this->_ids[] = $model->getPrimaryKey();
        }
        $this->afterSync();
    }

    public function actionLoad()
    {
        set_time_limit(0);
        $import = self::getTmpDir() . DIRECTORY_SEPARATOR . 'import.xml';
        $offers = self::getTmpDir() . DIRECTORY_SEPARATOR . 'offers.xml';
        $this->parsing($import, $offers);
    }

    public function actionImport($type, $filename)
    {
        if ($filename == 'offers.xml') {
            return true;
        }
        if ($archive = self::getData('archive')) {
            $zip = new \ZipArchive();
            $zip->open($archive);
            $zip->extractTo(dirname($archive));
            $zip->close();
        }
        $import = self::getTmpDir() . DIRECTORY_SEPARATOR . 'import.xml';
        $offers = self::getTmpDir() . DIRECTORY_SEPARATOR . 'offers.xml';
        $this->parsing($import, $offers);
        if (!$this->module->debug) {
            $this->clearTmp();
        }
        return true;
    }

    protected function clearTmp()
    {
        if ($archive = self::getData('archive')) {
            @unlink($archive);
        }
        if (is_dir($files = self::getTmpDir() . DIRECTORY_SEPARATOR . 'import_files')) {
            FileHelper::removeDirectory($files);
        }
        if (file_exists($import = self::getTmpDir() . DIRECTORY_SEPARATOR . 'import.xml')) {
            @unlink($import);
        }
        if ($offers = self::getTmpDir() . DIRECTORY_SEPARATOR . 'offers.xml') {
            @unlink($offers);
        }
    }

    public function actionQuery($type)
    {
        /**
         * @var DocumentInterface $document
         */

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

        $xml = new \SimpleXMLElement('<root></root>');
        $root = $xml->addChild('КоммерческаяИнформация');
        $root->addAttribute('ВерсияСхемы', '2.04');
        $root->addAttribute('ДатаФормирования', date('Y-m-d\TH:i:s'));
        return $root->asXML();
        /*
                if (!$this->getDocumentClass()) {
                    return $root->asXML();
                }
                $class = $this->getDocumentClass();
                $document = new $class;
                if ($document instanceof DocumentInterface) {
                    return $root->asXML();
                }
                $document::findOrders1c();

                return $root->asXML();
        */
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

    protected function getTmpDir()
    {
        $dir = Yii::getAlias($this->module->tmpDir);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    /**
     * @param                                     $model ProductInterface
     * @param \Zenwalker\CommerceML\Model\Product $product
     */
    protected function parseProduct($model, $product)
    {
        $this->beforeUpdate($model);
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
                case "характеристикиТовара":
                    $this->parseProductFeatures($model, $value);
                    break;
                default:
                    if (isset($fields[$property]) && $fields[$property]) {
                        $model->{$fields[$property]} = $value;
                    }
            }
        }
        $this->parseCost($model, $product->price);
        $this->parseImage($model, $product->images);
        $model->save();
        $this->afterUpdate($model);
    }

    public function afterUpdate($model)
    {
        $event = new ExchangeEvent();
        $event->model = $model;
        $this->module->trigger(self::EVENT_AFTER_UPDATE, $event);
    }

    public function beforeUpdate($model)
    {
        $event = new ExchangeEvent();
        $event->model = $model;
        $this->module->trigger(self::EVENT_BEFORE_UPDATE, $event);
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
     * @param ProductInterface $model
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
     * @param array $images
     */
    protected function parseImage($model, $images)
    {
        foreach ($images as $image => $name) {
            $path = realpath($this->getTmpDir() . DIRECTORY_SEPARATOR . $image);
            if (file_exists($path)) {
                $model->addImage1c($path, $name);
            }
        }
    }

    /**
     * @param ProductInterface $model
     * @param Property[] $properties
     */
    protected function parseProductProperty($model, $properties)
    {
        foreach ($properties as $property) {
            $model->setProperty1c($property->id, $property->name, $property->values);
        }
    }

    /**
     * @param ProductInterface $model
     * @param $properties
     */
    protected function parseProductFeatures($model, $properties)
    {
        foreach ($properties as $property => $value) {
            $model->setFeature1c($property, $value);
        }
    }

    /**
     * @param ProductInterface $model
     * @param Category[] $categories
     */
    protected function parseCategories($model, $categories)
    {
        foreach ($categories as $category) {
            $model->setCategory1c($category->id, $category->name, $category->parent, $category->owner);
        }
    }

    /**
     * @param ProductInterface $model
     * @param array $requisites
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

    /**
     * @return DocumentInterface
     */
    protected function getDocumentClass()
    {
        return $this->module->documentClass;
    }
}
