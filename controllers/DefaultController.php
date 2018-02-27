<?php


namespace carono\exchange1c\controllers;


use yii\data\ArrayDataProvider;
use yii\helpers\FileHelper;

/**
 * Class DefaultController
 *
 * @package carono\exchange1c\controllers
 */
class DefaultController extends Controller
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @return string
     */
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

    /**
     * @return string
     */
    public function actionImport()
    {
        return $this->render('import');
    }

    /**
     * @return string
     */
    public function actionExport()
    {
        return $this->render('export');
    }

    /**
     * @return string
     */
    public function actionMonitor()
    {
        return $this->render('monitor');
    }

    /**
     * @return string
     */
    public function actionDocumentation()
    {
        $dir = \Yii::getAlias('@vendor/carono/yii2-1c-exchange/files/doc');
        $data = [];
        foreach (FileHelper::findFiles($dir) as $file) {
            $data[] = ['filename' => substr($file, strlen($dir) + 1), 'size' => filesize($file)];
        }
        $dataProvider = new ArrayDataProvider(['allModels' => $data]);
        return $this->render('documentation', ['dataProvider' => $dataProvider]);
    }

    /**
     * @return string
     */
    public function actionInterfaces()
    {
        return $this->render('interfaces');
    }

    /**
     * @param $file
     */
    public function actionDownload($file)
    {
        $content = file_get_contents($this->module->getTmpDir($file));
        \Yii::$app->response->sendContentAsFile($content, basename($file));
    }

    /**
     * @return string
     */
    public function actionSettings()
    {
        return $this->render('settings');
    }

    /**
     * @return \yii\web\Response
     */
    public function actionClearTmp()
    {
        foreach (FileHelper::findFiles($this->module->getTmpDir()) as $file) {
            unlink($file);
        }
        return $this->redirect(\Yii::$app->request->referrer);
    }
}