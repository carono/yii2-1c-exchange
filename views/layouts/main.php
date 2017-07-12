<?php
use carono\exchange1c\assets\AppAsset;
use yii\widgets\Menu;

/**
 * @var \yii\web\View $this
 * @var string $content
 */

$actionId = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
$controller = Yii::$app->controller->id;

$bundle = AppAsset::register($this);

$this->beginPage();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lumino - Dashboard</title>
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
            <a class="navbar-brand" href="#"><span>YII2</span>Модуль обмена с 1С</a>
        </div>
    </div>
</nav>


<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
    <?= Menu::widget([
        'options' => ['class' => 'nav menu'],
        'items' => [
            [
                'label' => '<i class="glyph glyphicon glyphicon-book"></i>Start Yii2 1C Exchange',
                'url' => ['default/documentation'],
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
            ['options' => ['class' => 'divider']],
            [
                'label' => '<i class="glyph glyphicon glyphicon-book"></i>CommerceML (PDF)',
                'url' => ['default/documentation'],
                'encode' => false
            ],

            ['options' => ['class' => 'divider']],
            [
                'label' => '<i class="glyph glyphicon glyphicon-folder-open"></i>Файлы',
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
            ]
        ]
    ]);
    ?>

    <div class="attribution">Template by
        <a href="http://www.medialoot.com/item/lumino-admin-bootstrap-template/">Medialoot</a><br/>
    </div>
</div>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li>
                <a href="#">
                    <svg class="glyph stroked home">
                        <use xlink:href="#stroked-home"></use>
                    </svg>
                </a></li>
            <li class="active">Icons</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?= $this->title ?></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="canvas-wrapper">
                        <?= $content ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php $this->endBody() ?>
</body>
</html>

<?php $this->endPage() ?>

