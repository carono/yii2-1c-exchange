<?php


namespace carono\exchange1c\helpers;


use yii\helpers\ArrayHelper;

class NodeHelper
{
    /**
     * @param \SimpleXMLElement $parent
     * @param \SimpleXMLElement $child
     *
     * @return \SimpleXMLElement
     */
    public static function appendNode(\SimpleXMLElement $parent, \SimpleXMLElement $child)
    {
        $parentDom = dom_import_simplexml($parent);
        $childDom = dom_import_simplexml($child);
        $childDom = $parentDom->ownerDocument->importNode($childDom, true);
        $parentDom->appendChild($childDom);
        return $child;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param $model
     * @param $field1c
     * @param $attribute
     * @return int|\SimpleXMLElement|string
     */
    public static function addChild($xml, $model, $field1c, $attribute)
    {
        if ($attribute instanceof \Closure) {
            return self::addChild($xml, $model, $field1c, call_user_func($attribute, $model));
        } elseif (is_string($attribute) && isset($model->{$attribute})) {
            return self::addChild($xml, $model, $field1c, $model->{$attribute});
        } elseif (is_array($attribute)) {
            return self::addArrayChild($xml, $model, $field1c, $attribute);
        } elseif ($attribute instanceof \SimpleXMLElement) {
            return self::appendNode($xml, $attribute);
        } else {
            return $xml->addChild($field1c, $attribute);
        }
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param $model
     * @param $field1c
     * @param $attribute
     * @return int|\SimpleXMLElement|string
     */
    protected static function addArrayChild($xml, $model, $field1c, $attribute)
    {
        $array = $attribute;
        $content = ArrayHelper::remove($array, '@content', '');
        $field1c = ArrayHelper::remove($array, '@name', $field1c);
        $attributes = ArrayHelper::remove($array, '@attributes', []);
        if (is_array($content)) {
            $item = self::addChild($xml, $model, $field1c, $content);
            return $item;
        } else {
            $item = new \SimpleXMLElement("<{$field1c}>$content</{$field1c}>");
            foreach ($attributes as $name => $value) {
                $item->addAttribute($name, $value);
            }
            foreach ($array as $field => $value) {
                self::addChild($item, $model, $field, $value);
            }
            return self::appendNode($xml, $item);
        }
    }
}