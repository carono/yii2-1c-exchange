<?php


namespace carono\exchange1c\assets;


use yii\web\AssetBundle;

class LuminoAsset extends AssetBundle
{
    public $sourcePath = '@vendor/carono/yii2-1c-exchange/assets';
    public $css = [
        'css/bootstrap.min.css',
        'css/datepicker3.css',
        'css/styles.css',
        'css/default.css'
    ];
    public $js = [
        'js/lumino.glyphs.js',
        'js/html5shiv.min',
        'js/respond.min.js',
        'js/highlight.pack.js'
    ];
    public $publishOptions = [
        'forceCopy' => true
    ];
}