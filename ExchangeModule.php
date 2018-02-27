<?php

namespace carono\exchange1c;

use carono\exchange1c\helpers\ModuleHelper;
use yii\helpers\FileHelper;
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
     * @var \carono\exchange1c\interfaces\ProductInterface
     */
    public $productClass;
    /**
     * @var \carono\exchange1c\interfaces\OfferInterface
     */
    public $offerClass;
    /**
     * @var \carono\exchange1c\interfaces\DocumentInterface
     */
    public $documentClass;
    /**
     * @var \carono\exchange1c\interfaces\GroupInterface
     */
    public $groupClass;
    /**
     * @var \carono\exchange1c\interfaces\PartnerInterface
     */
    public $partnerClass;
    /**
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
    public $tmpDir = '@runtime/1c_exchange';
    /**
     * При сохранении товара, используем валидацию или нет
     *
     * @var bool
     */
    public $validateModelOnSave = false;
    public $timeLimit = 1800;
    public $memoryLimit = null;
    public $bootstrapUrlRule = true;
    public $auth;

    private function loadRedactorModule()
    {
        $redactorClass = 'yii\redactor\widgets\Redactor';
        $moduleRedactorName = 'carono-exchange-redactor';
        if (class_exists($redactorClass) && !Yii::$app->getModule($moduleRedactorName)) {
            \Yii::$app->setModule($moduleRedactorName, [
                'class' => 'yii\redactor\RedactorModule',
                'uploadDir' => '@vendor/carono/yii2-1c-exchange/files/articles',
                'imageAllowExtensions' => ['jpg', 'png', 'gif'],
                'on beforeAction' => function () use ($moduleRedactorName) {
                    $path = ModuleHelper::getModuleNameByClass('carono\exchange1c\ExchangeModule', 'exchange');
                    $redactor = \Yii::$app->getModule($moduleRedactorName);
                    $redactor->uploadUrl = "/$path/file/article?file=";
                    \Yii::$app->setModule($moduleRedactorName, $redactor);
                }
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
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
        if (!is_dir($dir)) {
            FileHelper::createDirectory($dir, 0777, true);
        }
        return $dir . ($part ? DIRECTORY_SEPARATOR . trim($part, '/\\') : '');
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