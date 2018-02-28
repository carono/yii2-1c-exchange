<?php

use carono\exchange1c\assets\AppAsset;
use carono\exchange1c\widgets\Menu;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 * @var string $content
 */

$actionId = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
$controller = Yii::$app->controller->id;

$bundle = AppAsset::register($this);
$ico = $this->assetManager->publish('@vendor/carono/yii2-1c-exchange/assets/other/favicon.ico');
$this->beginPage();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="<?= $ico[1] ?>"/>
    <title>1С Exchange - <?= $this->title ?></title>
    <?php $this->head() ?>
</head>

<body>
<?php $this->beginBody() ?>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#sidebar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?= Url::to(['default/index']) ?>">
                <span>YII2</span>Модуль обмена с 1С
            </a>
            <ul class="user-menu">
                <li class="pull-right">
                    <a href="/"><?= Yii::$app->name ?></a>
                </li>
            </ul>
        </div>
    </div>
</nav>


<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
    <?= Menu::widget([
        'options' => ['class' => 'nav menu'],
        'items' => [
            [
                'label' => '<i class="glyph glyphicon glyphicon-book"></i>Старт Yii2 1C Exchange',
                'url' => ['article/index'],
                'active' => in_array(Yii::$app->controller->id, ['article']),
                'encode' => false
            ],
            ['options' => ['class' => 'divider']],
            [
                'label' => '<i class="glyph glyphicon glyphicon-import"></i>Импорт',
                'url' => ['default/import'],
                'encode' => false
            ],
            [
                'label' => '<i class="glyph glyphicon glyphicon-export"></i>Экспорт',
                'url' => ['default/export'],
                'encode' => false
            ],
            [
                'label' => '<i class="glyph glyphicon glyphicon-blackboard"></i>Монитор',
                'url' => ['default/monitor'],
                'encode' => false
            ],
            ['options' => ['class' => 'divider']],
            [
                'label' => '<i class="glyph glyphicon glyphicon-book"></i>CommerceML',
                'url' => ['default/documentation'],
                'encode' => false
            ],

            ['options' => ['class' => 'divider']],
            [
                'label' => '<i class="glyph glyphicon glyphicon-folder-open"></i>Временные файлы',
                'url' => ['default/files'],
                'encode' => false
            ],
            [
                'label' => '<i class="glyph glyphicon glyphicon-cog"></i>Интерфейсы',
                'url' => ['default/interfaces'],
                'active' => in_array($controller, ['interface']) || $actionId == 'default/interfaces',
                'encode' => false
            ],
            [
                'label' => '<i class="glyph glyphicon glyphicon-wrench"></i>Настройки модуля',
                'url' => ['default/settings'],
                'encode' => false
            ],
            ['options' => ['class' => 'divider']],
            [
                'label' => '<i class="glyph glyphicon glyphicon-asterisk"></i>Тестирование',
                'url' => ['testing/index'],
                'encode' => false
            ],
        ]
    ]);
    ?>

    <div class="attribution">Template by
        <a href="http://www.medialoot.com/item/lumino-admin-bootstrap-template/">Medialoot</a><br/>
    </div>
</div>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <?php
        echo Breadcrumbs::widget([
            'links' => ArrayHelper::getValue($this->params, 'breadcrumbs', []),
            'options' => [
                'class' => 'breadcrumb col-lg-7'
            ],
            'homeLink' => [
                'label' => '<svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg>',
                'encode' => false,
                'url' => ['default/index']
            ]
        ]);
        ?>
        <div class="col-lg-5 action-menu">
            <?php
            echo Menu::widget([
                'items' => ArrayHelper::getValue($this->params, 'buttons', []),
                'linkTemplate' => '<a href="{url}"{options}>{label}</a>',
                'itemOptions' => ['tag' => false],
                'options' => [
                    'tag' => 'div',
                    'class' => ['btn-group pull-right'],
                ],
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?= $this->title ?></h1>
        </div>
    </div>

    <?= $content ?>

</div>

<?php $this->endBody() ?>
</body>
</html>

<?php $this->endPage() ?>

