<?php

use yii\data\ArrayDataProvider;
use Zenwalker\CommerceML\CommerceML;
use yii\grid\GridView;

$import = Yii::getAlias('@vendor/carono/yii2-1c-exchange/xml/import.xml');
$offers = Yii::getAlias('@vendor/carono/yii2-1c-exchange/xml/offers.xml');
$cml = new CommerceML($import, $offers);
$cml->addXmls($import, $offers);
$dataProvider = new ArrayDataProvider(['allModels' => $cml->catalog->getProducts()]);
?>
    <h2>Товары (import.xml)</h2>
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
    <h2>Предложения (offers.xml)</h2>
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


