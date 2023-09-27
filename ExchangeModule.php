<?php

namespace carono\exchange1c;

use carono\exchange1c\helpers\ModuleHelper;
use carono\exchange1c\queue\OfferParseQueue;
use carono\exchange1c\queue\ProductParseQueue;
use Exception;
use yii\base\InvalidArgumentException;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\web\IdentityInterface;
use Yii;

/**
 * exchange module definition class
 */
class ExchangeModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'carono\exchange1c\controllers';
    /**
     * Модель продукта
     *
     * @var \carono\exchange1c\interfaces\ProductInterface
     */
    public $productClass;
    /**
     * Модель предложения
     *
     * @var \carono\exchange1c\interfaces\OfferInterface
     */
    public $offerClass;
    /**
     * Модель документа
     *
     * @var \carono\exchange1c\interfaces\DocumentInterface
     */
    public $documentClass;
    /**
     * Модель группы продукта
     *
     * @var \carono\exchange1c\interfaces\GroupInterface
     */
    public $groupClass;
    /**
     * Модель пользователя
     *
     * @var \carono\exchange1c\interfaces\PartnerInterface
     */
    public $partnerClass;
    /**
     * Модель склада
     *
     * @var \carono\exchange1c\interfaces\WarehouseInterface
     */
    public $warehouseClass;
    /**
     * Обмен документами
     *
     * @var bool
     */
    public $exchangeDocuments = false;
    /**
     * Режим отладки - сохраняем xml файлы в runtime
     *
     * @var bool
     */
    public $debug = false;
    /**
     * При обмене используем архиватор, если расширения нет, то зачение не учитывается
     *
     * @var bool
     */
    public $useZip = true;
    /**
     * Папка, где будут сохранятся временные файлы
     *
     * @var string
     */
    public $tmpDir = '@runtime/1c_exchange';
    /**
     * При сохранении товара, используем валидацию или нет
     *
     * @var bool
     */
    public $validateModelOnSave = false;
    /**
     * Установка лимита выполнения скрипта
     *
     * @var int
     */
    public $timeLimit = 1800;
    /**
     * Установка лимита памяти скрипта
     *
     * @var mixed
     */
    public $memoryLimit;
    /**
     * Автоматическая установка правила для ссылки /1c_exchange.php
     *
     * @var bool
     */
    public $bootstrapUrlRule = true;

    /**
     * @var bool Добавлять правило в конец
     */
    public $appendRule = false;

    public $exchangeDocumentEncode = 'windows-1251';

    public $redactorModuleName = 'carono-exchange-redactor';
    /**
     * Функция авторизации пользователя
     * function ($login, $password): \yii\web\IdentityInterface|null
     *
     * @var \Closure
     */
    public $auth;

    public $offerParsing;

    public $productParsing;

    public $offerChunk;

    public $productChunk;

    public $parseChunk = 100;

    public $useQueue = false;

    public $offerParseClass = OfferParseQueue::class;

    public $productParseClass = ProductParseQueue::class;

    public $queue;

    private function loadRedactorModule()
    {
        $redactorClass = 'yii\redactor\widgets\Redactor';
        $moduleRedactorName = $this->redactorModuleName;
        if (class_exists($redactorClass) && !Yii::$app->getModule($moduleRedactorName)) {
            $routeName = Inflector::camel2id($moduleRedactorName);
            \Yii::$app->setModule($moduleRedactorName, [
                'class' => 'yii\redactor\RedactorModule',
                'uploadDir' => '@vendor/carono/yii2-1c-exchange/files/articles',
                'imageUploadRoute' => ["/$routeName/upload/image"],
                'fileUploadRoute' => ["/$routeName/upload/file"],
                'imageManagerJsonRoute' => ["/$routeName/upload/image-json"],
                'fileManagerJsonRoute' => ["/$routeName/upload/file-json"],
                'imageAllowExtensions' => ['jpg', 'png', 'gif'],
                'on beforeAction' => function () use ($moduleRedactorName) {
                    $redactor = \Yii::$app->getModule($moduleRedactorName);
                    $redactor->uploadUrl = '../file/article?file=';
                    \Yii::$app->setModule($moduleRedactorName, $redactor);
                }
            ]);
        }
    }

    /**
     * @return null|\yii\base\Module
     */
    public function getRedactor()
    {
        return Yii::$app->getModule($this->redactorModuleName);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->useQueue && !$this->queue) {
            throw new InvalidArgumentException('queue param is not set in exchange-1c module');
        }

        if (!isset(\Yii::$app->i18n->translations['models'])) {
            \Yii::$app->i18n->translations['models'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@app/messages',
                'sourceLanguage' => 'en',
            ];
        }
        $this->loadRedactorModule();

        parent::init();
    }

    public function getTmpDir($part = null)
    {
        $dir = \Yii::getAlias($this->tmpDir);
        $path = $dir . ($part ? DIRECTORY_SEPARATOR . trim($part, '/\\') : '');
        if (!is_dir($path)) {
            FileHelper::createDirectory($path, 0777, true);
        }
        return $path;
    }

    public function saveFileToTmp($file)
    {
        if (!$file || !file_exists($file)) {
            return null;
        }
        $dir = $this->getTmpDir('queue_tmp');
        $md5 = md5_file($file);
        $tmpFile = $dir . DIRECTORY_SEPARATOR . $md5 . '_' . basename($file);

        if (file_exists($tmpFile) && md5_file($tmpFile) == $md5) {
            return $tmpFile;
        }

        if (!copy($file, $tmpFile)) {
            throw new Exception("Fail copy '$file' to '$tmpFile'");
        }
        return $tmpFile;
    }

    /**
     * @param $login
     * @param $password
     * @return null|IdentityInterface
     */
    public function auth($login, $password)
    {
        /**
         * @var $class \yii\web\IdentityInterface
         * @var IdentityInterface $user
         */
        $class = \Yii::$app->user->identityClass;
        if (method_exists($class, 'findByUsername')) {
            $user = $class::findByUsername($login);
            if ($user && method_exists($user, 'validatePassword') && $user->validatePassword($password)) {
                return $user;
            }
        }
        return null;
    }
}