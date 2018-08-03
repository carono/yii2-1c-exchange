<?php

use yii\grid\GridView;
use carono\exchange1c\widgets\TestingGridView;
use yii\helpers\Html;
use yii\data\ArrayDataProvider;
use carono\exchange1c\widgets\Panel;

/**
 * @var \yii\web\View $this
 * @var \carono\exchange1c\models\TestingClass $testingClass
 * @var \carono\exchange1c\models\TestingClass $resultClass
 */
$this->title = 'Тестирование модуля';
echo Html::tag('div', 'В разработке', ['class' => 'alert alert-danger']);
if (YII_ENV_PROD) {
    ?>
    <div class="alert alert-danger">
        Проводить тестирование в окружении YII_ENV_PROD невозможно.
    </div>
    <?php
    return;
}

?>
    <div class="alert alert-warning">
        Внимание! Будет происходить тестирование вашей реализации методов.
    </div>
<?php

$links = [
    'groupClass' => ['testing/index', 'class' => 'TestingGroupClass'],
    'productClass' => ['testing/index', 'class' => 'TestingProductClass'],
    'documentClass' => ['testing/index', 'class' => 'TestingDocumentClass'],
    'partnerClass' => ['testing/index', 'class' => 'TestingPartnerClass'],
];
?>
    <div class="panel">
        <div class="btn-group panel-body">
            <?php
            foreach ($links as $name => $link) {
                echo Html::a($name, $link, ['class' => 'btn btn-primary']);
            }
            ?>
        </div>
    </div>


<?php
if (!$testingClass) {
    return;
}

$transaction = Yii::$app->db->beginTransaction();

Panel::begin();
$testingProductClass = $testingClass::testImplementsClass();


echo TestingGridView::widget([
    'formatter' => [
        'class' => \yii\i18n\Formatter::class,
        'nullDisplay' => ''
    ],
    'caption' => $testingClass->caption,
    'dataProvider' => new ArrayDataProvider(['allModels' => $testingClass::findAll()]),
]);

Panel::end();

if ($resultClass) {
    Panel::begin();
    $result = $resultClass->getResult();
    if (is_array($result)) {
        echo GridView::widget(['dataProvider' => new ArrayDataProvider(['allModels' => $result])]);
    } elseif (is_string($result)) {
        echo $result;
    } elseif (is_object($result)) {
        echo \yii\widgets\DetailView::widget(['model' => $result]);
    } else {
        var_dump($result);
    }
    Panel::end();
}

$transaction->rollBack();