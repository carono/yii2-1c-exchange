<?php

namespace carono\exchange1c\models\forms;

use carono\exchange1c\ExchangeModule;
use carono\exchange1c\helpers\ModuleHelper;
use carono\exchange1c\queue\ExchangeQueue;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ImportForm extends Model
{
    protected $_file;

    public function getFile()
    {
        return $this->_file;
    }

    public function setFile()
    {
        $this->_file = UploadedFile::getInstance($this, 'file');
    }

    public function rules()
    {
        return [
//            [['file'], 'file', 'uploadRequired' => true, 'extensions' => ['zip']],
        ];
    }

    public function import()
    {
        /**
         * @var ExchangeModule $module
         */
        $module = ModuleHelper::getModuleByClass();
        Yii::$app->get($module->queue)->push(new ExchangeQueue());
    }
}