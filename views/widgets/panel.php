<?php
/**
 * @var \yii\web\View $this
 * @var string $content
 * @var \carono\exchange1c\widgets\Panel $context
 */
$context = $this->context;
$id = $context->id;
?>
<div class="row" id="<?= $id ?>">
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