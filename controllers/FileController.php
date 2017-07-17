<?php


namespace carono\exchange1c\controllers;


use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use yii\helpers\FileHelper;

class FileController extends Controller
{
    protected static function getAlias($path)
    {
        return \Yii::getAlias("@vendor/carono/yii2-1c-exchange/" . ltrim($path, '/'));
    }

    protected function out($file, $options = [])
    {
        $filename = self::getAlias($file);
        if (!file_exists($filename)) {
            throw new FileNotFoundException($filename);
        }
        $content = file_get_contents($filename);
        return \Yii::$app->response->sendContentAsFile($content, basename($filename), $options);
    }

    protected function outAsFile($file)
    {
        return $this->out($file);
    }

    protected function outAsImage($file)
    {
        $options = [
            'inline' => true,
            'mimeType' => FileHelper::getMimeType(self::getAlias($file))
        ];
        return $this->out($file, $options);
    }

    public function actionArticle($file)
    {
        return $this->outAsImage("files/articles/$file");
    }

    public function actionDoc($file)
    {
        return $this->outAsImage("/files/doc/$file");
    }

    public function actionXml($file)
    {
        return $this->outAsImage("/files/xml/$file");
    }
}