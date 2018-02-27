<?php
use carono\exchange1c\models\InterfaceTest;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
/**
 * @var string $variable
 */
?>
<div class="row">
    <div class="col-lg-12">
        <?php
        $class = Yii::$app->controller->module->{$variable};
        $interfaceTest = InterfaceTest::findByClass($class);

        $hint = '';
        if ($interfaceTest->model) {
            $hint = "Модель найдена, $class::PK = " . $interfaceTest->model->primaryKey;
        }
        $form = ActiveForm::begin();
        echo $form->field($interfaceTest, 'class')->hiddenInput(['value' => $class])->label(false);
        echo $form->field($interfaceTest, 'id')->textInput(['placeholder' => "Найти модель $class через findOne()"])->hint($hint);
        echo Html::submitButton('Найти', ['class' => 'btn btn-primary pull-right']);
        ActiveForm::end();
        ?>
    </div>
</div>