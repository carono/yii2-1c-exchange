<?php


namespace carono\exchange1c\interfaces;


use yii\db\ActiveRecordInterface;

interface DocumentInterface extends ActiveRecordInterface, FieldsInterface
{
    /**
     * @return DocumentInterface[]
     */
    public static function findOrders1c();

    /**
     * @return ProductInterface[]
     */
    public function getProducts1c();

    public function getRequisites1c();

    /**
     * Получаем контрагента у документа
     * @return PartnerInterface
     */
    public function getPartner1c();
}