<?php

namespace carono\exchange1c\controllers;

use carono\exchange1c\behaviors\BomBehavior;
use carono\exchange1c\ExchangeEvent;
use carono\exchange1c\ExchangeModule;
use carono\exchange1c\helpers\ByteHelper;
use carono\exchange1c\helpers\NodeHelper;
use carono\exchange1c\helpers\SerializeHelper;
use carono\exchange1c\interfaces\DocumentInterface;
use carono\exchange1c\interfaces\ProductInterface;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\web\Response;
use Zenwalker\CommerceML\CommerceML;
use Zenwalker\CommerceML\Model\Group;
use Zenwalker\CommerceML\Model\Image;
use Zenwalker\CommerceML\Model\Offer;
use Zenwalker\CommerceML\Model\Product;
use Zenwalker\CommerceML\Model\PropertyCollection;
use Zenwalker\CommerceML\Model\Simple;
use Zenwalker\CommerceML\Model\RequisiteCollection;

/**
 * Default controller for the `api` module
 *
 * @property ExchangeModule $module
 */
class ApiController extends Controller
{
    public $enableCsrfValidation = false;
    const EVENT_BEFORE_UPDATE_PRODUCT = 'beforeUpdateProduct';
    const EVENT_AFTER_UPDATE_PRODUCT = 'afterUpdateProduct';
    const EVENT_BEFORE_UPDATE_OFFER = 'beforeUpdateOffer';
    const EVENT_AFTER_UPDATE_OFFER = 'afterUpdateOffer';
    const EVENT_BEFORE_PRODUCT_SYNC = 'beforeProductSync';
    const EVENT_AFTER_PRODUCT_SYNC = 'afterProductSync';
    const EVENT_AFTER_FINISH_UPLOAD_FILE = 'afterFinishUploadFile';

    private $_ids;

