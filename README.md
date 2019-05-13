[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/carono/yii2-1c-exchange/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/carono/yii2-1c-exchange/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/carono/yii2-1c-exchange/v/stable)](https://packagist.org/packages/carono/yii2-1c-exchange)
[![Total Downloads](https://poser.pugx.org/carono/yii2-1c-exchange/downloads)](https://packagist.org/packages/carono/yii2-1c-exchange)
[![License](https://poser.pugx.org/carono/yii2-1c-exchange/license)](https://packagist.org/packages/carono/yii2-1c-exchange)


* [Введение](#1)
* [Подключение модуля](#2)
* [Свойства модуля обмена](#5)
* [Настройка 1С](#3)
* [Настройка авторизации](#4)
* [Интерфейсы моделей](#31)
	* [groupClass Группа продуктов](#8)
		* [createTree1c](#18)
	* [productClass Модель продукта](#7)
		* [setRequisite1c](#9)
		* [setGroup1c](#11)
		* [createProperties1c](#15)
		* [setProperty1c](#12)
		* [addImage1c](#13)
		* [ getGroup1c](#14)
		* [getOffer1c](#16)
		* [createModel1c](#17)
	* [offerClass Модель предложения](#19)
		* [getGroup1c](#23)
		* [createPriceTypes1c](#25)
		* [setPrice1c](#24)
		* [setSpecification1c](#26)
	* [partnerClass Модель пользователя](#20)
	* [documentClass Модель документа](#22)
		* [findDocuments1c](#27)
		* [getOffers1c](#28)
		* [getRequisites1c](#29)
		* [getPartner1c](#30)
	* [warehouseClass Модель склада](#21)
	* [Общие методы](#32)
		* [getExportFields1c](#33)
		* [getIdFieldName1c](#34)
		* [setRaw1cData](#35)
* [Описание протокола обмена](#36)
* [Тестирование и поиск ошибок](#37)
* [Полезные ссылки](#38)
* [События модуля](#39)


<a name="1">Введение</a>
=

<h2>Что это за модуль, и какие задачи он должен выполнять?</h2><p>Установка этого модуля, должна упрощать интеграцию 1С в ваш сайт.</p><p>Модуль содержит набор интерфейсов, которые необходимо реализовать, чтобы получить возможность обмениваться товарами и документами с 1С. Предполагается, что у Вас есть 1С:Предприятие 8, Управление торговлей", редакция 11.3, версия 11.3.2 на платформе 8.3.9.2033. Если у вас версия конфигурации ниже, то скорее всего модуль все равно будет работать, т.к. по большей части, обмен с сайтами сильно не меняется в 1С от версии к версии.</p><p>После подключения модуля к вашему проекту, вы можете получить доступ к текущей документации по ссылке <strong>/exchange/article/index</strong></p>


<a name="2">Подключение модуля</a>
=

<p>1. Подключаем пакет через компосер
</p><pre>composer require carono/yii2-1c-exchange</pre><p>2. Подключаем модуль в конфиге приложения
</p><pre>'modules' =&gt; [
    'exchange' =&gt; [
        'class' =&gt; \carono\exchange1c\ExchangeModule::class
    ]
]
</pre><p>3. Если используете apache как веб сервер, не забудьте создать и настроить <strong>.htaccess</strong> в <strong>web </strong>директории</p>


<a name="5">Свойства модуля обмена</a>
=

<table>
<tbody>
<tr>
	<td style="text-align: center;"><strong>Свойство</strong>
	</td>
	<td style="text-align: center;"><strong>По умолчанию</strong>
	</td>
	<td style="text-align: center;"><strong>Описание</strong>
	</td>
</tr>
<tr>
	<td>productClass
	</td>
	<td>null
	</td>
	<td>Класс для продукта
	</td>
</tr>
<tr>
	<td>offerClass
	</td>
	<td>null
	</td>
	<td>Класс для предложения
	</td>
</tr>
<tr>
	<td>documentClass
	</td>
	<td>null
	</td>
	<td>Класс для документа (заказа)
	</td>
</tr>
<tr>
	<td>groupClass
	</td>
	<td>null
	</td>
	<td>Класс для группы продуктов
	</td>
</tr>
<tr>
	<td>partnerClass
	</td>
	<td>null
	</td>
	<td>Класс контрагента (пользователя/клиента)
	</td>
</tr>
<tr>
	<td>warehouseClass
	</td>
	<td>null
	</td>
	<td>Класс для склада (не используется, может быть удалён)
	</td>
</tr>
<tr>
	<td>exchangeDocuments
	</td>
	<td>false
	</td>
	<td>Обмен заказами
	</td>
</tr>
<tr>
	<td>debug
	</td>
	<td>false
	</td>
	<td>Режим отладки, данные сохраняются в tmpDir
	</td>
</tr>
<tr>
	<td>useZip
	</td>
	<td>true
	</td>
	<td>Использовать архивы при обмене, если доступны
	</td>
</tr>
<tr>
	<td>tmpDir
	</td>
	<td>@runtime/1c_exchange
	</td>
	<td>Папка для временных файлов
	</td>
</tr>
<tr>
	<td>validateModelOnSave
	</td>
	<td>false
	</td>
	<td>При сохранении товара, используем валидацию или нет (может быть удалено)
	</td>
</tr>
<tr>
	<td>timeLimit
	</td>
	<td>1800
	</td>
	<td>Время выполнения скрипта (set_time_limit)<br>
	</td>
</tr>
<tr>
	<td>memoryLimit
	</td>
	<td>null
	</td>
	<td>Ограничение памяти при обмене (memory_limit)<br>
	</td>
</tr>
<tr>
	<td>bootstrapUrlRule
	</td>
	<td>true
	</td>
	<td>Автоматически подключать правило для роутинга 1c_exchange.php
	</td>
</tr>
<tr>
	<td>appendRule
	</td>
	<td>false
	</td>
	<td>Добавлять правило роутинга в конец
	</td>
</tr>
<tr>
	<td>auth
	</td>
	<td>null
	</td>
	<td>Фукнция для авторизации
	</td>
</tr>
</tbody>
</table>


<a name="3">Настройка 1С</a>
=

<p>1. Устанавливаем 1С:Предприятие 8 Управление торговлей, Управление торговлей", редакция 11.3, версия 11.3.2 (<a href="magnet:?xt=urn:btih:AA1729FE7AE39FF43D5EB63CB8D5AFF19C891892&amp;tr=http%3A%2F%2Fbt3.t-ru.org%2Fann%3Fmagnet&amp;dn=1%D0%A1%20%D0%A3%D0%BF%D1%80%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5%20%D1%82%D0%BE%D1%80%D0%B3%D0%BE%D0%B2%D0%BB%D0%B5%D0%B9%2011.3%20%D0%9F%D1%80%D0%BE%D1%84.%20%D0%A3%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0%20%2B%20%D0%9E%D0%B1%D0%BD%D0%BE%D0%B2%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5%20v.%2011.3.2.157%20x86%20%5B29.12.2016%2C%20RUS%5D" target="_blank">магнитная ссылка</a>), платформа 8.3.9.2033 (и выше)</p><p>Настройки будут производиться на демо версии.</p><p>2. Переходим в настройки синхронизации данных, через пункт НСИ и администрирование, или через поиск</p><p><img src="https://raw.github.com/carono/yii2-1c-exchange/HEAD/files/articles/100/c2818da962-slide1.png"></p><p>3. Переходим в узлы обмена с сайтами</p><p><img src="https://raw.github.com/carono/yii2-1c-exchange/HEAD/files/articles/100/30de0bdc18-slide2.png"><br></p><p>4. Создаём новый узел, и заполняем данные</p><ul><li>Наименование</li><li>Выгрузка товаров</li><li>Адрес сайта, указываем ваш <strong>сайт/</strong><strong>1c_exchange.php</strong></li><li>Логин и пароль от пользователя, от чьего имени будем выгружать товары (<a href="#4">настройка авторизации</a>)</li></ul><p><img src="https://raw.github.com/carono/yii2-1c-exchange/HEAD/files/articles/100/dcf36639b2-2018-02-2619-55-41.png"><br></p>


<a name="4">Настройка авторизации</a>
=

<p>Авторизация в модуле реализована через поведение \yii\filters\auth\HttpBasicAuth</p><pre>'modules' =&gt; [
    'exchange' =&gt; [
        'class' =&gt; \carono\exchange1c\ExchangeModule::class,
        'auth' =&gt; function ($username, $password) {
            if ($user = \app\models\User::findByUsername($username)) {
                if ($user-&gt;validatePassword($password)) {
                    return $user;
                }
            }
            return false;
        }
     ]
],</pre><p><br></p>


<a name="8">groupClass Группа продуктов</a>
=

<p>Настройка модуля, указываем класс модели работы группы
</p><pre>[
    'exchange' =&gt; [
        'class' =&gt; \carono\exchange1c\ExchangeModule::class,
        'groupClass' =&gt; \app\models\Group::class,
    ]
]
</pre><p>Миграция, создаём группу, для хранения продукции, должна быть древовидная структура с неограниченной вложенностью, рекомендуется использовать <a href="https://packagist.org/packages/creocoder/yii2-nested-sets" target="_blank">nested sets</a> , но для примера используем более простой пример
</p><pre>$this-&gt;createTable('{{%group}}', [
    'id' =&gt; $this-&gt;primaryKey(),
    'name' =&gt; $this-&gt;string()-&gt;comment('Наименование группы'),
    'parent_id' =&gt; $this-&gt;integer()-&gt;comment('Родительская группа'),
    'accounting_id' =&gt; $this-&gt;string()-&gt;comment('Код в 1С')-&gt;unique(),
]);
</pre><p>Список интерфейсов, которые необходимо реализовать <a href="../interface/check?variable=groupClass" target="_blank">здесь</a>
</p>


<a name="18">createTree1c</a>
=

<h2>public static function createTree1c($groups) </h2><p>В функции <strong>createTree1c</strong> нам требуется реализовать создаение всего дерева продуктов родитель-&gt;потомок. Метод вызывается только один раз перед началом импорта, поэтому в этой функции нужно создать всё дерево групп полностью.
</p><pre class="pre">&lt;?php
/**
 * This class is generated using the package carono/codegen
 */
namespace app\models;
use carono\exchange1c\interfaces\GroupInterface;
/**
 * This is the model class for table "group".
 */
class Group extends base\Group implements GroupInterface
{
    /**
     * Возвращаем имя поля в базе данных, в котором хранится ID из 1с
     *
     * @return string
     */
    public static function getIdFieldName1c()
    {
        return 'accounting_id';
    }
    /**
     * Создание дерева групп
     * в параметр передаётся массив всех групп (import.xml &gt; Классификатор &gt; Группы)
     * $groups[0]-&gt;parent - родительская группа
     * $groups[0]-&gt;children - дочерние группы
     *
     * @param \Zenwalker\CommerceML\Model\Group[] $groups
     * @return void
     */
    public static function createTree1c($groups)
    {
        foreach ($groups as $group) {
            self::createByML($group);
            if ($children = $group-&gt;getChildren()) {
                self::createTree1c($children);
            }
        }
    }
    /**
     * Создаём группу по модели группы CommerceML
     * проверяем все дерево родителей группы, если родителя нет в базе - создаём
     *
     * @param \Zenwalker\CommerceML\Model\Group $group
     * @return Group|array|null
     */
    public static function createByML(\Zenwalker\CommerceML\Model\Group $group)
    {
        /**
         * @var \Zenwalker\CommerceML\Model\Group $parent
         */
        if (!$model = Group::findOne(['accounting_id' =&gt; $group-&gt;id])) {
            $model = new self;
            $model-&gt;accounting_id = $group-&gt;id;
        }
        $model-&gt;name = $group-&gt;name;
        if ($parent = $group-&gt;getParent()) {
            $parentModel = self::createByML($parent);
            $model-&gt;parent_id = $parentModel-&gt;id;
            unset($parentModel);
        } else {
            $model-&gt;parent_id = null;
        }
        $model-&gt;save();
        return $model;
    }
}
</pre><p>Протестировать вашу реализацию, можно <a href="../testing/index?class=TestingGroupClass" target="_blank">здесь</a>
</p>


<a name="7">productClass Модель продукта</a>
=

<p>Продукт - моделью продукта является сам товар, картинки, его свойства и реквизиты, но не остаток или цена.
</p><p><em>Для тех разработчиков, которые не очень хорошо знакомы с концепцией хранения данных в 1С, нужно дополнительно пояснение. В 1С существуют продукты и предложения. Продукт эта сама сущность товара, предложение, это то что можно продать, т.е. предложения и учавствуют в продажах.</em>
</p><p><em>Пример:
	</em>
</p><p><em><strong>Туфли лабутены Модель X</strong> - это продукт, у него есть картинки, различные реквизиты (производитель, цвет, материал и т.д.), которые присущи данному продукту.
	</em>
</p><p><em><strong>Туфли лабутены Модель X, размер 32, за 20000р</strong>  - это предложение, от одного продукта может быть несколько предложений, с разными характеристиками, такими как размер, и разными ценами, на каждое предложение может быть свой остаток.</em>
</p><h2>Настройка</h2><p>Добавляем в настройки модуля вашу модель для продукта <strong>'productClass' =&gt; \app\models\Product::class</strong>
</p><h2>
<pre style="font-size: 12.6px;">[
    'exchange' =&gt; [
        'class' =&gt; \carono\exchange1c\ExchangeModule::class,
        'groupClass' =&gt; \app\models\Group::class,
        'productClass' =&gt; \app\models\Product::class,
    ]
]
</pre>
<table style="width: 1466px;">
<tbody>
<tr>
</tr>
</tbody>
</table></h2><h2>Интерфейсы</h2><p>В вашей модели имплементируем интерфейс <strong>carono\exchange1c\interfaces\ProductInterface</strong><strong></strong>
</p>


<a name="9">setRequisite1c</a>
=

<h2>public function setRequisite1c($name, $value)<br></h2><p>Установка реквизитов для продукта. Список резвизитов находится в <strong>import.xml &gt; Каталог &gt; Товары &gt; Товар &gt; ЗначенияРеквизитов &gt; ЗначениеРеквизита</strong></p><p>Для хранения реквизитов, потребуется таблица реквизитов, а также сводная таблица продут+реквизит+значение
</p><p><img src="https://raw.github.com/carono/yii2-1c-exchange/HEAD/files/articles/100/0489d0ae5b-requisite.png" width="653" height="310" style="width: 653px; height: 310px;"></p><pre>public function setRequisite1c($name, $value)
{
    if (!$requisite = Requisite::findOne(['name' =&gt; $name])) {
        $requisite = new Requisite();
        $requisite-&gt;name = $name;
        $requisite-&gt;save();
    }
    $this-&gt;addPivot($requisite, PvProductRequisite::class, ['value' =&gt; $value]);
}
</pre>


<a name="11">setGroup1c</a>
=

<h2>public function setGroup1c($group)</h2><p>Установка группы, где находится продукт. Все группы у вас уже должны быть сохранены в базе, т.к. ранее вызывался метод <strong>\carono\exchange1c\interfaces\GroupInterface::createTree1c</strong>, и все дерево групп уже создано Вами, а значит можно не проверять на существование группы.</p><pre>    public function setGroup1c($group)
    {
        $id = Group::find()-&gt;select(['id'])-&gt;andWhere(['accounting_id' =&gt; $group-&gt;id])-&gt;scalar();
        $this-&gt;updateAttributes(['group_id' =&gt; $id]);
    }
</pre>


<a name="15">createProperties1c</a>
=

<h2>public static function createProperties1c($properties)</h2>
<p>Функция вызывается один раз при импорте, в ней необходимо создать все свойста и значения свойств.
</p>
<pre>    /**
     * @param PropertyCollection $properties
     * @return mixed
     */
    public static function createProperties1c($properties)
    {
        /**
         * @var \Zenwalker\CommerceML\Model\Property $property
         */
        foreach ($properties as $property) {
            $propertyModel = Property::createByMl($property);
            foreach ($property->getAvailableValues() as $value) {
                if (!$propertyValue = PropertyValue::findOne(['accounting_id' => $value->id])) {
                    $propertyValue = new PropertyValue();
                    $propertyValue->name = (string)$value->Значение;
                    $propertyValue->property_id = $propertyModel->id;
                    $propertyValue->accounting_id = (string)$value->ИдЗначения;
                    $propertyValue->save();
                    unset($propertyValue);
                }
            }
        }
    }
</pre>
<p><img src="https://raw.github.com/carono/yii2-1c-exchange/HEAD/files/articles/100/30d7d0869c-property.png" style="color: rgb(95, 100, 104);">
</p>


<a name="12">setProperty1c</a>
=

<h2>public function setProperty1c($property)</h2><p>Свойство продукта и значение свойства являются отдельными сущностями, поэтому их нужно хранить в отдельных таблицах, а значения и продукт хранить в сводной таблице.
</p><p>Все свойства уже должны быть заполнены, т.к. ранее выполнялся <strong>createProperties1c($properties)</strong>, поэтому можем искать свойства и значения по id.</p><p>Значение свойства могут быть как отдельной сущностью, так и простым значением, поэтому есть в xml есть поле <strong>ИдЗначения</strong> значит нужно искать в таблице со значениями, иначе должно быть просто строка или число.</p><p><em>* в этой фукнции используется трейт из пакета carono/yii2-migrate</em><br>
</p><pre>    
    /**
     * $property - Свойство товара (import.xml &gt; Классификатор &gt; Свойства &gt; Свойство)
     * $property-&gt;value - Разыменованное значение (string) (import.xml &gt; Классификатор &gt; Свойства &gt; Свойство &gt; Значение)
     * $property-&gt;getValueModel() - Данные по значению, Ид значения, и т.д (import.xml &gt; Классификатор &gt; Свойства &gt; Свойство &gt; ВариантыЗначений &gt; Справочник)
     *
     * @param MlProperty $property
     * @return void
     */
    public function setProperty1c($property)
    {
        $propertyModel = Property::findOne(['accounting_id' =&gt; $property-&gt;id]);
        $propertyValue = $property-&gt;getValueModel();
        if ($propertyAccountingId = (string)$propertyValue-&gt;ИдЗначения) {
            $value = PropertyValue::findOne(['accounting_id' =&gt; $propertyAccountingId]);
            $attributes = ['property_value_id' =&gt; $value-&gt;id];
        } else {
            $attributes = ['value' =&gt; $propertyValue-&gt;value];
        }
        $this-&gt;addPivot($propertyModel, PvProductProperty::class, $attributes);
    }
</pre><p><br>
</p><p><img src="https://raw.github.com/carono/yii2-1c-exchange/HEAD/files/articles/100/adc45161dd-properties.png" style="color: rgb(95, 100, 104);">
</p>


<a name="13">addImage1c</a>
=

<h2>public function addImage1c($path, $caption)</h2><p>В этой фукнции мы получаем абсолютный путь до картинки и название изрбражения (для alt аттрибута)</p><p><em>* в этой фукнции используются трейт из пакета carono/yii2-migrate и управление файлами из carono/yii2-file-upload</em></p><pre>    /**
     * @param string $path
     * @param string $caption
     * @return mixed
     */
    public function addImage1c($path, $caption)
    {
        if (!$this-&gt;getImages()-&gt;andWhere(['md5' =&gt; md5_file($path)])-&gt;exists()) {
            $this-&gt;addPivot(FileUpload::startUpload($path)-&gt;process(), PvProductImage::class, ['caption' =&gt; $caption]);
        }
    }
</pre>


<a name="14"> getGroup1c</a>
=

<h2>public function getGroup1c()</h2><p>Получаем группу, где находится текущий продукт, группа должна наследовать интерфейс <strong>\carono\exchange1c\interfaces\GroupInterface</strong><strong></strong><span></span></p><pre>    /**
     * @return GroupInterface
     */
    public function getGroup1c()
    {
        return $this-&gt;group;
    }
</pre>


<a name="16">getOffer1c</a>
=

<h2>public function getOffer1c($offer)</h2>
<p>В эту фукнцию отправляется xml данные предложения из файла, необходимо создать или найти вашу модель предложения (интерфейс <strong>\carono\exchange1c\interfaces\OfferInterface</strong><strong></strong>) и вернуть в результате.
</p>
<pre>    /**
     * @param \Zenwalker\CommerceML\Model\Offer $offer
     * @return OfferInterface
     */
    public function getOffer1c($offer)
    {
        $offerModel = Offer::createByMl($offer);
        $offerModel-&gt;product_id = $this-&gt;id;
        if ($offerModel-&gt;getDirtyAttributes()) {
            $offerModel-&gt;save();
        }
        return $offerModel;
    }
</pre>

Пример парсинга предложения
<pre>    
class Offer extends BaseOffer implements OfferInterface {   
    /**
     * @param MlOffer $offer
     * @return Offer
     */
    public static function createByMl($offer)
    {
        if (!$offerModel = self::findOne(['accounting_id' =&gt; $offer-&gt;id])) {
            $offerModel = new self;
            $offerModel-&gt;name = (string)$offer-&gt;name;
            $offerModel-&gt;accounting_id = (string)$offer-&gt;id;
        }
        $offerModel-&gt;remnant = (string)$offer-&gt;Количество;
        return $offerModel;
    }
}
</pre>


<a name="17">createModel1c</a>
=

<h2>public static function createModel1c($product)</h2><p>В этой фукнции мы должны найти или создать новый продукт и вернуть вашу модель.</p><pre>    /**
     * @param \Zenwalker\CommerceML\Model\Product $product
     * @return self
     */
    public static function createModel1c($product)
    {
        if (!$model = Product::findOne(['accounting_id' =&gt; $product-&gt;id])) {
            $model = new Product();
            $model-&gt;accounting_id = $product-&gt;id;
        }
        $model-&gt;name = $product-&gt;name;
        $model-&gt;description = (string)$product-&gt;Описание;
        $model-&gt;article = (string)$product-&gt;Артикул;
        $model-&gt;save();
        return $model;
    }
</pre>


<a name="19">offerClass Модель предложения</a>
=

<p>Предложение - модель товара, которая учавствует в продажах, у нее есть остаток и набор цен
</p>
<h2>Настройка</h2>
<p>Добавляем в настройки модуля вашу модель для предложения <strong>'offerClass' =&gt; \app\models\Offer::class</strong>
</p>

<pre>[
    'exchange' =&gt; [
        'class' =&gt; \carono\exchange1c\ExchangeModule::class,
        'groupClass' =&gt; \app\models\Group::class,
        'productClass' =&gt; \app\models\Product::class,
        'offerClass' =&gt; \app\models\Offer::class,
    ]
]
</pre>

<h2>Интерфейсы</h2>
<p>В вашей модели имплементируем интерфейс <strong>carono\exchange1c\interfaces\OfferInterface</strong><strong></strong>
</p>


<a name="23">getGroup1c</a>
=

<h2>public function getGroup1c()</h2><p>Здесь нам необходимо получить группу, где находится предложение. Берем её через связь с продуктом.</p><p><em><span style="color: rgb(247, 150, 70);">* Вероятно в будущем будет заменено на getProduct1c()</span></em></p><pre>    /**
     * @return GroupInterface
     */
    public function getGroup1c()
    {
        return $this-&gt;product-&gt;group;
    }
</pre>


<a name="25">createPriceTypes1c</a>
=

<h2>public static function createPriceTypes1c($types)</h2><p>В этом методе необходимо создать все типы цен, фукнция вызывается один раз. Тип цены содержит название (розничная, оптовая и др.), а так же название валюты.
</p><p><img src="https://raw.github.com/carono/yii2-1c-exchange/HEAD/files/articles/100/47e17f3950-pricetype.png">
</p><pre>    /**
     * @param $types
     * @return void
     */
    public static function createPriceTypes1c($types)
    {
        foreach ($types as $type) {
            PriceType::createByMl($type);
        }
    }
</pre><p>Пример реализации создания типа</p><pre>class PriceType extends BasePriceType
{
    /**
     * @param Simple $type
     * @return PriceType
     */
    public static function createByMl($type)
    {
        if (!$priceType = self::findOne(['accounting_id' =&gt; $type-&gt;id])) {
            $priceType = new self;
            $priceType-&gt;accounting_id = $type-&gt;id;
        }
        $priceType-&gt;name = $type-&gt;name;
        $priceType-&gt;currency = (string)$type-&gt;Валюта;
        if ($priceType-&gt;getDirtyAttributes()) {
            $priceType-&gt;save();
        }
        return $priceType;
    }
}
</pre>


<a name="24">setPrice1c</a>
=

<h2>public function setPrice1c($price)</h2><p>Цена является отдельной сущностью, поэтому должна храниться в отдельной таблице, а с предложением должна быть связана через сводную таблицу.
</p><p><img src="https://raw.github.com/carono/yii2-1c-exchange/HEAD/files/articles/100/f4ca0f06dc-prices.png"><br></p><pre>    /**
     * offers.xml &gt; ПакетПредложений &gt; Предложения &gt; Предложение &gt; Цены
     *
     * Цена товара,
     * К $price можно обратиться как к массиву, чтобы получить список цен (Цены &gt; Цена)
     * $price-&gt;type - тип цены (offers.xml &gt; ПакетПредложений &gt; ТипыЦен &gt; ТипЦены)
     *
     * @param \Zenwalker\CommerceML\Model\Price $price
     * @return void
     */
    public function setPrice1c($price)
    {
        $priceType = PriceType::findOne(['accounting_id' =&gt; $price-&gt;getType()-&gt;id]);
        $priceModel = Price::createByMl($price, $this, $priceType);
        $this-&gt;addPivot($priceModel, PvOfferPrice::class);
    }
</pre><p>Пример создания цены
</p><pre>class Price extends BasePrice
{
    /**
     * @param MlPrice $price
     * @param Offer $offer
     * @param PriceType $type
     * @return Price
     */
    public static function createByMl($price, $offer, $type)
    {
        if (!$priceModel = $offer-&gt;getPrices()-&gt;andWhere(['type_id' =&gt; $type-&gt;id])-&gt;one()) {
            $priceModel = new self();
        }
        $priceModel-&gt;value = $price-&gt;cost;
        $priceModel-&gt;performance = $price-&gt;performance;
        $priceModel-&gt;currency = $price-&gt;currency;
        $priceModel-&gt;rate = $price-&gt;rate;
        $priceModel-&gt;type_id = $type-&gt;id;
        $priceModel-&gt;save();
        return $priceModel;
    }
}

</pre>


<a name="26">setSpecification1c</a>
=

<h2>public function setSpecification1c($specification)</h2><p>Характеристики для предложения являются отдельной сущностью и должны соединятся через сводную таблицу.</p><p><img src="https://raw.github.com/carono/yii2-1c-exchange/HEAD/files/articles/100/b10bbf4908-specifications.png"></p><pre>    /**
     * offers.xml &gt; ПакетПредложений &gt; Предложения &gt; Предложение &gt; ХарактеристикиТовара &gt; ХарактеристикаТовара
     *
     * Характеристики товара
     * $name - Наименование
     * $value - Значение
     *
     * @param \Zenwalker\CommerceML\Model\Simple $specification
     * @return void
     */
    public function setSpecification1c($specification)
    {
        $specificationModel = Specification::createByMl($specification);
        $this-&gt;addPivot($specificationModel, PvOfferSpecification::class, ['value' =&gt; (string)$specification-&gt;Значение]);
    }
</pre><p>Пример парсинга характеристики</p><pre>class Specification extends BaseSpecification
{
    public static function createByMl($specification)
    {
        if (!$specificationModel = self::findOne(['accounting_id' =&gt; $specification-&gt;id])) {
            $specificationModel = new self;
            $specificationModel-&gt;name = $specification-&gt;name;
            $specificationModel-&gt;accounting_id = $specification-&gt;id;
            $specificationModel-&gt;save();
        }
        return $specificationModel;
    }
}
</pre>


<a name="20">partnerClass Модель пользователя</a>
=

<p>Данный интерфейс на данный момент требуется только для работы обмена документов. Единственное что нужно реализовать, это общий метод public function getExportFields1c, который описывает поля для сериализации <span class="redactor-invisible-space">в xml при обмене. Необходимо возвращать массив, где ключ, это тег в xml, а значение - ваши данные. Все поддерживаемые стандартом данные можно найти в <a href="/exchange/default/documentation" target="_blank">спецификации</a>, исчерпывающую информацию лучше смотреть в xsd файлах, для этого потребуется visual studio, т.к. в официальных pdf файлах присутствуют неточности. Чуть подробнее о методе можно почитать <a href="#33" target="_blank">здесь</a></span>
</p><h2>Настройка</h2><p>Добавляем в настройки модуля вашу модель для предложения <strong>'partnerClass' =&gt; \app\models\Partner::class</strong>
</p><h2>
<pre style="font-size: 12.6px;">[
    'exchange' =&gt; [
        'class' =&gt; \carono\exchange1c\ExchangeModule::class,
        'groupClass' =&gt; \app\models\Group::class,
        'productClass' =&gt; \app\models\Product::class,
        'offerClass' =&gt; \app\models\Offer::class,
        'partnerClass' =&gt; \app\models\Partner::class, 
    ]
]
</pre>
<table style="width: 1466px;">
<tbody>
<tr>
</tr>
</tbody>
</table></h2><h2>Интерфейсы</h2><p>В вашей модели имплементируем интерфейс <strong>carono\exchange1c\interfaces\PartnerInterface</strong></p><pre>    public function getExportFields1c($context = null)
    {
        return [
            'Ид' =&gt; 'id',
            'Наименование' =&gt; 'username',
            'ПолноеНаименование' =&gt; 'full_name',
            'Фамилия' =&gt; 'surname',
            'Имя' =&gt; 'name',
        ];
    }
</pre>


<a name="22">documentClass Модель документа</a>
=

<p>Документ в 1С, он же заказ на сайте. У документа дожны быть связи на <strong>предложения </strong>через сводную таблицу. Суммы желательно указывать и в сводной таблице и в самом заказе, чтобы не расчитывать её в динамике т.к. цена на предложение может поменятся и клиенту в итоге поступит счет на другую сумму.</p><h2>Настройка</h2><p>Добавляем в настройки модуля вашу модель для предложения <strong>'documentClass' =&gt; \app\models\Document::class</strong>
</p><h2>
<pre style="font-size: 12.6px;">[
    'exchange' =&gt; [
        'class' =&gt; \carono\exchange1c\ExchangeModule::class,
        'groupClass' =&gt; \app\models\Group::class,
        'productClass' =&gt; \app\models\Product::class,
        'offerClass' =&gt; \app\models\Offer::class,
        'partnerClass' =&gt; \app\models\Partner::class, 
        'documentClass' =&gt; \app\models\Document::class, 
    ]
]
</pre>
<table style="width: 1466px;">
<tbody>
<tr>
</tr>
</tbody>
</table></h2><h2>Интерфейсы</h2><p>В вашей модели имплементируем интерфейс <strong>carono\exchange1c\interfaces\DocumentInterface</strong>
</p><p><strong><br></strong></p><p><strong><img src="https://raw.github.com/carono/yii2-1c-exchange/HEAD/files/articles/100/79d570a828-order.png" style="color: rgb(95, 100, 104);"><br></strong></p>


<a name="27">findDocuments1c</a>
=

<h2>public static function findDocuments1c()</h2><p>Получение всех подготовленных документов (заказов). В этой функции необходимо возвращать все документы, которые готовы для импорта в 1С.
</p><p>Необходимо обратить внимение, 1С заменит существующие документы, если при следующем импорте они будут в этой фукнции, поэтому по завершению импорта, необходимо выставлять флаг или менять статус, чтобы при повторном импорте этих документов уже небыло. Это значит, что если вы создали документы в 1С, начали с ними работать, двигать по статусам или наполнять данными, то при следующем импорте, они перезапишутся и придется всё начинать сначала. Как обновлять уже экспортированные данные, можно почитать в разделе с <a href="#39" target="_blank">событиями</a>.</p><pre>    /**    
     * @return DocumentInterface[]
     */
    public static function findDocuments1c()
    {
        return self::find()-&gt;andWhere(['status_id' =&gt; 2])-&gt;all();
    }
</pre>


<a name="28">getOffers1c</a>
=

<h2>public function getOffers1c()</h2><p>Получаем все предложения, которые были добавлены в этот документ (заказ)</p><pre>    /**
     * @return OfferInterface[]
     */
    public function getOffers1c()
    {
        return $this-&gt;offers;
    }
</pre>


<a name="29">getRequisites1c</a>
=

<h2>public function getRequisites1c()</h2><p><span style="color: rgb(247, 150, 70);">Еще сам не уверен где эта фукнция используется, пока не обязательно к заполнению.</span></p>


<a name="30">getPartner1c</a>
=

<h2>public function getPartner1c()</h2><p>Необходимо вернуть пользователя, который сделал заказ, класс должен имплементировать <strong>\carono\exchange1c\interfaces\PartnerInterface</strong><strong></strong></p><pre>    /**
     * Получаем контрагента у документа
     *
     * @return PartnerInterface
     */
    public function getPartner1c()
    {
        return $this-&gt;user;
    }
</pre>


<a name="21">warehouseClass Модель склада</a>
=

<p><span style="color: rgb(247, 150, 70);">На данный момент не используется</span></p>


<a name="33">getExportFields1c</a>
=

<h2>public function getExportFields1c($context = null)</h2>
<p>Объекты, которые создаются в 1С, имеют интерфейс <strong>\carono\exchange1c\interfaces\ExportFieldsInterface</strong> с этим методом. В этом методе мы должны вернуть массив, который сериализуется в xml для 1С. Ключ массива, это название тега в xml.
</p>
<p>Значение может иметь разные типы, аналогично с функцией <strong>fields</strong> для rest api.
</p>
<p><strong>string</strong> - передаём название аттрибута, если такого аттрибута нет, вернется эта строка
</p>
<p><strong>Closure</strong><span class="redactor-invisible-space"> - передаём фукцнию function(</span>$model)
</p>
<p><strong>array</strong> - с помощью массива можно кастомизировать xml, или когда требуется создать несколько элементов. В таком массиве есть несколько зарезервированных ключей: @content - тело тега, @name - имя тега, @attributes - массив аттрибутов.
</p>
<p>Все значения обрабатываются рекурсивно, поэтому можно составлять сложные структуры.
</p>
<p>Входной параметр $context - это контекст, в рамках которого необходимо сериализовать объект, например для предложения, контектом будет заказ.
</p>
<p>Пример сериализации для контрагента в документе.
</p>
<pre>    public function getExportFields1c($context = null)
    {
        return [
            'Ид' =&gt; 'id',
            'Наименование' =&gt; 'login',
            'ПолноеНаименование' =&gt; 'full_name',
            'Фамилия' =&gt; 'surname',
            'Имя' =&gt; 'name',
            'Контакты' =&gt; [
                [
                    '@name' =&gt; 'Контакт',
                    'Тип' =&gt; 'Почта',
                    'Значение' =&gt; $this-&gt;email,
                ],
                [
                    '@name' =&gt; 'Контакт',
                    'Тип' =&gt; 'ТелефонРабочий',
                    'Значение' =&gt; $this-&gt;phone,
                ],
            ],
        ];
    }
</pre>
<p>Результат</p>
<pre>&lt;Контрагенты&gt;
    &lt;Контрагент&gt;
        &lt;Ид&gt;13&lt;/Ид&gt;
        &lt;Наименование&gt;info@carono.ru&lt;/Наименование&gt;
        &lt;ПолноеНаименование&gt;Иванов Иван Иванович&lt;/ПолноеНаименование&gt;
        &lt;Контакт&gt;
            &lt;Тип&gt;Почта&lt;/КонтактВид&gt;
            &lt;Значение&gt;info@carono.ru&lt;/Значение&gt;
        &lt;/Контакт&gt;
        &lt;Контакт&gt;
            &lt;Тип&gt;ТелефонРабочий&lt;/КонтактВид&gt;
            &lt;Значение&gt;+8(908)123-45-67&lt;/Значение&gt;
        &lt;/Контакт&gt;
    &lt;/Контрагент&gt;
&lt;/Контрагенты&gt;
</pre>


<a name="34">getIdFieldName1c</a>
=

<h2>public static function getIdFieldName1c()</h2><p>У всех сущностей в 1С имеется уникальный идентификатор <strong>Ид</strong>, необходимо вернуть название поля, в котором будет хранится это значение.</p><pre>    /**
     * Возвращаем имя поля в базе данных, в котором хранится ID из 1с
     *
     * @return string
     */
    public static function getIdFieldName1c()
    {
        return 'accounting_id';
    }
</pre>


<a name="35">setRaw1cData</a>
=

<h2>public function setRaw1cData($cml, $object)</h2><p>Если по каким то причинам файлы import.xml или offers.xml были модифицированы <br>и какие то данные не попадают в парсер, в самом начале вызывается данный метод, в<br>$object и $cml можно получить все данные для ручного парсинга. </p><p><span style="color: rgb(247, 150, 70);">К сожалению на данный момент, не все сущности проходят этот метод.</span></p>


<a name="36">Описание протокола обмена</a>
=

<p>Источник: <a href="http://v8.1c.ru/edi/edi_stnd/131/">http://v8.1c.ru/edi/edi_stnd/131/</a></p><p>Данный открытый протокол разработан компаниями "1С" и <a href="http://www.1c-bitrix.ru/">"1С-Битрикс"</a>.</p><p>Протокол используется штатной процедурой обмена коммерческими данными между системой "1С:Предприятие", с одной стороны, и системой управления сайтом, с другой стороны.</p><p>Функционально обмен делится на два блока:</p><ul><li><a href="http://v8.1c.ru/edi/edi_stnd/131/#1">выгрузка на сайт торговых предложений (каталогов продукции), данных об остатках на складах (с разбивкой и сводно), данных только о ценах и остатках (без описания номенклатуры)</a>;</li><li><a href="http://v8.1c.ru/edi/edi_stnd/131/#2">обмен информацией о заказах</a>.</li></ul><p>Первый блок обеспечивает публикацию на сайте каталога номенклатурных позиций и данных. Второй блок необходим для передачи с сайта в систему "1С:Предприятие" информации о заказах интернет-магазина, и дальнейшую синхронизацию статусов и параметров заказов.</p><p>В обоих случаях инициатором обмена выступает система "1С:Предприятие". Обмен электронными документами осуществляется в соответствии с правилами и форматами, описанными в стандарте <a href="http://www.v8.1c.ru/edi/edi_stnd/90/92.htm">CommerceML 2</a>.</p><p>При инициализации взаимодействия устанавливается HTTP соединение. Система "1С:Предприятие" запрашивает у сайта необходимые параметры, такие, как максимальный объем пакета, поддержка сжатия и др.. На основании этих данных система 1С:Предприятие формирует XML сообщения и передает их на сайт.</p><h2><img src="http://v8.1c.ru/rombik.gif"><b><a name="1"></a>Выгрузка на сайт</b></h2><p>Данные для публикации на сайте выгружаются одним пакетом.</p><h3>A. Начало сеанса</h3><p>Выгрузка данных начинается с того, что система "1С:Предприятие" отправляет http-запрос следующего вида: <br><b>http://&lt;сайт&gt;/&lt;путь&gt; /1c_exchange.php?type=catalog&amp;mode=checkauth.</b></p><p>В ответ система управления сайтом передает системе «1С:Предприятие» три строки (используется разделитель строк "\n"):</p><ul><li>слово <i>"success"</i>;</li><li>имя Cookie;</li><li>значение Cookie.</li></ul><p><b><i>Примечание. </i></b><i>Все последующие запросы к системе управления сайтом со стороны "1С:Предприятия" содержат в заголовке запроса имя и значение Cookie.</i></p><h3>B. Запрос параметров от сайта</h3><p>Далее следует запрос следующего вида:<b> <br></b><b>http://&lt;сайт&gt;/&lt;путь&gt; /1c_exchange.php?type=catalog&amp;mode=init</b></p><p>В ответ система управления сайтом передает две строки:</p><p>1. <b>zip=yes</b>, если сервер поддерживает обмен в zip-формате - в этом случае на следующем шаге файлы должны быть упакованы в zip-формате<i><br>или<br></i><b>zip=no</b> - в этом случае на следующем шаге файлы не упаковываются и передаются каждый по отдельности.</p><p>2. <b>file_limit=&lt;число&gt;</b>, где &lt;число&gt; - максимально допустимый размер файла в байтах для передачи за один запрос. Если системе "1С:Предприятие" понадобится передать файл большего размера, его следует разделить на фрагменты.</p><h3>C. Выгрузка на сайт файлов обмена</h3><p>Затем "1С:Предприятие" запросами с параметрами вида <br><b>http://&lt;сайт&gt;/&lt;путь&gt; /1c_exchange.php?type=catalog&amp;mode=file&amp;filename=&lt;имя файла&gt;</b> <br>выгружает на сайт файлы обмена в формате CommerceML 2, посылая содержимое файла или его части в виде POST.</p><p>В случае успешной записи файла система управления сайтом выдает строку "<i>success</i>".</p><h3>D. Пошаговая загрузка данных</h3><p>На последнем шаге по запросу из "1С:Предприятия" производится пошаговая загрузка данных по запросу с параметрами вида <b>http://&lt;сайт&gt;/&lt;путь&gt; /1c_exchange.php?type=catalog&amp;mode=import&amp;filename=&lt;имя файла&gt;</b></p><p>Во время загрузки система управления сайтом может отвечать в одном из следующих вариантов.</p><p>1. Если в первой строке содержится слово "<i>progress</i>" - это означает необходимость послать тот же запрос еще раз. В этом случае во второй строке будет возвращен текущий статус обработки, объем загруженных данных, статус импорта и т.д.</p><p>2. Если в ответ передается строка со словом "<i>success</i>", то это будет означать сообщение об успешном окончании обработки файла.</p><p><b><i>Примечание. </i></b><i>Если в ходе какого-либо запроса произошла ошибка, то в первой строке ответа системы управления сайтом будет содержаться слово "failure", а в следующих строках - описание ошибки, произошедшей в процессе обработки запроса.<br> Если произошла необрабатываемая ошибка уровня ядра продукта или sql-запроса, то будет возвращен html-код.</i></p><h3>Примеры файлов выгрузки</h3><p><a href="http://v8.1c.ru/edi/edi_stnd/131/import.xml">Сведения о товарах в формате XML.</a><br><a href="http://v8.1c.ru/edi/edi_stnd/131/offers.xml">Сведения о ценах в формате XML</a>.</p><h2><img src="http://v8.1c.ru/rombik.gif"><b><a name="2"></a>Обмен информацией о заказах</b></h2><p>Заказы, оформленные на сайте, загружаются в систему "1С:Предприятие".</p><p><b>Последовательность действий при работе с заказом</b></p><p>1. Заказ оформляется на сайте</p><p>2. При передаче в систему "1С:Предприятие" в заказе устанавливается категория "Заказ с сайта".<br>При формировании заказа в системе "1С:Предприятие" записываются номер и дата заказа, с которыми он оформлен на сайте. Поиск контрагента осуществляется по ИНН или наименованию, в зависимости от указанных настроек.</p><p>3. При загрузке заказа производится поиск договора с контрагентом. Договор ищется среди существующих договоров с клиентом, с признаком ведения взаиморасчетов по заказам (по указанной в настройках загрузки Организации). Если не находится ни один договор, то создается новый.</p><p>4. При загрузке заказа загружаются все его свойства, переданные с сайта. Свойства ищутся в системе "1С:Предприятие" по наименованию. Если с таким наименованием свойства нет, то заводится новое свойство со значениями типа строка или число.</p><p>5. Заказ может модифицироваться в системе "1С:Предприятие", при этом его изменения будут выгружаться на сайт</p><p>6. Если заказ оплачивается или отгружается в системе "1С:Предприятие", то состояния заказа по оплате и по отгрузке выгружаются на сайт только при полном выполнении операции (полной оплате и полной отгрузке). До этого момента заказ считается не оплаченным и не отгруженным.</p><p>7. При попытке в системе "1С:Предприятие" изменить заказ, по которому произведена оплата или отгрузка, заказ на сайт не загрузится как измененный. При этом пользователь получит об этом сообщение.</p><p>8. После каждой выгрузка заказа на сайт, на стороне сайта определяются значения его категорий (ссылка на категории). Эти значения устанавливаются в системе "1С:Предприятие" так, как они присвоены заказу на сайте</p><h3>A. Начало сеанса</h3><p>Выгрузка данных начинается с того, что система "1С:Предприятие" отправляет http-запрос следующего вида: <br><b>http://&lt;сайт&gt;/&lt;путь&gt; /1c_exchange.php?type=sale&amp;mode=checkauth.</b></p><p>В ответ система управления сайтом передает системе «1С:Предприятие» три строки (используется разделитель строк "\n"):</p><ul><li>слово <i>"success"</i>;</li><li>имя Cookie;</li><li>значение Cookie.</li></ul><p><b><i>Примечание. </i></b><i>Все последующие запросы к системе управления сайтом со стороны "1С:Предприятия" содержат в заголовке запроса имя и значение Cookie.</i></p><h3>B. Уточнение параметров сеанса</h3><p>Далее следует запрос следующего вида:<b> <br></b><b>http://&lt;сайт&gt;/&lt;путь&gt; /1c_exchange.php?type=sale&amp;mode=init</b></p><p>В ответ система управления сайтом передает две строки:</p><p>1. <b>zip=yes</b>, если сервер поддерживает обмен в zip-формате - в этом случае на следующем шаге файлы должны быть упакованы в zip-формате<i><br>или<br></i><b>zip=no</b> - в этом случае на следующем шаге файлы не упаковываются и передаются каждый по отдельности.</p><p>2. <b>file_limit=&lt;число&gt;</b>, где &lt;число&gt; - максимально допустимый размер файла в байтах для передачи за один запрос. Если системе "1С:Предприятие" понадобится передать файл большего размера, его следует разделить на фрагменты.</p><h3>C. Получение файла обмена с сайта</h3><p>Затем на сайт отправляется запрос вида<br><b>http://&lt;сайт&gt;/&lt;путь&gt; /1c_exchange.php?type=sale&amp;mode=query.</b></p><p>Сайт передает сведения о заказах в формате <a href="http://www.v8.1c.ru/edi/edi_stnd/90/92.htm">CommerceML 2</a>. В случае успешного получения и записи заказов "1С:Предприятие" передает на сайт запрос вида<b> <br></b><b>http://&lt;сайт&gt;/&lt;путь&gt; /1c_exchange.php?type=sale&amp;mode=success</b></p><h3>D. Отправка файла обмена на сайт</h3><p>Затем система "1С:Предприятие" отправляет на сайт запрос вида<b> <br>http://&lt;сайт&gt;/&lt;путь&gt; /1c_exchange.php?type=sale&amp;mode=file&amp;filename=&lt;имя файла&gt;</b>, <br>который загружает на сервер файл обмена, посылая содержимое файла в виде POST.</p><p>В случае успешной записи файла система управления сайтом передает строку со словом "<i>success</i>". Дополнительно на следующих строчках могут содержаться замечания по загрузке.</p><p><b><i>Примечание. </i></b><i>Если в ходе какого-либо запроса произошла ошибка, то в первой строке ответа системы управления сайтом будет содержаться слово "failure", а в следующих строках - описание ошибки, произошедшей в процессе обработки запроса.<br> Если произошла необрабатываемая ошибка уровня ядра продукта или sql-запроса, то будет возвращен html-код.</i></p><h3>Примеры файлов обмена информацией</h3><p><a href="http://v8.1c.ru/edi/edi_stnd/131/to.xml">Заказ на сайт в формате XML</a>.<br><a href="http://v8.1c.ru/edi/edi_stnd/131/from.xml">Заказ с сайта в формате XML</a>.</p><p>Представленный протокол используется для интеграции системы "1С:Предприятие" с системами <a href="http://www.1c-bitrix.ru/products/cms/1c/">"1С-Битрикс: Управление сайтом"</a>, <a href="http://www.umi-cms.ru/product/system/integration/">"UMI.CMS"</a> и другими.</p>


<a name="37">Тестирование и поиск ошибок</a>
=

<p><span style="color: rgb(247, 150, 70);">Раздел находится в разработке</span></p>


<a name="39">События модуля</a>
=

<p><strong>beforeUpdateProduct</strong></p><p>Событие перед началом парсинга продукта</p><p><strong>afterUpdateProduct</strong></p><p>Событие после парсинга продукта</p><p><strong>beforeUpdateOffer</strong></p><p>Событие перед началом парсинга предложения<span class="redactor-invisible-space"></span></p><p><strong>afterUpdateOffer</strong></p><p>Событие после парсинга предложения<br></p><p><strong>beforeProductSync</strong></p><p>Событие перед началом парсинга всех продуктов<span class="redactor-invisible-space"></span></p><p><strong>afterProductSync</strong></p><p>Событие после парсинга всех продуктов<br></p><p><strong>beforeOfferSync</strong></p><p>Событие перед началом парсинга всех предложений<br></p><p><strong>afterOfferSync</strong></p><p>Событие после парсинга всех предложений<br></p><p><strong>afterFinishUploadFile</strong></p><p>Событие, которое вызывается после загрузки архива или xml файла от 1С на ваш сайт</p><p><strong>afterExportOrders</strong></p><p>Событие после формирования заказов из вашего сайта для 1С, в этом методе предлагается вам реализовать смену статусов или указания флага, чтобы исключить повторную выгрузку документов, т.к. они заменят те, что были загружены ранее.</p>