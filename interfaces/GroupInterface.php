<?php


namespace carono\exchange1c\interfaces;


interface GroupInterface
{
    /**
     * @return string
     */
    public function getId1c();

    /**
     * @param \Zenwalker\CommerceML\Model\Group[] $groups
     * @return void
     */
    public static function createTree1c($groups);
}