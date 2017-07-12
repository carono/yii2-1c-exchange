<?php


namespace carono\exchange1c\interfaces;


use yii\db\ActiveRecordInterface;

interface DocumentInterface extends ActiveRecordInterface, FieldsInterface, RawInterface
{
    /**
     * @return DocumentInterface[]
     */
    public static function findDocuments1c();

    /**
     * @return OfferInterface[]
     */
    public function getOffers1c();

    /**
     * @return mixed
     */
    public function getRequisites1c();

    /**
     * Получаем контрагента у документа
     *
     * @return PartnerInterface
     */
    public function getPartner1c();
}