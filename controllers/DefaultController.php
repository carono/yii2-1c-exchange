<?php


namespace carono\exchange1c\controllers;


use yii\data\ArrayDataProvider;
use yii\helpers\FileHelper;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionFiles()
    {
        $data = [];
        $dir = $this->module->getTmpDir();
        foreach (FileHelper::findFiles($dir) as $file) {
            $data[] = ['filename' => substr($file, strlen($dir) + 1), 'size' => filesize($file)];
        }
        $dataProvider = new ArrayDataProvider(['allModels' => $data]);
        return $this->render('files', ['dataProvider' => $dataProvider]);
    }

    public function actionImport()
    {
        return $this->render('import');
    }

    public function actionExport()
    {
        return $this->render('export');
    }

    public function actionDocumentation()
    {
        $dir = \Yii::getAlias('@vendor/carono/yii2-1c-exchange/doc');
        $data = [];
        foreach (FileHelper::findFiles($dir) as $file) {
            $data[] = ['filename' => substr($file, strlen($dir) + 1), 'size' => filesize($file)];
        }
        $dataProvider = new ArrayDataProvider(['allModels' => $data]);
        return $this->render('documentation', ['dataProvider' => $dataProvider]);
    }

    public function actionStart()
    {
        return $this->render('start');
    }

    public function actionInterfaces()
    {
        return $this->render('interfaces');
    }

    public function actionDownload($file)
    {
        $content = file_get_contents($this->module->getTmpDir($file));
        return \Yii::$app->response->sendContentAsFile($content, basename($file));
    }

    public function actionViewDoc($file)
    {
        $filePath = \Yii::getAlias("@vendor/carono/yii2-1c-exchange/doc/$file");
        $content = file_get_contents($filePath);
        $options = [
            'inline' => true,
            'mimeType' => FileHelper::getMimeType($filePath)
        ];
        return \Yii::$app->response->sendContentAsFile($content, basename($file), $options);
    }

    public function actionSettings()
    {
        return $this->render('settings');
    }

    public function actionClearTmp()
    {
        foreach (FileHelper::findFiles($this->module->getTmpDir()) as $file) {
            @unlink($file);
        }
        return $this->redirect(\Yii::$app->request->referrer);
    }
}