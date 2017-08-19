<?php
use yii\helpers\Html;

/**
 * @var \carono\exchange1c\models\Article $model
 */
?>
<?= Html::a('редактировать', ['default/update-article', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
<h1><?= $model->name ?></h1>
<div>
    <?= $model->content ?>
</div>
