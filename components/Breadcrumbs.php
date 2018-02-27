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
     * @param $action
     * @param $params
     */
    protected static function callCrumb($action, $params)
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
    }

    /**
     * @param $action
     * @param $params
     */
    protected static function callButton($action, $params)
    {
        $name = 'button' . Inflector::camelize($action->controller->id . '-' . $action->id);
        $class = get_called_class();
        if (method_exists($class, $name)) {
            $reflectionMethod = new \ReflectionMethod($class, $name);
            $data = [];
            foreach ($reflectionMethod->getParameters() as $p) {
                $data[] = isset($params[$p->getName()]) ? $params[$p->getName()] : null;
            }
            $action->controller->getView()->params['buttons'] = call_user_func_array([$class, $name], $data);
        }
    }

    /**
     * @param Action $action
     * @param $params
     */
    public static function formCrumbs($action, $params)
    {
        self::callCrumb($action, $params);
        self::callButton($action, $params);
    }

    public static function buttonArticleIndex()
    {
        return [
            [
                'label' => 'Добавить статью',
                'url' => ['article/create'],
                'linkOptions' => ['class' => 'btn-xs btn btn-primary']
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
                'linkOptions' => ['class' => 'btn-xs btn btn-primary']
            ],
            [
                'label' => 'Добавить подстатью',
                'url' => ['article/create', 'parent' => $article->id],
                'linkOptions' => ['class' => 'btn-xs  btn btn-primary']
            ],
            [
                'label' => 'Удалить',
                'url' => ['article/delete', 'id' => $article->id],
                'options' => ['data-confirm' => 'Удалить статью?'],
                'linkOptions' => ['class' => 'btn-xs btn btn-danger']
            ]
        ];
    }

    #############################################################################

    public static function crumbTestingIndex()
    {
        return [
            'Тестирование модуля',
        ];
    }

    /**
     * @param Article $article
     * @return array
     */
    public static function crumbArticleView($article)
    {
        $items = [
            ['label' => 'Старт', 'url' => ['article/index']]
        ];
        $parent = $article;
        while ($parent = $parent->parent) {
            $items[] = ['label' => $parent->name, 'url' => ['article/view', 'id' => $parent->id]];
        }
        $items[] = $article->name;
        return $items;
    }

    public static function crumbArticleUpdate($article)
    {
        $items = [
            ['label' => 'Старт', 'url' => ['article/index']]
        ];
        $parent = $article;
        while ($parent = $parent->parent) {
            $items[] = ['label' => $parent->name, 'url' => ['article/view', 'id' => $parent->id]];
        }
        $items[] = ['label' => $article->name, 'url' => ['article/view', 'id' => $article->id]];
        $items [] = 'Редактирование';
        return $items;
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