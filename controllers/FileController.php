<?php


namespace carono\exchange1c\controllers;


use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;

class FileController extends Controller
{
    protected static function getAlias($path)
    {
        return \Yii::getAlias("@vendor/carono/yii2-1c-exchange/" . ltrim($path, '/'));
    }

    /**
     * @param $file
     * @param array $options
     * @throws NotFoundHttpException
     */
    protected function out($file, $options = [])
    {
        $filename = self::getAlias($file);
        if (!file_exists($filename)) {
            throw new NotFoundHttpException("File $filename not found");
        }
        $content = file_get_contents($filename);
        \Yii::$app->response->sendContentAsFile($content, basename($filename), $options);
    }

    /**
     * @param $file
     */
    protected function outAsFile($file)
    {
        $this->out($file);
    }

    /**
     * @param $file
     */
    protected function outAsImage($file)
    {
        $options = [
            'inline' => true,
            'mimeType' => FileHelper::getMimeType(self::getAlias($file))
        ];
        $this->out($file, $options);
    }

    /**
     * @param $file
     */
    public function actionArticle($file)
    {
        $this->outAsImage("files/articles/$file");
    }

    /**
     * @param $file
     */
    public function actionDoc($file)
    {
        $path = "/files/doc/$file";
        if (pathinfo($file, PATHINFO_EXTENSION) == 'xsd') {
            $this->outAsFile($path);
        } else {
            $this->outAsImage($path);
        }
    }

    /**
     * @param $file
     */
    public function actionXml($file)
    {
        $this->outAsImage("/files/xml/$file");
    }
}