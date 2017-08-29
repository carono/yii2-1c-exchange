<?php


namespace carono\exchange1c\controllers;


use carono\exchange1c\models\Article;
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
        if (\Yii::$app->request->isPost){

        }
        return $this->render('import');
    }

    public function actionExport()
    {
        return $this->render('export');
    }

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

    public function actionStart()
    {
        $dataProvider = Article::find()->search();
        return $this->render('start', ['dataProvider' => $dataProvider]);
    }

    public function actionUpdateArticle($id)
    {
        $article = Article::findOne($id, true);
        if ($article->load(\Yii::$app->request->post())) {
            if ($article->save()) {
                return $this->refresh();
            } else {

            }
        }
        return $this->render('update-article', ['article' => $article]);
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

    public function actionClearTmp()
    {
        foreach (FileHelper::findFiles($this->module->getTmpDir()) as $file) {
            @unlink($file);
        }
        return $this->redirect(\Yii::$app->request->referrer);
    }
}