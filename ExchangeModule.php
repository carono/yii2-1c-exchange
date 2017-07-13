<?php

namespace carono\exchange1c;

use yii\helpers\FileHelper;

/**
 * exchange module definition class
 */
class ExchangeModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'carono\exchange1c\controllers';
    public $productClass;
    public $documentClass;
    public $groupClass;
    public $partnerClass;
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
    public $auth;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function getTmpDir($part = null)
    {
        $dir = \Yii::getAlias($this->tmpDir);
        if (!is_dir($dir)) {
            FileHelper::createDirectory($dir, 0777, true);
        }
        return $dir . ($part ? DIRECTORY_SEPARATOR . trim($part, '/\\') : '');
    }

    public function auth($login, $password)
    {
        /**
         * @var $class \yii\web\IdentityInterface
         */
        $class = \Yii::$app->user->identityClass;
        $user = $class::findByUsername($login);
        if ($user && $user->validatePassword($password)) {
            return $user;
        } else {
            return null;
        }
    }
}
