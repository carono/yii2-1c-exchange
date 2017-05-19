<?php

namespace carono\exchange1c\interfaces;

use yii\db\ActiveRecordInterface;
use Zenwalker\CommerceML\CommerceML;
use Zenwalker\CommerceML\Model\Group;
use Zenwalker\CommerceML\Model\Offer;
use Zenwalker\CommerceML\Model\Price;
use Zenwalker\CommerceML\Model\Product;
use Zenwalker\CommerceML\Model\Property;
use Zenwalker\CommerceML\Model\Simple;

interface ProductInterface extends ActiveRecordInterface
{
    /**
     * Если по каким то причинам файлы import.xml или offers.xml были модифицированы и какие то данные
     * не попадают в парсер, в самом конце вызывается данный метод, в $product и $cml можно получить все
     * возможные данные для ручного парсинга
     *
     * @param CommerceML $cml
     * @param Product $product
     * @return void
     */
    public function setRaw1cData($cml, $product);

    /**
     * Ассоциативный массив, где
     * Ключ - имя xml тега (import.xml > Каталог > Товары > Товар)
     * Значение - атрибут в модели
     * Например:
     * [
     *      'id'           => 'accounting_id',
     *      'Наименование' => 'title',
     *      'Количество'   => 'remnant',
     *      'Штрихкод'     => 'barcode'
     * ]
     *
     * @return array
     */
    public static function getFields1c();

    /**
     * Установка реквизитов, (import.xml > Каталог > Товары > Товар > ЗначенияРеквизитов > ЗначениеРеквизита)
     * $name - Наименование
     * $value - Значение
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setRequisite1c($name, $value);

    /**
     * @param Group $group
     * @return mixed
     */
    public function setGroup1c($group);

    /**
     * Характеристики товара, (offers.xml > ПакетПредложений > Предложения > Предложение > ХарактеристикиТовара > ХарактеристикаТовара)
     * $name - Наименование
     * $value - Значение
     *
     * @param Offer $offer
     * @param Simple $specification
     * @return void
     */
    public function setSpecification1c($offer, $specification);

    /**
     * $property - Свойство товара (import.xml > Классификатор > Свойства > Свойство)
     * $property->value - Разыменованное значение (string) (import.xml > Классификатор > Свойства > Свойство > Значение)
     * $property->getValueModel() - Данные по значению, Ид значения, и т.д (import.xml > Классификатор > Свойства > Свойство > ВариантыЗначений > Справочник)
     *
     * @param Property $property
     * @return void
     */
    public function setProperty1c($property);


    /**
     * Цена товара, (offers.xml > ПакетПредложений > Предложения > Предложение > Цены)
     * К $price можно обратиться как к массиву, чтобы получить список цен (Цены > Цена)
     * $price->type - тип цены (offers.xml > ПакетПредложений > ТипыЦен > ТипЦены)
     *
     * @param Offer $offer
     * @param Price $price
     * @return void
     */
    public function setPrice1c($offer, $price);

    /**
     * @param string $path
     * @param string $caption
     * @return mixed
     */
    public function addImage1c($path, $caption);

    public function getRequisite1c($name);

    public function getRequisites1c();

    public function getCategory1c($id);

    public function getProperty1c($id);

    /**
     * @return GroupInterface
     */
    public function getGroup1c();

    public function getExportFields1c($context = null);
}