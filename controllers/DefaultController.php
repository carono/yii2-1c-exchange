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
        return $this->render('documentation');
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

    public function actionSettings()
    {
        return $this->render('settings');
    }
}