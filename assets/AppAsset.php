<?php


namespace carono\exchange1c\assets;


use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'carono\exchange1c\assets\LuminoAsset',
    ];
}