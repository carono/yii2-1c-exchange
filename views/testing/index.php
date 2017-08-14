<?php

use yii\grid\GridView;
use carono\exchange1c\models\TestingProductClass;
use carono\exchange1c\models\TestingDocumentClass;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use carono\exchange1c\models\InterfaceTest;
use yii\data\ArrayDataProvider;
use carono\exchange1c\widgets\Panel;

/**
 * @var \yii\web\View $this
 * @var \carono\exchange1c\models\InterfaceTest $interfaceTest
 */
$this->title = 'Тестирование модуля';

Panel::begin();
echo $this->render('../partial/find-model', ['variable' => 'productClass']);
echo GridView::widget([
    'dataProvider' => new ArrayDataProvider(['allModels' => TestingProductClass::findAll()]),
    'rowOptions' => function ($data) {
        if ($data->result === true) {
            return ['class' => 'success'];
        } elseif ($data->result === false) {
            return ['class' => 'danger'];
        } else {
            return ['class' => 'warning'];
        }
    },
    'columns' => [
        'name',
        'comment:raw'
    ]
]);
Panel::end();

Panel::begin();
echo $this->render('../partial/find-model', ['variable' => 'documentClass']);
echo GridView::widget([
    'dataProvider' => new ArrayDataProvider(['allModels' => TestingDocumentClass::findAll()]),
    'rowOptions' => function ($data) {
        if (!$data->result) {
            return ['class' => 'danger'];
        } else {
            return ['class' => 'success'];
        }
    },
    'columns' => [
        'name',
        'comment:raw'
    ]
]);

Panel::end();
return;
/*
$import = Yii::getAlias('@vendor/carono/yii2-1c-exchange/files/xml/import.xml');
$offers = Yii::getAlias('@vendor/carono/yii2-1c-exchange/files/xml/offers.xml');
$cml = new CommerceML($import, $offers);
$cml->addXmls($import, $offers);
$dataProvider = new ArrayDataProvider(['allModels' => $cml->catalog->getProducts()]);
?>
    <h2>Товары (<?= Html::a('import.xml', ['file/xml', 'file' => 'import.xml'], ['target' => '_blank']) ?>)</h2>
<?php

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'Ид',
        'Артикул',
        'Наименование',
        'group.name'
    ]
]);

?>
    <h2>Предложения (<?= Html::a('offers.xml', ['file/xml', 'file' => 'offers.xml'], ['target' => '_blank']) ?>)</h2>
<?php
$dataProvider = new ArrayDataProvider(['allModels' => $cml->offerPackage->offers]);
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'Ид',
        'Артикул',
        'Наименование',
        'Количество',
        [
            'attribute' => 'price',
            'format' => 'raw',
            'value' => function (\Zenwalker\CommerceML\Model\Offer $model) {
                $result = [];
                foreach ($model->prices as $price) {
                    $result[] = Yii::$app->formatter->asCurrency($price->cost, $price->currency);
                }
                return join('<br>', $result);
            }
        ]
    ]
]);
*/


