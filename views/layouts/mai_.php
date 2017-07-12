<?php
use carono\exchange1c\assets\AppAsset;
use yii\widgets\Menu;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
AppAsset::register($this);

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>1C exchange</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Set height of the grid so .sidenav can be 100% (adjust if needed) */
        .row.content {
            height: 1500px
        }

        /* Set gray background color and 100% height */
        .sidenav {
            background-color: #f1f1f1;
            height: 100%;
        }

        /* Set black background color, white text and some padding */
        footer {
            background-color: #555;
            color: white;
            padding: 15px;
        }

        /* On small screens, set height to 'auto' for sidenav and grid */
        @media screen and (max-width: 767px) {
            .sidenav {
                height: auto;
                padding: 15px;
            }

            .row.content {
                height: auto;
            }
        }
    </style>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="container-fluid">
    <div class="row content">
        <div class="col-sm-3 sidenav">
            <h4></h4>
            <?=
            Menu::widget([
                'options' => ['class' => 'nav nav-pills nav-stacked'],
                'items'   => [
                    ['label' => 'Импорт', 'url' => ['default/import']],
                    ['label' => 'Экспорт', 'url' => ['default/export']],
                    ['label' => '<h3>Документация</h3>', 'encode' => false, 'url' => null],
                    ['label' => 'CommerceML', 'url' => ['default/documentation']],
                    ['label' => 'Yii2 1C Exchange', 'url' => ['default/documentation']],
                    ['label' => '<h3>Отладка</h3>', 'encode' => false, 'url' => null],
                    ['label' => 'Файлы', 'url' => ['default/files']],
                    ['label' => 'Интерфейсы', 'url' => ['default/interfaces']]
                ]
            ]);
            ?>
        </div>

        <div class="col-sm-9">
            <h4>
                <small><?= $this->title ?></small>
            </h4>
            <hr>
            <?= $content ?>
        </div>
    </div>
</div>

<footer class="container-fluid">
    <p>Footer Text</p>
</footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
