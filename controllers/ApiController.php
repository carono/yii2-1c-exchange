<?php

namespace carono\exchange1c\controllers;

use carono\exchange1c\behaviors\BomBehavior;
use carono\exchange1c\ExchangeEvent;
use carono\exchange1c\ExchangeModule;
use carono\exchange1c\helpers\ByteHelper;
use carono\exchange1c\helpers\NodeHelper;
use carono\exchange1c\helpers\SerializeHelper;
use carono\exchange1c\interfaces\DocumentInterface;
use carono\exchange1c\interfaces\OfferInterface;
use carono\exchange1c\interfaces\ProductInterface;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\web\Response;
use Zenwalker\CommerceML\CommerceML;
use Zenwalker\CommerceML\Model\Classifier;
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
    const EVENT_BEFORE_OFFER_SYNC = 'beforeOfferSync';
    const EVENT_AFTER_OFFER_SYNC = 'afterOfferSync';
    const EVENT_AFTER_FINISH_UPLOAD_FILE = 'afterFinishUploadFile';
    const EVENT_AFTER_EXPORT_ORDERS = 'afterExportOrders';

    private $_ids;

    public function init()
    {
        set_time_limit($this->module->timeLimit);
        parent::init();
    }


    /**
     * @return array
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'bom' => [
                'class' => BomBehavior::className(),
                'only' => ['query'],
            ],
        ]);
    }

    /**
     * @param \yii\base\Action $action
     * @param mixed $result
     * @return mixed|string
     */
    public function afterAction($action, $result)
    {
        Yii::$app->response->headers->set('uid', Yii::$app->user->getId());
        parent::afterAction($action, $result);
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

    /**
     * @param $type
     * @return array|bool
     */
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

    /**
     * @return float|int
     */
    protected function getFileLimit()
    {
        $limit = ByteHelper::maximum_upload_size();
        if (!($limit % 2)) {
            $limit--;
        }
        return $limit;
    }

    /**
     * @return array
     */
    public function actionInit()
    {
        return [
            "zip" => class_exists('ZipArchive') && $this->module->useZip ? "yes" : "no",
            "file_limit" => $this->getFileLimit(),
        ];
    }

    /**
     * @param $type
     * @param $filename
     * @return bool
     */
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

    public function beforeOfferSync()
    {
        $this->module->trigger(self::EVENT_BEFORE_OFFER_SYNC, new ExchangeEvent());
    }

    public function afterOfferSync()
    {
        $this->module->trigger(self::EVENT_AFTER_OFFER_SYNC, new ExchangeEvent(['ids' => $this->_ids]));
    }

    /**
     * @param $file
     */
    public function parsingImport($file)
    {
        $this->_ids = [];
        $commerce = new CommerceML();
        $commerce->loadImportXml($file);
        $classifierFile = Yii::getAlias($this->module->tmpDir . '/classifier.xml');
        if ($commerce->classifier->xml) {
            $commerce->classifier->xml->saveXML($classifierFile);
        } else {
            $commerce->classifier->xml = simplexml_load_file($classifierFile);
        }
        $this->beforeProductSync();
        if ($groupClass = $this->getGroupClass()) {
            $groupClass::createTree1c($commerce->classifier->getGroups());
        }
        $productClass = $this->getProductClass();
        $productClass::createProperties1c($commerce->classifier->getProperties());
        foreach ($commerce->catalog->getProducts() as $product) {
            if (!$model = $productClass::createModel1c($product)) {
                Yii::error("Модель продукта не найдена, проверьте реализацию $productClass::createModel1c", 'exchange1c');
                continue;
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

    /**
     * @param $file
     */
    public function parsingOffer($file)
    {
        $this->_ids = [];
        $commerce = new CommerceML();
        $commerce->loadOffersXml($file);
        if ($offerClass = $this->getOfferClass()) {
            $offerClass::createPriceTypes1c($commerce->offerPackage->getPriceTypes());
        }
        $this->beforeOfferSync();
        foreach ($commerce->offerPackage->getOffers() as $offer) {
            $product = $this->findProductModelById($offer->getClearId());
            $model = $product->getOffer1c($offer);
            $this->parseProductOffer($model, $offer);
            $this->_ids[] = $model->getPrimaryKey();
            unset($model);
        }
        $this->afterOfferSync();
    }

    /**
     * @param $file
     */
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

    /**
     * @param $type
     * @param $filename
     * @return bool
     */
    public function actionImport($type, $filename)
    {
        if (($archive = self::getData('archive')) && file_exists($archive)) {
            $zip = new \ZipArchive();
            $zip->open($archive);
            $zip->extractTo(dirname($archive));
            $zip->close();
            @unlink($archive);
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
        FileHelper::removeDirectory($this->module->getTmpDir());
    }

    /**
     * @param $type
     * @return mixed
     */
    public function actionQuery($type)
    {
        /**
         * @var DocumentInterface $document
         */
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->getHeaders()->set('Content-Type', 'application/xml; charset=windows-1251');

        $root = new \SimpleXMLElement('<КоммерческаяИнформация></КоммерческаяИнформация>');
        $root->addAttribute('ВерсияСхемы', '2.10');
        $root->addAttribute('ДатаФормирования', date('Y-m-d\TH:i:s'));

        $ids = [];
        if ($this->module->exchangeDocuments) {
            $document = $this->module->documentClass;
            foreach ($document::findDocuments1c() as $order) {
                $ids[] = $order->getPrimaryKey();
                NodeHelper::appendNode($root, SerializeHelper::serializeDocument($order));
            }
            if ($this->module->debug) {
                $xml = $root->asXML();
                $xml = html_entity_decode($xml, ENT_NOQUOTES, 'UTF-8');
                file_put_contents($this->module->getTmpDir() . '/query.xml', $xml);
            }
        }
        $this->afterExportOrders($ids);
        return $root->asXML();
    }

    /**
     * @param $type
     * @return bool
     */
    public function actionSuccess($type)
    {
        return true;
    }

    /**
     * @param $name
     * @param $value
     */
    protected static function setData($name, $value)
    {
        Yii::$app->session->set($name, $value);
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    protected static function getData($name, $default = null)
    {
        return Yii::$app->session->get($name, $default);
    }

    /**
     * @return bool
     */
    protected static function clearData()
    {
        return Yii::$app->session->closeSession();
    }

    /**
     * @param ProductInterface $model
     * @param \Zenwalker\CommerceML\Model\Product $product
     */
    protected function parseProduct($model, $product)
    {
        $this->beforeUpdateProduct($model);
        $model->setRaw1cData($product->owner, $product);
        $this->parseGroups($model, $product);
        $this->parseProperties($model, $product);
        $this->parseRequisites($model, $product);
        $this->parseImage($model, $product);
        $this->afterUpdateProduct($model);
        unset($group);
    }

    /**
     * @param OfferInterface $model
     * @param Offer $offer
     */
    protected function parseProductOffer($model, $offer)
    {
        $this->beforeUpdateOffer($model, $offer);
        $this->parseSpecifications($model, $offer);
        $this->parsePrice($model, $offer);
        $model->{$model::getIdFieldName1c()} = $offer->id;
        $model->save();
        $this->afterUpdateOffer($model, $offer);
        unset($model);
    }

    /**
     * @param $model
     */
    public function afterUpdateProduct($model)
    {
        $this->module->trigger(self::EVENT_AFTER_UPDATE_PRODUCT, new ExchangeEvent(['model' => $model]));
    }

    /**
     * @param $model
     */
    public function beforeUpdateProduct($model)
    {
        $this->module->trigger(self::EVENT_BEFORE_UPDATE_PRODUCT, new ExchangeEvent(['model' => $model]));
    }

    /**
     * @param $model
     * @param $offer
     */
    public function beforeUpdateOffer($model, $offer)
    {
        $this->module->trigger(self::EVENT_BEFORE_UPDATE_OFFER, new ExchangeEvent([
            'model' => $model,
            'ml' => $offer,
        ]));
    }

    /**
     * @param $model
     * @param $offer
     */
    public function afterUpdateOffer($model, $offer)
    {
        $this->module->trigger(self::EVENT_AFTER_UPDATE_OFFER, new ExchangeEvent(['model' => $model, 'ml' => $offer]));
    }

    /**
     * @param $ids
     */
    public function afterExportOrders($ids)
    {
        $this->module->trigger(self::EVENT_AFTER_EXPORT_ORDERS, new ExchangeEvent(['ids' => $ids]));
    }

    /**
     * @param string $id
     *
     * @return ProductInterface|null
     */
    protected function findProductModelById($id)
    {
        /**
         * @var $class ProductInterface
         */
        $class = $this->getProductClass();
        return $class::find()->andWhere([$class::getIdFieldName1c() => $id])->one();
    }

    /**
     * @param Offer $offer
     *
     * @return OfferInterface|null
     */
    protected function findOfferModel($offer)
    {
        /**
         * @var $class ProductInterface
         */
        $class = $this->getOfferClass();
        return $class::find()->andWhere([$class::getIdFieldName1c() => $offer->id])->one();
    }

    /**
     * @return ActiveRecord
     */
    protected function createProductModel($data)
    {
        $class = $this->getProductClass();
        if ($model = $class::createModel1c($data)) {
            return $model;
        } else {
            return Yii::createObject(['class' => $class]);
        }
    }

    /**
     * @param OfferInterface $model
     * @param Offer $offer
     */
    protected function parsePrice($model, $offer)
    {
        foreach ($offer->getPrices() as $price) {
            $model->setPrice1c($price);
        }
    }

    /**
     * @param ProductInterface $model
     * @param Product $product
     */
    protected function parseImage($model, $product)
    {
        $images = $product->getImages();
        foreach ($images as $image) {
            $path = realpath($this->module->getTmpDir() . DIRECTORY_SEPARATOR . $image->path);
            if (file_exists($path)) {
                $model->addImage1c($path, $image->caption);
            }
        }
    }

    /**
     * @param ProductInterface $model
     * @param Product $product
     */
    protected function parseGroups($model, $product)
    {
        $group = $product->getGroup();
        $model->setGroup1c($group);
    }

    /**
     * @param ProductInterface $model
     * @param Product $product
     */
    protected function parseRequisites($model, $product)
    {
        $requisites = $product->getRequisites();
        foreach ($requisites as $requisite) {
            $model->setRequisite1c($requisite->name, $requisite->value);
        }
    }

    /**
     * @param OfferInterface $model
     * @param Offer $offer
     */
    protected function parseSpecifications($model, $offer)
    {
        foreach ($offer->getSpecifications() as $specification) {
            $model->setSpecification1c($specification);
        }
    }

    /**
     * @param ProductInterface $model
     * @param Product $product
     */
    protected function parseProperties($model, $product)
    {
        $properties = $product->getProperties();
        foreach ($properties as $property) {
            $model->setProperty1c($property);
        }
    }

    /**
     * @return OfferInterface
     */
    protected function getOfferClass()
    {
        return $this->module->offerClass;
    }

    /**
     * @return ProductInterface
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

    /**
     * @return \carono\exchange1c\interfaces\GroupInterface
     */
    protected function getGroupClass()
    {
        return $this->module->groupClass;
    }

    /**
     * @return bool
     */
    public function actionError()
    {
        return false;
    }
}