    public function init()
    {
        set_time_limit($this->module->timeLimit);
        if (!$this->module->productClass) {
            throw new Exception('1');
        }
        $c = new $this->module->productClass;

        if (!$c instanceof ProductInterface) {
            throw new Exception('2');
        }
        parent::init();
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'bom' => [
                'class' => BomBehavior::className(),
                'only' => ['query'],
            ],
        ]);
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
                Yii::$app->session->getId(),
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
        @unlink($this->module->getTmpDir() . DIRECTORY_SEPARATOR . 'import.xml');
        @unlink($this->module->getTmpDir() . DIRECTORY_SEPARATOR . 'offers.xml');
        return [
            "zip" => class_exists('ZipArchive') && $this->module->useZip ? "yes" : "no",
            "file_limit" => $this->getFileLimit(),
        ];
    }

    public function actionFile($type, $filename)
    {
        $body = Yii::$app->request->getRawBody();
        $filePath = $this->module->getTmpDir() . DIRECTORY_SEPARATOR . $filename;
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
        $this->module->trigger(self::EVENT_AFTER_FINISH_UPLOAD_FILE, new ExchangeEvent());
    }

    public function beforeProductSync()
    {
        $this->module->trigger(self::EVENT_BEFORE_PRODUCT_SYNC, new ExchangeEvent());
    }


    public function afterProductSync()
    {
        $this->module->trigger(self::EVENT_AFTER_PRODUCT_SYNC, new ExchangeEvent(['ids' => $this->_ids]));
    }

    public function parsing($import, $offers)
    {
        $this->_ids = [];
        $commerce = new CommerceML();
        $commerce->addXmls(file_exists($import) ? $import : false, file_exists($offers) ? $offers : false);
        if ($import) {
            $this->beforeProductSync();
            foreach ($commerce->catalog->getProducts() as $product) {
                if (!$model = $this->findModel($product)) {
                    $model = $this->createModel();
                    $model->save($this->module->validateModelOnSave);
                }
                $this->parseProduct($model, $product);
                $this->_ids[] = $model->getPrimaryKey();
                $model = null;
                unset($model);
                unset($product);
                gc_collect_cycles();
            }
            $this->afterProductSync();

        }
        if ($offers) {
            foreach ($commerce->offerPackage->getOffers() as $offer) {
                if ($model = $this->findModel($offer)) {
                    $this->parseProductOffer($model, $offer);
                    unset($model);
                }
            }
        }
    }

    public function actionLoad()
    {
        $this->actionImport('catalog', 'import.xml');
        $this->actionImport('catalog', 'offers.xml');
    }

    public function parsingImport($file)
    {
        $this->parsing($file, false);
    }

    public function parsingOrder($file)
    {
        /**
         * @var DocumentInterface $documentModel
         */
        $commerce = new CommerceML();
        $commerce->addXmls(false, false, $file);
        $documentClass = $this->module->documentClass;
        foreach ($commerce->order->documents as $document) {
            if ($documentModel = $documentClass::findOne((string)$document->Номер)) {
                $documentModel->setRaw1cData($commerce, $document);
            }
        }
    }

    public function parsingOffer($file)
    {
        $this->parsing(false, $file);
    }

    public function actionImport($type, $filename)
    {
        if ($archive = self::getData('archive')) {
            $zip = new \ZipArchive();
            $zip->open($archive);
            $zip->extractTo(dirname($archive));
            $zip->close();
        }
        $file = $this->module->getTmpDir() . DIRECTORY_SEPARATOR . $filename;
        if ($type == 'catalog') {
            if (strpos($file, 'offer') !== false) {
                $this->parsingOffer($file);
            } elseif (strpos($file, 'import') !== false) {
                $this->parsingImport($file);
            }
        } elseif ($type == 'sale' && strpos($file, 'order') !== false) {
            $this->parsingOrder($file);
        }
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
        if (is_dir($files = $this->module->getTmpDir() . DIRECTORY_SEPARATOR . 'import_files')) {
            FileHelper::removeDirectory($files);
        }
        if (file_exists($import = $this->module->getTmpDir() . DIRECTORY_SEPARATOR . 'import.xml')) {
            @unlink($import);
        }
        if ($offers = $this->module->getTmpDir() . DIRECTORY_SEPARATOR . 'offers.xml') {
            @unlink($offers);
        }
    }

    public function actionQuery($type)
    {
        /**
         * @var DocumentInterface $document
         */
        if (!$this->module->exchangeDocuments) {
            echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml = new \SimpleXMLElement('<root></root>');
            $root = $xml->addChild('КоммерческаяИнформация');
            $root->addAttribute('ВерсияСхемы', '2.04');
            $root->addAttribute('ДатаФормирования', date('Y-m-d\TH:i:s'));
            return $root->asXML();
        }

        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->getHeaders()->set('Content-Type', 'application/xml; charset=windows-1251');

        $root = new \SimpleXMLElement('<КоммерческаяИнформация></КоммерческаяИнформация>');

        $root->addAttribute('ВерсияСхемы', '2.04');
        $root->addAttribute('ДатаФормирования', date('Y-m-d\TH:i:s'));

        $document = $this->module->documentClass;

        foreach ($document::findDocuments1c() as $order) {
            NodeHelper::appendNode($root, SerializeHelper::serializeDocument($order));
        }

        if ($this->module->debug) {
            $xml = $root->asXML();
            $xml = html_entity_decode($xml, ENT_NOQUOTES, 'UTF-8');
            file_put_contents($this->module->getTmpDir() . '/query.xml', $xml);
        }
        return $root->asXML();
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

    /**
     * @param                                     $model ProductInterface
     * @param \Zenwalker\CommerceML\Model\Product $product
     */
    protected function parseProduct($model, $product)
    {
        /**
         * @var Simple $value
         */
        $fields = $model::getFields1c();
        $this->beforeUpdateProduct($model);
        $model->setRaw1cData($product->owner, $product);
        $group = $product->getGroup();
        $this->parseGroups($model, $group);
        $this->parseProperties($model, $product->getProperties());
        $this->parseRequisites($model, $product->getRequisites());
        $this->parseImage($model, $product->getImages());
        foreach ($fields as $accountingField => $modelField) {
            if ($modelField) {
                $model->{$modelField} = (string)$product->{$accountingField};
            }
        }
        $model->save();
        unset($group);
        $this->afterUpdateProduct($model);
    }

    /**
     * @param ProductInterface $model
     * @param Offer $offer
     */
    protected function parseProductOffer($model, $offer)
    {
        /**
         * @var Simple $value
         */
        $fields = $model::getFields1c();
        $this->beforeUpdateOffer($model, $offer);
        $this->parseSpecifications($model, $offer);
        $this->parsePrice($model, $offer);
        foreach ($fields as $accountingField => $modelField) {
            if ($modelField) {
                if ($accountingField == 'id') {
                    continue;
                }
                if (is_null($offer->{$accountingField})) {
                    continue;
                }
                $model->{$modelField} = (string)$offer->{$accountingField};
            }
        }
        $model->save();
        $this->afterUpdateOffer($model);
    }

    public function afterUpdateProduct($model)
    {
        $this->module->trigger(self::EVENT_AFTER_UPDATE_PRODUCT, new ExchangeEvent(['model' => $model]));
    }

    public function beforeUpdateProduct($model)
    {
        $this->module->trigger(self::EVENT_BEFORE_UPDATE_PRODUCT, new ExchangeEvent(['model' => $model]));
    }

    public function beforeUpdateOffer($model, $offer)
    {
        $this->module->trigger(self::EVENT_BEFORE_UPDATE_OFFER, new ExchangeEvent([
            'model' => $model,
            'ml' => $offer,
        ]));
    }

    public function afterUpdateOffer($model)
    {
        $this->module->trigger(self::EVENT_AFTER_UPDATE_OFFER, new ExchangeEvent(['model' => $model]));
    }

    /**
     * @param Product|Offer $object
     *
     * @return ProductInterface|null
     */
    protected function findModel($object)
    {
        /**
         * @var $class ProductInterface
         */
        $class = $this->getProductClass();
        $id = $class::getFields1c()['id'];
        return $class::find()->andWhere([$id => $object->getClearId()])->one();
    }

    /**
     * @return mixed
     */
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
     * @param Offer $offer
     */
    protected function parsePrice($model, $offer)
    {
        foreach ($offer->getPrices() as $price) {
            $model->setPrice1c($offer, $price);
        }
    }

    /**
     * @param ProductInterface $model
     * @param Image $images
     */
    protected function parseImage($model, $images)
    {
        foreach ($images as $image) {
            $path = realpath($this->module->getTmpDir() . DIRECTORY_SEPARATOR . $image->path);
            if (file_exists($path)) {
                $model->addImage1c($path, $image->caption);
            }
        }
    }

    /**
     * @param ProductInterface $model
     * @param Group $group
     */
    protected function parseGroups($model, $group)
    {
        $model->setGroup1c($group);
    }

    /**
     * @param ProductInterface $model
     * @param RequisiteCollection $requisites
     */
    protected function parseRequisites($model, $requisites)
    {
        foreach ($requisites as $requisite) {
            $model->setRequisite1c($requisite->name, $requisite->value);
        }
    }

    /**
     * @param ProductInterface $model
     * @param Offer $offer
     */
    protected function parseSpecifications($model, $offer)
    {
        foreach ($offer->getSpecifications() as $specification) {
            $model->setSpecification1c($offer, $specification);
        }
    }

    /**
     * @param ProductInterface $model
     * @param PropertyCollection $properties
     */
    protected function parseProperties($model, $properties)
    {
        foreach ($properties as $property) {
            $model->setProperty1c($property);
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
