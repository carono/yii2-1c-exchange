<?php
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use carono\exchange1c\models\InterfaceTest;
use yii\helpers\StringHelper;
use carono\exchange1c\models\InterfaceModel;
use carono\exchange1c\helpers\ClassHelper;

/**
 * @var \yii\web\View $this
 * @var string $variable
 * @var string $class
 * @var InterfaceTest $interfaceTest
 */

?>

    <div class="row">
        <div class="col-lg-12">
            <?php
            if ($interfaceTest->model) {
                echo Html::tag('div', "Модель найдена, PK = " . $interfaceTest->model->primaryKey, ['class' => 'alert alert-success']);
            }
            $form = ActiveForm::begin(['layout' => 'horizontal']);
            echo $form->field($interfaceTest, 'id')->textInput(['placeholder' => 'Найти модель через findOne()']);
            echo Html::submitButton('Протестировать', ['class' => 'btn btn-primary pull-right']);
            ActiveForm::end();

            ?>
        </div>
    </div>

<?php
$classes = [
    'productClass' => ['interface' => 'carono\exchange1c\interfaces\ProductInterface'],
    'documentClass' => ['interface' => 'carono\exchange1c\interfaces\DocumentInterface'],
];
$interfaceClass = $classes[$variable]['interface'];

$this->title = "Класс $class";


$data = [];

foreach (ClassHelper::getInterfaceMethods($interfaceClass) as $method) {
    $data[] = new InterfaceModel([
        'function' => $method->name,
        'interface' => $interfaceClass,
        'description' => $method->getDocComment(),
        'class' => $class
    ]);
}

echo GridView::widget([
    'dataProvider' => new ArrayDataProvider(['allModels' => $data]),
    'rowOptions' => function (InterfaceModel $model) {
        if ($model->status_id == InterfaceModel::STATUS_METHOD_NOT_FOUND) {
            return ['class' => 'danger'];
        }
        return [];
    },
    'columns' => [
        'function',
        [
            'attribute' => 'description',
            'format' => 'raw',
            'value' => function ($data) {
                $description = trim($data['description'], '/*');
                return $data['description'] ? Html::tag('pre', $description) : '';
            }
        ],
        'status_name',
    ]
]);