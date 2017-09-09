<?php


namespace carono\exchange1c\interfaces;


interface GroupInterface extends IdentifierInterface
{
    /**
     * @param \Zenwalker\CommerceML\Model\Group[] $groups
     * @return void
     */
    public static function createTree1c($groups);
}