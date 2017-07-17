<?php

namespace carono\exchange1c\models;

use Yii;
use \carono\exchange1c\models\base\Article as BaseArticle;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "article".
 */
class Article extends BaseArticle
{
    public static function getDb()
    {
        return Yii::$app->get('exchangeDb');
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('CURRENT_TIMESTAMP')
            ]
        ];
    }
}
