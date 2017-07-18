<?php

use yii\grid\GridView;
use carono\exchange1c\models\TestingProductClass;
use carono\exchange1c\models\TestingDocumentClass;

/**
 * @var \yii\web\View $this
 */
$this->title = 'Тестирование модуля';

echo GridView::widget([
    'dataProvider' => TestingProductClass::findAll(),
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

echo GridView::widget([
    'dataProvider' => TestingDocumentClass::findAll(),
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


