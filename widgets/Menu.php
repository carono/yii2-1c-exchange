<?php


namespace carono\exchange1c\widgets;


use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class Menu extends \yii\widgets\Menu
{
    /**
     * @param array $item
     * @return string
     */
    protected function renderItem($item)
    {
        if (isset($item['url'])) {
            $template = ArrayHelper::getValue($item, 'template', $this->linkTemplate);
            return strtr($template, [
                '{url}' => Html::encode(Url::to($item['url'])),
                '{label}' => $item['label'],
                '{options}' => trim(Html::renderTagAttributes(ArrayHelper::getValue($item, 'linkOptions', [])))
            ]);
        }
        $template = ArrayHelper::getValue($item, 'template', $this->labelTemplate);
        return strtr($template, [
            '{label}' => $item['label'],
        ]);
    }
}