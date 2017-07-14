<?php


namespace carono\exchange1c\interfaces;



interface OfferInterface extends FieldsInterface
{
    /**
     * @return \Zenwalker\CommerceML\Model\Group
     */
    public function getGroup1c();
}