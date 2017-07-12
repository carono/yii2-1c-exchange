<?php


namespace carono\exchange1c\interfaces;


interface FieldsInterface
{
    /**
     * @return array
     */
    public static function getFields1c();

    /**
     * @param null $context
     * @return array
     */
    public function getExportFields1c($context = null);
}