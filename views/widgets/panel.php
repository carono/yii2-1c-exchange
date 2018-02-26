<?php
/**
 * @var \yii\web\View $this
 * @var string $content
 */
$id = $this->context->id;
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