<?php


namespace carono\exchange1c\interfaces;



interface OfferInterface extends FieldsInterface
{
    /**
     * @return GroupInterface
     */
    public function getGroup1c();
}