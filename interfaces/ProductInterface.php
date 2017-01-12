<?php

namespace carono\exchange1c\interfaces;

interface ProductInterface
{
    public static function getFields1c();

    public function setRequisite1c($name, $value);

    public function getRequisite1c($name);

    public function setCategory1c($id, $name, $parent, $owner);

    public function getCategory1c($id);

    public function setProperty1c($id, $name, $values);

    public function getProperty1c($id);

    public function setPrice1c($cost, $type, $currency);
}