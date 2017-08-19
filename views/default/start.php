<?php

use yii\widgets\ListView;
use carono\exchange1c\widgets\Panel;

/**
 * @var \yii\data\ActiveDataProvider $dataProvider
 */

Panel::begin();
echo ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '@vendor/carono/yii2-1c-exchange/views/widgets/article-item'
]);
Panel::end();