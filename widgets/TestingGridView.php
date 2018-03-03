<?php


namespace carono\exchange1c\widgets;

use carono\exchange1c\models\TestingClass;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

class TestingGridView extends GridView
{
    public function initColumns()
    {
        $this->columns = [
            'name',
            'expect',
            [
                'attribute' => 'comment',
                'value' => function ($data) {
                    /**
                     * @var TestingClass $data
                     */
                    if ($data->hasErrors()) {
                        return Html::errorSummary($data);
                    }
                    return $data->comment;
                }
            ],
            [
                'class' => ActionColumn::class,
                'header' => 'Result',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $data) {
                        /**
                         * @var TestingClass $data
                         */
                        if ($data->method && !$data->isAutoTest()) {
                            $span = Html::tag('i', '', [
                                'class' => 'glyphicon glyphicon-eye-open',
                                'title' => "Выполнить метод {$data->method}"
                            ]);
                            return Html::a($span, [
                                'testing/index',
                                'class' => (new \ReflectionClass($data))->getShortName(),
                                'result' => $data->method
                            ]);
                        } else {
                            return $data->result;
                        }
                    }
                ]
            ]
        ];
        parent::initColumns();
    }

    public function init()
    {
        $this->rowOptions = function ($data) {
            /**
             * @var TestingClass $data
             */
            $result = $data->testing();
            if (($result === true || $data->hasResult()) && !$data->hasErrors()) {
                return ['class' => 'success'];
            } elseif ($result === false || $data->hasErrors()) {
                return ['class' => 'danger'];
            } else {
                return ['class' => 'warning'];
            }
        };


        parent::init();
    }
}