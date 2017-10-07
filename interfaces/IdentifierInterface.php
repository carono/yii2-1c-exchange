<?php


namespace carono\exchange1c\interfaces;


use yii\db\ActiveRecordInterface;

interface IdentifierInterface extends ActiveRecordInterface
{
    /**
     * Возвращаем имя поля в базе данных, в котором хранится ID из 1с
     *
     * @return string
     */
    public static function getIdFieldName1c();
}