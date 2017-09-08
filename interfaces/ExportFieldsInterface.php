<?php


namespace carono\exchange1c\interfaces;


interface ExportFieldsInterface
{
    /**
     * @param null $context
     * @return array
     */
    public function getExportFields1c($context = null);
}