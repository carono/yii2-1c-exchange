<?php
use yii\widgets\ListView;

/**
 * @var \yii\data\ActiveDataProvider $dataProvider
 */
echo ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '@vendor/carono/yii2-1c-exchange/views/widgets/article-item'
]);