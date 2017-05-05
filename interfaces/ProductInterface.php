<?php

namespace carono\exchange1c\interfaces;

use yii\db\ActiveRecordInterface;

interface ProductInterface extends ActiveRecordInterface
{
    public static function getFields1c();

    public function setRequisite1c($name, $value);

    public function getRequisite1c($name);

    public function getRequisites1c();

    public function setCategory1c($id, $name, $parent, $owner);

    public function getCategory1c($id);

    public function setProperty1c($id, $name, $values);

    public function setFeature1c($name, $value);

    public function getProperty1c($id);

    public function setPrice1c($cost, $type, $currency);

    public function addImage1c($path, $caption);

    /**
     * @return GroupInterface
     */
    public function getGroup1c();

    public function getExportFields1c($context = null);
}