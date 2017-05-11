<?php


namespace carono\exchange1c\helpers;


use yii\helpers\ArrayHelper;

class NodeHelper
{
    /**
     * @param \SimpleXMLElement $parent
     * @param \SimpleXMLElement $child
     */
    public static function appendNode(\SimpleXMLElement $parent, \SimpleXMLElement $child)
    {
        $partnersDom = dom_import_simplexml($parent);
        $partnerDom = dom_import_simplexml($child);
        $partnerDom = $partnersDom->ownerDocument->importNode($partnerDom, TRUE);
        $partnersDom->appendChild($partnerDom);
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param $model
     * @param $field1c
     * @param $attribute
     */
    public static function addChild($xml, $model, $field1c, $attribute)
    {
        if ($attribute instanceof \Closure) {
            self::addChild($xml, $model, $field1c, call_user_func($attribute, $model));
        } elseif (is_string($attribute) && isset($model->{$attribute})) {
            self::addChild($xml, $model, $field1c, $model->{$attribute});
        } elseif (is_array($attribute)) {
            $array = $attribute;
            $content = ArrayHelper::remove($array, '@content', '');
            $attributes = ArrayHelper::remove($array, '@attributes', []);
            $item = new \SimpleXMLElement("<{$field1c}>{$content}</{$field1c}>");
            foreach ($attributes as $attribute => $value) {
                $item->addAttribute($attribute, $value);
            }
            foreach ($array as $field => $value) {
                self::addChild($item, $model, $field, $value);
            }
            self::appendNode($xml, $item);
        } elseif ($attribute instanceof \SimpleXMLElement) {
            self::appendNode($xml, $attribute);
        } else {
            $xml->addChild($field1c, $attribute);
        }
    }
}