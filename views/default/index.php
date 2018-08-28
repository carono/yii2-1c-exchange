<?php

use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 */
$this->title = '';
?>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="canvas-wrapper">
                <h3 class="text-center">Добро пожаловать в модуль обмена товарами с 1С</h3>
                <p>Этот модуль поможет вам настроить обмен с Вашим сайтом и 1С Предприятие: <b>Управление торговлей</b>
                    или <b>розница</b>, так же модуль может обмениваться документами с 1С.</p>
                <p>Чтобы начать интеграцию, прочитайте <?= Html::a('документацию', ['article/view?id=1']) ?></p>
                <p>Описание находится еще в стадии наполнения, могут присутсвовать ошибки и неточности.</p>
                <p>Если есть вопросы по настройки или возможностям, пишите на  <a href="https://github.com/carono/yii2-1c-exchange/issues">странице репозитория</a>, мне на почту info@carono.ru или в телеграм <a href="https://t.me/Carno59">Carno59</a> </p>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="canvas-wrapper text-center">
                <iframe src="https://money.yandex.ru/quickpay/shop-widget?writer=seller&targets=%D0%9F%D0%BE%D0%B4%D0%B4%D0%B5%D1%80%D0%B6%D0%BA%D0%B0%20carono%2Fyii2-1c-exchange&targets-hint=&default-sum=1000&button-text=14&payment-type-choice=on&mobile-payment-type-choice=on&hint=&successURL=&quickpay=shop&account=410014600834879"
                        width="423" height="222" frameborder="0" allowtransparency="true" scrolling="no"></iframe>
            </div>
        </div>
    </div>
</div>

