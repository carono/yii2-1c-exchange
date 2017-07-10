<?php


namespace carono\exchange1c\interfaces;


use yii\db\ActiveRecordInterface;
use Zenwalker\CommerceML\CommerceML;
use Zenwalker\CommerceML\Model\Document;

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
     *
     * @return PartnerInterface
     */
    public function getPartner1c();

    /**
     * @param CommerceML $cml
     * @param Document $document
     */
    public function setRaw1cData($cml, $document);
}