<?php

namespace carono\exchange1c\components;

use carono\exchange1c\models\Article;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;

class Breadcrumbs
{
    /**
     * @param Action $action
     * @param $params
     */
    public static function formCrumbs($action, $params)
    {
        $name = 'crumb' . Inflector::camelize($action->controller->id . '-' . $action->id);
        $class = get_called_class();
        if (method_exists($class, $name)) {
            $reflectionMethod = new \ReflectionMethod($class, $name);
            $data = [];
            foreach ($reflectionMethod->getParameters() as $p) {
                $data[] = isset($params[$p->getName()]) ? $params[$p->getName()] : null;
            }
            $action->controller->getView()->params['breadcrumbs'] = call_user_func_array([$class, "$name"], $data);
        }

        $name = 'button' . Inflector::camelize($action->controller->id . '-' . $action->id);
        $class = get_called_class();
        if (method_exists($class, $name)) {
            $reflectionMethod = new \ReflectionMethod($class, $name);
            $data = [];
            foreach ($reflectionMethod->getParameters() as $p) {
                $data[] = isset($params[$p->getName()]) ? $params[$p->getName()] : null;
            }
            foreach ($buttons = call_user_func_array([$class, "$name"], $data) as &$button) {
                Html::addCssClass($button['options'], 'btn-xs');
                $button['options']['href'] = Url::to(ArrayHelper::remove($button, 'url'));
                $button['options']['tag'] = 'a';
            }
            $action->controller->getView()->params['buttons'] = $buttons;
        }
    }

    public static function buttonArticleIndex()
    {
        return [
            [
                'label' => 'Добавить статью',
                'url' => ['article/create'],
                'options' => ['class' => 'btn btn-primary', 'tag' => 'a']
            ]
        ];
    }

    /**
     * @param Article $article
     * @return array
     */
    public static function buttonArticleView($article)
    {
        return [
            [
                'label' => 'Редактировать',
                'url' => ['article/update', 'id' => $article->id],
                'options' => ['class' => 'btn btn-primary']
            ],
            [
                'label' => 'Удалить',
                'url' => ['article/delete', 'id' => $article->id],
                'options' => ['class' => 'btn btn-danger', 'data-confirm' => 'Удалить статью?']
            ]
        ];
    }

    #############################################################################

    /**
     * @param Article $article
     * @return array
     */
    public static function crumbArticleView($article)
    {
        return [
            ['label' => 'Старт', 'url' => ['article/index']],
            $article->name,
        ];
    }

    public static function crumbInterfaceCheck($variable, $class, $interfaceTest)
    {
        return [
            ['label' => 'Интерфейсы', 'url' => ['default/interfaces']],
            $class,
        ];
    }

    public static function crumbDefaultInterfaces()
    {
        return [
            'Интерфейсы',
        ];
    }

    public static function crumbDefaultFiles()
    {
        return [
            'Временные файлы',
        ];
    }

    public static function crumbDefaultSettings()
    {
        return [
            'Настройки модуля',
        ];
    }

    public static function crumbDefaultDocumentation()
    {
        return [
            'Документация по CommerceML',
        ];
    }

    public static function crumbDefaultExport()
    {
        return [
            'Экспорт',
        ];
    }


    public static function crumbDefaultImport()
    {
        return [
            'Импорт',
        ];
    }

    public static function crumbArticleIndex()
    {
        return [
            'Старт',
        ];
    }
}