<?php

namespace carono\exchange1c\components;

use yii\base\Action;
use yii\helpers\Inflector;

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

    public static function crumbDefaultStart()
    {
        return [
            'Старт',
        ];
    }
}