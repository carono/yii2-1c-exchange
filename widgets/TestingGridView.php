<?php


namespace carono\exchange1c\widgets;

use yii\grid\GridView;

class TestingGridView extends GridView
{
    public function init()
    {
        $this->rowOptions = function ($data) {
            if ($data->result === true) {
                return ['class' => 'success'];
            } elseif ($data->result === false) {
                return ['class' => 'danger'];
            } else {
                return ['class' => 'warning'];
            }
        };

        $this->columns = [
            'name',
            'comment:raw'
        ];
        parent::init();
    }
}